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
$results = $bot->search(['keyword'=>"something"]);
```

Advanced:
```php
 $results = $bot->search([
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
List all the recent changes to the wiki, in the same manner as Special:RecentChanges lists them

Simple:
```php
$results = $bot->recent();
```

Advanced:
```php
$results = $bot->recent([
        'limit'=>30,
        'ns'=>0,
        'sort'=>'older',
        'type'=>'!patrolled'
        ]);
```

Parameters:
|Name|Description|Values|Default|
|-----------|------------------------|-------------------------------------|-------|
|namespace|Search only within these namespaces| [Namespace numbers](https://en.wikipedia.org/wiki/Wikipedia:Namespace) separate with \| |0|
|limit|How many total changes to return|The value must be between 1 and 500|10|
|user|Only list changes by this user| user name, IP or user ID (e.g. #12345)|null|
|order|In which direction to enumerate|newer, older|older|
|type|Show only items that meet these criteria. For example: minor edits done by logged-in users|!anon, !autopatrolled, !bot, !minor, !patrolled, !redirect, anon, autopatrolled, bot, minor, patrolled, redirect, unpatrolled - separate with \| | null|

### Content
Get the original wikitext content of a page:
```php
$result = $bot->content("Software bot");
```

Get parsed HTML content of a page:
```php
$result = $bot->content("Software bot", "text");
```

### Add/Edit
Create and edit pages

Simple Edit/Create:
```php
$bot->edit([
        'title'=>'page title',
        'text'=>'Hello'
        ]);
```

Advanced sample:
```php
$bot->edit([
        'pageid'=>22817,
        'appendtext'=>'Goodbye',
        'summary'=>'Test Edit comment',
        'recreate'=>true,
        'section'=>'new',
        'sectiontitle'=>'Something'
        ]);
```

Parameters:
|Name|Description|Values|Default|
|-----------|------------------------|-------------------------------------|-------|
|title|Title of the page to edit. Cannot be used together with pageid|-|-|
|pageid|Page ID of the page to edit. Cannot be used together with title|-|-|
|summary|Edit summary|-|section title when section=new and sectiontitle is not set|
|section|Section number. 0 for the top section, new for a new section|-|-|
|sectiontitle|The title for a new section|-|-|
|text|Page content|-|-|
|appendtext|Add this text to the end of the page. Overrides text|Use section=new to append a new section, rather than this parameter|-|
|prependtext|Add this text to the beginning of the page. Overrides text|-|-|
|createonly|Don't edit the page if it exists already|true or false|false|
|recreate|Override any errors about the page having been deleted in the meantime|true or false|false|
|undo|Undo this revision. Overrides text, prependtext and appendtext|The value must be no less than 0|-|
|undoafter|Undo all revisions from undo to this one. If not set, just undo one revision|The value must be no less than 0|-|
|redirect|Automatically resolve redirects|true or false|false|

