<?php

/*  Login request
<?xml version='1.0' standalone='yes'?>
<request>
    <type>login</type>
    <email>...</email>
    <pass>...</pass>
</request>
*
*   Login result 
<?xml version='1.0' standalone='yes'?>
<response>
    <type>login</type>
    <status>ok, error</status>
    <data>
        <id></id>
        <name></name>
    </data>
    <error>...</error>
    <time>...</time>
</response>
*/

/*  Send message request
<?xml version='1.0' standalone='yes'?>
<request>
    <email>...</email>
    <pass>...</pass>
</request>
*/

$xmlstr = <<<XML
<?xml version='1.0' standalone='yes'?>
<movies>
    <movie>
        <title>Movies/movie/title</title>
        <characters>
            <character>
                <name>Movies/movie/characters/character(1)/name</name>
                <actor>Movies/movie/characters/character(1)/actor</actor>
            </character>
            <character>
                <name>Movies/movie/characters/character(2)/name</name>
                <actor>Movies/movie/characters/character(2)/actor</actor>
            </character>
        </characters>
        <rating type="thumbs">7</rating>
        <rating type="stars">5</rating>
    </movie>
</movies>
XML;
$movies = new SimpleXMLElement($xmlstr);

/* Для каждого узла <character>, мы отдельно выведем имя <name>. */
foreach ($movies->movie->characters->character as $character) {
   echo $character->name, ' играет ', $character->actor, PHP_EOL;
}
/* Доступ к узлу <rating> первого фильма.
 * Так же выведем шкалу оценок. */
foreach ($movies->movie[0]->rating as $rating) {
    switch((string) $rating['type']) { // Получение атрибутов элемента по индексу
    case 'thumbs':
        echo $rating, ' thumbs up';
        break;
    case 'stars':
        echo $rating, ' stars';
        break;
    }
}
/* Вывод XML данных */
echo $movies->asXML();