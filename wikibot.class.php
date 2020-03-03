<?php
/**
 * LM Wikipedia PHP bot
 * 
 * @package lm-wiki-bot
 * @author Arash Soleimani <arash@leomoon.com>
 * @link http://arashsoleimani.com
 * @license https://opensource.org/licenses/MIT - The MIT License
 * @version 1.0
 * 
 */
class lm_wiki_bot {
    
    private $url = "https://test.wikipedia.org/w/api.php";
    private $username = "";
    private $password = "";
    private $logintoken = null;
    public $errorMessage = null;

    /**
     * Constractor
     * @param array $config [
     *                      'username'=> string,
     *                      'password'=> string,
     *                      'url'=>string
     *                      ]
     * 
     * You just need username and password for edit action. if you want to use search or get recent changes just set your local url:
     * <code>
     * $bot = new lm_wiki_bot(['url'=>'https://fa.wikipedia.org']);
     * </code>
     * 
     * For add/edit or patrol you should do this:
     * <code>
     * $bot = new lm_wiki_bot([
     *                  'username'=>'YourBotUsername',
     *                  'password'=>'YourBotPassword',
     *                  'url'=>'https://en.wikipedia.org'
     *              ]);
     * </code>
     * 
     */
    function __construct($config=null){
        if(isset($config['url'])){
            $this->url = $config['url']."/w/api.php";
        }
        if(isset($config['username'])){
            $this->username = $config['username'];
        }
        if(isset($config['password'])){
            $this->password = $config['password'];
        }
 
    }

    /**
     * Get token for login, edit (csrf), patrol
     * 
     * @param string $type // login|patrol - default: csrf
     * 
     * @return string
     */
    private function token($type=null) {
        $requestInfo = [
            "action" => "query",
            "meta" => "tokens"
        ];
        if($type == "login"){
            $requestInfo["type"] = "login";
            $returnName = "logintoken";
        }else{
            $returnName = "csrftoken";
        }
      
        $result = $this->get($requestInfo);
        $token = $result["query"]["tokens"][$returnName];
        if($type == "login" && !is_null($token))
            $this->logintoken = $token;

        return $token;
    }

    /**
     * Login request - needed for edit
     * 
     * @return bool
     */
    private function login() {
        if(!is_null($this->logintoken))
            return true;
        $requestInfo = [
            "action" => "login",
            "lgname" => $this->username,
            "lgpassword" => $this->password,
            "lgtoken" => $this->token('login')
        ];
        $result = $this->post($requestInfo);
        if($result['login']['result'] == "Success"){
            return true;
        }else{
            $this->errorMessage = $result['login']['reason'];
            return false;
        }
    }

