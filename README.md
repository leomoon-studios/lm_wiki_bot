# LM Wikipedia bot 
LM Wikipedia bot is a PHP class to create, edit and search articles on Wikipedia

## Documentation

### Installation
Include wikibot class:
```php
require_once('wikibot.class.php');
```

You just need a username and password for edit action. if you want to use search or get recent changes just set your local URL:
```php
$bot = new lm_wiki_bot(['url'=>'https://fa.wikipedia.org']);
```

For add/edit or patrol you should do this:
```php
$bot_config = [
    'url'=>'https://en.wikipedia.org',
    'username'=>'account username',
    'password'=>'account password'
];
$bot = new lm_wiki_bot($bot_config);
```

### Search
Perform a full text search

Simple:
```php
$bot->search(['keyword'=>"something"]);
```

Advanced:
```php
 $bot->search([
            'offset'=>10,
            'limit'=>10,
            'sort'=>'last_edit_desc'
            'keyword'=>'something'
            ]);
```

Parameters:
|Name|Description|Values|Default|
|-----------|------------------------|-------------------------------------|-------|
|namespace|Search only within these namespaces| [Namespace numbers](https://en.wikipedia.org/wiki/Wikipedia:Namespace) separate with \| |0|
|limit|How many total pages to return|The value must be between 1 and 500|10|
|offset|When more results are available, use this to continue|-|0
|sort|Set the sort order of returned results|create_timestamp_asc, create_timestamp_desc, incoming_links_asc, incoming_links_desc, just_match, last_edit_asc, last_edit_desc, none, random, relevance|relevance|
|prefix|Perform a prefix search for page titles|true or false|false|

### Recent Changes


### Content


### Add/Edit

