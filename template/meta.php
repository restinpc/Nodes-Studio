<?php
/**
* Site meta data.
* @path /template/meta.php
*
* @name    Nodes Studio    @version 2.0.2
* @author  Alexandr Virtual    <developing@nodes-tech.ru>
* @license http://nodes-studio.com/license.txt GNU Public License
*/
if(!isset($fout)) $fout = '';
$fout .= '<meta name="viewport" content="width=device-width, initial-scale=1.0" />
<meta http-equiv="Cache-control" content="no-cache" />
<meta name="copyright" content="Copyright '.$_SERVER["HTTP_HOST"].', '.date("Y").'" />
<meta name="robots" content="index, follow" />
<link rel="apple-touch-icon" sizes="180x180" href="'.$_SERVER["DIR"].'/apple-touch-icon.png">
<link rel="icon" type="image/png" href="'.$_SERVER["DIR"].'/favicon/favicon-32x32.png" sizes="32x32">
<link rel="icon" type="image/png" href="'.$_SERVER["DIR"].'/favicon/favicon-16x16.png" sizes="16x16">
<link rel="manifest" href="'.$_SERVER["DIR"].'/favicon/manifest.json">
<link rel="mask-icon" href="'.$_SERVER["DIR"].'/favicon/safari-pinned-tab.svg" color="#5bbad5">
<link rel="shortcut icon" href="'.$_SERVER["DIR"].'/favicon.ico">
<meta name="msapplication-config" content="'.$_SERVER["DIR"].'/favicon/browserconfig.xml">
<meta name="theme-color" content="#ffffff">';