    /**
     * Send POST request to API
     * 
     * @param array $requestInfo
     * 
     * @return array
     */
    private function post($requestInfo){
       
        $requestInfo['format'] = 'json';
        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, $this->url);
        curl_setopt($ch, CURLOPT_POST, true);
        curl_setopt($ch, CURLOPT_POSTFIELDS, http_build_query($requestInfo));
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_COOKIEJAR, "cookie.txt");
        curl_setopt($ch, CURLOPT_COOKIEFILE, "cookie.txt");
        $output = curl_exec($ch);
        curl_close($ch);
        $output = json_decode($output, true);
        return $output;
    }

    /**
     * Send GET request to API
     * 
     * @param array $requestInfo
     * 
     * @return array
     */
    private function get($requestInfo){
       
        $requestInfo['format'] = 'json';
        $ch = curl_init($this->url."?".http_build_query($requestInfo));
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_COOKIEJAR, "cookie.txt");
        curl_setopt($ch, CURLOPT_COOKIEFILE, "cookie.txt");
        $output = curl_exec($ch);
        curl_close($ch);
        $output = json_decode($output, true);
        return $output;
    }

    /**
     * Search
     * @param array $params
     * 
     * Simple usage:
     * <code>
     * $bot->search(['keyword'=>"something"]);
     * </code>
     * 
     * Advanced usage:
     * <code>
     * $bot->search([
     *              'offset'=>10,
     *              'limit'=>10,
     *              'sort'=>'last_edit_desc'
     *              'keyword'=>'something'
     *              ]);
     * </code>
     * 
     * @return array
     */
    public function search($params){
        $requestInfo = ['action'=>'query'];
        if(isset($params['prefix'])){
             $requestInfo['list'] = "prefixsearch";
             $requestInfo['pssearch'] = $params['keyword'];
             $searchType = "prefixsearch";
        }else{
            $requestInfo['list'] = "search";
            $requestInfo['srsearch'] = $params['keyword'];
            $searchType = "search";
        } 
        if(isset($params['ns'])) $requestInfo['srnamespace'] = $params['ns'];
        if(isset($params['offset'])) $requestInfo['sroffset'] = $params['offset'];
        if(isset($params['limit'])) $requestInfo['srlimit'] = $params['limit'];
        if(isset($params['sort'])) $requestInfo['srsort'] = $params['sort'];
  
        $data = $this->get($requestInfo);
        return $data['query'][$searchType];
    }

    /**
     * Recent Changes
     * 
     * @param array $params
     * 
     * Simple usage:
     * <code>
     * $bot->recent();
     * </code>
     * 
     * Advanced sample:
     * <code>
     * $bot->recent([
     *              'limit'=>30,
     *              'ns'=>0,
     *              'sort'=>'older',
     *              'type'=>'!patrolled'
     *             ]);
     * </code>
     * 
     * @return array
     */
    public function recent($params=null){
        $requestInfo = [
            'action'=>'query',
            'list'=>'recentchanges'
        ];
        if(isset($params['limit'])) $requestInfo['rclimit'] = $params['limit'];
        if(isset($params['user'])) $requestInfo['rcuser'] = $params['user'];
        if(isset($params['order'])) $requestInfo['rcdir'] = $params['order'];
        if(isset($params['ns'])) $requestInfo['rcnamespace'] = $params['ns'];
        if(isset($params['type'])){
            $requestInfo['rcshow'] = $params['type'];
            $requestInfo['prop'] = $params['info'];
        }
        $requestInfo['rcprop'] = "title|sizes|timestamp|ids|user|userid|comment|redirect|tags";
        $data = $this->get($requestInfo);
        printr($data);
        return $data['query']["recentchanges"];
    }

    /**
     * Create and edit pages 
     * @param array $params
     * 
     * Simple Edit/Create
     * <code>
     * $bot->edit([
     *             'title'=>'something',
     *             'text'=>'Hello'
     *            ]);
     * </code>
     * 
     * Advanced sample
     * <code>
     * $bot->edit([
     *             'pageid'=>22817,
     *             'appendtext'=>'Hello',
     *             'summary'=>'Test Edit',
     *             'recreate'=>true,
     *             'section'=>'new',
     *             'sectiontitle'=>'Something'
     *            ]);
     * </code>
     * 
     * @return array
     */
    public function edit($params){
        $data = $this->login();
        $requestInfo = [
            "action" => "edit",
            "token" => $this->token()
        ];

        if(isset($params['title'])) $requestInfo['title'] = $params['title'];
        if(isset($params['summary'])) $requestInfo['summary'] = $params['summary'];
        if(isset($params['recreate'])) $requestInfo['recreate'] = $params['recreate'];
        if(isset($params['createonly'])) $requestInfo['createonly'] = $params['createonly'];
        if(isset($params['prependtext'])) $requestInfo['prependtext'] = $params['prependtext'];
        if(isset($params['text'])) $requestInfo['text'] = $params['text'];
        if(isset($params['appendtext'])) $requestInfo['appendtext'] = $params['appendtext'];
        if(isset($params['pageid'])) $requestInfo['pageid'] = $params['pageid'];
        if(isset($params['section'])) $requestInfo['section'] = $params['section'];
        if(isset($params['sectiontitle'])) $requestInfo['sectiontitle'] = $params['sectiontitle'];
        $result = $this->post($requestInfo);
        return $result;
    }

    /**
     * Get the contents of a page
     * @param string $pageTitle
     * @param string $format
     * 
     * Get the original wikitext content of a page:
     * <code>
     * $bot->content("Software bot");
     * </code>
     * 
     * Get parsed HTML content of a page:
     * <code>
     * $bot->content("Software bot", "text");
     * </code>
     * 
     * @return string
     */
    public function content($title, $format="wikitext"){
        $requestInfo = [
            'action'=>'parse',
            'page'=>$title,
            'prop'=>$format,
            'formatversion'=>2
        ];
        $data = $this->get($requestInfo);
        return $data['parse'][$format];
    }

    /**
     * Show Errors
     */
    public function show_errors(){
        echo $this->errorMessage."\r\n";
    }

}
?>