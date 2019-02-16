<?php
/**
* Robots.txt generator.
* @path /engine/code/robots.php
*
* @name    Nodes Studio    @version 3.0.0.1
* @author  Aleksandr Vorkunov  <developing@nodes-tech.ru>
* @license http://www.apache.org/licenses/LICENSE-2.0
*/
echo 'User-agent: *
Host: '.$_SERVER["HTTP_HOST"].$_SERVER["DIR"].'
Disallow: /admin$
Disallow: /account$
Disallow: /engine/
Disallow: /font/
Disallow: /res/
Disallow: *.php
Allow: /sitemap.php
Sitemap: '.$_SERVER["PUBLIC_URL"].'/sitemap.xml';