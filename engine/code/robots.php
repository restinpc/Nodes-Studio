<?php
/**
* Robots.txt generator.
* @path /engine/code/robots.php
*
* @name    Nodes Studio    @version 2.0.2
* @author  Alexandr Virtual    <developing@nodes-tech.ru>
* @license http://nodes-studio.com/license.txt GNU Public License
*/
echo 'User-agent: *
Host: '.$_SERVER["HTTP_HOST"].$_SERVER["DIR"].'
Disallow: /engine/
Disallow: /res/
Disallow: /script/
Disallow: /font/
Disallow: /admin$
Disallow: /account$
Disallow: *.php
Allow: /sitemap.php
Sitemap: //'.$_SERVER["HTTP_HOST"].$_SERVER["DIR"].'/sitemap.xml';