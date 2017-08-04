<?php
/**
* Site meta data.
* @path /template/meta.php
*
* @name    Nodes Studio    @version 2.0.3
* @author  Alex Developer  <developing@nodes-tech.ru>
* @license http://www.apache.org/licenses/LICENSE-2.0 GNU Public License
*/
if(!isset($fout)) $fout = '';
$fout .= '
<meta name="copyright" content="Copyright '.$_SERVER["HTTP_HOST"].', '.date("Y").'" />
<link rel="apple-touch-icon" sizes="180x180" href="'.$_SERVER["DIR"].'/apple-touch-icon.png" />
<link rel="icon" type="image/png" href="'.$_SERVER["DIR"].'/favicon/favicon-32x32.png" sizes="32x32" />
<link rel="icon" type="image/png" href="'.$_SERVER["DIR"].'/favicon/favicon-16x16.png" sizes="16x16" />
<link rel="manifest" href="'.$_SERVER["DIR"].'/favicon/manifest.json" />
<link rel="mask-icon" href="'.$_SERVER["DIR"].'/favicon/safari-pinned-tab.svg" color="#5bbad5" />
<link rel="shortcut icon" href="'.$_SERVER["DIR"].'/favicon.ico" />
<meta name="msapplication-config" content="'.$_SERVER["DIR"].'/favicon/browserconfig.xml" />
<meta name="theme-color" content="#ffffff" />';