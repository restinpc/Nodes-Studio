<?php
/**
* Robots.txt generator.
* @path /engine/code/robots.php
*
* @name    Nodes Studio    @version 2.0.3
* @author  Aleksandr Vorkunov  <developing@nodes-tech.ru>
* @license http://www.apache.org/licenses/LICENSE-2.0 GNU Public License
*/
echo 'User-agent: *
Host: '.$_SERVER["HTTP_HOST"].$_SERVER["DIR"].'
Disallow: /admin$
Disallow: /account$
Disallow: /engine/
Disallow: /font/
Disallow: /res/
Disallow: /script/
Disallow: /template/
Disallow: *.php
Allow: /sitemap.php
Sitemap: '.$_SERVER["PUBLIC_URL"].'/sitemap.xml';