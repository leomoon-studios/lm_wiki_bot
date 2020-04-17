<?php
require_once 'wikibot.class.php';

$bot_config = [
    'url'=>'https://en.wikipedia.org',
    'username'=>'Your Username',
    'password'=>'Your Password'
];

$bot = new lm_wiki_bot($bot_config);

$data = $bot->search([
    'offset'=>10,
    'limit'=>10,
    'sort'=>'last_edit_desc',
    'keyword'=>'cellular'
]);

print_r($data);