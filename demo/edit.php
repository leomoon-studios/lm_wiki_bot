<?php
require_once 'wikibot.class.php';

$bot_config = [
    'url'=>'https://test.wikipedia.org',
    'username'=>'Your Username',
    'password'=>'Your Password'
];

$bot = new lm_wiki_bot($bot_config);

$bot->edit([
    'title'=>'page title',
    'text'=>'Hello'
    ]);
