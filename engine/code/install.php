<?php
/**
* Framework Installer.
* @path /engine/code/install.php
*
* @name    Nodes Studio    @version 3.0.0.1
* @author  Aleksandr Vorkunov  <developing@nodes-tech.ru>
* @license http://www.apache.org/licenses/LICENSE-2.0
*/
function output(){
array_push($_SERVER["CONSOLE"], "output()");
$output = '';
if(!empty($_POST["mysql_server"])){
    $error_output = '<br/><br/>'
    . '<center>'
    . '<a href="'.$_SERVER["DIR"].'/install.php"><input type="button" value="Reinstall site" class="btn" style="width: 280px;" /></a><br/><br/>'
    . '</center>';
    $flag = 0;
    $output .= 'Checking MySQL connection.. ';
    $link = mysqli_connect($_POST["mysql_server"], 
        $_POST["mysql_login"],
        $_POST["mysql_pass"]);
    if($link){
        if(mysqli_select_db($link, $_POST["mysql_db"]))
            $flag = 1;
    }if($flag){
        $crypt_salt = str_replace("+", ".", substr(md5(date("U").$_SERVER["HTTP_HOST"]), 0, 6));
        $salt = substr(md5(date("U").$_SERVER["REMOTE_ADDR"]),0,22);
        $salt = '$'.implode('$',array( "2y", str_pad(11, 2, "0", STR_PAD_LEFT), str_replace("+",".",$salt)));
        $salt = substr($salt, 0, (strlen($salt) - strlen($_SERVER["config"]["crypt_salt"]))-1);
        $key = $salt.$crypt_salt.'.';
        $hashed_password = crypt($password, $key);
        $pass = str_replace($key, '', $hashed_password);
        $output .= "Ok.<br/>";
        
        $query = "DROP TABLE IF EXISTS 
            `nodes_access`,
            `nodes_admin`,
            `nodes_aframe`,
            `nodes_agent`,
            `nodes_anonim`,
            `nodes_attendance`,
            `nodes_backend`,
            `nodes_cache`,
            `nodes_catalog`,
            `nodes_comment`,
            `nodes_config`,
            `nodes_content`,
            `nodes_error`,
            `nodes_firebase`,
            `nodes_image`,
            `nodes_inbox`,
            `nodes_invoice`,
            `nodes_language`,
            `nodes_meta`,
            `nodes_order`,
            `nodes_outbox`,
            `nodes_pattern`,
            `nodes_perfomance`,
            `nodes_product`,
            `nodes_product_data`,
            `nodes_product_order`,
            `nodes_product_property`,
            `nodes_property_data`,
            `nodes_referrer`,
            `nodes_server_client`,
            `nodes_server_data`,
            `nodes_server_lobby`,
            `nodes_server_object`,
            `nodes_server_scene`,
            `nodes_shipping`,
            `nodes_transaction`,
            `nodes_user`,
            `nodes_user_outbox`,
            `nodes_vr_level`,
            `nodes_vr_link`,
            `nodes_vr_navigation`,
            `nodes_vr_object`,
            `nodes_vr_project`,
            `nodes_vr_scene`;

CREATE TABLE IF NOT EXISTS `nodes_access` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `admin_id` int(11) NOT NULL,
  `user_id` int(11) NOT NULL,
  `access` int(1) NOT NULL,
  PRIMARY KEY (`id`),
  KEY `user_id` (`user_id`)
) ENGINE=MyISAM  DEFAULT CHARSET=utf8 AUTO_INCREMENT=1;


CREATE TABLE IF NOT EXISTS `nodes_admin` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `name` varchar(40) NOT NULL,
  `url` varchar(40) NOT NULL,
  `img` varchar(40) NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=MyISAM  DEFAULT CHARSET=utf8 AUTO_INCREMENT=1 ;


CREATE TABLE IF NOT EXISTS `nodes_user` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `admin` int(1) NOT NULL,
  `name` varchar(25) NOT NULL,
  `photo` varchar(400) NOT NULL,
  `url` varchar(400) NOT NULL,
  `email` varchar(400) NOT NULL,
  `pass` varchar(64) NOT NULL,
  `salt` varchar(32) NOT NULL,
  `lang` varchar(3) NOT NULL,
  `balance` double NOT NULL,
  `ip` varchar(20) NOT NULL,
  `ban` tinyint(1) NOT NULL,
  `online` int(11) NOT NULL,
  `rating` int(11) NOT NULL DEFAULT '0',
  `votes` int(11) NOT NULL DEFAULT '0',
  `token` varchar(32) NOT NULL,
  `confirm` tinyint(1) NOT NULL,
  `code` varchar(4) NOT NULL,
  `bulk_ignore` tinyint(4) NOT NULL DEFAULT '0',
  PRIMARY KEY (`id`)
) ENGINE=MyISAM  DEFAULT CHARSET=utf8 AUTO_INCREMENT=1 ;


CREATE TABLE IF NOT EXISTS `nodes_config` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `name` varchar(100) NOT NULL,
  `value` text NOT NULL,
  `text` varchar(100) NOT NULL,
  `type` varchar(10) NOT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `name` (`name`)
) ENGINE=MyISAM  DEFAULT CHARSET=utf8 AUTO_INCREMENT=1 ;


INSERT INTO `nodes_config` (`name`, `value`, `text`, `type`) VALUES
('name', '".mysqli_real_escape_string($link,$_POST["name"])."', 'Site name', 'string'),
('description', '".mysqli_real_escape_string($link,$_POST["description"])."', 'Description', 'string'),
('email', '".mysqli_real_escape_string($link,$_POST["admin_email"])."', 'Site email', 'string'),
('language', '".mysqli_real_escape_string($link,$_POST["language"])."', 'Site language', 'string'),
('languages', '".mysqli_real_escape_string($link,str_replace("'", "\'", $_POST["languages"]))."', 'Available languages', 'string'),
('image', '".$_SERVER["PUBLIC_URL"]."/img/cms/nodes_studio.png', 'Site image', 'string'),
('email_image', '".$_SERVER["PUBLIC_URL"]."/img/logo.png', 'Email header image', 'string'),
('invoice_image', '".$_SERVER["PUBLIC_URL"]."/img/logo.png', 'Invoice logo image', 'string');


INSERT INTO `nodes_user` (`admin`, `name`, `photo`, `url`, `email`, `pass`, `salt`, `lang`, `balance`, `ip`, `ban`, `online`, `token`, `confirm`, `code`, `bulk_ignore`) VALUES
('1', '".mysqli_real_escape_string($link,str_replace("'", "\'", $_POST["admin_name"]))."', 'admin.jpg', '', '".htmlspecialchars($_POST["admin_email"])."', '".$pass."', '".$salt."', '".$_POST["language"]."', 0, '', -1, 0, '', 1, 0, 0);
     

INSERT INTO `nodes_admin` (`id`, `name`, `url`, `img`) VALUES
(1, 'Access', 'access', 'cms/access.jpg'),
(2, 'Pages', 'pages', 'cms/pages.jpg'),
(3, 'Content', 'content', 'cms/content.jpg'),
(4, 'Products', 'products', 'cms/products.jpg'),
(5, 'Panoramas', 'panoramas', 'cms/pano.jpg'),
(6, 'Users', 'users', 'cms/users.jpg'),
(7, 'Orders', 'orders', 'cms/orders.jpg'),
(8, 'Finance', 'finance', 'cms/finance.jpg'),
(9, 'Language', 'language', 'cms/language.jpg'),
(10, 'Attendance', 'attendance', 'cms/attendance.jpg'),
(11, 'Config', 'config', 'cms/config.jpg'),
(12, 'Backend', 'backend', 'cms/backend.jpg'),
(13, 'Templates', 'templates', 'cms/templates.jpg'),
(14, 'Perfomance', 'perfomance', 'cms/perfomance.jpg'),
(15, 'Outbox', 'outbox', 'cms/outbox.jpg'),
(16, 'Errors', 'errors', 'cms/errors.jpg');


INSERT INTO `nodes_access` (`id`, `admin_id`, `user_id`, `access`) VALUES
(1, 1, 1, 2),
(2, 2, 1, 2),
(3, 3, 1, 2),
(4, 4, 1, 2),
(5, 5, 1, 2),
(6, 6, 1, 2),
(7, 7, 1, 2),
(8, 8, 1, 2),
(9, 9, 1, 2),
(10, 10, 1, 2),
(11, 11, 1, 2),
(12, 12, 1, 2),
(13, 13, 1, 2),
(14, 14, 1, 2),
(15, 15, 1, 2),
(16, 16, 1, 2);


INSERT INTO `nodes_config` (`name`, `value`, `text`, `type`) VALUES
('template', 'default', 'Template', 'string'),
('default', 'content.php', 'System', 'string'),
('debug', '0', 'Debug mode', 'bool'),
('cron', '1', 'jQuery cron', 'bool'),
('cache', '1', 'Allow cache', 'bool'),
('compress', '1', 'Compress HTML', 'bool'),
('cardboard', '1', 'Cardboard mode (beta)', 'bool'),
('sandbox', '1', 'Sandbox payment mode', 'bool'),
('autoupdate', '1', 'Engine auto-update', 'bool'),
('autobackup', '1', 'Auto backup', 'bool'),
('catch_patterns', '1', 'Behavioral monitoring', 'bool'),
('backup_files', '0', 'Backup files', 'bool'),
('free_message', '0', 'Messages between users', 'bool'),
('daily_report', '1', 'Daily report to email', 'bool'),
('confirm_signup_email', '1', 'Email confirmation while sign up', 'bool'),
('send_comments_email', '1', 'Email admin on comment', 'bool'),
('send_registration_email', '1', 'Email user on sign up', 'bool'),
('send_message_email', '1', 'Email user on message', 'bool'),
('send_paypal_email', '1', 'Email user on payment', 'bool'),
('public_notifications', '1', 'Push notification for guests', 'bool'),
('firebase_sender_id', '', '<a href=\"http://console.firebase.google.com/\" target=\"_blank\">Firebase Sender ID</a>', 'string'),
('firebase_server_key', '', '<a href=\"http://console.firebase.google.com/\" target=\"_blank\">Firebase Server Key</a>', 'string'),
('yandex_money', '', '<a href=\"https://money.yandex.ru/\" target=\"_blank\">Yandex Money ID</a>', 'string'),
('paypal_test', '1', 'PayPal test mode', 'bool'),
('paypal_id', '', '<a href=\"https://www.paypal.com/\" target=\"_blank\">PayPal user ID</a>', 'string'),
('payment_description', '', 'Payment description', 'string'),
('coinpayment_private', '', '<a href=\"https://www.coinpayments.net/\" target=\"_blank\">Coinpayments Private Key</a>', 'string'),
('coinpayment_public', '', '<a href=\"https://www.coinpayments.net/\" target=\"_blank\">Coinpayments Public Key</a>', 'string'),
('vk_id', '', 'VK client ID', 'string'),
('fb_link', 'https://facebook.com/', '<a href=\"https://facebook.com/\" target=\"_blank\">Facebook page URL</a>', 'string'),
('fb_id', '', 'Facebook client ID', 'string'),
('fb_secret', '', 'Facebook client secret', 'string'),
('tw_link', 'https://twitter.com', '<a href=\"https://twitter.com/\" target=\"_blank\">Twitter page URL</a>', 'string'),
('tw_key', '', 'Twitter consumer key', 'string'),
('tw_secret', '', 'Twitter consumer secret', 'string'),
('gp_link', 'https://plus.google.com/', '<a href=\"https://plus.google.com/\" target=\"_blank\">Google+ page URL</a>', 'string'),
('gp_id', '0', 'Google+ user ID', 'string'),
('gp_secret', '', 'Google+ client secret', 'string'),
('gp_dev', '', 'Google+ developer key', 'string'),
('backup_interval', '10', 'Day interval of backup', 'int'),
('db_table_limit', '1000000', 'Max rows inside DB table', 'int'),
('token_limit', '20', 'Max queries  minute from session', 'int'),
('ip_limit', '60', 'Max queries  minute from IP', 'int'),
('outbox_limit', '1', 'Max outbox  minute', 'int'),
('version', '1', 'System', 'int'),
('lastupdate', '0', 'System', 'int'),
('checkupdate', '0', 'System', 'int'),
('cron_images', '0', 'System', 'int'),
('cron_exec', '0', 'System', 'int'),
('cron_done', '0', 'System', 'int'),
('lastreport', '0', 'System', 'int'),
('backup_limit', '3', 'System', 'int'),
('backup_date', '0', 'System', 'int');


CREATE TABLE IF NOT EXISTS `nodes_attendance` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `cache_id` int(11) NOT NULL,
  `user_id` int(11) NOT NULL,
  `token` varchar(32) NOT NULL,
  `ref_id` int(11) NOT NULL,
  `ip` varchar(20) NOT NULL,
  `agent_id` int(11) NOT NULL,
  `date` int(11) NOT NULL,
  `display` tinyint(1) NOT NULL,
PRIMARY KEY (`id`),
KEY `token` (`token`),
KEY `display` (`display`)
) ENGINE=MyISAM  DEFAULT CHARSET=utf8 AUTO_INCREMENT=1 ;

CREATE TABLE IF NOT EXISTS `nodes_aframe` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `caption` varchar(100) NOT NULL,
  `text` text NOT NULL,
  `url` varchar(40) NOT NULL,
  `image` varchar(100) NOT NULL,
  `file` varchar(40) NOT NULL,
  `lang` varchar(4) NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=MyISAM  DEFAULT CHARSET=utf8 AUTO_INCREMENT=1 ;


CREATE TABLE IF NOT EXISTS `nodes_server_client` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `lobby_id` int(11) NOT NULL,
  `user_id` int(11) NOT NULL,
  `object_id` int(11) NOT NULL,
  `online` int(11) NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=MyISAM  DEFAULT CHARSET=utf8 AUTO_INCREMENT=1 ;


CREATE TABLE IF NOT EXISTS `nodes_server_data` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `lobby_id` int(11) NOT NULL,
  `user_id` int(11) NOT NULL,
  `position` varchar(60) NOT NULL,
  `rotation` varchar(60) NOT NULL,
  `timestamp` double NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=MyISAM  DEFAULT CHARSET=utf8 AUTO_INCREMENT=1 ;


CREATE TABLE IF NOT EXISTS `nodes_server_lobby` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `scene_id` int(11) NOT NULL,
  `user_id` int(11) NOT NULL,
  `date` int(11) NOT NULL,
  `last` int(11) NOT NULL,
  `active` tinyint(1) NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=MyISAM  DEFAULT CHARSET=utf8 AUTO_INCREMENT=1 ;


CREATE TABLE IF NOT EXISTS `nodes_server_object` (
  `object_id` int(11) NOT NULL AUTO_INCREMENT,
  `id` varchar(40) NOT NULL,
  `scene_id` int(11) NOT NULL,
  `type` tinyint(1) NOT NULL,
  `collada-model` varchar(40) NOT NULL,
  `position` varchar(60) NOT NULL,
  `rotation` varchar(60) NOT NULL,
  `scale` varchar(20) NOT NULL,
  `nodes-click` text NOT NULL,
  PRIMARY KEY (`object_id`)
) ENGINE=MyISAM  DEFAULT CHARSET=utf8 AUTO_INCREMENT=1 ;


CREATE TABLE IF NOT EXISTS `nodes_server_scene` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `max_radius` double NOT NULL,
  `radius` double NOT NULL,
  `terrain` longtext NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=MyISAM  DEFAULT CHARSET=utf8 AUTO_INCREMENT=1 ;


CREATE TABLE IF NOT EXISTS `nodes_backend` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `mode` varchar(400) DEFAULT NULL,
  `file` varchar(400) DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=MyISAM  DEFAULT CHARSET=utf8 AUTO_INCREMENT=1 ;


CREATE TABLE IF NOT EXISTS `nodes_invoice` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `user_id` int(11) NOT NULL,
  `order_id` int(11) NOT NULL,
  `amount` double NOT NULL,
  `date` datetime NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=MyISAM  DEFAULT CHARSET=utf8 AUTO_INCREMENT=1 ;


CREATE TABLE IF NOT EXISTS `nodes_firebase` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `user_id` int(11) NOT NULL,
  `token` varchar(400) NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=MyISAM  DEFAULT CHARSET=utf8 AUTO_INCREMENT=1 ;


INSERT INTO `nodes_backend` (`mode`, `file`) VALUES
('', 'main.php'),
('admin', 'admin.php'),
('account', 'account.php'),
('register', 'register.php'),
('login', 'login.php'),
('content', 'content.php'),
('product', 'product.php'),
('search', 'search.php'),
('profile', 'profile.php'),
('aframe', 'aframe.php');


CREATE TABLE IF NOT EXISTS `nodes_cache` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `url` varchar(400) NOT NULL,
  `title` varchar(100) NOT NULL,
  `date` int(11) NOT NULL,
  `lang` varchar(3) NOT NULL,
  `interval` int(11) NOT NULL,
  `html` longtext NOT NULL,
  `description` varchar(200) NOT NULL DEFAULT '',
  `keywords` varchar(300) NOT NULL DEFAULT '',
  `content` longtext NOT NULL,
  `time` double NOT NULL DEFAULT '0',
  PRIMARY KEY (`id`),
  KEY `interval` (`interval`),
  KEY `lang` (`lang`)
) ENGINE=MyISAM  DEFAULT CHARSET=utf8 AUTO_INCREMENT=1 ;


CREATE TABLE IF NOT EXISTS `nodes_catalog` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `caption` varchar(400) NOT NULL,
  `description` mediumtext NOT NULL,
  `text` mediumtext NOT NULL,
  `url` varchar(400) NOT NULL,
  `img` varchar(100) NOT NULL,
  `visible` tinyint(1) NOT NULL,
  `lang` varchar(3) NOT NULL,
  `order` int(11) NOT NULL,
  `date` int(11) NOT NULL DEFAULT '0',
  `public_date` int(11) NOT NULL DEFAULT '0',
  PRIMARY KEY (`id`),
  KEY `visible` (`visible`),
  KEY `lang` (`lang`)
) ENGINE=MyISAM  DEFAULT CHARSET=utf8 AUTO_INCREMENT=1 ;


CREATE TABLE IF NOT EXISTS `nodes_comment` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `url` varchar(400) NOT NULL,
  `reply` int(11) NOT NULL,
  `user_id` int(11) NOT NULL,
  `text` text NOT NULL,
  `date` int(11) NOT NULL DEFAULT '0',
  PRIMARY KEY (`id`),
  KEY `url` (`url`),
  KEY `reply` (`reply`),
  KEY `user_id` (`user_id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8 AUTO_INCREMENT=1 ;


CREATE TABLE IF NOT EXISTS `nodes_content` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `cat_id` int(11) NOT NULL,
  `url` varchar(100) NOT NULL,
  `lang` varchar(3) NOT NULL,
  `order` int(11) NOT NULL DEFAULT '0',
  `caption` varchar(400) NOT NULL,
  `text` text NOT NULL,
  `img` varchar(100) NOT NULL,
  `date` int(11) NOT NULL DEFAULT '0',
  `public_date` int(11) NOT NULL DEFAULT '0',
  PRIMARY KEY (`id`),
  KEY `cat_id` (`cat_id`),
  KEY `lang` (`lang`)
) ENGINE=MyISAM  DEFAULT CHARSET=utf8 AUTO_INCREMENT=1 ;


CREATE TABLE IF NOT EXISTS `nodes_error` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `url` varchar(400) NOT NULL,
  `lang` varchar(3) NOT NULL,
  `date` int(11) NOT NULL,
  `ip` varchar(20) NOT NULL,
  `get` text NOT NULL,
  `post` text NOT NULL,
  `session` text NOT NULL,
  `count` int(11) NOT NULL DEFAULT '0',
  PRIMARY KEY (`id`),
  KEY `url` (`url`),
  KEY `lang` (`lang`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8 AUTO_INCREMENT=1 ;


CREATE TABLE IF NOT EXISTS `nodes_image` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `name` varchar(100) NOT NULL,
  `color` varchar(10) NOT NULL,
  `width` int(11) NOT NULL,
  `height` int(11) NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=MyISAM  DEFAULT CHARSET=utf8 AUTO_INCREMENT=1 ;


CREATE TABLE IF NOT EXISTS `nodes_inbox` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `from` int(11) NOT NULL,
  `to` int(11) NOT NULL,
  `text` text NOT NULL,
  `date` int(11) NOT NULL,
  `readed` int(11) NOT NULL,
  `inform` tinyint(1) NOT NULL,
  `system` tinyint(1) NOT NULL,
  PRIMARY KEY (`id`),
  KEY `from` (`from`),
  KEY `to` (`to`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8 AUTO_INCREMENT=1 ;


CREATE TABLE IF NOT EXISTS `nodes_meta` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `url` varchar(400) NOT NULL,
  `lang` varchar(4) NOT NULL,
  `title` varchar(100) NOT NULL,
  `description` varchar(200) NOT NULL,
  `keywords` varchar(300) NOT NULL,
  `mode` int(1) NOT NULL,
  PRIMARY KEY (`id`),
  KEY `url` (`url`),
  KEY `lang` (`lang`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8 AUTO_INCREMENT=1 ;


CREATE TABLE IF NOT EXISTS `nodes_order` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `user_id` int(11) NOT NULL,
  `date` int(11) NOT NULL,
  `status` int(11) NOT NULL,
  `shipping` int(11) NOT NULL,
  `payment` int(1) NOT NULL,
  PRIMARY KEY (`id`),
  KEY `user_id` (`user_id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8 AUTO_INCREMENT=1 ;


CREATE TABLE IF NOT EXISTS `nodes_outbox` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `caption` varchar(400) NOT NULL,
  `text` text NOT NULL,
  `action` tinyint(4) NOT NULL,
  `date` int(11) NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8 AUTO_INCREMENT=1 ;


CREATE TABLE IF NOT EXISTS `nodes_pattern` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `attendance_id` int(11) NOT NULL,
  `action` int(1) NOT NULL,
  `x` int(11) NOT NULL,
  `y` int(11) NOT NULL,
  `top` int(11) NOT NULL,
  `width` int(11) NOT NULL,
  `height` int(11) NOT NULL,
  `date` int(11) NOT NULL,
  PRIMARY KEY (`id`),
  KEY `attendance_id` (`attendance_id`)
) ENGINE=MyISAM  DEFAULT CHARSET=utf8 AUTO_INCREMENT=1 ;


CREATE TABLE IF NOT EXISTS `nodes_perfomance` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `server_time` double NOT NULL,
  `script_time` double NOT NULL,
  `cache_id` int(11) NOT NULL,
  `date` int(11) NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=MyISAM  DEFAULT CHARSET=utf8 AUTO_INCREMENT=1 ;


CREATE TABLE IF NOT EXISTS `nodes_product` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `user_id` int(11) NOT NULL,
  `title` varchar(100) NOT NULL,
  `text` text NOT NULL,
  `description` text NOT NULL,
  `img` text NOT NULL,
  `shipping` int(11) NOT NULL,
  `price` double NOT NULL,
  `order` int(11) NOT NULL,
  `date` int(11) NOT NULL,
  `status` int(11) NOT NULL,
  `views` int(11) NOT NULL,
  `rating` int(11) NOT NULL,
  `votes` int(11) NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=MyISAM  DEFAULT CHARSET=utf8 AUTO_INCREMENT=1 ;


CREATE TABLE IF NOT EXISTS `nodes_product_data` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `cat_id` int(11) NOT NULL,
  `value` varchar(100) NOT NULL,
  `url` varchar(40) NOT NULL,
  `order` int(11) NOT NULL,
  PRIMARY KEY (`id`),
  KEY `cat_id` (`cat_id`),
  KEY `value` (`value`)
) ENGINE=MyISAM  DEFAULT CHARSET=utf8 AUTO_INCREMENT=1 ;


CREATE TABLE IF NOT EXISTS `nodes_product_order` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `product_id` int(11) NOT NULL,
  `order_id` int(11) NOT NULL,
  `price` double NOT NULL,
  `count` int(11) NOT NULL,
  `track` varchar(100) NOT NULL,
  `status` int(11) NOT NULL,
  `date` int(11) NOT NULL,
  PRIMARY KEY (`id`),
  KEY `product_id` (`product_id`),
  KEY `order_id` (`order_id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8 AUTO_INCREMENT=1 ;


CREATE TABLE IF NOT EXISTS `nodes_product_property` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `cat_id` int(11) NOT NULL,
  `value` varchar(100) NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=MyISAM  DEFAULT CHARSET=utf8 AUTO_INCREMENT=1 ;


INSERT INTO `nodes_product_property` (`id`, `cat_id`, `value`) VALUES
(1, 0, 'Category');


CREATE TABLE IF NOT EXISTS `nodes_property_data` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `product_id` int(11) NOT NULL,
  `property_id` int(11) NOT NULL,
  `data_id` int(11) NOT NULL,
  PRIMARY KEY (`id`),
  KEY `product_id` (`product_id`),
  KEY `property_id` (`property_id`)
) ENGINE=MyISAM  DEFAULT CHARSET=utf8 AUTO_INCREMENT=1 ;


CREATE TABLE IF NOT EXISTS `nodes_referrer` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `name` text NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8 AUTO_INCREMENT=1 ;


CREATE TABLE IF NOT EXISTS `nodes_shipping` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `user_id` int(11) NOT NULL,
  `fname` varchar(100) NOT NULL,
  `lname` varchar(100) NOT NULL,
  `country` varchar(100) NOT NULL,
  `state` varchar(100) NOT NULL,
  `city` varchar(100) NOT NULL,
  `zip` varchar(100) NOT NULL,
  `street1` varchar(100) NOT NULL,
  `street2` varchar(100) NOT NULL,
  `phone` varchar(20) NOT NULL,
  PRIMARY KEY (`id`),
  KEY `user_id` (`user_id`)
) ENGINE=MyISAM  DEFAULT CHARSET=utf8 AUTO_INCREMENT=1 ;


INSERT INTO `nodes_shipping` (`id`, `user_id`, `fname`, `lname`, `country`, `state`, `city`, `zip`, `street1`, `street2`, `phone`) VALUES
(1, 1, '', '', 'United States', 'Columbia D.C', 'Washington', '20001', 'District of Columbia', '', '+1234567890');


CREATE TABLE IF NOT EXISTS `nodes_transaction` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `user_id` int(11) NOT NULL,
  `invoice_id` int(11) NOT NULL,
  `order_id` int(11) NOT NULL,
  `txn_id` varchar(40) NOT NULL,
  `amount` double NOT NULL,
  `status` int(11) NOT NULL,
  `date` int(11) NOT NULL,
  `gateway` varchar(40) NOT NULL,
  `payment_date` varchar(100) NOT NULL,
  `comment` text NOT NULL,
  `ip` varchar(40) NOT NULL,
  PRIMARY KEY (`id`),
  KEY `user_id` (`user_id`),
  KEY `invoice_id` (`invoice_id`),
  KEY `order_id` (`order_id`),
  KEY `status` (`status`)
) ENGINE=MyISAM  DEFAULT CHARSET=utf8 AUTO_INCREMENT=1 ;


CREATE TABLE IF NOT EXISTS `nodes_user_outbox` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `user_id` int(11) NOT NULL,
  `outbox_id` int(11) NOT NULL,
  `date` int(11) NOT NULL,
  `status` tinyint(4) NOT NULL,
  PRIMARY KEY (`id`),
  KEY `status` (`status`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8 AUTO_INCREMENT=1 ;


CREATE TABLE IF NOT EXISTS `nodes_agent` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `name` text NOT NULL,
  `bot` tinyint(1) NOT NULL DEFAULT '0',
  PRIMARY KEY (`id`)
) ENGINE=MyISAM  DEFAULT CHARSET=utf8 AUTO_INCREMENT=1 ;

CREATE TABLE `nodes_vr_level` (
  `id` int(11) NOT NULL,
  `project_id` int(11) NOT NULL,
  `name` varchar(100) NOT NULL,
  `text` text NOT NULL,
  `image` varchar(100) NOT NULL,
  `rotation` int(11) NOT NULL,
  `scale` double NOT NULL
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

CREATE TABLE `nodes_vr_link` (
  `id` int(11) NOT NULL,
  `project_id` int(11) NOT NULL,
  `level_id` int(11) NOT NULL,
  `scene_id` int(11) NOT NULL,
  `url` varchar(200) NOT NULL,
  `position` varchar(100) NOT NULL,
  `scale` varchar(100) NOT NULL
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

CREATE TABLE `nodes_vr_navigation` (
  `id` int(11) NOT NULL,
  `project_id` int(11) NOT NULL,
  `level_id` int(11) NOT NULL,
  `scene_id` int(11) NOT NULL,
  `target` int(11) NOT NULL,
  `position` varchar(100) NOT NULL,
  `scale` varchar(100) NOT NULL
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

CREATE TABLE `nodes_vr_object` (
  `id` int(11) NOT NULL,
  `scene_id` int(11) NOT NULL,
  `level_id` int(11) NOT NULL,
  `project_id` int(11) NOT NULL,
  `text` text NOT NULL,
  `width` double NOT NULL,
  `height` double NOT NULL,
  `color` varchar(40) NOT NULL,
  `base64` text NOT NULL,
  `position` varchar(100) NOT NULL,
  `rotation` varchar(100) NOT NULL,
  `scale` varchar(100) NOT NULL
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

CREATE TABLE `nodes_vr_project` (
  `id` int(11) NOT NULL,
  `name` varchar(100) NOT NULL,
  `url` varchar(100) NOT NULL,
  `text` text NOT NULL
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

CREATE TABLE `nodes_vr_scene` (
  `id` int(11) NOT NULL,
  `project_id` int(11) NOT NULL,
  `level_id` int(11) NOT NULL,
  `name` varchar(100) NOT NULL,
  `position` varchar(400) NOT NULL,
  `rotation` varchar(400) NOT NULL,
  `lat` double NOT NULL,
  `lng` double NOT NULL,
  `top` int(11) NOT NULL DEFAULT '0',
  `left` int(11) NOT NULL DEFAULT '0',
  `height` double NOT NULL,
  `floor_position` varchar(400) NOT NULL,
  `floor_radius` double NOT NULL,
  `logo_size` double NOT NULL
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

ALTER TABLE `nodes_vr_level`
  ADD PRIMARY KEY (`id`);

ALTER TABLE `nodes_vr_link`
  ADD PRIMARY KEY (`id`);

ALTER TABLE `nodes_vr_navigation`
  ADD PRIMARY KEY (`id`);

ALTER TABLE `nodes_vr_object`
  ADD PRIMARY KEY (`id`);

ALTER TABLE `nodes_vr_project`
  ADD PRIMARY KEY (`id`);

ALTER TABLE `nodes_vr_scene`
  ADD PRIMARY KEY (`id`);

ALTER TABLE `nodes_vr_level`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=1;

ALTER TABLE `nodes_vr_link`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=1;

ALTER TABLE `nodes_vr_navigation`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=1;

ALTER TABLE `nodes_vr_object`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=1;

ALTER TABLE `nodes_vr_project`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=1;

ALTER TABLE `nodes_vr_scene`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=1;


INSERT INTO `nodes_agent` (`name`, `bot`) VALUES
('a.pr-cy.ru', 1),
('Apache-HttpClient/4.5 (Java/1.8.0_60)', 1),
('facebookexternalhit/1.1', 1),
('facebookexternalhit/1.1 (+http://www.facebook.com/externalhit_uatext.php)', 1),
('Google favicon', 1),
('GuzzleHttp/6.1.0 curl/7.26.0 PHP/5.5.29-1~dotdeb+7.1', 1),
('GuzzleHttp/6.1.0 curl/7.35.0 PHP/5.6.14-1+deb.sury.org~trusty+1', 1),
('Java/1.4.1_04', 1),
('Java/1.8.0_60', 1),
('ltx71 - (http://ltx71.com/)', 1),
('Mozilla/5.0 (compatible; Baiduspider/2.0; +http://www.baidu.com/search/spider.html)', 1),
('Mozilla/5.0 (compatible; Google-Site-Verification/1.0)', 1),
('Mozilla/5.0 (compatible; vkShare; +http://vk.com/dev/Share)', 1),
('Mozilla/5.0 (compatible; Web-Monitoring/1.0; +http://monoid.nic.ru/)', 1),
('Mozilla/5.0 (compatible; Yahoo! Slurp; http://help.yahoo.com/help/us/ysearch/slurp)', 1),
('parser3', 1),
('PEAR HTTP_Request class ( http://pear.php.net/ )', 1),
('python-requests/2.8.1', 1),
('Riddler (http://riddler.io/about)', 1),
('SeopultContentAnalyzer/1.0', 1),
('Validator.nu/LV http://validator.w3.org/services', 1),
('W3C_Validator/1.3 http://validator.w3.org/services', 1),
('W3C_Validator/1.3 libwww-perl/6.05', 1),
('Yandex/1.01.001 (compatible; Win16; I)', 1),
('Nodes Studio 3.0', 1),
('Mozilla/5.0 (compatible; YandexOntoDB/1.0; +http://yandex.com/bots)', 1),
('Mozilla/5.0 (compatible; AhrefsBot/5.2; +http://ahrefs.com/robot/)', 1),
('Mozilla/5.0 (Linux; Android 6.0.1; Nexus 5X Build/MMB29P) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/41.0.2272.96 Mobile Safari/537.36 (compatible; Googlebot/2.1; +http://www.google.com/bot.html)', 1),
('Googlebot/2.1 (+http://www.google.com/bot.html)', 1),
('CRAZYWEBCRAWLER 0.9.10, http://www.crazywebcrawler.com', 1),
('C-T bot', 1),
('Mozilla/5.0 (compatible; Uptimebot/1.0; +http://www.uptime.com/uptimebot)', 1),
('Virusdie crawler/2.1', 1),
('Mozilla/5.0 (compatible; Google-Structured-Data-Testing-Tool +https://search.google.com/structured-data/testing-tool)', 1),
('Mozilla/5.0 (compatible; YandexPagechecker/2.0; +http://yandex.com/bots)', 1),
('GuzzleHttp/6.1.0 curl/7.38.0 PHP/7.0.13-1~dotdeb+8.1', 1),
('GoogleProducer; (+http://goo.gl/7y4SX)', 1),
('Mozilla/5.0 (compatible; SeznamBot/3.2; +http://napoveda.seznam.cz/en/seznambot-intro/)', 1),
('SafeDNSBot (https://www.safedns.com/searchbot)', 1),
('Mozilla/5.0 (compatible; WOT VerifyBot/1.1; +https://www.mywot.com/)', 1),
('Mozilla/5.0 (compatible; Exabot/3.0; +http://www.exabot.com/go/robot)', 1),
('LinkedInBot/1.0 (compatible; Mozilla/5.0; Jakarta Commons-HttpClient/3.1 +http://www.linkedin.com)', 1),
('Twitterbot/1.0', 1),
('Mozilla/5.0 (Macintosh; Intel Mac OS X 10_10_1) AppleWebKit/600.2.5 (KHTML, like Gecko) Version/8.0.2 Safari/600.2.5 (Applebot/0.1; +http://www.apple.com/go/applebot)', 1),
('python-requests/2.12.4', 1),
('Mozilla/5.0 (compatible; DuckDuckGo-Favicons-Bot/1.0; +http://duckduckgo.com)', 1),
('Jigsaw/2.3.0 W3C_CSS_Validator_JFouffa/2.0 (See <http://validator.w3.org/services>)', 1),
('W3C-checklink/4.81 libwww-perl/6.08', 1),
('Mozilla/4.0 (compatible;HostTracker/2.0;+http://www.host-tracker.com/)', 1),
('Mozilla/5.0 (en-us) AppleWebKit/537.36 (KHTML, like Gecko; Google PP Default) Chrome/27.0.1453 Safari/537.36', 1),
('Mozilla/5.0 (compatible; SemrushBot/1.2~bl; +http://www.semrush.com/bot.html)', 1),
('Mozilla/5.0 (compatible; spbot/5.0.3; +http://OpenLinkProfiler.org/bot )', 1),
('Mozilla/5.0 (iPhone; CPU iPhone OS 8_3 like Mac OS X) AppleWebKit/600.1.4 (KHTML, like Gecko) Version/8.0 Mobile/12F70 Safari/600.1.4 (compatible; Laserlikebot/0.1)', 1),
('Google-Adwords-Instant (+http://www.google.com/adsbot.html)', 1),
('Mozilla/5.0 (iPhone; CPU iPhone OS 9_1 like Mac OS X) AppleWebKit/601.1.46 (KHTML, like Gecko) Version/9.0 Mobile/13B143 Safari/601.1 (compatible; AdsBot-Google-Mobile; +http://www.google.com/mobile/adsbot.html)', 1),
('Mozilla/5.0 (iPhone; CPU iPhone OS 7_0 like Mac OS X) AppleWebKit/537.51.1 (KHTML, like Gecko) Version/7.0 Mobile/11A465 Safari/9537.53 (compatible; bingbot/2.0; +http://www.bing.com/bingbot.htm)', 1),
('Mozilla/5.0 (compatible; SEOkicks-Robot; +http://www.seokicks.de/robot.html)', 1),
('Mozilla/5.0 (compatible; Plukkie/1.6; http://www.botje.com/plukkie.htm)', 1),
('Mozilla/5.0 (compatible; YandexDirectDyn/1.0; +http://yandex.com/bots)', 1),
('CCBot/2.0 (http://commoncrawl.org/faq/)', 1),
('msnbot-media/1.1 (+http://search.msn.com/msnbot.htm)', 1),
('Wotbox/2.01 (+http://www.wotbox.com/bot/)', 1),
('Mozilla/5.0 (compatible; WebHistoryBot/1.2.1 IS NOT SE bot like Googlebot/2.1; +http://www.google.com/bot.html,Yahoo! Slurp or Bingbot)', 1),
('AportCatalogRobot/2.0', 1),
('Mozilla/5.0 (compatible; WBSearchBot/1.1; +http://www.warebay.com/bot.html)', 1),
('Mozilla/5.0 (compatible; special_archiver/3.1.1 +http://www.archive.org/details/archive.org_bot)', 1),
('SEMrushBot', 1),
('BOT/0.1 (BOT for JCE)', 1),
('Mozilla/5.0 (compatible; LinkpadBot/1.12; +http://www.linkpad.ru)', 1),
('Mozilla/5.0 (TweetmemeBot/4.0; +http://datasift.com/bot.html) Gecko/20100101 Firefox/31.0', 1),
('Mediatoolkitbot (complaints@mediatoolkit.com)', 1),
('Mozilla/5.0 (compatible; Yeti/1.1; +http://naver.me/bot)', 1),
('Mozilla/5.0 (compatible; IDBot/1.1; +http://www.id-search.xyz/bot.html)', 1),
('Mozilla/5.0 (compatible; SemrushBot-BA; +http://www.semrush.com/bot.html)', 1),
('TurnitinBot (https://turnitin.com/robot/crawlerinfo.html)', 1),
('Mozilla/5.0 (compatible; archive.org_bot +http://www.archive.org/details/archive.org_bot)', 1),
('Digincore crawler bot. See https://www.digincore.com/crawler.html for rules and instructions.', 1),
('Linguee Bot (http://www.linguee.com/bot; bot@linguee.com)', 1),
('RankingBot2 -- https://varocarbas.com/bot_ranking2/', 1),
('Mozilla/5.0 (compatible; SEOkicks-Robot +http://www.seokicks.de/robot.html)', 1),
('rogerbot/1.1 (http://moz.com/help/guides/search-overview/crawl-diagnostics#more-help, rogerbot-crawler+pr4-crawler-15@moz.com)', 1),
('Mozilla/5.0 (compatible; Linux x86_64; Mail.RU_Bot/Robots/2.0; +http://go.mail.ru/help/robots)', 1),
('Mozilla/5.0 (compatible; oBot/2.3.1; http://filterdb.iss.net/crawler/)', 1),
('Mozilla/5.0 (compatible; MixrankBot; crawler@mixrank.com)', 1),
('Mozilla/5.0 (compatible; TOBBOT; +http://tobbot.com/)', 1),
('Googlebot-Video/1.0', 1),
('Googlebot-News', 1),
('woobot/2.0', 1),
('Mozilla/5.0 (compatible; ExtLinksBot/1.5 +https://extlinks.com/Bot.html)', 1),
('Links Guru Bot v.2.0; http://links.guru/', 1),
('Mozilla/5.0 (Nekstbot; http://nekst.ipipan.waw.pl/nekstbot/)', 1),
('TelegramBot (like TwitterBot)', 1),
('Mozilla/5.0 (compatible; DnyzBot/1.0)', 1),
('Mozilla/5.0 (compatible; InbyBot/2.1; +http://inbyapp.com)', 1),
('C-T bot /ru/', 1),
('Mozilla/5.0 (compatible; PiStato/v0.0.1; http://carsopict.com/pages/bot.html+)', 1),
('Mozilla/5.0 (compatible; SeoUtilsBot/1.0; +https://seo-utils.ru/; support@seo-utils.ru)', 1),
('Mozilla/5.0 (compatible; DnyzBot/1.0) AppleWebKit/537.36 (KHTML, like Gecko) HeadlessChrome/64.0.3264.0 Safari/537.36', 1),
('Mozilla/5.0 (compatible; Konqueror/3.5; Linux) KHTML/3.5.5 (like Gecko) (Exabot-Thumbnails)', 1),
('Mozilla/5.0 (compatible; YandexVideo/3.0; +http://yandex.com/bots)', 1),
('Mozilla/5.0 (compatible; ExtLinksBot/1.5; +https://extlinks.com/Bot.html)', 1),
('Mozilla/5.0 (compatible; YandexAntivirus/2.0; +http://yandex.com/bots)', 1),
('Mozilla/5.0 (compatible; YandexSitelinks; Dyatel; +http://yandex.com/bots)', 1),
('Mozilla/5.0 (compatible; redditbot/1.0; +http://www.reddit.com/feedback)', 1),
('Mozilla/5.0 (compatible; YandexImageResizer/2.0; +http://yandex.com/bots)', 1),
('Mozilla/5.0 (compatible; YandexAccessibilityBot/3.0; +http://yandex.com/bots)', 1),
('Mozilla/5.0 (compatible; DnyzBot/1.0) AppleWebKit/537.36 (KHTML, like Gecko) HeadlessChrome/64.0.3282.167 Safari/537.36', 1),
('Mozilla/5.0 (compatible; YandexMedia/3.0; +http://yandex.com/bots)', 1),
('MauiBot (crawler.feedback+wc@gmail.com)', 1),
('Mozilla/5.0 (compatible; YandexBot/3.0; MirrorDetector; +http://yandex.com/bots)', 1),
('Mozilla/5.0 (compatible; AhrefsBot/2.0; +http://ahrefs.com/robot/)', 1),
('Mozilla/5.0 (compatible; Linux x86_64; Meta_Bot/1.1)', 1),
('Mozilla/5.0 (compatible; Linux x86_64; Mail.RU_Bot/2.0;)', 1),
('Mozilla/5.0 (compatible; Linux x86_64; Yapl.RU_Bot/1.0;)', 1),
('Mozilla/5.0 (compatible; Googlebot/2.1; +http://www.google.com/bot.html', 1),
('Mozilla/5.0 (compatible; sukibot_heritrix/3.3.0-SNAPSHOT-20180329-1050 +http://suki.ling.helsinki.fi/eng/webmasters.html)', 1),
('Mozilla/5.0 (compatible; YandexBlogs/0.99; robot; +http://yandex.com/bots)', 1),
('Mozilla/5.0 (compatible; YandexFavicons/1.0; +http://yandex.com/bots)', 1),
('Mozilla/5.0 (compatible; YandexVertis/3.0; +http://yandex.com/bots)', 1),
('Mozilla/5.0 (iPhone; CPU iPhone OS 8_1 like Mac OS X) AppleWebKit/600.1.4 (KHTML, like Gecko) Version/8.0 Mobile/12B411 Safari/600.1.4 (compatible; YandexBot/3.0; +http://yandex.com/bots)', 1),
('Mozilla/5.0 (compatible; YandexPagechecker/1.0; +http://yandex.com/bots)', 1),
('Mozilla/5.0 (compatible; YandexDirectDyn/1.0; +http://yandex.com/bots', 1),
('Mozilla/5.0 (compatible; YandexCalendar/1.0; +http://yandex.com/bots)', 1),
('Mozilla/5.0 (compatible; SemrushBot/2~bl; +http://www.semrush.com/bot.html)', 1),
('Mozilla/5.0 AppleWebKit/537.36 (KHTML, like Gecko; compatible; Googlebot/2.1; +http://www.google.com/bot.html) Safari/537.36', 1),
('Mozilla/5.0 (compatible; Cliqzbot/2.0; +http://cliqz.com/company/cliqzbot)', 1),
('Mozilla/5.0 (compatible; LetsearchBot/1.0; +http://letsearch.ru)', 1),
('Mozilla/5.0 (compatible; linkdexbot/2.0; +http://www.linkdex.com/bots/)', 1),
('Mozilla/5.0 (compatible; Googlebot/2.1 +http://www.googlebot.com/bot.html)', 1),
('Mozilla/5.0 (compatible; nsrbot/1.0; &#43;http://netsystemsresearch.com)', 1),
('Mozilla/5.0 (Windows NT 6.1; rv:38.0) Gecko/20100101 Firefox/38.0 (IndeedBot 1.1)', 1),
('Mozilla/5.0 (compatible; nsrbot/1.0; http://netsystemsresearch.com)', 1),
('CCBot/2.0 (https://commoncrawl.org/faq/)', 1),
('Mozilla/5.0 (X11; Linux x86_64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/41.0.2228.0 Safari/537.36 (compatible; YandexScreenshotBot/3.0; +http://yandex.com/bots)', 1),
('DF Bot 1.0', 1),
('Mozilla/5.0 (compatible; Nimbostratus-Bot/v1.3.2; http://cloudsystemnetworks.com)', 1),
('Mozilla/5.0 (compatible; YandexCatalog/3.0; +http://yandex.com/bots)', 1),
('Mozilla/6.0 (compatible; Googlebot/2.1; +http://www.google.com/bot.html)', 1),
('Mozilla/5.0 (compatible; woobot/2.0; +https://www.woorank.com/bot)', 1),
('Mozilla/5.0 (compatible; Googlebot/2.1; +http://www.google.com/bot.html )', 1),
('Mozilla/5.0 (compatible; SemrushBot/3~bl; +http://www.semrush.com/bot.html)', 1),
('B2B Bot', 1),
('Mozilla/5.0 (compatible; AhrefsBot/6.1; +http://ahrefs.com/robot/)', 1),
('Mozilla/5.0 (compatible; LetsearchBot/1.0; +https://letsearch.ru)', 1),
('Mozilla/5.0 (compatible; oBot/2.3.1; +http://filterdb.iss.net/crawler/)', 1),
('PleskBot', 1);


CREATE TABLE IF NOT EXISTS `nodes_language` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `name` text NOT NULL,
  `lang` varchar(3) NOT NULL,
  `value` text NOT NULL,
  PRIMARY KEY (`id`),
  KEY `lang` (`lang`)
) ENGINE=MyISAM  DEFAULT CHARSET=utf8 AUTO_INCREMENT=1 ;


INSERT INTO `nodes_language` (`id`, `name`, `lang`, `value`) VALUES
(1, 'Upload', 'en', 'Upload'),
(2, 'Link', 'en', 'Link'),
(3, 'Cancel', 'en', 'Cancel'),
(4, 'Uploaded', 'en', 'Uploaded'),
(5, 'For uploading selected area use double click', 'en', 'For uploading selected area use double click'),
(6, 'Error', 'en', 'Error'),
(7, 'Or file', 'en', 'Or file'),
(8, 'Upload image', 'en', 'Upload image'),
(9, 'Received', 'ru', 'Получено'),
(10, 'Sitemap', 'en', 'Sitemap'),
(11, 'Format', 'en', 'Format'),
(12, 'Upload new image', 'en', 'Upload new image'),
(13, 'Logout sucsessful', 'en', 'Logout sucsessful'),
(14, 'Quit', 'en', 'Quit'),
(15, 'Site', 'en', 'Site'),
(16, 'Users', 'en', 'Users'),
(17, 'Comments', 'en', 'Comments'),
(18, 'Content', 'en', 'Content'),
(19, 'Errors', 'en', 'Errors'),
(20, 'Files', 'en', 'Files'),
(21, 'Images', 'en', 'Images'),
(22, 'Videos', 'en', 'Videos'),
(23, 'Access', 'en', 'Access'),
(24, 'Config', 'en', 'Config'),
(25, 'Backend', 'en', 'Backend'),
(26, 'Logout', 'en', 'Logout'),
(27, 'new messages', 'ru', 'новые сообщения'),
(28, 'Password', 'en', 'Password'),
(29, 'Upload selection as thumb?', 'en', 'Upload selection as thumb?'),
(30, 'Save uploaded images', 'en', 'Save uploaded images'),
(31, 'Previous page', 'en', 'Previous page'),
(32, 'Next page', 'en', 'Next page'),
(33, 'Updated', 'en', 'Updated'),
(34, 'Interval', 'en', 'Interval'),
(35, 'Not existing', 'en', 'Not existing'),
(36, 'Not cathing', 'en', 'Not cathing'),
(37, 'Not refreshing', 'en', 'Not refreshing'),
(38, 'minut', 'en', 'minut'),
(39, 'minuts', 'en', 'minuts'),
(40, 'hour', 'en', 'hour'),
(41, 'hours', 'en', 'hours'),
(42, 'Dayly', 'en', 'Dayly'),
(43, 'Change interval', 'en', 'Change interval'),
(44, 'Refresh catch', 'en', 'Refresh catch'),
(45, 'Edit article', 'en', 'Edit article'),
(46, 'Caption', 'en', 'Caption'),
(47, 'No image', 'en', 'No image'),
(48, 'Edit directory', 'en', 'Edit directory'),
(49, 'Return', 'en', 'Return'),
(50, 'Add new article', 'en', 'Add new article'),
(51, 'Edit', 'en', 'Edit'),
(52, 'Delete', 'en', 'Delete'),
(53, 'Add a new directory', 'en', 'Add a new directory'),
(54, 'Description', 'en', 'Description'),
(55, 'Back', 'en', 'Back'),
(56, 'List', 'en', 'List'),
(57, 'Add', 'en', 'Add'),
(58, 'Preview', 'en', 'Preview'),
(59, 'Uploading files', 'en', 'Uploading files'),
(60, 'File', 'en', 'File'),
(61, 'Submit', 'en', 'Submit'),
(62, 'Included files', 'en', 'Included files'),
(63, 'Path', 'en', 'Path'),
(64, 'Class', 'en', 'Class'),
(65, 'Create new file', 'en', 'Create new file'),
(66, 'Lost password', 'en', 'Lost password'),
(67, 'allready exist', 'en', 'already exist'),
(68, 'Incorrect email of password', 'en', 'Incorrect email of password'),
(69, 'not found', 'en', 'not found'),
(70, 'Message with new password is sended to email', 'en', 'Message with new password is sended to email'),
(71, 'New password is', 'en', 'New password is'),
(72, 'New password for', 'en', 'New password for'),
(73, 'Registration sucsessful', 'en', 'Registration successful'),
(74, 'We are glad to confirm sucsessful registration at', 'en', 'We are glad to confirm successful registration at'),
(75, 'About software', 'en', 'About software'),
(76, 'Errors not found', 'en', 'Errors not found'),
(77, 'Clear log', 'en', 'Clear log'),
(78, 'Date', 'en', 'Date'),
(79, 'New user', 'en', 'New user'),
(80, 'Name', 'en', 'Name'),
(81, 'Ban', 'en', 'Ban'),
(82, 'Unban', 'en', 'Unban'),
(83, 'Confirm deleting banned user', 'en', 'Confirm deleting banned user'),
(84, 'Create user', 'en', 'Create user'),
(85, 'Incorrect email', 'en', 'Incorrect email'),
(86, 'Enter password', 'en', 'Enter password'),
(87, 'Update interval', 'en', 'Update interval'),
(88, 'All pages', 'en', 'All pages'),
(89, 'Logs', 'en', 'Logs'),
(90, 'Text', 'en', 'Text'),
(91, 'Pages not found', 'en', 'Pages not found'),
(92, 'Sign Up', 'en', 'Sign Up'),
(93, 'Admin', 'en', 'Admin'),
(94, 'Upload files', 'en', 'Upload files'),
(95, 'Account', 'en', 'Account'),
(96, 'Save settings', 'en', 'Save settings'),
(97, 'Access denied', 'en', 'Access denied'),
(98, 'Message sent successfully', 'en', 'Message sent successfully'),
(99, 'New message from', 'en', 'New message from'),
(100, 'My Account', 'en', 'My Account'),
(101, 'Show navigation', 'en', 'Show navigation'),
(102, 'Get in Touch', 'en', 'Get in Touch'),
(103, 'Contact Us', 'en', 'Contact Us'),
(104, 'Your message here', 'en', 'Your message here'),
(105, 'Send message', 'en', 'Send message'),
(106, 'Developed by', 'en', 'Developed by'),
(107, 'Up', 'en', 'Up'),
(108, 'Show All', 'en', 'Show All'),
(109, 'Change picture', 'en', 'Change picture'),
(110, 'Enter your email and password to continue', 'en', 'Enter your email and password to continue'),
(111, 'Settings', 'en', 'Settings'),
(112, 'Email', 'en', 'Email'),
(113, 'New password', 'en', 'New password'),
(114, 'Enter your email', 'en', 'Enter your email'),
(115, 'Enter your password', 'en', 'Enter your password'),
(116, 'Save changes', 'en', 'Save changes'),
(117, 'Back to account', 'en', 'Back to account'),
(118, 'Invalid conformation code', 'en', 'Invalid conformation code'),
(119, 'Registration at', 'en', 'Registration at'),
(120, 'Confirmation code', 'en', 'Confirmation code'),
(121, 'No articles found', 'en', 'No articles found'),
(122, 'Messages', 'en', 'Messages'),
(123, 'Back to admin', 'en', 'Back to admin'),
(124, 'Status', 'en', 'Status'),
(125, 'Yes', 'en', 'Yes'),
(126, 'No', 'en', 'No'),
(127, 'Show in navigation', 'en', 'Show in navigation'),
(128, 'Back to content', 'en', 'Back to content'),
(129, 'Delete image', 'en', 'Delete image'),
(130, 'Comments not found', 'en', 'Comments not found'),
(131, 'Add reply', 'en', 'Add reply'),
(132, 'Users not found', 'en', 'Users not found'),
(133, 'Share friends in', 'en', 'Share friends in'),
(134, 'Language', 'en', 'Language'),
(135, 'Home', 'en', 'Home'),
(136, 'Connect us at', 'en', 'Connect us at'),
(137, 'Copyright', 'en', 'Copyright'),
(138, 'All rights reserved', 'en', 'All rights reserved'),
(139, 'There is no users, you can send a message', 'en', 'There is no users, you can send a message'),
(140, 'offline', 'en', 'offline'),
(141, 'Add comment', 'en', 'Add comment'),
(142, 'Submit comment', 'en', 'Submit comment'),
(143, 'Add new comment', 'en', 'Add new comment'),
(144, 'There is no comments', 'en', 'There is no comments'),
(145, 'Comment submited!', 'en', 'Comment submited!'),
(146, 'To post a comment, please', 'en', 'To post a comment, please'),
(147, 'Updating engine from version', 'ru', 'Обновления движка с версии'),
(148, 'Reply', 'en', 'Reply'),
(149, 'Upload', 'ru', 'Загрузить'),
(150, 'Link', 'ru', 'Ссылка'),
(151, 'Cancel', 'ru', 'Отмена'),
(152, 'Uploaded', 'ru', 'Загружено'),
(153, 'For uploading selected area use double click', 'ru', 'Для сохранения выделенной области используй двойной клик'),
(154, 'Error', 'ru', 'Ошибка'),
(155, 'Upload image', 'ru', 'Загрузить изображение'),
(156, 'Sitemap', 'ru', 'Карта сайта'),
(157, 'Format', 'ru', 'Маска'),
(158, 'Upload new image', 'ru', 'Загрузить новое изобр.'),
(159, 'Logout sucsessful', 'ru', 'Сессия завершена'),
(160, 'Quit', 'ru', 'Выход'),
(161, 'Site', 'ru', 'Сайт'),
(162, 'Users', 'ru', 'Пользователи'),
(163, 'Comments', 'ru', 'Комментарии'),
(164, 'Content', 'ru', 'Контент'),
(165, 'Errors', 'ru', 'Ошибки'),
(166, 'Files', 'ru', 'Файлы'),
(167, 'Images', 'ru', 'Изображения'),
(168, 'Videos', 'ru', 'Видео'),
(169, 'Access', 'ru', 'Доступ'),
(170, 'Config', 'ru', 'Настройки'),
(171, 'Backend', 'ru', 'Бекенд'),
(172, 'Logout', 'ru', 'Выход'),
(173, 'Authentication timeout', 'ru', 'Таймаут аутенфикации'),
(174, 'Password', 'ru', 'Пароль'),
(175, 'Upload selection as thumb?', 'ru', 'Загрузить выделенную облать как превью?'),
(176, 'Save uploaded images', 'ru', 'Сохранить загруженные'),
(177, 'Previous page', 'ru', 'Предыдущая страница'),
(178, 'Next page', 'ru', 'Следующая страница'),
(179, 'Updated', 'ru', 'Обновлено'),
(180, 'Interval', 'ru', 'Интервал'),
(181, 'Not existing', 'ru', 'Не существует'),
(182, 'Not cathing', 'ru', 'Не кешируется'),
(183, 'Not refreshing', 'ru', 'Не обновляется'),
(184, 'minut', 'ru', 'минут'),
(185, 'minuts', 'ru', 'минут'),
(186, 'hour', 'ru', 'час'),
(187, 'hours', 'ru', 'часов'),
(188, 'Dayly', 'ru', 'Суточно'),
(189, 'Change interval', 'ru', 'Изменить интервал'),
(190, 'Refresh catch', 'ru', 'Обновить кэш'),
(191, 'Edit article', 'ru', 'Редактировать статью'),
(192, 'Caption', 'ru', 'Название'),
(193, 'No image', 'ru', 'Нет изображения'),
(194, 'Edit directory', 'ru', 'Редактировать раздел'),
(195, 'Return', 'ru', 'Назад'),
(196, 'Add new article', 'ru', 'Добавить новую статью'),
(197, 'Edit', 'ru', 'Редактировать'),
(198, 'Delete', 'ru', 'Удалить'),
(199, 'Add a new directory', 'ru', 'Добавить раздел'),
(200, 'Description', 'ru', 'Описание'),
(201, 'Back', 'ru', 'Назад'),
(202, 'List', 'ru', 'Список'),
(203, 'Add', 'ru', 'Добавить'),
(204, 'Preview', 'ru', 'Превью'),
(205, 'Uploading files', 'ru', 'Загрузка файлов'),
(206, 'File', 'ru', 'Файл'),
(207, 'Submit', 'ru', 'Отправить'),
(208, 'Included files', 'ru', 'Подключенные файлы'),
(209, 'Path', 'ru', 'Путь'),
(210, 'Class', 'ru', 'Класс'),
(211, 'Create new file', 'ru', 'Создать новый файл'),
(212, 'Lost password', 'ru', 'Забыли пароль'),
(213, 'allready exist', 'ru', 'уже существует'),
(214, 'Incorrect email of password', 'ru', 'Неправильный email или пароль'),
(215, 'not found', 'ru', 'не найден'),
(216, 'Message with new password is sended to email', 'ru', 'Письмо с новым паролем отправлено на указанный email'),
(217, 'New password is', 'ru', 'Новый пароль'),
(218, 'New password for', 'ru', 'Новый пароль для'),
(219, 'Registration sucsessful', 'ru', 'Вы успешно зарегистрировались'),
(220, 'We are glad to confirm sucsessful registration at', 'ru', 'Мы рады сообщить, что Вы успешно зарегистрировались на'),
(221, 'About software', 'ru', 'О программе'),
(222, 'Errors not found', 'ru', 'Ошибок не найдено'),
(223, 'Clear log', 'ru', 'Очистить список'),
(224, 'Date', 'ru', 'Дата'),
(225, 'New user', 'ru', 'Новый пользователь'),
(226, 'Name', 'ru', 'Имя'),
(227, 'Ban', 'ru', 'Забанить'),
(228, 'Unban', 'ru', 'Разбанить'),
(229, 'Confirm deleting banned user', 'ru', 'Удалить забаненного пользователя, без возможности восстановления'),
(230, 'Create user', 'ru', 'Создать пользователя'),
(231, 'Incorrect email', 'ru', 'Неправильный email'),
(232, 'Enter password', 'ru', 'Введите пароль'),
(233, 'Update interval', 'ru', 'Обновить интервал'),
(234, 'All pages', 'ru', 'Все страницы'),
(235, 'Logs', 'ru', 'Логи'),
(236, 'Text', 'ru', 'Текст'),
(237, 'Pages not found', 'ru', 'Страниц не найдено'),
(238, 'Sign Up', 'ru', 'Регистрация'),
(239, 'Admin', 'ru', 'Админ'),
(240, 'Upload files', 'ru', 'Загрузка файлов'),
(241, 'Account', 'ru', 'Аккаунт'),
(242, 'Save settings', 'ru', 'Сохранить настройки'),
(243, 'Access denied', 'ru', 'Доступ запрещен'),
(244, 'Message sent successfully', 'ru', 'Сообщение успешно отправлено'),
(245, 'New message from', 'ru', 'Новое сообщение от'),
(246, 'My Account', 'ru', 'Мой аккаунт'),
(247, 'Show navigation', 'ru', 'Показать навигацию'),
(248, 'Your message here', 'ru', 'Ваше сообщение'),
(249, 'Send message', 'ru', 'Отправить сообщение'),
(250, 'Developed by', 'ru', 'Разработано на базе'),
(251, 'Up', 'ru', 'Вверх'),
(252, 'Show All', 'ru', 'Показать все'),
(253, 'Change picture', 'ru', 'Сменить изображение'),
(254, 'Enter your email and password to continue', 'ru', 'Для продолжения укажите ваш email и пароль'),
(255, 'Settings', 'ru', 'Настройки'),
(256, 'Email', 'ru', 'Email'),
(257, 'New password', 'ru', 'Новый пароль'),
(258, 'Enter your email', 'ru', 'Укажите ваш email'),
(259, 'Enter your password', 'ru', 'Укажите ваш пароль'),
(260, 'Save changes', 'ru', 'Сохранить настройки'),
(261, 'Back to account', 'ru', 'Назад в аккаунт'),
(262, 'Invalid conformation code', 'ru', 'Неправильный код подтверждения'),
(263, 'Registration at', 'ru', 'Регистрация на'),
(264, 'Confirmation code', 'ru', 'Код подтверждения'),
(265, 'No articles found', 'ru', 'Статей не найдено'),
(266, 'Messages', 'ru', 'Сообщения'),
(267, 'Back to admin', 'ru', 'Назад в админку'),
(268, 'Status', 'ru', 'Статус'),
(269, 'Yes', 'ru', 'Да'),
(270, 'No', 'ru', 'Нет'),
(271, 'Show in navigation', 'ru', 'Показать в навигации'),
(272, 'Back to content', 'ru', 'Назад к содержанию'),
(273, 'Delete image', 'ru', 'Удалить изображение'),
(274, 'Comments not found', 'ru', 'Комментариев не найдено'),
(275, 'Add reply', 'ru', 'Добавить ответ'),
(276, 'Users not found', 'ru', 'Пользователей не найдено'),
(277, 'Share friends in', 'ru', 'Рассказать друзьям в'),
(278, 'Language', 'ru', 'Язык'),
(279, 'Home', 'ru', 'Главная'),
(280, 'Connect us at', 'ru', 'Связаться в '),
(281, 'All rights reserved', 'ru', 'Все права защищены'),
(282, 'There is no users, you can send a message', 'ru', 'Контакты отсутствуют'),
(283, 'Add comment', 'ru', 'Добавить комментарий'),
(284, 'Submit comment', 'ru', 'Отправить комментарий'),
(285, 'Add new comment', 'ru', 'Добавить новый комментарий'),
(286, 'There is no comments', 'ru', 'Комментарии отсутствуют'),
(287, 'Comment submited!', 'ru', 'Комментарий отправлен!'),
(288, 'To post a comment, please', 'ru', 'Чтобы отправить комментарий, '),
(289, 'Restore password', 'ru', 'Восстановить пароль'),
(290, 'Reply', 'ru', 'Ответ'),
(291, 'Enabled', 'ru', 'Включено'),
(292, 'Autoupdate', 'ru', 'Автообновление'),
(293, 'Authentication error', 'ru', 'Ошибка аутенфикации'),
(294, 'Disabled', 'ru', 'Отключено'),
(295, 'Select your language', 'en', 'Select your language'),
(296, 'Select your language', 'ru', 'Выбирете язык'),
(297, 'Search', 'en', 'Search'),
(298, 'Search results by request', 'en', 'Search results by request'),
(299, 'Documentation', 'en', 'Documentation'),
(300, 'Add new value', 'en', 'Add new value'),
(301, 'Or file', 'ru', 'Или файл'),
(302, 'Online', 'ru', 'онлайн'),
(303, 'Get in Touch', 'ru', 'Связаться'),
(304, 'Contact Us', 'ru', 'Напишите нам'),
(305, 'Copyright', 'ru', 'Copyright'),
(306, 'offline', 'ru', 'оффлайн'),
(307, 'Search', 'ru', 'Поиск'),
(308, 'Search results by request', 'ru', 'Поиск по запросу'),
(309, 'Documentation', 'ru', 'Документация'),
(310, 'Add new value', 'ru', 'Добавить запись'),
(311, 'Download', 'en', 'Download'),
(312, 'Install', 'en', 'Install'),
(313, 'Framework', 'en', 'Framework'),
(314, 'auth', 'ru', 'Авторизуйтесь'),
(315, 'Useful services', 'en', 'Useful services'),
(316, 'Setup', 'en', 'Setup'),
(317, 'Login to send message', 'en', 'Login to send message'),
(318, 'Add article', 'en', 'Add article'),
(319, 'Download', 'ru', 'Скачать'),
(320, 'Install', 'ru', 'Установить'),
(321, 'Framework', 'ru', 'Структура'),
(322, 'Useful services', 'ru', 'Полезные сервисы'),
(323, 'Setup', 'ru', 'Установка'),
(324, 'Login to send message', 'ru', 'Авторизуйтесь, чтобы отправить сообщение'),
(325, 'Add article', 'ru', 'Добавить статью'),
(326, 'register now', 'ru', 'Зарегистрируйтесь'),
(327, 'register now', 'en', 'register now'),
(328, 'auth', 'en', 'auth'),
(329, 'There is no articles', 'en', 'There is no articles'),
(330, 'Functions', 'en', 'Functions'),
(331, 'Database', 'en', 'Database'),
(332, 'There is no articles', 'ru', 'Статей не найдено'),
(333, 'Functions', 'ru', 'Функции'),
(334, 'Database', 'ru', 'База данных'),
(335, 'Clear logs', 'ru', 'Очистить логи'),
(336, 'Logs not found', 'ru', 'Логи не найдены'),
(337, 'Sorry, no results found', 'en', 'Sorry, no results found'),
(338, 'Search results for', 'ru', 'Результаты поиска'),
(339, 'Sorry, no results found', 'ru', 'По вашему запросу ничего не нашлось'),
(340, 'Value', 'en', 'Value'),
(341, 'Showing', 'en', 'Showing'),
(342, 'to', 'en', 'to'),
(343, 'from', 'en', 'from'),
(344, 'entries', 'en', 'entries'),
(345, 'per page', 'en', 'per page'),
(346, 'Next', 'en', 'Next'),
(347, 'IP', 'ru', ''),
(348, 'New message', 'ru', 'Новое сообщение'),
(349, 'Previous', 'en', 'Previous'),
(350, 'Code', 'en', 'Code'),
(351, 'Select option', 'en', 'Select option'),
(352, 'Content not found', 'en', 'Content not found'),
(353, 'Crop image', 'en', 'Crop image'),
(354, 'Value', 'ru', 'Значение'),
(355, 'Showing', 'ru', 'Отображаются'),
(356, 'to', 'ru', 'по'),
(357, 'from', 'ru', 'из'),
(358, 'entries', 'ru', 'вхождений'),
(359, 'per page', 'ru', 'на страницу'),
(360, 'Next', 'ru', 'Вперед'),
(361, 'Account confirmation', 'ru', 'Подтверждение аккаунта'),
(362, 'Previous', 'ru', 'Назад'),
(363, 'Code', 'ru', 'Код'),
(364, 'Select option', 'ru', 'Выберете опцию'),
(365, 'Content not found', 'ru', 'Данных не найдено'),
(366, 'Crop image', 'ru', 'Обрезать изображение'),
(367, 'Sended', 'en', 'Sended'),
(368, 'at', 'en', 'at'),
(369, 'Sended', 'ru', 'Отправлено'),
(370, 'at', 'ru', 'в'),
(371, 'Too many failed attempts', 'en', 'Too many failed attempts'),
(372, 'Try again after', 'en', 'Try again after'),
(373, 'seconds', 'en', 'seconds'),
(374, 'Action', 'en', 'Action'),
(375, 'User', 'en', 'User'),
(376, 'IP', 'en', 'IP'),
(377, 'Too many failed attempts', 'ru', 'Слишком много неудачных попыток'),
(378, 'User', 'ru', 'Пользователь'),
(379, 'Action', 'ru', 'Действие'),
(380, 'seconds', 'ru', 'секунд'),
(381, 'Try again after', 'ru', 'Попробуйте еще через'),
(382, 'New message', 'en', 'New message'),
(383, 'Clear logs', 'en', 'Clear logs'),
(384, 'Logs not found', 'en', 'Logs not found'),
(385, 'Updates', 'en', 'Updates'),
(386, 'Autoupdate', 'en', 'Autoupdate'),
(387, 'Enabled', 'en', 'Enabled'),
(388, 'Disabled', 'en', 'Disabled'),
(389, 'Authentication error', 'en', 'Authentication error'),
(390, 'Authentication timeout', 'en', 'Authentication timeout'),
(391, 'Restore password', 'en', 'Restore password'),
(392, 'Received', 'en', 'Received'),
(393, 'new messages', 'en', 'new messages'),
(394, 'Updating engine from version', 'en', 'Updating engine from version'),
(395, 'Downloading files', 'en', 'Downloading files'),
(396, 'Receiving', 'en', 'Receiving'),
(397, 'Update aborted', 'en', 'Update aborted'),
(398, 'Replacing downloaded files from', 'en', 'Replacing downloaded files from'),
(399, 'Receiving MySQL data', 'en', 'Receiving MySQL data'),
(400, 'Executed', 'en', 'Executed'),
(401, 'commands', 'en', 'commands'),
(402, 'Updating to version', 'en', 'Updating to version'),
(403, 'is complete', 'en', 'is complete'),
(404, 'after 5 seconds', 'en', 'after 5 seconds'),
(405, 'Current version', 'en', 'Current version'),
(406, 'New updates available', 'en', 'New updates available'),
(407, 'Update Now', 'en', 'Update Now'),
(408, 'No updates available', 'en', 'No updates available'),
(409, 'Invalid confirmation code', 'en', 'Invalid confirmation code'),
(410, 'Data not found', 'en', 'Data not found'),
(411, 'Trying to register', 'en', 'Trying to register'),
(412, 'Comment posted', 'en', 'Comment posted'),
(413, 'There is no files', 'en', 'There is no files'),
(414, 'Downloading files', 'ru', 'Скачивание файлов'),
(415, 'Receiving', 'ru', 'Получение'),
(416, 'Update aborted', 'ru', 'Обновление прекращено'),
(417, 'Replacing downloaded files from', 'ru', 'Перемещение скачанных файлов из'),
(418, 'Receiving MySQL data', 'ru', 'Получение MySQL данных'),
(419, 'Executed', 'ru', 'Исполнено'),
(420, 'commands', 'ru', 'комманд'),
(421, 'Updating to version', 'ru', 'Обновление до версии'),
(422, 'is complete', 'ru', 'завершено'),
(423, 'after 5 seconds', 'ru', 'через 5 секунд'),
(424, 'Current version', 'ru', 'Текущая версия'),
(425, 'New updates available', 'ru', 'Доступно обновление'),
(426, 'Update Now', 'ru', 'Обновить сейчас'),
(427, 'No updates available', 'ru', 'Обновление недоступно'),
(428, 'Invalid confirmation code', 'ru', 'Неверный код подтверждения'),
(429, 'Data not found', 'ru', 'Данные не найдены'),
(430, 'Trying to register', 'ru', 'Попытка регистрации'),
(431, 'Comment posted', 'ru', 'Добавлен комментарий'),
(432, 'There is no files', 'ru', 'Файлов не найдено'),
(433, 'Updates', 'ru', 'Обновления'),
(434, 'Login', 'en', 'Login'),
(435, 'Loading', 'en', 'Loading'),
(436, 'Page not found', 'en', 'Page not found'),
(437, 'Back to Home Page', 'en', 'Back to Home Page'),
(438, 'or', 'en', 'or'),
(439, 'Login', 'ru', 'Логин'),
(440, 'Attendance', 'en', 'Attendance'),
(441, 'Page not found', 'ru', 'Страница не найдена'),
(442, 'Back to Home Page', 'ru', 'Назад на главную'),
(443, 'or', 'ru', 'или'),
(444, 'Loading', 'ru', 'Загрузка'),
(445, 'Back to Top', 'en', 'Back to Top'),
(446, 'Search results for', 'en', 'Search results for'),
(447, 'Templates', 'en', 'Templates'),
(448, 'Default file', 'en', 'Default file'),
(449, 'New file', 'en', 'New file'),
(450, 'New template', 'en', 'New template'),
(451, 'Default template', 'en', 'Default template'),
(452, 'Template name', 'en', 'Template name'),
(453, 'Views', 'en', 'Views'),
(454, 'Visitors', 'en', 'Visitors'),
(455, 'Statistic', 'en', 'Statistic'),
(456, 'Pages', 'en', 'Pages'),
(457, 'Referrers', 'en', 'Referrers'),
(458, 'By days', 'en', 'By days'),
(459, 'By weeks', 'en', 'By weeks'),
(460, 'By months', 'en', 'By months'),
(461, 'Amount', 'en', 'Amount'),
(462, 'Blank', 'en', 'Blank'),
(463, 'Restore your password', 'en', 'Restore your password'),
(464, 'To restore your password, use this code', 'en', 'To restore your password, use this code'),
(465, 'Message with confirmation code is sended to email', 'en', 'Message with confirmation code is sended to email'),
(466, 'There is no templates', 'en', 'There is no templates'),
(467, 'Register', 'en', 'Register'),
(468, 'Try to register', 'en', 'Try to register'),
(469, 'Trying to login', 'en', 'Trying to login'),
(470, 'Engine update', 'en', 'Engine update'),
(471, 'Attendance', 'ru', 'Посещаемость'),
(472, 'Back to Top', 'ru', 'Наверх'),
(473, 'Templates', 'ru', 'Шаблоны'),
(474, 'Default file', 'ru', 'Файл по-умолчанию'),
(475, 'New file', 'ru', 'Новый файл'),
(476, 'New template', 'ru', 'Новый шаблон'),
(477, 'Default template', 'ru', 'Шаблон по-умолчанию'),
(478, 'Template name', 'ru', 'Название шаблона'),
(479, 'Views', 'ru', 'Просмотры'),
(480, 'Visitors', 'ru', 'Посетители'),
(481, 'Statistic', 'ru', 'Статистика'),
(482, 'Pages', 'ru', 'Страницы'),
(483, 'Referrers', 'ru', 'Источники'),
(484, 'By days', 'ru', 'По дням'),
(485, 'By weeks', 'ru', 'По неделям'),
(486, 'By months', 'ru', 'По месяцам'),
(487, 'Amount', 'ru', 'Количество'),
(488, 'Blank', 'ru', 'Пусто'),
(489, 'Restore your password', 'ru', 'Восстановить пароль'),
(490, 'To restore your password, use this code', 'ru', 'Для восстановления пароля используйте этот код'),
(491, 'Message with confirmation code is sended to email', 'ru', 'Письмо с кодом подтверждения отправлено на указаный email'),
(492, 'There is no templates', 'ru', 'Шаблоны отсутствуют'),
(493, 'Register', 'ru', 'Регистрация'),
(494, 'Try to register', 'ru', 'Попытка регистрации'),
(495, 'Trying to login', 'ru', 'Попытка авторизации'),
(496, 'Engine update', 'ru', 'Обновление движка'),
(497, 'Withdrawal already requested', 'en', 'Withdrawal already requested'),
(498, 'Withdrawal request accepted', 'en', 'Withdrawal request accepted'),
(499, 'Select file', 'en', 'Select file'),
(500, 'Editable file', 'en', 'Editable file'),
(501, 'Source file', 'en', 'Source file'),
(502, 'Gateway Timeout', 'en', 'Gateway Timeout'),
(503, 'Image too small. Minimal size is 400x400', 'en', 'Image too small. Minimal size is 400x400'),
(504, 'Drop files here', 'en', 'Drop files here'),
(505, 'System message', 'en', 'System message'),
(506, 'sign in', 'en', 'sign in'),
(507, 'Confirm Shipment', 'en', 'Confirm Shipment'),
(508, 'Post track number', 'en', 'Post track number'),
(509, 'Shipment is confirmed', 'en', 'Shipment is confirmed'),
(510, 'This item is sold out now?', 'en', 'This item is sold out now?'),
(511, 'New order', 'en', 'New order'),
(512, 'Archive order', 'en', 'Archive order'),
(513, 'Finished', 'en', 'Finished'),
(514, 'Purchaser', 'en', 'Purchaser'),
(515, 'Shipping address', 'en', 'Shipping address'),
(516, 'All Items', 'en', 'All Items'),
(517, 'Buy Now!', 'en', 'Buy Now!'),
(518, 'Title', 'en', 'Title'),
(519, 'Ask price', 'en', 'Ask price'),
(520, 'Edit product', 'en', 'Edit product'),
(521, 'Click to Deactivate', 'en', 'Click to Deactivate'),
(522, 'Click to Activate', 'en', 'Click to Activate'),
(523, 'You might also be interested in', 'en', 'You might also be interested in'),
(524, 'Products not found', 'en', 'Products not found'),
(525, 'Category', 'en', 'Category'),
(526, 'Reset Filter', 'en', 'Reset Filter'),
(527, 'Unable to purchase your own product', 'en', 'Unable to purchase your own product'),
(528, 'Buy Now', 'en', 'Buy Now'),
(529, 'Tracking number', 'en', 'Tracking number'),
(530, 'Shipment in process', 'en', 'Shipment in process'),
(531, 'Confirm receipt', 'en', 'Confirm receipt'),
(532, 'Seller', 'en', 'Seller'),
(533, 'Shipping from', 'en', 'Shipping from'),
(534, 'Category', 'ru', 'Категория'),
(535, 'Reset Filter', 'ru', 'Сбросить'),
(536, 'Unable to purchase your own product', 'ru', 'Нельзя приобрести свой товар'),
(537, 'New comment at', 'en', 'New comment at'),
(538, 'You might also be interested in', 'ru', 'Вас может заинтересовать'),
(539, 'Products not found', 'ru', 'Товары не найдены'),
(540, 'New message at', 'en', 'New message at'),
(541, 'Dear', 'en', 'Dear'),
(542, 'sent a message for you', 'en', 'sent a message for you'),
(543, 'For details, click', 'en', 'For details, click'),
(544, 'here', 'en', 'here'),
(545, 'Withdrawal request at', 'en', 'Withdrawal request at'),
(546, 'You withdrawal request is pending now', 'en', 'You withdrawal request is pending now'),
(547, 'After some time you will receive', 'en', 'After some time you will receive'),
(548, 'on your PayPal account', 'en', 'on your PayPal account'),
(549, 'There in new withdrawal request at', 'en', 'There in new withdrawal request at'),
(550, 'Need to pay', 'en', 'Need to pay'),
(551, 'on PayPal account', 'en', 'on PayPal account'),
(552, 'and confirm request', 'en', 'and confirm request'),
(553, 'Details', 'en', 'Details'),
(554, 'Withdrawal is complete at', 'en', 'Withdrawal is complete at'),
(555, 'You withdrawal is complete', 'en', 'You withdrawal is complete'),
(556, 'Thanks for using our service and have a nice day', 'en', 'Thanks for using our service and have a nice day'),
(557, 'New purchase at', 'en', 'New purchase at'),
(558, 'Congratulations on your purchase at', 'en', 'Congratulations on your purchase at'),
(559, 'You can see details of your purchases', 'en', 'You can see details of your purchases'),
(560, 'There in new purchase at', 'en', 'There in new purchase at'),
(561, 'make a payment', 'en', 'make a payment'),
(562, 'to your PayPal account', 'en', 'to your PayPal account'),
(563, 'Your order has been shipped at', 'en', 'Your order has been shipped at'),
(564, 'Your order', 'en', 'Your order'),
(565, 'has been shipped', 'en', 'has been shipped'),
(566, 'After receiving, please update purchase status', 'en', 'After receiving, please update purchase status'),
(567, 'Your order has been completed at', 'en', 'Your order has been completed at'),
(568, 'Your order has been completed', 'en', 'Your order has been completed'),
(569, 'Funds added to your account and available for withdrawal', 'en', 'Funds added to your account and available for withdrawal'),
(570, 'Orders not found', 'en', 'Orders not found'),
(571, 'Type', 'en', 'Type'),
(572, 'Withdrawal request', 'en', 'Withdrawal request'),
(573, 'Money deposit', 'en', 'Money deposit'),
(574, 'Order', 'en', 'Order'),
(575, 'Please, confirm transaction', 'en', 'Please, confirm transaction'),
(576, 'Confirm payment', 'en', 'Confirm payment'),
(577, 'Bots', 'en', 'Bots'),
(578, 'Banned', 'en', 'Banned'),
(579, 'Active', 'en', 'Active'),
(580, 'Please, describe this item', 'en', 'Please, describe this item'),
(581, 'Description (e.g. Blue Nike Vapor Cleats Size 10. Very comfortable and strong ankle support.)', 'en', 'Description (e.g. Blue Nike Vapor Cleats Size 10. Very comfortable and strong ankle support.)'),
(582, 'Please, confirm item shipping address', 'en', 'Please, confirm item shipping address'),
(583, 'State', 'en', 'State'),
(584, 'City', 'en', 'City'),
(585, 'Zip code', 'en', 'Zip code'),
(586, 'Street', 'en', 'Street'),
(587, 'Phone number', 'en', 'Phone number'),
(588, 'Price', 'en', 'Price'),
(589, 'Choose action', 'en', 'Choose action'),
(590, 'Edit item', 'en', 'Edit item'),
(591, 'Deactivate item', 'en', 'Deactivate item'),
(592, 'Activate item', 'en', 'Activate item'),
(593, 'List new Item', 'en', 'List new Item'),
(594, 'Edit Properties', 'en', 'Edit Properties'),
(595, 'Keywords', 'en', 'Keywords'),
(596, 'Mode', 'en', 'Mode'),
(597, 'Replace', 'en', 'Replace'),
(598, 'Cron error', 'en', 'Cron error'),
(599, 'Save', 'en', 'Save'),
(600, 'Are you sure?', 'en', 'Are you sure?'),
(601, 'Add value', 'en', 'Add value'),
(602, 'Edit property', 'en', 'Edit property'),
(603, 'New value', 'en', 'New value'),
(604, 'Delete property', 'en', 'Delete property'),
(605, 'Add new property', 'en', 'Add new property'),
(606, 'Back to products', 'en', 'Back to products'),
(607, 'Connect with social network', 'en', 'Connect with social network'),
(608, 'Delivery confirmation', 'en', 'Delivery confirmation'),
(609, 'Quality', 'en', 'Quality'),
(610, 'Your comment here', 'en', 'Your comment here'),
(611, 'Purchases', 'en', 'Purchases'),
(612, 'Thank you for your order! Shipment in process now.', 'en', 'Thank you for your order! Shipment in process now.'),
(613, 'There is no purchases', 'en', 'There is no purchases'),
(614, 'Finances', 'en', 'Finances'),
(615, 'The funds have been added to your account', 'en', 'The funds have been added to your account'),
(616, 'Balance', 'en', 'Balance'),
(617, 'Pending', 'en', 'Pending'),
(618, 'New', 'en', 'New'),
(619, 'Transactions not found', 'en', 'Transactions not found'),
(620, 'Request withdrawal', 'en', 'Request withdrawal'),
(621, 'Confirm your PayPal', 'en', 'Confirm your PayPal'),
(622, 'Deposit money', 'en', 'Deposit money'),
(623, 'Amount to deposit', 'en', 'Amount to deposit'),
(624, 'About', 'en', 'About'),
(625, 'Products', 'en', 'Products'),
(626, 'Orders', 'en', 'Orders'),
(627, 'Finance', 'en', 'Finance'),
(628, 'Order products', 'en', 'Order products'),
(629, 'Confirmation', 'en', 'Confirmation'),
(630, 'Please, confirm your order', 'en', 'Please, confirm your order'),
(631, 'Remove product', 'en', 'Remove product'),
(632, 'Sorry, there is no products', 'en', 'Sorry, there is no products'),
(633, 'Total price', 'en', 'Total price'),
(634, 'Shipping', 'en', 'Shipping'),
(635, 'Please, confirm your shipping address', 'en', 'Please, confirm your shipping address'),
(636, 'Payment', 'en', 'Payment'),
(637, 'First name', 'en', 'First name'),
(638, 'Last name', 'en', 'Last name'),
(639, 'Country', 'en', 'Country'),
(640, 'Process payment', 'en', 'Process payment'),
(641, 'FILTER RESULTS', 'en', 'FILTER RESULTS'),
(642, 'Already have an account?', 'en', 'Already have an account?'),
(643, 'Empty search query', 'en', 'Empty search query'),
(644, 'Cart', 'en', 'Cart'),
(645, 'Withdrawal already requested', 'ru', 'Вывод средств уже запрошен'),
(646, 'Withdrawal request accepted', 'ru', 'Запрос на вывод средств принят'),
(647, 'Select file', 'ru', 'Выберите файл'),
(648, 'Editable file', 'ru', 'Редактируемый файл'),
(649, 'Source file', 'ru', 'Исходный файл'),
(650, 'Gateway Timeout', 'ru', 'Таймаут запроса'),
(651, 'Image too small. Minimal size is 400x400', 'ru', 'Изображение слишком маленькое. Минимальный размер 400x400'),
(652, 'Drop files here', 'ru', 'Пертащите файлы сюда'),
(653, 'System message', 'ru', 'Системное сообщение'),
(654, 'sign in', 'ru', 'авторизуйтесь'),
(655, 'Confirm Shipment', 'ru', 'Подтвердите отправку'),
(656, 'Post track number', 'ru', 'Почтовый трек-номер'),
(657, 'Shipment is confirmed', 'ru', 'Отправка подтверждена'),
(658, 'This item is sold out now?', 'ru', 'Снять товар с продажи?'),
(659, 'New order', 'ru', 'Новый заказ'),
(660, 'Archive order', 'ru', 'Архивировать заказ'),
(661, 'Finished', 'ru', 'Завершено'),
(662, 'Purchaser', 'ru', 'Покупатель'),
(663, 'Shipping address', 'ru', 'Адрес доставки'),
(664, 'All Items', 'ru', 'Все товары'),
(665, 'Buy Now!', 'ru', 'Купить!'),
(666, 'Title', 'ru', 'Название'),
(667, 'Ask price', 'ru', 'Стоимость'),
(668, 'Edit product', 'ru', 'Редактировать товар'),
(669, 'Click to Deactivate', 'ru', 'Деактивировать'),
(670, 'Click to Activate', 'ru', 'Активировать'),
(671, 'Buy Now', 'ru', 'Купить'),
(672, 'Tracking number', 'ru', 'Трек-номер'),
(673, 'Shipment in process', 'ru', 'Доставка в процессе'),
(674, 'Confirm receipt', 'ru', 'Подтвердить получение'),
(675, 'Seller', 'ru', 'Продавец'),
(676, 'Shipping from', 'ru', 'Доставка из'),
(677, 'New comment at', 'ru', 'Новый комментарий на'),
(678, 'Ok', 'ru', 'Ok'),
(679, 'New message at', 'ru', 'Новое сообщение на'),
(680, 'Dear', 'ru', 'Дорогой'),
(681, 'sent a message for you', 'ru', 'отправил(а) сообщение вам'),
(682, 'For details, click', 'ru', 'Для подробностей, кликните'),
(683, 'here', 'ru', 'здесь'),
(684, 'Withdrawal request at', 'ru', 'Вывод средств'),
(685, 'You withdrawal request is pending now', 'ru', 'Ваш запрос на вывод средств обрабатывается'),
(686, 'After some time you will receive', 'ru', 'Через некоторое время вам придет'),
(687, 'on your PayPal account', 'ru', 'на ваш PayPal аккаунт'),
(688, 'There in new withdrawal request at', 'ru', 'Новый запрос на вывод средств на'),
(689, 'Need to pay', 'ru', 'Нужно оплатить'),
(690, 'on PayPal account', 'ru', 'на PayPal аккаунт'),
(691, 'and confirm request', 'ru', 'и подтвредить запрос'),
(692, 'Details', 'ru', 'Подробности'),
(693, 'Withdrawal is complete at', 'ru', 'Вывод средств завершен на'),
(694, 'You withdrawal is complete', 'ru', 'Ваш запрос средств обработан'),
(695, 'Thanks for using our service and have a nice day', 'ru', 'Спасибо за использование нашего сервиса и удачного Вам дня'),
(696, 'New purchase at', 'ru', 'Новая покупка на'),
(697, 'Congratulations on your purchase at', 'ru', 'Поздравляем с покупкой на'),
(698, 'You can see details of your purchases', 'ru', 'Вы можете посмотреть подробную информацию о покупке'),
(699, 'There in new purchase at', 'ru', 'Новая покупка на'),
(700, 'make a payment', 'ru', 'совершил платеж'),
(701, 'to your PayPal account', 'ru', 'на ваш PayPal аккаунт'),
(702, 'Your order has been shipped at', 'ru', 'Ваш заказ был отправлен на'),
(703, 'Your order', 'ru', 'Ваш заказ'),
(704, 'has been shipped', 'ru', 'был отправлен'),
(705, 'After receiving, please update purchase status', 'ru', 'После получения обновите статус покупки'),
(706, 'Your order has been completed at', 'ru', 'Ваш заказ был завершен на'),
(707, 'Your order has been completed', 'ru', 'Ваш заказ был завершен'),
(708, 'Funds added to your account and available for withdrawal', 'ru', 'Средства зачислены на Ваш аккаунт и доступны для вывода'),
(709, 'Orders not found', 'ru', 'Заказов не найдено'),
(710, 'Type', 'ru', 'Тип'),
(711, 'Withdrawal request', 'ru', 'Запрос на вывод'),
(712, 'Money deposit', 'ru', 'Зачисление средств'),
(713, 'Order', 'ru', 'Заказ'),
(714, 'Please, confirm transaction', 'ru', 'Пожалуйста, подтвердите'),
(715, 'Confirm payment', 'ru', 'Подтвердить платеж'),
(716, 'Bots', 'ru', 'Боты'),
(717, 'Banned', 'ru', 'Забанен'),
(718, 'Active', 'ru', 'Активен'),
(719, 'Please, describe this item', 'ru', 'Пожалуйста, опишите товар'),
(720, 'Description (e g  Blue Nike Vapor Cleats Size 10  Very comfortable and strong ankle support )', 'ru', 'Описание (прим. Синиие Nike Vapor Бутсы. Размер 10. Очень удобные и сильная поддержка лодыжки.)'),
(721, 'Please, confirm item shipping address', 'ru', 'Пожалуйста, подтвердите адрес'),
(722, 'State', 'ru', 'Область'),
(723, 'City', 'ru', 'Город'),
(724, 'Zip code', 'ru', 'Почтовый индекс'),
(725, 'Street', 'ru', 'Улица'),
(726, 'Phone number', 'ru', 'Номер телефона'),
(727, 'Price', 'ru', 'Цена'),
(728, 'Choose action', 'ru', 'Выберете действие'),
(729, 'Edit item', 'ru', 'Редактировать товар'),
(730, 'Deactivate item', 'ru', 'Деактивировать'),
(731, 'Activate item', 'ru', 'Активировать'),
(732, 'List new Item', 'ru', 'Добавить новый товар'),
(733, 'Edit Properties', 'ru', 'Редактировать свойства'),
(734, 'Keywords', 'ru', 'Ключевые слова'),
(735, 'Mode', 'ru', 'Режим'),
(736, 'Replace', 'ru', 'Заменить'),
(737, 'Cron error', 'ru', 'Ошибка cron'),
(738, 'Save', 'ru', 'Сохранить'),
(739, 'Are you sure?', 'ru', 'Вы уверены?'),
(740, 'Add value', 'ru', 'Добавить значение'),
(741, 'Edit property', 'ru', 'Редактировать свойство'),
(742, 'New value', 'ru', 'Новое значение'),
(743, 'Delete property', 'ru', 'Удалить свойство'),
(744, 'Add new property', 'ru', 'Добавить новое свойство'),
(745, 'Back to products', 'ru', 'Назад к товарам'),
(746, 'Connect with social network', 'ru', 'Подключить аккаунт соц. сети'),
(747, 'Delivery confirmation', 'ru', 'Подтверждение получения'),
(748, 'Quality', 'ru', 'Качество'),
(749, 'Your comment here', 'ru', 'Ваш комментарий'),
(750, 'Purchases', 'ru', 'Покупки'),
(751, 'Thank you for your order! Shipment in process now.', 'ru', 'Спасибо за Ваш заказ! Отправление в процессе.'),
(752, 'There is no purchases', 'ru', 'Нет новых покупок'),
(753, 'Finances', 'ru', 'Финансы'),
(754, 'The funds have been added to your account', 'ru', 'Средства были зачислены на Ваш аккаунт'),
(755, 'Balance', 'ru', 'Баланс'),
(756, 'Pending', 'ru', 'Ожидание'),
(757, 'New', 'ru', 'Новый'),
(758, 'Transactions not found', 'ru', 'Транзакций не найдено'),
(759, 'Request withdrawal', 'ru', 'Запрос на вывод'),
(760, 'Confirm your PayPal', 'ru', 'Укажите Ваш PayPal'),
(761, 'Deposit money', 'ru', 'Зачислить средства'),
(762, 'Amount to deposit', 'ru', 'Количество к зачислению'),
(763, 'About', 'ru', 'О программе'),
(764, 'Products', 'ru', 'Товары'),
(765, 'Orders', 'ru', 'Заказы'),
(766, 'Finance', 'ru', 'Финансы'),
(767, 'Order products', 'ru', 'Заказать товары'),
(768, 'Confirmation', 'ru', 'Подтверждение'),
(769, 'Please, confirm your order', 'ru', 'Пожалуйста, подтвердите Ваш заказ'),
(770, 'Remove product', 'ru', 'Удалить товар'),
(771, 'Sorry, there is no products', 'ru', 'Извините, товаров не найдено'),
(772, 'Total price', 'ru', 'Итоговая цена'),
(773, 'Shipping', 'ru', 'Доставка'),
(774, 'Please, confirm your shipping address', 'ru', 'Пожалуйста, подтвержите адрес'),
(775, 'Payment', 'ru', 'Платеж'),
(776, 'First name', 'ru', 'Имя'),
(777, 'Last name', 'ru', 'Фамилия'),
(778, 'Country', 'ru', 'Страна'),
(779, 'Process payment', 'ru', 'Оплатить'),
(780, 'FILTER RESULTS', 'ru', 'ФИЛЬТР РЕЗУЛЬТАТОВ'),
(781, 'Already have an account?', 'ru', 'Уже зарегистрированы?'),
(782, 'Empty search query', 'ru', 'Пустой поисковый запрос'),
(783, 'Cart', 'ru', 'Корзина'),
(784, 'Description (e.g. Blue Nike Vapor Cleats Size 10. Very comfortable and strong ankle support.)', 'ru', 'Описание (прим. Синиие Nike Vapor Бутсы. Размер 10. Очень удобные и сильная поддержка лодыжки.)'),
(785, 'Views per day', 'ru', 'Просмотров в день'),
(786, 'Last visit', 'en', 'Last visit'),
(787, 'b', 'ru', 'Ваш заказ был завершен'),
(788, 'Visitors per day', 'ru', 'Посетителей в день'),
(789, 'Total comments', 'ru', 'Всего комментариев'),
(790, 'Total users', 'ru', 'Всего пользователей'),
(791, 'Total articles', 'ru', 'Всего статей'),
(792, 'Total products', 'ru', 'Всего товаров'),
(793, 'Total pages', 'ru', 'Всего страниц'),
(794, 'Engine version', 'ru', 'Версия движка'),
(795, 'Delete article', 'ru', 'Удалить статью'),
(796, 'Page generation time', 'ru', 'Скорость генерации страницы'),
(797, 'Select an action', 'ru', 'Выберите действие'),
(798, 'Up to top', 'ru', 'Поднять вверх'),
(799, 'Transaction completed', 'ru', 'Перевод завершен'),
(800, 'Transaction from admin', 'ru', 'Перевод от админа'),
(801, 'New transaction', 'ru', 'Сделать перевод'),
(802, 'Transfer amount', 'ru', 'Сумма перевода'),
(803, 'This is a daily report for the website traffic and performance on', 'ru', 'Это дневной отчет о посещаемости и нагрузки на сайт'),
(804, 'Thanks for using our service', 'ru', 'Спасибо за использование нашего сервиса'),
(805, 'daily report', 'ru', 'дневной отчет'),
(806, 'Under construction', 'ru', 'В разработке'),
(807, 'Time', 'ru', 'Время'),
(808, 'Continue shopping', 'ru', 'Продолжить покупки'),
(809, 'Process order?', 'ru', 'Оформить заказ?'),
(810, 'A new item has been added to your Shopping Cart', 'ru', 'Новый товар был добавлен в корзину'),
(811, 'item(s)', 'ru', 'товар(ов)'),
(812, 'Your Shopping Cart', 'ru', 'Ваша корзина'),
(813, 'Checkout order', 'ru', 'Оформить заказ'),
(814, 'Navigation', 'ru', 'Навигация'),
(815, 'Profile image', 'ru', 'Изображение профиля'),
(816, 'Page generation speed', 'ru', 'Скорость генерации страниц'),
(817, 'By hours', 'ru', 'По часам'),
(818, 'Average Server Response', 'ru', 'Среднее время ответа сервера'),
(819, 'Average Site Response', 'ru', 'Среднее время ответа сайта'),
(820, 'Perfomance', 'ru', 'Нагрузка'),
(821, 'The user makes a purchase', 'ru', 'Пользователь сделал покупку'),
(822, 'User confirmed the receipt of the order', 'ru', 'Пользователь подтвердил получение заказа'),
(823, 'Order has been shipped', 'ru', 'Заказ был отправлен'),
(824, 'The user makes a purchase and payment!', 'ru', 'Пользователь сделал покупку с оплатой!'),
(825, 'Checkout order?', 'ru', 'Оформить заказ?'),
(826, 'Drop file here', 'ru', 'Перетащите сюда файл'),
(827, 'Uploading', 'ru', 'Загрузка'),
(828, 'Open', 'ru', 'Открыть'),
(829, 'The funds', 'ru', 'Средства'),
(830, 'has beed added to your account balance', 'ru', 'были зачислины на счет Вашего аккаунта'),
(831, 'this link', 'ru', 'эту ссылку'),
(832, 'To confirm this password, use', 'ru', 'Для подтверждения пароля, используйте'),
(833, 'Close window', 'ru', 'Закрыть окно'),
(834, 'Sorry, this email already registered', 'ru', 'Извините, этот email уже зарегистрирован'),
(835, 'The funds have been added to your account balance', 'ru', 'Средства были зачислены на Ваш счет'),
(836, 'New password activated!', 'ru', 'Новый пароль активирован!'),
(837, 'already exist', 'ru', 'уже существует'),
(838, 'Checkout order?', 'en', 'Checkout order?'),
(839, 'Error! Drag-n-drop disabled on this server', 'en', 'Error! Drag-n-drop disabled on this server'),
(840, 'Uploading', 'en', 'Uploading'),
(841, 'Drop file here', 'en', 'Drop file here'),
(842, 'Error! Drag-n-drop disabled on this server', 'ru', 'Ошибка. Функция перетаскивания отключена'),
(843, 'The user makes a purchase and payment!', 'en', 'The user makes a purchase and payment!'),
(844, 'Order has been shipped', 'en', 'Order has been shipped'),
(845, 'User confirmed the receipt of the order', 'en', 'User confirmed the receipt of the order'),
(846, 'The user makes a purchase', 'en', 'The user makes a purchase'),
(847, 'online', 'en', 'online'),
(848, 'Perfomance', 'en', 'Perfomance'),
(849, 'By hours', 'en', 'By hours'),
(850, 'Average Server Response', 'en', 'Average Server Response'),
(851, 'Average Site Response', 'en', 'Average Site Response'),
(852, 'Page generation speed', 'en', 'Page generation speed'),
(853, 'Profile image', 'en', 'Profile image'),
(854, 'Navigation', 'en', 'Navigation'),
(855, 'Checkout order', 'en', 'Checkout order'),
(856, 'Your Shopping Cart', 'en', 'Your Shopping Cart'),
(857, 'item(s)', 'en', 'item(s)'),
(858, 'Process order?', 'en', 'Process order?'),
(859, 'A new item has been added to your Shopping Cart', 'en', 'A new item has been added to your Shopping Cart'),
(860, 'Continue shopping', 'en', 'Continue shopping'),
(861, 'Time', 'en', 'Time'),
(862, 'Under construction', 'en', 'Under construction'),
(863, 'daily report', 'en', 'daily report'),
(864, 'Thanks for using our service', 'en', 'Thanks for using our service'),
(865, 'This is a daily report for the website traffic and performance on', 'en', 'This is a daily report for the website traffic and performance on'),
(866, 'Transfer amount', 'en', 'Transfer amount'),
(867, 'New transaction', 'en', 'New transaction'),
(868, 'Transaction completed', 'en', 'Transaction completed'),
(869, 'Transaction from admin', 'en', 'Transaction from admin'),
(870, 'Page generation time', 'en', 'Page generation time'),
(871, 'Select an action', 'en', 'Select an action'),
(872, 'Up to top', 'en', 'Up to top'),
(873, 'Delete article', 'en', 'Delete article'),
(874, 'Engine version', 'en', 'Engine version'),
(875, 'Total pages', 'en', 'Total pages'),
(876, 'Total articles', 'en', 'Total articles'),
(877, 'Total products', 'en', 'Total products'),
(878, 'Total users', 'en', 'Total users'),
(879, 'Total comments', 'en', 'Total comments'),
(880, 'Visitors per day', 'en', 'Visitors per day'),
(881, 'Views per day', 'en', 'Views per day'),
(882, 'Cron status', 'ru', 'Cron статус'),
(883, 'Cache', 'ru', 'Кеш'),
(884, 'Any action', 'ru', 'Любое действие'),
(885, 'Email sended', 'ru', 'Email отправлен'),
(886, 'Empty', 'ru', 'Пусто'),
(887, 'Last visit', 'ru', 'Последний визит'),
(888, 'Profile', 'ru', 'Профиль'),
(889, 'Continue', 'ru', 'Продолжить'),
(890, 'Checkout', 'ru', 'Оформить'),
(891, 'Edit catalog', 'ru', 'Редактировать каталог'),
(892, 'Delete catalog', 'ru', 'Удалить каталог'),
(893, 'Articles', 'ru', 'Статьи');


INSERT INTO `nodes_language` (`id`, `name`, `lang`, `value`) VALUES
(894, 'Down to bottom', 'ru', 'Опустить вниз'),
(895, 'List articles', 'ru', 'Список статей'),
(896, 'User banned', 'ru', 'Пользователь забанен'),
(897, 'Show more', 'en', 'Show more'),
(898, 'Privacy Policy', 'en', 'Privacy Policy'),
(899, 'Terms & Conditions', 'en', 'Terms & Conditions'),
(900, 'Any', 'en', 'Any'),
(901, 'Outbox', 'en', 'Outbox'),
(902, 'Continue', 'en', 'Continue'),
(903, 'Checkout', 'en', 'Checkout'),
(904, 'Show comments', 'en', 'Show comments'),
(905, 'Latest comments', 'en', 'Latest comments'),
(906, 'Show more', 'ru', 'Показать все'),
(907, 'Privacy Policy', 'ru', 'Конфиденциальность'),
(908, 'Terms & Conditions', 'ru', 'Правила и условия'),
(909, 'Any', 'ru', 'Любой'),
(910, 'Outbox', 'ru', 'Рассылка'),
(911, 'Show comments', 'ru', 'Показать комментарии'),
(912, 'Latest comments', 'ru', 'Последние комментарии'),
(913, 'View source', 'en', 'View source'),
(914, 'Delete file', 'en', 'Delete file'),
(915, 'Delete template', 'en', 'Delete template'),
(916, 'View source', 'ru', 'Посмотреть исходник'),
(917, 'Delete file', 'ru', 'Удалить файл'),
(918, 'Delete template', 'ru', 'Удалить шаблон'),
(919, 'Articles', 'en', 'Articles'),
(920, 'Member of', 'en', 'Member of'),
(921, 'community', 'en', 'community'),
(922, 'Property', 'en', 'Property'),
(923, 'Copy to', 'en', 'Copy to'),
(924, 'translation', 'en', 'translation'),
(925, 'Member of', 'ru', 'Участник '),
(926, 'community', 'ru', 'комьюнити'),
(927, 'Property', 'ru', 'Свойство'),
(928, 'Copy to', 'ru', 'Скопировать в'),
(929, 'translation', 'ru', 'перевод'),
(930, 'Color', 'en', 'Color'),
(931, 'White', 'en', 'White'),
(932, 'Black', 'en', 'Black'),
(933, 'Silver', 'en', 'Silver'),
(934, 'Gray', 'en', 'Gray'),
(935, 'Color', 'ru', 'Цвет'),
(936, 'White', 'ru', 'Белый'),
(937, 'Black', 'ru', 'Черный'),
(938, 'Silver', 'ru', 'Серебристый'),
(939, 'Gray', 'ru', 'Серый'),
(940, 'New bulk message', 'en', 'New bulk message'),
(941, 'Profile', 'en', 'Profile'),
(942, 'Ok', 'en', 'Ok'),
(943, 'Cron status', 'en', 'Cron status'),
(944, 'Cache', 'en', 'Cache'),
(945, 'List articles', 'en', 'List articles'),
(946, 'Edit catalog', 'en', 'Edit catalog'),
(947, 'Delete catalog', 'en', 'Delete catalog'),
(948, 'New bulk message', 'ru', 'Новая рассылка'),
(949, 'Resolution', 'en', 'Resolution'),
(950, 'Resolution', 'ru', 'Разрешение'),
(951, '1440 x 2560', 'en', '1440 x 2560'),
(952, '1080 x 1920', 'en', '1080 x 1920'),
(953, 'Close', 'en', 'Close'),
(954, 'Toggle fullscreen', 'en', 'Toggle fullscreen'),
(955, 'Zoom in/out', 'en', 'Zoom in/out'),
(956, 'Previous (arrow left)', 'en', 'Previous (arrow left)'),
(957, 'Next (arrow right)', 'en', 'Next (arrow right)'),
(958, 'votes', 'en', 'votes'),
(959, 'Share friends', 'en', 'Share friends'),
(960, 'Submitted on', 'en', 'Submitted on'),
(961, 'Close', 'ru', 'Закрыть'),
(962, 'Toggle fullscreen', 'ru', 'Включить полноэкранный режим'),
(963, 'Zoom in/out', 'ru', 'Увеличить/Уменьшить'),
(964, 'Previous (arrow left)', 'ru', 'Назад (стрелка влево)'),
(965, 'Next (arrow right)', 'ru', 'Вперед (стрелка вправо)'),
(966, 'votes', 'ru', 'голосов'),
(967, 'Share friends', 'ru', 'Поделиться'),
(968, 'Submitted on', 'ru', 'Опубликовано'),
(969, 'Share', 'en', 'Share'),
(970, 'Subscription', 'en', 'Subscription'),
(971, 'Down to bottom', 'en', 'Down to bottom'),
(972, 'Back to list', 'en', 'Back to list'),
(973, 'Passwords do not match', 'en', 'Passwords do not match'),
(974, 'Repeat password', 'en', 'Repeat password'),
(975, 'We are glad to confirm successful registration at', 'en', 'We are glad to confirm successful registration at'),
(976, 'By registering on the site, you accept the', 'en', 'By registering on the site, you accept the'),
(977, 'Terms and Conditions', 'en', 'Terms and Conditions'),
(978, 'and are familiar with the', 'en', 'and are familiar with the'),
(979, 'Delete account', 'en', 'Delete account'),
(980, 'Are you sure you want to delete your account', 'en', 'Are you sure you want to delete your account'),
(981, 'Empty', 'en', 'Empty'),
(982, 'Any action', 'en', 'Any action'),
(983, 'Email sended', 'en', 'Email sended'),
(984, 'Messages not found', 'en', 'Messages not found'),
(985, 'Send to email', 'en', 'Send to email'),
(986, 'Send in chat', 'en', 'Send in chat'),
(987, 'Text of message', 'en', 'Text of message'),
(988, 'Send messages', 'en', 'Send messages'),
(989, 'Back to outbox', 'en', 'Back to outbox'),
(990, 'Referrer', 'en', 'Referrer'),
(991, 'Actions', 'en', 'Actions'),
(992, 'View session', 'en', 'View session'),
(993, 'Mouse move to', 'en', 'Mouse move to'),
(994, 'Actions are finished at', 'en', 'Actions are finished at'),
(995, 'Click to', 'en', 'Click to'),
(996, 'Session finished', 'en', 'Session finished'),
(997, 'Mouse move to', 'ru', 'Перемещение мышки на'),
(998, 'Actions are finished at', 'ru', 'Действия закончены '),
(999, 'Click to', 'ru', 'Клик на'),
(1000, 'Session finished', 'ru', 'Сессия окночена'),
(1001, 'Passwords do not match', 'ru', 'Пароли не совпадают'),
(1002, 'Repeat password', 'ru', 'Повтрный пароль'),
(1003, 'We are glad to confirm successful registration at', 'ru', 'Мы рады подтвердить успешную регистрацию на'),
(1004, 'By registering on the site, you accept the', 'ru', 'Регистрируясь, вы принимаете условия раздела'),
(1005, 'Terms and Conditions', 'ru', 'Пользовательское соглашение'),
(1006, 'and are familiar with the', 'ru', 'и согласны с условиями раздела'),
(1007, 'Delete account', 'ru', 'Удалить аккаунт'),
(1008, 'Are you sure you want to delete your account', 'ru', 'Вы уверены что хотите удалить Ваш аккаунт?'),
(1009, 'Messages not found', 'ru', 'Сообщения не найдены'),
(1010, 'Send to email', 'ru', 'Отправить на email'),
(1011, 'Send in chat', 'ru', 'Отправить в чат'),
(1012, 'Text of message', 'ru', 'Текст сообщения'),
(1013, 'Send messages', 'ru', 'Отправить сообщения'),
(1014, 'Back to outbox', 'ru', 'Назад к рассылкам'),
(1015, 'Referrer', 'ru', 'Реферер'),
(1016, 'Actions', 'ru', 'Действия'),
(1017, 'View session', 'ru', 'Просмотр сессии'),
(1020, 'Share', 'ru', 'Поделиться'),
(1021, 'Subscription', 'ru', 'Рассылка'),
(1022, 'Back to list', 'ru', 'Вернуться к списку'),
(1023, 'Internal Server Error', 'en', 'Internal Server Error'),
(1024, 'Pending payment', 'en', 'Pending payment'),
(1025, 'Invoice', 'en', 'Invoice'),
(1026, 'An invoice for payment', 'en', 'An invoice for payment'),
(1027, 'Invoice date for payment', 'en', 'Invoice date for payment'),
(1029, 'Total', 'en', 'Total'),
(1030, 'Total Paid', 'en', 'Total Paid'),
(1031, 'Amount to be paid', 'en', 'Amount to be paid'),
(1032, 'Make payment', 'en', 'Make payment'),
(1033, 'Successfully paid', 'en', 'Successfully paid'),
(1034, 'Partially paid', 'en', 'Partially paid'),
(1035, 'The funds have been added to your account balance', 'en', 'The funds have been added to your account balance'),
(1036, 'Your cart is empty', 'en', 'Your cart is empty'),
(1037, 'View invoice', 'en', 'View invoice'),
(1038, 'Order payment', 'en', 'Order payment'),
(1039, 'Step', 'en', 'Step'),
(1040, 'Login to website', 'en', 'Login to website'),
(1041, 'Close window', 'en', 'Close window'),
(1042, 'Step', 'ru', 'Шаг'),
(1043, 'Login to website', 'ru', 'Авторизоваться на сайте'),
(1044, 'Internal Server Error', 'ru', 'Внутренняя ошибка сервера'),
(1045, 'Pending payment', 'ru', 'Ожидается оплата'),
(1046, 'Invoice', 'ru', 'Cчет'),
(1047, 'An invoice for payment', 'ru', 'Счет на оплату'),
(1048, 'Invoice date for payment', 'ru', 'Дата '),
(1049, 'Bill for payment for the user', 'ru', ''),
(1050, 'Total', 'ru', 'Итого'),
(1051, 'Total Paid', 'ru', 'Оплачено'),
(1052, 'Amount to be paid', 'ru', 'К оплате'),
(1053, 'Make payment', 'ru', 'Совершить платеж'),
(1054, 'Successfully paid', 'ru', 'Успешно оплачено'),
(1055, 'Partially paid', 'ru', 'Частично оплачено'),
(1056, 'Your cart is empty', 'ru', 'Ваша корзина пуста'),
(1057, 'View invoice', 'ru', 'Счет на оплату'),
(1058, 'Order payment', 'ru', 'Оплата заказа'),
(1059, 'Select payment method', 'en', 'Select payment method'),
(1060, 'Toggle navigation', 'en', 'Toggle navigation'),
(1061, 'Select an image to upload', 'en', 'Select an image to upload'),
(1097, 'Select payment method', 'ru', 'Выберите способ оплаты'),
(1064, 'Administrators not found', 'en', 'Administrators not found'),
(1099, 'Select an image to upload', 'ru', 'Выберите изображение для загрузки'),
(1098, 'Toggle navigation', 'ru', 'Переключить навигацию'),
(1081, 'Edit permission', 'en', 'Edit permission'),
(1082, 'Delete from admin', 'en', 'Delete from admin'),
(1083, 'Make admin', 'en', 'Make admin'),
(1084, 'Manage permission', 'en', 'Manage permission'),
(1085, 'Manage permission of', 'en', 'Manage permission of'),
(1086, 'Back to access', 'en', 'Back to access'),
(1087, 'Section', 'en', 'Section'),
(1088, 'Permission', 'en', 'Permission'),
(1089, 'No access', 'en', 'No access'),
(1090, 'Read-only', 'en', 'Read-only'),
(1091, 'Full access', 'en', 'Full access'),
(1092, 'Primary admin', 'en', 'Primary admin'),
(1093, 'Send as notification', 'en', 'Send as notification'),
(1094, 'Bulk message is sending now', 'en', 'Bulk message is sending now'),
(1095, 'Complete item description', 'en', 'Complete item description'),
(1096, 'Add new admin', 'en', 'Add new admin'),
(1100, 'Administrators not found', 'ru', 'Администраторы не найдены'),
(1101, 'Edit permission', 'ru', 'Редактировать права'),
(1102, 'Delete from admin', 'ru', 'Удалить из админов'),
(1103, 'Make admin', 'ru', 'Сделать админом'),
(1104, 'Manage permission', 'ru', 'Управление правами'),
(1105, 'Manage permission of', 'ru', 'Управление правами пользователя'),
(1106, 'Back to access', 'ru', 'Наза к доступу'),
(1107, 'Section', 'ru', 'Секция'),
(1108, 'Permission', 'ru', 'Права'),
(1109, 'No access', 'ru', 'Нет доступа'),
(1110, 'Read-only', 'ru', 'Только чтение'),
(1111, 'Full access', 'ru', 'Полный доступ'),
(1112, 'Primary admin', 'ru', 'Главный админ'),
(1113, 'Send as notification', 'ru', 'Отправить уведомление'),
(1114, 'Bulk message is sending now', 'ru', 'Массовое сообщение отправляется'),
(1115, 'Complete item description', 'ru', 'Полное описание товара'),
(1116, 'Add new admin', 'ru', 'Добавить администратора'),
(1117, 'Do not have an account?', 'en', 'Do not have an account?'),
(1118, 'Forgot password', 'en', 'Forgot password'),
(1119, 'Reset password', 'en', 'Reset password'),
(1120, 'To confirm this password, use', 'en', 'To confirm this password, use'),
(1121, 'this link', 'en', 'this link'),
(1122, 'To process restore, please check your email', 'en', 'To process restore, please check your email'),
(1123, 'Setup new password', 'en', 'Setup new password'),
(1124, 'New password activated!', 'en', 'New password activated!'),
(1125, 'Request for withdrawal of funds', 'en', 'Request for withdrawal of funds'),
(1126, 'Your withdrawal request will be processed within a few days of submitting', 'en', 'Your withdrawal request will be processed within a few days of submitting'),
(1127, 'Wallet', 'en', 'Wallet'),
(1128, 'Wallet ID', 'en', 'Wallet ID'),
(1129, 'Confirm request', 'en', 'Confirm request'),
(1130, 'Back to finances', 'en', 'Back to finances'),
(1131, 'Withdrawal confirmation', 'en', 'Withdrawal confirmation'),
(1132, 'Withdrawal request processed', 'en', 'Withdrawal request processed'),
(1133, 'Do not have an account?', 'ru', 'Еще не зарегистрированы?'),
(1134, 'Forgot password', 'ru', 'Забыли пароль'),
(1135, 'Reset password', 'ru', 'Сбросить пароль'),
(1136, 'To process restore, please check your email', 'ru', 'Чтобы восстановить пароль, пожалуйста, проверьте Ваш email'),
(1137, 'Setup new password', 'ru', 'Установить новый пароль'),
(1138, 'Request for withdrawal of funds', 'ru', 'Запрос на вывод средств'),
(1139, 'Your withdrawal request will be processed within a few days of submitting', 'ru', 'Ваш запрос на снятие средств будет обработан в течение нескольких дней после отправки'),
(1140, 'Wallet', 'ru', 'Кошелек'),
(1141, 'Wallet ID', 'ru', 'ID кошелька'),
(1142, 'Confirm request', 'ru', 'Подтвердить запрос'),
(1143, 'Back to finances', 'ru', 'Назад к финансам'),
(1144, 'Withdrawal confirmation', 'ru', 'Подтверждение вывода средств'),
(1145, 'Withdrawal request processed', 'ru', 'Запрос на снятие средств обработан'),
(1146, 'Place insert your phone into cardboard', 'en', 'Place insert your phone into cardboard'),
(1147, 'Virtual Reality', 'en', 'Virtual Reality'),
(1148, 'Latest Articles', 'en', 'Latest Articles'),
(1149, 'Popular goods', 'en', 'Popular goods'),
(1150, 'Place insert your phone into cardboard', 'ru', 'Вставьте Ваеш телефон в Cardboard'),
(1151, 'Virtual Reality', 'ru', 'Виртуальная реальность'),
(1152, 'Latest Articles', 'ru', 'Последние статьи'),
(1153, 'Popular goods', 'ru', 'Популярные товары'),
(1154, 'To confirm your email, please enter or click on the following code', 'en', 'To confirm your email, please enter or click on the following code'),
(1155, 'Account confirmation', 'en', 'Account confirmation'),
(1156, 'Anonim', 'en', 'Anonim'),
(1157, 'Panoramas', 'en', 'Panoramas'),
(1158, 'Project Name', 'en', 'Project Name'),
(1159, 'Levels', 'en', 'Levels'),
(1160, 'Scenes', 'en', 'Scenes'),
(1161, 'Objects', 'en', 'Objects'),
(1162, 'Portals', 'en', 'Portals'),
(1163, 'Links', 'en', 'Links'),
(1164, 'Projects not found', 'en', 'Projects not found'),
(1165, 'Add new project', 'en', 'Add new project'),
(1166, 'Level Name', 'en', 'Level Name'),
(1167, 'Levels not found', 'en', 'Levels not found'),
(1168, 'Show project scheme', 'en', 'Show project scheme'),
(1169, 'Add new level', 'en', 'Add new level'),
(1170, 'Project properties', 'en', 'Project properties'),
(1171, 'Scene Name', 'en', 'Scene Name'),
(1172, 'Scenes not found', 'en', 'Scenes not found'),
(1173, 'Show level plan', 'en', 'Show level plan'),
(1174, 'Add new scene', 'en', 'Add new scene'),
(1175, 'Position', 'en', 'Position'),
(1176, 'Rotation', 'en', 'Rotation'),
(1177, 'Latitude', 'en', 'Latitude'),
(1178, 'Longitude', 'en', 'Longitude'),
(1179, 'Floor position', 'en', 'Floor position'),
(1180, 'Floor radius', 'en', 'Floor radius'),
(1181, 'Logo size', 'en', 'Logo size'),
(1182, 'level properties', 'en', 'level properties'),
(1183, 'VR Projects', 'en', 'VR Projects'),
(1184, 'Project details', 'en', 'Project details'),
(1185, 'Delete project', 'en', 'Delete project'),
(1186, 'Are you sure you want to delete a project with all levels, scenes and custom objects?', 'en', 'Are you sure you want to delete a project with all levels, scenes and custom objects?'),
(1187, 'Level details', 'en', 'Level details'),
(1188, 'Delete level', 'en', 'Delete level'),
(1189, 'Are you sure you want to delete a level with all scenes and custom objects?', 'en', 'Are you sure you want to delete a level with all scenes and custom objects?'),
(1190, 'Upload a panoramic image', 'en', 'Upload a panoramic image'),
(1191, 'Or upload 6 images of cubemap', 'en', 'Or upload 6 images of cubemap'),
(1192, 'Cube Rotation', 'en', 'Cube Rotation'),
(1193, 'The resampling algorithm to use when generating the cubemap.', 'en', 'The resampling algorithm to use when generating the cubemap.'),
(1194, 'Lanczos (best but slower)', 'en', 'Lanczos (best but slower)'),
(1195, 'Cubic (sharper details)', 'en', 'Cubic (sharper details)'),
(1196, 'Linear (softer details)', 'en', 'Linear (softer details)'),
(1197, 'Output format', 'en', 'Output format'),
(1198, 'Generating', 'en', 'Generating'),
(1199, 'Scene position', 'en', 'Scene position'),
(1200, 'Scene rotation', 'en', 'Scene rotation'),
(1201, 'Scene latitude', 'en', 'Scene latitude'),
(1202, 'Scene longitude', 'en', 'Scene longitude'),
(1203, 'Drag-n-drop points to locate scenes on level.', 'en', 'Drag-n-drop points to locate scenes on level.'),
(1204, 'Double click to open scene preview', 'en', 'Double click to open scene preview'),
(1205, 'Image rotation', 'en', 'Image rotation'),
(1206, 'Image scale', 'en', 'Image scale'),
(1207, 'Apply chages', 'en', 'Apply chages'),
(1208, 'Save chages', 'en', 'Save chages'),
(1209, 'View scene', 'en', 'View scene'),
(1210, 'Delete scene', 'en', 'Delete scene'),
(1211, 'Are you sure you want to delete a scene with all custom objects?', 'en', 'Are you sure you want to delete a scene with all custom objects?'),
(1212, 'Build navigation', 'en', 'Build navigation'),
(1213, 'Are you sure you want to build navigation to neightbors scenes?', 'en', 'Are you sure you want to build navigation to neightbors scenes?'),
(1214, 'Show editor', 'en', 'Show editor'),
(1215, 'Default camera position', 'en', 'Default camera position'),
(1216, 'Default camera rotation', 'en', 'Default camera rotation'),
(1217, 'Height', 'en', 'Height'),
(1218, 'Apply changes', 'en', 'Apply changes'),
(1219, 'Load default setting', 'en', 'Load default setting'),
(1220, 'Save scene settings', 'en', 'Save scene settings'),
(1221, 'Add new object', 'en', 'Add new object'),
(1222, 'Add new navigation', 'en', 'Add new navigation'),
(1223, 'Add new link', 'en', 'Add new link'),
(1224, 'Reset scene objects', 'en', 'Reset scene objects'),
(1225, 'Panoramas not found', 'en', 'Panoramas not found'),
(1226, 'Show map', 'en', 'Show map'),
(1227, 'Object properties', 'en', 'Object properties'),
(1228, 'Scale', 'en', 'Scale'),
(1229, 'Point properties', 'en', 'Point properties'),
(1230, 'Target', 'en', 'Target'),
(1231, 'Link properties', 'en', 'Link properties'),
(1232, 'Delete point', 'en', 'Delete point'),
(1233, 'Delete object', 'en', 'Delete object'),
(1234, 'To confirm your email, please enter or click on the following code', 'ru', 'Для подтверждения Email, пожалуйтса, используйте код'),
(1235, 'Anonim', 'ru', 'Аноним'),
(1236, 'Panoramas', 'ru', 'Панорамы'),
(1237, 'Project Name', 'ru', 'Название проекта'),
(1238, 'Levels', 'ru', 'Уровни'),
(1239, 'Scenes', 'ru', 'Сцены'),
(1240, 'Objects', 'ru', 'Обьекты'),
(1241, 'Portals', 'ru', 'Порталы'),
(1242, 'Links', 'ru', 'Ссылки'),
(1243, 'Projects not found', 'ru', 'Проекты не найдены'),
(1244, 'Add new project', 'ru', 'Добавить новый проект'),
(1245, 'Level Name', 'ru', 'Название уровня'),
(1246, 'Levels not found', 'ru', 'Уровни не найдены'),
(1247, 'Show project scheme', 'ru', 'Показать схему проекта'),
(1248, 'Add new level', 'ru', 'Добавить новый уровень'),
(1249, 'Project properties', 'ru', 'Свойства проекта'),
(1250, 'Scene Name', 'ru', 'Название сцены'),
(1251, 'Scenes not found', 'ru', 'Сцены не найдены'),
(1252, 'Show level plan', 'ru', 'Показать карту уровня'),
(1253, 'Add new scene', 'ru', 'Добавить новую сцену'),
(1254, 'Position', 'ru', 'Позиция'),
(1255, 'Rotation', 'ru', 'Вращение'),
(1256, 'Latitude', 'ru', 'Широта'),
(1257, 'Longitude', 'ru', 'Долгота'),
(1258, 'Floor position', 'ru', 'Позиция пола'),
(1259, 'Floor radius', 'ru', 'Радиус пола'),
(1260, 'Logo size', 'ru', 'Размер лого'),
(1261, 'level properties', 'ru', 'Свойста уровня'),
(1262, 'VR Projects', 'ru', 'VR Проекты'),
(1263, 'Project details', 'ru', 'Детали уровня'),
(1264, 'Delete project', 'ru', 'Удалить проект'),
(1265, 'Are you sure you want to delete a project with all levels, scenes and custom objects?', 'ru', 'Вы уверены, что хотите удалить проект со всеми уровнями, сценами и объектами?'),
(1266, 'Level details', 'ru', 'Детали уровня'),
(1267, 'Delete level', 'ru', 'Удалить уровень'),
(1268, 'Are you sure you want to delete a level with all scenes and custom objects?', 'ru', 'Вы уверены, что хотите удалить уровень со всеми сценами и объектами?'),
(1269, 'Upload a panoramic image', 'ru', 'Загрузить панорамное изображение'),
(1270, 'Or upload 6 images of cubemap', 'ru', 'Или загрузите 6 изображений кубической карты'),
(1271, 'Cube Rotation', 'ru', 'Вращение куба'),
(1272, 'The resampling algorithm to use when generating the cubemap.', 'ru', 'Алгоритм передискретизации, используемый при создании кубической карты.'),
(1273, 'Lanczos (best but slower)', 'ru', 'Lanczos (лучше, но медленно)'),
(1274, 'Cubic (sharper details)', 'ru', 'Cubic (жестче)'),
(1275, 'Linear (softer details)', 'ru', 'Linear (мягче)'),
(1276, 'Output format', 'ru', 'Выходной формат'),
(1277, 'Generating', 'ru', 'Генерирование'),
(1278, 'Scene position', 'ru', 'Позиция сцены'),
(1279, 'Scene rotation', 'ru', 'Вращение сцены'),
(1280, 'Scene latitude', 'ru', 'Широта сцены'),
(1281, 'Scene longitude', 'ru', 'Долгота сцены'),
(1282, 'Drag-n-drop points to locate scenes on level.', 'ru', 'Перетаскивайте точки, чтобы расположить сцены на уровне.'),
(1283, 'Double click to open scene preview', 'ru', 'Двойной клик для открытия превью сцены'),
(1284, 'Image rotation', 'ru', 'Вращение изображения'),
(1285, 'Image scale', 'ru', 'Масштаб изображения'),
(1286, 'Apply chages', 'ru', 'Применить изменения'),
(1287, 'Save chages', 'ru', 'Сохранить изменения'),
(1288, 'View scene', 'ru', 'Смотреть сцену'),
(1289, 'Delete scene', 'ru', 'Удалить сцену'),
(1290, 'Are you sure you want to delete a scene with all custom objects?', 'ru', 'Вы уверены, что хотите удалить сцену со всеми объектами?'),
(1291, 'Build navigation', 'ru', 'Создать навигацию'),
(1292, 'Are you sure you want to build navigation to neightbors scenes?', 'ru', 'Вы уверены, что хотите построить навигацию к соседним сценам?'),
(1293, 'Show editor', 'ru', 'Показать редактор'),
(1294, 'Default camera position', 'ru', 'Позиция камеры по-умолчанию'),
(1295, 'Default camera rotation', 'ru', 'Вращение камеры по-умолчанию'),
(1296, 'Height', 'ru', 'Высота'),
(1297, 'Apply changes', 'ru', 'Применить изменения'),
(1298, 'Load default setting', 'ru', 'Стандартные настройки'),
(1299, 'Save scene settings', 'ru', 'Сохранить настройки'),
(1300, 'Add new object', 'ru', 'Добавить обьект'),
(1301, 'Add new navigation', 'ru', 'Добавить навигацию'),
(1302, 'Add new link', 'ru', 'Добавить ссылку'),
(1303, 'Reset scene objects', 'ru', 'Сбросить обьекты'),
(1304, 'Panoramas not found', 'ru', 'Панорамы не найдены'),
(1305, 'Show map', 'ru', 'Показать карту'),
(1306, 'Object properties', 'ru', 'Свойста обьекта'),
(1307, 'Scale', 'ru', 'Масштаб'),
(1308, 'Point properties', 'ru', 'Свойста  точки'),
(1309, 'Target', 'ru', 'Цель'),
(1310, 'Link properties', 'ru', 'Свойства ссылки'),
(1311, 'Delete point', 'ru', 'Удалить точку'),
(1312, 'Delete object', 'ru', 'Удалить обьект');


INSERT INTO `nodes_catalog` (`caption`, `description`, `text`, `url`, `img`, `visible`, `lang`, `order`, `date`, `public_date`) VALUES
('Политика конфиденциальности', '', '<ul><li><a href=\"#1\">Сбор информации</a></li><li><a href=\"#2\">Использование информации</a></li><li><a href=\"#3\">Защита информации</a></li><li><a href=\"#4\">Использование cookie</a></li><li><a href=\"#5\">Раскрытие информации</a></li><li><a href=\"#6\">Сторонние ссылки</a></li><li><a href=\"#7\">CalOPPA</a></li><li><a href=\"#8\">COPPA</a></li><li><a href=\"#9\">Как с нами связаться</a></li></ul><p>&nbsp;</p><p>Данные положения касаются персональных данных клиентов (далее &ndash; &laquo;Персональные данные&raquo;, &laquo;Личная информация&raquo;, &laquo;Личные данные&raquo;), которые могут быть идентифицированы каким-либо образом, и которые посещают веб-сайт (далее - &ldquo;Сайт&rdquo;) и пользуются его услугами (далее - &ldquo;Сервисы&rdquo;). <br /> Поправки к настоящей Политике конфиденциальности будут размещены на Сайте и/или в Сервисах и будут являться действительными сразу после публикации. Ваше дальнейшее использование Сервисов после внесения любых поправок в Политике конфиденциальности означает Ваше принятие данных изменений. <br /> Регистрируясь на Сайте, Вы подтверждаете принятие Вами решения о предоставлении своих персональных данных и даете согласие на их обработку своей волей и в своем интересе, за исключением случаев, предусмотренных законодательством.</p><p><br /> <a name=\"1\"></a><strong>Какую личную информацию мы собираем от людей, которые посещают наш Сайт?</strong></p><p>Сайт собирает только личную информацию, которую Вы предоставляете добровольно при посещении или регистрации на Сайте. Понятие \"личная информация\" включает информацию, которая определяет Вас как конкретное лицо, например, Ваше имя или адрес электронной почты. Тогда как просматривать содержание Сайта можно без прохождения процедуры регистрации, Вам потребуется зарегистрироваться, чтобы воспользоваться некоторыми функциями, например, оставить свой комментарий к статье.</p><p><br /> <strong>Когда мы собираем информацию?</strong></p><p>Мы собираем информацию от вас, когда вы регистрируетесь на нашем сайте, оформиляете заказ, подписыветесь на рассылку или иным образом вводите информацию на нашем сайте.</p><p><br /> <a name=\"2\"></a><strong>Как мы используем вашу информацию?</strong></p><p>Мы можем использовать информацию, которую мы получаем от вас, следующими способами:</p><ul><li>Для того, чтобы улучшить качество сервиса.</li><li>Для администрирования контента и других функций сайта.</li><li>Чтобы быстро обрабатывать ваши транзакции.</li><li>Для того, чтобы составить рейтинги и обзоры услуг или продуктов.</li><li>Для дальнейшего взаимодействия после переписки (чат, электронную почту или телефон).</li></ul><p><br /> <a name=\"3\"></a><strong>Как мы защищаем вашу информацию?</strong></p><p>Мы будем стремиться предотвратить несанкционированный доступ к Вашей личной информации, однако, никакая передача данных через интернет, мобильное устройство или через беспроводное устройство не могут гарантировать 100%-ную безопасность. Мы будем продолжать укреплять систему безопасности по мере доступности новых технологий и методов.</p><p><br /> <a name=\"4\"></a><strong>Используем ли мы \'cookies\'?</strong></p><p>Да, мы используем &ldquo;куки&rdquo; (cookies) для отслеживания информации о пользователях. Cookies являются небольшими по объему данными, которые передаются веб-сервером через Ваш веб-браузер и хранятся на жестком диске Вашего компьютера. Мы используем cookies для отслеживания вариантов страниц, которые видел посетитель, для подсчета нажатий сделанных посетителем на том или ином варианте страницы, для мониторинга трафика и для измерения популярности сервисных настроек. Мы будем использовать данную информацию, чтобы предоставить Вам релевантные данные и услуги. Данная информация также позволяет нам убедиться, что посетители видят именно ту целевую страницу, которую они ожидают увидеть, в том случае, если они возвращаются через тот же URL-адрес, и это позволяет нам сказать, сколько людей нажимает на Ваши целевые страницы.</p><p><br /> <a name=\"5\"></a><strong>Раскрытие информации</strong></p><p>Мы будем раскрывать Вашу информацию третьим лицам только в соответствии с Вашими инструкциями или в случае необходимости для того, чтобы предоставить Вам определенный сервис, или по другим причинам в соответствии с действующим законодательством Российской Федерации. Мы не осуществляем, не продаем, не распространяем или раскрываем Вашу личную информацию без предварительного получения Вашего на то разрешения за исключением случаев, предусмотренных международным или федеральным законодательством.</p><p><br /> <a name=\"6\"></a><strong>Сторонние ссылки</strong></p><p>Сайт может содержать ссылки на другие сайты, и мы не несем ответственности за политику конфиденциальности или содержание данных сайтов. Мы рекомендуем Вам ознакомиться с политикой конфиденциальности связанных сайтов. Их политика конфиденциальности и деятельность отличаются от наших Политики конфиденциальности и деятельности.</p><p><br /> <a name=\"7\"></a><strong>CalOPPA (California Online Privacy Protection Act)</strong></p><p>Для того, чтобы отвечать требованиям CalOPPA, мы согласны на следующее:</p><ul><li>Пользователи могут посетить наш сайт анонимно.</li><li>Ссылка на эту страницу размещена на главной старницы сайта.</li><li>Вы будете уведомлены о любых изменениях политики конфиденциальности.</li><li>Пользователи могут изменить вашу личную информацию, войдя в свой аккаунт.</li></ul><p><br /> <a name=\"8\"></a><strong>COPPA (Children Online Privacy Protection Act)</strong></p><p>Мы стремимся защищать конфиденциальность информации о детях. Наш сайт предназначен для широкой общественности, и мы не получаем преднамеренно личную информацию от детей моложе 13 лет. Когда и если на нашем сайте запрашивается информация о возрасте, и пользователи указывают возраст до 13 лет, сайт автоматически блокирует получение личной информации от таких пользователей и блокирует их регистрацию в качестве пользователей сайта.</p><p><br /> <strong>CAN SPAM Act</strong></p><p>Для того, чтобы отвечать требованиям CAN SPAM (Для регулирования торговли между штатами путем введения ограничений и санкций на передачу нежелательной коммерческой электронной почты через Интернет., мы согласны на следующее: <br /> Если в любое время вы хотите отказаться от получения электронных сообщений, вы можете из личного кабинета.</p><p><br /> <a name=\"9\"></a><strong>Как с нами связаться</strong></p><p>Если есть какие-либо вопросы относительно политики конфиденциальности, вы можете связаться с нами, используя информацию на сайте.</p>', 'privacy_policy', '', 0, 'ru', 0, ".date("U").", ".date("U")."),
('Пользовательское соглашение', '', '<ul><li><a href=\"#1\">Общие условия</a></li><li><a href=\"#2\">Обязательства Пользователя</a></li><li><a href=\"#3\">Прочие условия</a></li></ul><p>&nbsp;</p><p>Настоящее Соглашение определяет условия использования Пользователями материалов и сервисов данного вебсайта, Пользователи &mdash; физические лица (в том числе представители юридических лиц), обладающие возможностью визуального ознакомления с размещенной на сайте информацией и размещением собственной.</p><p><br /> <a name=\"1\"></a><strong>Общие условия</strong></p><p>Использование материалов и сервисов Сайта регулируется нормами федерального законодательства и международными нормами. <br /> Настоящее Соглашение является публичной офертой. Получая доступ к материалам Сайта, Пользователь считается присоединившимся к настоящему Соглашению. <br /> При использовании ресурсов Сайта требующих предоставления персональных данных необходимо выполнить регистрацию Пользователя на Сайте. <br /> Администрация Сайта вправе в любое время в одностороннем порядке изменять условия настоящего Соглашения. Такие изменения вступают в силу по истечении 3 (Трех) дней с момента размещения новой версии Соглашения на сайте. При несогласии Пользователя с внесенными изменениями он обязан отказаться от доступа к Сайту, прекратить использование материалов и сервисов Сайта. <br /> Администрация Сайта может устанавливать для Пользователей рассылку информации, связанной с заказами, графиками работы офисов, проводимыми акциями и иной необходимой информацией. Рассылка осуществляется посредством сообщений sms, push или электронной почты. Необходимая контактная информация предоставляется Пользователем при регистрации.</p><p><br /> <a name=\"2\"></a><strong>Обязательства Пользователя</strong></p><p>Пользователь соглашается не предпринимать действий, которые могут рассматриваться как нарушающие российское законодательство или нормы международного права, в том числе в сфере интеллектуальной собственности, авторских и/или смежных правах, а также любых действий, которые приводят или могут привести к нарушению нормальной работы Сайта и сервисов Сайта. <br /> Использование материалов Сайта без согласия правообладателей не допускается. Для правомерного использования материалов Сайта необходимо заключение лицензионных договоров (получение лицензий) от Правообладателей. <br /> При цитировании материалов Сайта, включая охраняемые авторские произведения, ссылка на Сайт обязательна. <br /> Комментарии и иные записи Пользователя на Сайте не должны вступать в противоречие с требованиями законодательства Российской Федерации и общепринятых норм морали и нравственности. <br /> Пользователь предупрежден о том, что Администрация Сайта не несет ответственности за посещение и использование им внешних ресурсов, ссылки на которые могут содержаться на сайте. <br /> Пользователь согласен с тем, что Администрация Сайта не несет ответственности и не имеет прямых или косвенных обязательств перед Пользователем в связи с любыми возможными или возникшими потерями или убытками, связанными с любым содержанием Сайта, регистрацией авторских прав и сведениями о такой регистрации, товарами или услугами, доступными на или полученными через внешние сайты или ресурсы либо иные контакты Пользователя, в которые он вступил, используя размещенную на Сайте информацию или ссылки на внешние ресурсы. <br /> Пользователь принимает положение о том, что все материалы и сервисы Сайта или любая их часть могут сопровождаться рекламой. Пользователь согласен с тем, что Администрация Сайта не несет какой-либо ответственности и не имеет каких-либо обязательств в связи с такой рекламой.</p><p><br /> <a name=\"3\"></a><strong>Прочие условия</strong></p><p>Все возможные споры, вытекающие из настоящего Соглашения или связанные с ним, подлежат разрешению в соответствии с действующим законодательством Российской Федерации. <br /> Ничто в Соглашении не может пониматься как установление между Пользователем и Администрации Сайта агентских отношений, отношений товарищества, отношений по совместной деятельности, отношений личного найма, либо каких-то иных отношений, прямо не предусмотренных Соглашением. <br /> Признание судом какого-либо положения Соглашения недействительным или не подлежащим принудительному исполнению не влечет недействительности иных положений Соглашения. <br /> Бездействие со стороны Администрации Сайта в случае нарушения кем-либо из Пользователей положений Соглашения не лишает Администрацию Сайта права предпринять позднее соответствующие действия в защиту своих интересов и защиту авторских прав на охраняемые в соответствии с законодательством материалы Сайта. <br /> Пользователь подтверждает, что ознакомлен со всеми пунктами настоящего Соглашения и безусловно принимает их.</p>', 'terms_and_conditions', '', 0, 'ru', 0, ".date("U").", ".date("U")."),
('Privacy Policy', '', '<ul><li><a href=\"#1\">Information Collection</a></li><li><a href=\"#2\">Information Usage</a></li><li><a href=\"#3\">Information Protection</a></li><li><a href=\"#4\">Cookie Usage</a></li><li><a href=\"#5\">3rd Party Disclosure</a></li><li><a href=\"#6\">3rd Party Links</a></li><li><a href=\"#7\">CalOPPA</a></li><li><a href=\"#8\">COPPA</a></li><li><a href=\"#9\">Contact Information</a></li></ul><p>&nbsp;</p><p>This privacy policy has been compiled to better serve those who are concerned with how their \'Personally Identifiable Information\' (PII) is being used online. PII, as described in US privacy law and information security, is information that can be used on its own or with other information to identify, contact, or locate a single person, or to identify an individual in context. Please read our privacy policy carefully to get a clear understanding of how we collect, use, protect or otherwise handle your Personally Identifiable Information in accordance with our website.</p><p><br /> <a name=\"1\"></a><strong>What personal information do we collect from the people that visit our website?</strong></p><p>When ordering or registering on our site, as appropriate, you may be asked to enter your name, email address, mailing address or other details to help you with your experience.</p><p><br /> <strong>When do we collect information?</strong></p><p>We collect information from you when you register on our site, place an order, subscribe to a newsletter, respond to a survey, fill out a form, Use Live Chat or enter information on our site.</p><p><br /> <a name=\"2\"></a><strong>How do we use your information?</strong></p><p>We may use the information we collect from you when you register, make a purchase, sign up for our newsletter, respond to a survey or marketing communication, surf the website, or use certain other site features in the following ways:</p><ul><li>To improve our website in order to better serve you.</li><li>To allow us to better service you in responding to your customer service requests</li><li>To administer a contest, promotion, survey or other site feature.</li><li>To quickly process your transactions.</li><li>To ask for ratings and reviews of services or products.</li><li>To follow up with them after correspondence (live chat, email or phone inquiries).</li></ul><p><br /> <a name=\"3\"></a><strong>How do we protect your information?</strong></p><p>Our site is scanned on a regular basis for security holes and known vulnerabilities in order to make your visit to our site as safe as possible. We will never ask for personal or sensitive information such as names, email addresses and credit card numbers from unauthorized users.</p><p><br /> <a name=\"4\"></a><strong>Do we use \'cookies\'?</strong></p><p>Yes. Cookies are small files that a site or its service provider transfers to your computer\'s hard drive through your Web browser (if you allow) that enables the site\'s or service provider\'s systems to recognize your browser and capture and remember certain information. For instance, we use cookies to help us remember and process the items in your shopping cart. They are also used to help us understand your preferences based on previous or current site activity, which enables us to provide you with improved services. We also use cookies to help us compile aggregate data about site traffic and site interaction so that we can offer better site experiences and tools in the future. We use cookies to:</p><ul><li>Help remember and process the items in the shopping cart.</li><li>Understand and save user\'s preferences for future visits.</li></ul><p>You can choose to have your computer warn you each time a cookie is being sent, or you can choose to turn off all cookies. You do this through your browser settings. Since browser is a little different, look at your browser\'s Help Menu to learn the correct way to modify your cookies.</p><p><br /> <strong>If users disable cookies in their browser</strong></p><p>If you turn cookies off, some features will be disabled. Some of the features that make your site experience more efficient and may not function properly. <br /> However, you will still be able to place orders .</p><p><br /> <a name=\"5\"></a><strong>Third-party disclosure</strong></p><p>We do not sell, trade, or otherwise transfer to outside parties your Personally Identifiable Information unless we provide users with advance notice. This does not include website hosting partners and other parties who assist us in operating our website, conducting our business, or serving our users, so long as those parties agree to keep this information confidential. We may also release information when it\'s release is appropriate to comply with the law, enforce our site policies, or protect ours or others\' rights, property or safety. <br /> However, non-personally identifiable visitor information may be provided to other parties for marketing, advertising, or other uses.</p><p><br /> <a name=\"6\"></a><strong>Third-party links</strong></p><p>Occasionally, at our discretion, we may include or offer third-party products or services on our website. These third-party sites have separate and independent privacy policies. We therefore have no responsibility or liability for the content and activities of these linked sites. Nonetheless, we seek to protect the integrity of our site and welcome any feedback about these sites.</p><p><br /> <a name=\"7\"></a><strong>California Online Privacy Protection Act</strong></p><p>CalOPPA is the first state law in the nation to require commercial websites and online services to post a privacy policy. The law\'s reach stretches well beyond California to require any person or company in the United States (and conceivably the world) that operates websites collecting Personally Identifiable Information from California consumers to post a conspicuous privacy policy on its website stating exactly the information being collected and those individuals or companies with whom it is being shared. <br /> According to CalOPPA, we agree to the following:</p><ul><li>Users can visit our site anonymously.</li><li>Once this privacy policy is created, we will add a link to it on our home page or as a minimum, on the first significant page after entering our website.</li><li>Our Privacy Policy link includes the word \'Privacy\' and can easily be found on the page specified above.</li><li>You will be notified of any Privacy Policy changes on our Privacy Policy Page.</li><li>Users can change your personal information by logging in to your account.</li></ul><p><br /> <strong>How does our site handle Do Not Track signals?</strong></p><p>We honor Do Not Track signals and Do Not Track, plant cookies, or use advertising when a Do Not Track (DNT) browser mechanism is in place.</p><p><br /> <strong>Does our site allow third-party behavioral tracking?</strong></p><p>It\'s also important to note that we allow third-party behavioral tracking</p><p><br /> <a name=\"8\"></a><strong>COPPA (Children Online Privacy Protection Act)</strong></p><p>When it comes to the collection of personal information from children under the age of 13 years old, the Children\'s Online Privacy Protection Act (COPPA) puts parents in control. The Federal Trade Commission, United States\' consumer protection agency, enforces the COPPA Rule, which spells out what operators of websites and online services must do to protect children\'s privacy and safety online. <br /> We do not specifically market to children under the age of 13 years old.</p><p><br /> <strong>CAN SPAM Act</strong></p><p>The CAN-SPAM Act is a law that sets the rules for commercial email, establishes requirements for commercial messages, gives recipients the right to have emails stopped from being sent to them, and spells out tough penalties for violations. <br /> To be in accordance with CANSPAM, we agree to the following: <br /> If at any time you would like to unsubscribe from receiving future emails, you can email us at and we will promptly remove you from ALL correspondence.</p><p><br /> <a name=\"9\"></a><strong>Contacting Us</strong></p><p>If there are any questions regarding this privacy policy, you may contact us using the information below.</p>', 'privacy_policy', '', 0, 'en', 0, ".date("U").", ".date("U")."),
('Terms and Conditions', '', '<ul><li><a href=\"#1\">Intellectual Property Rights</a></li><li><a href=\"#2\">Restrictions</a></li><li><a href=\"#3\">Your Content</a></li><li><a href=\"#4\">No warranties</a></li><li><a href=\"#5\">Limitation of liability</a></li><li><a href=\"#6\">Indemnification</a></li><li><a href=\"#7\">Severability</a></li><li><a href=\"#8\">Variation of Terms</a></li><li><a href=\"#9\">Assignment</a></li><li><a href=\"#10\">Entire Agreement</a></li><li><a href=\"#11\">Governing Law &amp; Jurisdiction</a></li></ul><p>&nbsp;</p><p>These Website Standard Terms and Conditions written on this webpage shall manage your use of this website. These Terms will be applied fully and affect to your use of this Website. By using this Website, you agreed to accept all terms and conditions written in here. You must not use this Website if you disagree with any of these Website Standard Terms and Conditions. <br /> Minors or people below 18 years old are not allowed to use this Website.</p><p><br /> <a name=\"1\"></a><strong>Intellectual Property Rights</strong></p><p>Other than the content you own, under these Terms, and/or its licensors own all the intellectual property rights and materials contained in this Website. <br /> You are granted limited license only for purposes of viewing the material contained on this Website.</p><p><br /> <a name=\"2\"></a><strong>Restrictions</strong></p><p>You are specifically restricted from all of the following:</p><ul><li>Publishing any Website material in any other media.</li><li>Selling, sublicensing and/or otherwise commercializing any Website material.</li><li>Publicly performing and/or showing any Website material.</li><li>Using this Website in any way that is or may be damaging to this Website.</li><li>Using this Website in any way that impacts user access to this Website.</li><li>Using this Website contrary to applicable laws and regulations, or in any way may cause harm to the Website, or to any person or business entity.</li><li>Engaging in any data mining, data harvesting, data extracting or any other similar activity in relation to this Website.</li><li>Using this Website to engage in any advertising or marketing.</li></ul><p>Certain areas of this Website are restricted from being access by you and may further restrict access by you to any areas of this Website, at any time, in absolute discretion. Any user ID and password you may have for this Website are confidential and you must maintain confidentiality as well.</p><p><br /> <a name=\"3\"></a><strong>Your Content</strong></p><p>In these Website Standard Terms and Conditions, &ldquo;Your Content&rdquo; shall mean any audio, video text, images or other material you choose to display on this Website. By displaying Your Content, you grant a non-exclusive, worldwide irrevocable, sub licensable license to use, reproduce, adapt, publish, translate and distribute it in any and all media. <br /> Your Content must be your own and must not be invading any third-party&rsquo;s rights. reserves the right to remove any of Your Content from this Website at any time without notice.</p><p><br /> <a name=\"4\"></a><strong>No warranties</strong></p><p>This Website is provided &ldquo;as is,&rdquo; with all faults, and express no representations or warranties, of any kind related to this Website or the materials contained on this Website. Also, nothing contained on this Website shall be interpreted as advising you.</p><p><br /> <a name=\"5\"></a><strong>Limitation of liability</strong></p><p>In no event shall , nor any of its officers, directors and employees, shall be held liable for anything arising out of or in any way connected with your use of this Website whether such liability is under contract. , including its officers, directors and employees shall not be held liable for any indirect, consequential or special liability arising out of or in any way related to your use of this Website.</p><p><br /> <a name=\"6\"></a><strong>Indemnification</strong></p><p>You hereby indemnify to the fullest extent from and against any and/or all liabilities, costs, demands, causes of action, damages and expenses arising in any way related to your breach of any of the provisions of these Terms.</p><p><br /> <a name=\"7\"></a><strong>Severability</strong></p><p>If any provision of these Terms is found to be invalid under any applicable law, such provisions shall be deleted without affecting the remaining provisions herein.</p><p><br /> <a name=\"8\"></a><strong>Variation of Terms</strong></p><p>is permitted to revise these Terms at any time as it sees fit, and by using this Website you are expected to review these Terms on a regular basis.</p><p><br /> <a name=\"9\"></a><strong>Assignment</strong></p><p>The is allowed to assign, transfer, and subcontract its rights and/or obligations under these Terms without any notification. However, you are not allowed to assign, transfer, or subcontract any of your rights and/or obligations under these Terms.</p><p><br /> <a name=\"10\"></a><strong>Entire Agreement</strong></p><p>These Terms constitute the entire agreement between and you in relation to your use of this Website, and supersede all prior agreements and understandings.</p><p><br /> <a name=\"11\"></a><strong>Governing Law &amp; Jurisdiction</strong></p><p>These Terms will be governed by and interpreted in accordance with the laws of the State of , and you submit to the non-exclusive jurisdiction of the state and federal courts located in for the resolution of any disputes.</p>', 'terms_and_conditions', '', 0, 'en', 0, ".date("U").", ".date("U").");

";      $arr = explode(";
", $query);
    $flag = 0;
        foreach($arr as $a){
            $a = trim($a);
            if(!empty($a)){
                @mysqli_query($link, "SET NAMES utf8");
                mysqli_query($link, $a) or die(mysqli_error($link));
            }
        }
        $output .= 'Generation config.php.. ';
        $fname = "engine/nodes/config.php";
        $fname = fopen($fname, 'w') or die("Error. Can't open file engine/nodes/config.php");
        $source = '/**'."\n".'
* Framework config file'."\n".'
*/'."\n".'
$_SERVER["config"] = array('."\n".'
    "name" => "'. mysqli_real_escape_string($link,$_POST["name"]).'",'."\n".'
    "sql_server" => "'. mysqli_real_escape_string($link,$_POST["mysql_server"]).'",'."\n".'
    "sql_login" => "'. mysqli_real_escape_string($link,$_POST["mysql_login"]).'",'."\n".'
    "sql_pass" => "'. mysqli_real_escape_string($link,$_POST["mysql_pass"]).'",'."\n".'
    "sql_db" => "'. mysqli_real_escape_string($link,$_POST["mysql_db"]).'",'."\n".'
    "crypt_salt" => "'.$crypt_salt.'"'."\n".'
);';    
        if(intval($_POST["encoding"])){
            $encode = base64_encode($source);
            $code = '<?php eval(base64_decode("'.$encode.'"));';
        }else{
            $code = '<?php '."\n".$source;
        }
        fwrite($fname, $code);
        fclose($fname);
        $output .= 'Ok.<br/>Generation cron.php.. ';
        $name = "cron.php";
        $fname = fopen($name, 'w') or die("Error. Can't open file cron.php");
        $code = '#!/usr/bin/php'."\n".
'<?php'."\n".
'/**'."\n".
'* Executable crontab file.'."\n".
'* Should be configured on autoexec every 1 minute.'."\n".
'*'."\n".
'* @name    Nodes Studio    @version 3.0.0.1'."\n".
'* @author  Aleksandr Vorkunov  <developing@nodes-tech.ru>'."\n".
'* @license http://www.apache.org/licenses/LICENSE-2.0'."\n".
'*/'."\n".
'if(isset($argv[1])) $_SERVER["HTTP_HOST"] = $argv[1];'."\n".
'else $_SERVER["HTTP_HOST"] = "'.$_SERVER["HTTP_HOST"].'";'."\n".
'$_SERVER["DOCUMENT_ROOT"] = "'.$_SERVER["DOCUMENT_ROOT"].'";'."\n".
'$_SERVER["REQUEST_URI"] = "/cron.php";'."\n".
'ini_set(\'include_path\', $_SERVER["DOCUMENT_ROOT"]);'."\n".
'require_once("engine/nodes/autoload.php");';
        fwrite($fname, $code);
        fclose($fname);
        chmod($name, 0705);
        $output .= 'Ok.<br/>';
        if(!empty($_POST["temp"])){
            $output .= 'Replacing temp data.. ';
            $query = "
INSERT INTO `nodes_catalog` (`id`, `caption`, `text`, `url`, `img`, `visible`, `lang`, `order`, `date`, `public_date`) VALUES
(5, 'Blog', '<p>Blog</p>', 'blog', '', 1, 'en', 0, 0, ".date("U")."),
(6, 'News', '<p>News</p>', 'news', '', 1, 'en', 0, 0, ".date("U")."),
(7, 'Events', '<p>Events</p>', 'events', '', 1, 'en', 0, 0, ".date("U")."),
(8, 'Reviews', '<p>Reviews</p>', 'reviews', '', 1, 'en', 0, 0, ".date("U")."),
(9, 'Блог', '<p>Блог</p>', 'blog', '', 1, 'ru', 0, 0, ".date("U")."),
(10, 'Новости', '<p>Новости</p>', 'news', '', 1, 'ru', 0, 0, ".date("U")."),
(11, 'События', '<p>События</p>', 'events', '', 1, 'ru', 0, 0, ".date("U")."),
(12, 'Обзоры', '<p>Обзоры</p>', 'reviews', '', 1, 'ru', 0, 0, ".date("U").");


INSERT INTO `nodes_content` (`id`, `cat_id`, `url`, `lang`, `order`, `caption`, `text`, `img`, `date`, `public_date`) VALUES
(1, 5, 'lorem_ipsum', 'en', 0, 'Lorem ipsum', '<p>Lorem ipsum dolor sit amet, consectetur adipiscing elit. Nullam pulvinar congue porta. Integer laoreet ante eu tellus pharetra viverra. Donec massa lorem, congue vel orci eget, porttitor accumsan justo. Duis suscipit consequat congue. Lorem ipsum dolor sit amet, consectetur adipiscing elit. Duis vel nisi orci. Sed luctus in ante sed suscipit. Sed lacinia velit scelerisque purus efficitur viverra. Sed laoreet blandit mauris. Vestibulum at elit vel dui mollis tristique. Fusce risus nunc, tempor sed blandit sed, ullamcorper a dui. Fusce eu auctor neque, at commodo eros. Phasellus ligula ex, malesuada in luctus vitae, laoreet in enim. Etiam pretium nulla nec finibus semper. Pellentesque efficitur eros in magna luctus commodo.</span></p>', '5f8119c6c035102f15716fd6c7a09ad6.jpg', ".date("U").", ".date("U")."),
(2, 5, 'in_tempor_turpis_sit', 'en', 0, 'In tempor turpis sit', '<p>In tempor turpis sit amet augue scelerisque, non porta enim vestibulum. Aliquam sed nibh fringilla lorem feugiat mollis ac nec nunc. Aenean finibus metus non lacinia laoreet. Nulla at malesuada ligula. Donec nec orci vel ante ultricies aliquam. Cras vel ipsum ut libero condimentum blandit nec vel arcu. Proin et ligula dignissim, consequat sem at, efficitur est. Proin id viverra massa. Nunc tempus dolor eget ligula commodo, et iaculis quam volutpat. In non dictum ipsum, vel ultricies quam.</span></p>', 'cc5155f90c9eec7ed1311540fb3865f0.jpg', ".date("U").", ".date("U")."),
(3, 5, 'nunc_aliquet_tellus_metus', 'en', 0, 'Nunc aliquet tellus metus', '<p>Nunc aliquet tellus metus, eget vehicula turpis elementum eu. Sed eu venenatis orci. Fusce sit amet erat ut est convallis molestie. Aliquam ut vehicula lorem, at interdum urna. Curabitur nulla eros, consectetur id finibus sed, condimentum ac nibh. Nulla id dolor lobortis, fringilla est id, scelerisque neque. Vivamus quis libero non nisi rutrum tincidunt. Curabitur varius mi vitae mauris cursus condimentum. Proin at placerat ex. Sed hendrerit quam id porta maximus. Duis ut gravida lorem. Aenean sit amet interdum sapien.</span></p>', '73c46f4a48df76e2ea1b78e743cf73a6.jpg', ".date("U").", ".date("U")."),
(4, 6, 'nullam_a_velit_feugiat', 'en', 0, 'Nullam a velit feugiat', '<p>Nullam a velit feugiat, eleifend est vel, egestas arcu. Nulla viverra bibendum eros, ac efficitur diam rutrum in. Vestibulum vel magna vulputate, porta felis vitae, egestas purus. Vestibulum egestas turpis purus, eget aliquet massa scelerisque vestibulum. Fusce sed magna orci. Praesent cursus libero ut ultricies cursus. Morbi id sapien sed diam laoreet porttitor porta quis velit. Cras tortor ipsum, auctor sit amet sem ac, viverra feugiat velit. Duis nec dapibus turpis. Quisque lorem dolor, tempor non enim et, condimentum ultrices dolor. Donec quis semper diam, ut vulputate magna. Cras hendrerit risus sit amet massa vestibulum porta. Nam elementum elit erat, vitae feugiat tellus dapibus eget. Phasellus accumsan ullamcorper quam.</span></p>', '039f622d5dce82f34d18cde2dbb53a90.jpg', ".date("U").", ".date("U")."),
(5, 6, 'sed_augue_elit_interdum_et_risus_eu', 'en', 0, 'Sed augue elit, interdum et risus eu', '<p>Sed augue elit, interdum et risus eu, dapibus laoreet ligula. Sed interdum mi vitae tempor dignissim. Phasellus et accumsan elit, placerat finibus dolor. Sed consectetur leo ut augue sollicitudin, ut fermentum tortor aliquet. Donec at nibh sagittis, pretium ex a, malesuada ligula. Donec efficitur at felis nec volutpat. Praesent imperdiet enim sed magna accumsan semper quis eu nisi. Nunc sed leo porttitor, hendrerit est vel, dictum felis.</p><p>Phasellus at erat vitae purus efficitur commodo eu eu orci. Quisque auctor lobortis justo ut venenatis. Nulla facilisi. Integer non scelerisque ex, sed tristique enim. Nunc lacus nunc, bibendum id semper in, porta consectetur est. Duis ultrices et ex at imperdiet. Nullam non scelerisque metus. Nullam fringilla ligula efficitur nibh venenatis, et dictum mi commodo. Aenean ultrices ut lectus sit amet venenatis. Vivamus finibus sit amet turpis quis sagittis. Pellentesque porttitor sapien et tortor lacinia, id malesuada metus dictum. Pellentesque ac volutpat urna.</p>', '6bae51404bbb794916fd5608afaab13f.jpg', ".date("U").", ".date("U")."),
(6, 7, 'maecenas_laoreet_ut_purus_nec_dictum', 'en', 0, 'Maecenas laoreet ut purus nec dictum', '<p>Maecenas laoreet ut purus nec dictum. Pellentesque condimentum nunc tellus, sed consectetur nulla efficitur nec. Nulla blandit a lorem at laoreet. Aliquam auctor aliquet viverra. Quisque hendrerit felis in quam hendrerit, vel porta nisi sodales. Aenean ac rhoncus libero. Morbi sit amet erat sed leo lacinia tempor nec eu velit. Pellentesque quis elementum ante. Quisque consectetur nisi ut ex aliquet varius. Nullam molestie mi sed odio hendrerit, ac semper nisl lobortis. Sed erat libero, dapibus sit amet posuere in, vestibulum hendrerit nisl. Fusce facilisis, arcu et convallis dignissim, dolor nibh fringilla massa, eget sodales turpis ligula quis lectus. Quisque dictum ex diam, vel sagittis ligula faucibus quis. Morbi pretium sapien ut fringilla aliquet. Nam sed fermentum ligula.</p><p>Suspendisse vehicula nibh libero, et commodo enim imperdiet in. Phasellus ut laoreet arcu. Proin tincidunt odio vel arcu efficitur, eu ullamcorper magna venenatis. Pellentesque condimentum dictum tempus. Sed lacinia ante mi, vel convallis lectus vehicula id. Morbi ac interdum justo. Ut venenatis feugiat ligula, non rutrum lacus sollicitudin sed.</p>', '90a71fb7815be592731c9a7f904a105d.jpg', ".date("U").", ".date("U")."),
(7, 8, 'suspendisse_egestas', 'en', 0, 'Suspendisse Egestas', '<div>Suspendisse egestas auctor orci quis convallis. Curabitur commodo dapibus urna at tincidunt. Aliquam imperdiet condimentum gravida. Vivamus nisi nisl, pharetra quis accumsan a, fermentum eu augue. Morbi vel risus a neque lacinia luctus ut ac urna. Lorem ipsum dolor sit amet, consectetur adipiscing elit. Morbi tempus non ex eu aliquet.<br />Pellentesque leo quam, congue id imperdiet at, ullamcorper sit amet mi. Duis ligula yusto, feugiat in sem lobortis, cursus blandit neque. Vestibulum dignissim imperdiet elit, sed faucibus nulla tincidunt id. Sed sem dui, pellentesque eu ligula ac, dictum placerat ligula. Cum sociis natoque penatibus et magnis dis parturient montes, nascetur ridiculus mus. Nam accumsan quam nibh, ultricies fringilla odio egestas a. Mauris in rhoncus risus. Nulla blandit sem neque, ut hendrerit tellus luctus non. Nullam sed blandit lectus.</div>', '1594d26e8cac71099fc3e6079cc0ddf5.jpg', ".date("U").", ".date("U")."),
(8, 8, 'nunts_platserat_dolor', 'en', 0, 'Nunts platserat dolor', '<div>Nunc placerat, dolor quis tincidunt venenatis, sem lorem tincidunt felis, at vestibulum felis yusto quis velit. Fusce sit amet nunc nec nibh interdum sodales. Sed magna sapien, maximus sed bibendum sit amet, porttitor eu diam. Suspendisse tristique elit ante, sit amet efficitur nulla pellentesque at. Etiam pharetra odio et urna viverra, et luctus erat efficitur. Donec volutpat nulla ut lorem semper aliquet eget quis ipsum. Mauris eu dolor metus. Vivamus vekhicula metus dolor, ac posuere lorem aliquam a. Quisque tincidunt, erat eu bibendum mollis, ligula dolor interdum augue, et euismod yusto augue ac leo.<br />Curabitur et malesuada tortor, a volutpat yusto. Proin finibus leo eros, ut consequat metus euismod rutrum. Nulla vitae purus rhoncus, porttitor velit sed, consequat mauris. Vivamus aliquet pellentesque odio, sed scelerisque arcu iaculis nec. Vestibulum turpis ante, placerat ac ultricies sit amet, pharetra ullamcorper nibh. Vivamus ac nisi dui. Fusce felis nulla, fringilla sodales velit in, consectetur finibus sapien. Morbi molestie iaculis est, at lacinia arcu tristique vitae. In vulputate nulla eget iaculis pulvinar.</div>', 'af49139bf8468d73fdaf5d1353846cd9.jpg', ".date("U").", ".date("U")."),
(9, 8, 'mauris_non_letstus', 'en', 0, 'Mauris non letstus', '<div>Mauris non lectus elit. Fusce convallis eu quam at iaculis. Donec eu faucibus arcu. Nam non purus at neque imperdiet dignissim. Nullam fringilla augue eget nisi fringilla, eu consequat dolor ornare. Duis vekhicula tempus nunc vitae blandit. Aliquam ut aliquam ex. Donec et lobortis dolor. Fusce gravida eu ex eu fringilla. Aenean non quam molestie, elementum ipsum egestas, congue libero. Suspendisse vel nunc risus. Aenean eget mollis dolor.<br />Donec nec ipsum sollicitudin, facilisis mauris a, volutpat quam. Sed mollis mauris nulla, sit amet tempus est laoreet luctus. Suspendisse vitae dui tortor. Class aptent taciti sociosqu ad litora torquent per conubia nostra, per inceptos himenaeos. Morbi dignissim mauris sem, at dignissim leo accumsan id. Ut eget suscipit ligula. Cras nibh dolor, sodales non dapibus id, dictum nec nulla. Cras vitae odio imperdiet, sodales velit non, lacinia ante. Donec magna magna, imperdiet sit amet magna at, dictum faucibus risus. Quisque at suscipit est. Duis non ligula sed quam tincidunt fermentum et ac purus. Integer molestie lectus id nisi auctor suscipit in id quam.</div>', 'c19f3ddc36a060a42bb88ceb9498a86c.jpg', ".date("U").", ".date("U")."),
(10, 9, 'lorem_ipsum', 'ru', 1, 'Лорем ипсум', '<p>Лорем ипсум долор сит амет, цонсецтетур адиписцинг елит. Нуллам пулвинар цонгуе порта. Интегер лаореет анте еу теллус пхаретра виверра. Донец масса лорем, цонгуе вел орци егет, порттитор аццумсан юсто. Дуис сусципит цонсеqуат цонгуе. Лорем ипсум долор сит амет, цонсецтетур адиписцинг елит. Дуис вел ниси орци. Сед луцтус ин анте сед сусципит. Сед лациниа велит сцелерисqуе пурус еффицитур виверра. Сед лаореет бландит маурис. Вестибулум ат елит вел дуи моллис тристиqуе. Фусце рисус нунц, темпор сед бландит сед, улламцорпер а дуи. Фусце еу ауцтор неqуе, ат цоммодо ерос. Пхаселлус лигула еx, малесуада ин луцтус витае, лаореет ин еним. Етиам претиум нулла нец финибус семпер. Пеллентесqуе еффицитур ерос ин магна луцтус цоммодо.</p>', '5f8119c6c035102f15716fd6c7a09ad6.jpg', ".date("U").", ".date("U")."),
(11, 9, 'in_tempor_turpis_sit', 'ru', 2, 'Ин темпор турпис сит', '<p>Ин темпор турпис сит амет аугуе сцелерисqуе, нон порта еним вестибулум. Алиqуам сед нибх фрингилла лорем феугиат моллис ац нец нунц. Аенеан финибус метус нон лациниа лаореет. Нулла ат малесуада лигула. Донец нец орци вел анте ултрициес алиqуам. Црас вел ипсум ут либеро цондиментум бландит нец вел арцу. Проин ет лигула дигниссим, цонсеqуат сем ат, еффицитур ест. Проин ид виверра масса. Нунц темпус долор егет лигула цоммодо, ет иацулис qуам волутпат. Ин нон дицтум ипсум, вел ултрициес qуам.</p>', 'cc5155f90c9eec7ed1311540fb3865f0.jpg', ".date("U").", ".date("U")."),
(12, 9, 'nunc_aliquet_tellus_metus', 'ru', 3, 'Нунц алиqует теллус метус', '<p>Нунц алиqует теллус метус, егет вехицула турпис елементум еу. Сед еу вененатис орци. Фусце сит амет ерат ут ест цонваллис молестие. Алиqуам ут вехицула лорем, ат интердум урна. Цурабитур нулла ерос, цонсецтетур ид финибус сед, цондиментум ац нибх. Нулла ид долор лобортис, фрингилла ест ид, сцелерисqуе неqуе. Вивамус qуис либеро нон ниси рутрум тинцидунт. Цурабитур вариус ми витае маурис цурсус цондиментум. Проин ат плацерат еx. Сед хендрерит qуам ид порта маxимус. Дуис ут гравида лорем. Аенеан сит амет интердум сапиен.</p>', '73c46f4a48df76e2ea1b78e743cf73a6.jpg', ".date("U").", ".date("U")."),
(13, 10, 'nullam_a_velit_feugiat', 'ru', 0, 'Нуллам а велит феугиат', '<p>Нуллам а велит феугиат, елеифенд ест вел, егестас арцу. Нулла виверра бибендум ерос, ац еффицитур диам рутрум ин. Вестибулум вел магна вулпутате, порта фелис витае, егестас пурус. Вестибулум егестас турпис пурус, егет алиqует масса сцелерисqуе вестибулум. Фусце сед магна орци. Праесент цурсус либеро ут ултрициес цурсус. Морби ид сапиен сед диам лаореет порттитор порта qуис велит. Црас тортор ипсум, ауцтор сит амет сем ац, виверра феугиат велит. Дуис нец дапибус турпис. Qуисqуе лорем долор, темпор нон еним ет, цондиментум ултрицес долор. Донец qуис семпер диам, ут вулпутате магна. Црас хендрерит рисус сит амет масса вестибулум порта. Нам елементум елит ерат, витае феугиат теллус дапибус егет. Пхаселлус аццумсан улламцорпер qуам.</p>', '039f622d5dce82f34d18cde2dbb53a90.jpg', ".date("U").", ".date("U")."),
(14, 10, 'sed_augue_elit_interdum_et_risus_eu', 'ru', 0, 'Сед аугуе елит, интердум ет рисус', '<p>Сед аугуе елит, интердум ет рисус еу, дапибус лаореет лигула. Сед интердум ми витае темпор дигниссим. Пхаселлус ет аццумсан елит, плацерат финибус долор. Сед цонсецтетур лео ут аугуе соллицитудин, ут ферментум тортор алиqует. Донец ат нибх сагиттис, претиум еx а, малесуада лигула. Донец еффицитур ат фелис нец волутпат. Праесент импердиет еним сед магна аццумсан семпер qуис еу ниси. Нунц сед лео порттитор, хендрерит ест вел, дицтум фелис.<br />Пхаселлус ат ерат витае пурус еффицитур цоммодо еу еу орци. Qуисqуе ауцтор лобортис юсто ут вененатис. Нулла фацилиси. Интегер нон сцелерисqуе еx, сед тристиqуе еним. Нунц лацус нунц, бибендум ид семпер ин, порта цонсецтетур ест. Дуис ултрицес ет еx ат импердиет. Нуллам нон сцелерисqуе метус. Нуллам фрингилла лигула еффицитур нибх вененатис, ет дицтум ми цоммодо. Аенеан ултрицес ут лецтус сит амет вененатис. Вивамус финибус сит амет турпис qуис сагиттис. Пеллентесqуе порттитор сапиен ет тортор лациниа, ид малесуада метус дицтум. Пеллентесqуе ац волутпат урна.</p>', '6bae51404bbb794916fd5608afaab13f.jpg', ".date("U").", ".date("U")."),
(15, 11, 'maecenas_laoreet_ut_purus_nec_dictum', 'ru', 0, 'Маеценас лаореет ут пурус', '<p>Маеценас лаореет ут пурус нец дицтум. Пеллентесqуе цондиментум нунц теллус, сед цонсецтетур нулла еффицитур нец. Нулла бландит а лорем ат лаореет. Алиqуам ауцтор алиqует виверра. Qуисqуе хендрерит фелис ин qуам хендрерит, вел порта ниси содалес. Аенеан ац рхонцус либеро. Морби сит амет ерат сед лео лациниа темпор нец еу велит. Пеллентесqуе qуис елементум анте. Qуисqуе цонсецтетур ниси ут еx алиqует вариус. Нуллам молестие ми сед одио хендрерит, ац семпер нисл лобортис. Сед ерат либеро, дапибус сит амет посуере ин, вестибулум хендрерит нисл. Фусце фацилисис, арцу ет цонваллис дигниссим, долор нибх фрингилла масса, егет содалес турпис лигула qуис лецтус. Qуисqуе дицтум еx диам, вел сагиттис лигула фауцибус qуис. Морби претиум сапиен ут фрингилла алиqует. Нам сед ферментум лигула.<br />Суспендиссе вехицула нибх либеро, ет цоммодо еним импердиет ин. Пхаселлус ут лаореет арцу. Проин тинцидунт одио вел арцу еффицитур, еу улламцорпер магна вененатис. Пеллентесqуе цондиментум дицтум темпус. Сед лациниа анте ми, вел цонваллис лецтус вехицула ид. Морби ац интердум юсто. Ут вененатис феугиат лигула, нон рутрум лацус соллицитудин сед.</p>', '90a71fb7815be592731c9a7f904a105d.jpg', ".date("U").", ".date("U")."),
(16, 12, 'suspendisse_egestas', 'ru', 0, 'Суспендиссе егестас', '<div>Суспендиссе егестас ауцтор орци qуис цонваллис. Цурабитур цоммодо дапибус урна ат тинцидунт. Алиqуам импердиет цондиментум гравида. Вивамус ниси нисл, пхаретра qуис аццумсан а, ферментум еу аугуе. Морби вел рисус а неqуе лациниа луцтус ут ац урна. Лорем ипсум долор сит амет, цонсецтетур адиписцинг елит. Морби темпус нон еx еу алиqует.</div><div>Пеллентесqуе лео qуам, цонгуе ид импердиет ат, улламцорпер сит амет ми. Дуис лигула юсто, феугиат ин сем лобортис, цурсус бландит неqуе. Вестибулум дигниссим импердиет елит, сед фауцибус нулла тинцидунт ид. Сед сем дуи, пеллентесqуе еу лигула ац, дицтум плацерат лигула. Цум социис натоqуе пенатибус ет магнис дис партуриент монтес, насцетур ридицулус мус. Нам аццумсан qуам нибх, ултрициес фрингилла одио егестас а. Маурис ин рхонцус рисус. Нулла бландит сем неqуе, ут хендрерит теллус луцтус нон. Нуллам сед бландит лецтус.</div>', '1594d26e8cac71099fc3e6079cc0ddf5.jpg', ".date("U").", ".date("U")."),
(17, 12, 'nunts_platserat_dolor', 'ru', 0, 'Нунц плацерат, долор', '<div>Нунц плацерат, долор qуис тинцидунт вененатис, сем лорем тинцидунт фелис, ат вестибулум фелис юсто qуис велит. Фусце сит амет нунц нец нибх интердум содалес. Сед магна сапиен, маxимус сед бибендум сит амет, порттитор еу диам. Суспендиссе тристиqуе елит анте, сит амет еффицитур нулла пеллентесqуе ат. Етиам пхаретра одио ет урна виверра, ет луцтус ерат еффицитур. Донец волутпат нулла ут лорем семпер алиqует егет qуис ипсум. Маурис еу долор метус. Вивамус вехицула метус долор, ац посуере лорем алиqуам а. Qуисqуе тинцидунт, ерат еу бибендум моллис, лигула долор интердум аугуе, ет еуисмод юсто аугуе ац лео.</div><div>Цурабитур ет малесуада тортор, а волутпат юсто. Проин финибус лео ерос, ут цонсеqуат метус еуисмод рутрум. Нулла витае пурус рхонцус, порттитор велит сед, цонсеqуат маурис. Вивамус алиqует пеллентесqуе одио, сед сцелерисqуе арцу иацулис нец. Вестибулум турпис анте, плацерат ац ултрициес сит амет, пхаретра улламцорпер нибх. Вивамус ац ниси дуи. Фусце фелис нулла, фрингилла содалес велит ин, цонсецтетур финибус сапиен. Морби молестие иацулис ест, ат лациниа арцу тристиqуе витае. Ин вулпутате нулла егет иацулис пулвинар.</div>', 'af49139bf8468d73fdaf5d1353846cd9.jpg', ".date("U").", ".date("U")."),
(18, 12, 'mauris_non_letstus', 'ru', 0, 'Маурис нон лецтус', '<div>Маурис нон лецтус елит. Фусце цонваллис еу qуам ат иацулис. Донец еу фауцибус арцу. Нам нон пурус ат неqуе импердиет дигниссим. Нуллам фрингилла аугуе егет ниси фрингилла, еу цонсеqуат долор орнаре. Дуис вехицула темпус нунц витае бландит. Алиqуам ут алиqуам еx. Донец ет лобортис долор. Фусце гравида еу еx еу фрингилла. Аенеан нон qуам молестие, елементум ипсум егестас, цонгуе либеро. Суспендиссе вел нунц рисус. Аенеан егет моллис долор.</div><div>Донец нец ипсум соллицитудин, фацилисис маурис а, волутпат qуам. Сед моллис маурис нулла, сит амет темпус ест лаореет луцтус. Суспендиссе витае дуи тортор. Цласс аптент тацити социосqу ад литора торqуент пер цонубиа ностра, пер инцептос хименаеос. Морби дигниссим маурис сем, ат дигниссим лео аццумсан ид. Ут егет сусципит лигула. Црас нибх долор, содалес нон дапибус ид, дицтум нец нулла. Црас витае одио импердиет, содалес велит нон, лациниа анте. Донец магна магна, импердиет сит амет магна ат, дицтум фауцибус рисус. Qуисqуе ат сусципит ест. Дуис нон лигула сед qуам тинцидунт ферментум ет ац пурус. Интегер молестие лецтус ид ниси ауцтор сусципит ин ид qуам.</div>', 'c19f3ddc36a060a42bb88ceb9498a86c.jpg', ".date("U").", ".date("U").");


INSERT INTO `nodes_image` (`id`, `name`, `color`, `width`, `height`) VALUES
(1, '039f622d5dce82f34d18cde2dbb53a90.jpg', '861414', 400, 398),
(2, '1594d26e8cac71099fc3e6079cc0ddf5.jpg', 'e8c706', 400, 400),
(3, '24c06a8089c98ee219e73ef23fa11c11.jpg', 'ececec', 400, 400),
(4, '3f61bef77de2589f1d8659b3ba559916.jpg', 'cbdbdb', 400, 400),
(5, '417a4d50fcc94a87e88a29eb9dfd3069.jpg', 'bababa', 400, 400),
(6, '447a655794910fcbdbf48157a2638c3d.png', 'dcdcdc', 400, 400),
(7, '4c77d7f1a2d165b9b7c3046374ad58a0.jpg', 'dcdcdc', 400, 400),
(8, '54f3ccdc4a3367040cf55db8aafe7776.jpg', 'ecdbdb', 400, 400),
(9, '5f8119c6c035102f15716fd6c7a09ad6.jpg', '489742', 400, 400),
(10, '621c7486aef3fc9c3ebc3ef535c7f932.jpg', 'dcdbdb', 400, 400),
(11, '6bae51404bbb794916fd5608afaab13f.jpg', '972715', 399, 400),
(12, '6bec9290a39a809bba33ba2e891129a6.jpg', 'cccccb', 400, 400),
(13, '73c46f4a48df76e2ea1b78e743cf73a6.jpg', '537693', 400, 400),
(14, '8a630515790453498d6987c5018b5add.jpg', 'cccccb', 400, 400),
(15, '90a71fb7815be592731c9a7f904a105d.jpg', '76a602', 400, 400),
(16, 'ab0a56a55b02391414084f8d77c85a0d.jpg', 'cbdcdb', 400, 400),
(17, 'af49139bf8468d73fdaf5d1353846cd9.jpg', 'ecc735', 400, 400),
(18, 'c19f3ddc36a060a42bb88ceb9498a86c.jpg', 'edb901', 400, 400),
(19, 'c84ed8754d4a11245a082c6e99d283a4.jpg', 'ddeded', 400, 400),
(20, 'cc5155f90c9eec7ed1311540fb3865f0.jpg', '79d287', 400, 400),
(21, 'f138cb1d03ffa62618545ac621cc98be.jpg', 'ccdcdc', 400, 400);


INSERT INTO `nodes_product` (`id`, `user_id`, `title`, `text`, `description`, `img`, `shipping`, `price`, `date`, `status`, `views`, `rating`, `votes`) VALUES
(1, 1, 'Apple iPhone 6S Plus', 'Lorem ipsum dolor sit amet, consectetur adipiscing elit. Nullam pulvinar congue porta. Integer laoreet ante eu tellus pharetra viverra.', '', '24c06a8089c98ee219e73ef23fa11c11.jpg;54f3ccdc4a3367040cf55db8aafe7776.jpg;', 1, 1000, ".date("U").", 1, 0, 0, 0),
(2, 1, 'Apple iPhone 7', 'In tempor turpis sit amet augue scelerisque, non porta enim vestibulum. Aliquam sed nibh fringilla lorem feugiat mollis ac nec nunc.', '', 'c84ed8754d4a11245a082c6e99d283a4.jpg;417a4d50fcc94a87e88a29eb9dfd3069.jpg;', 1, 1000, ".date("U").", 1, 0, 0, 0),
(3, 1, 'Samsung Galaxy S7', 'Nunc aliquet tellus metus, eget vehicula turpis elementum eu. Sed eu venenatis orci. Fusce sit amet erat ut est convallis molestie.', '', '8a630515790453498d6987c5018b5add.jpg;', 1, 800, ".date("U").", 1, 0, 0, 0),
(4, 1, 'Samsung Galaxy S7 Edge', 'Nullam a velit feugiat, eleifend est vel, egestas arcu. Nulla viverra bibendum eros, ac efficitur diam rutrum in.', '', '6bec9290a39a809bba33ba2e891129a6.jpg;', 1, 1000, ".date("U").", 1, 0, 0, 0),
(5, 1, 'HTC One (M8)', 'Sed augue elit, interdum et risus eu, dapibus laoreet ligula. Sed interdum mi vitae tempor dignissim. Phasellus et accumsan elit, placerat finibus dolor.', '', '447a655794910fcbdbf48157a2638c3d.png;', 1, 400, ".date("U").", 1, 0, 0, 0),
(6, 1, 'Nokia Lumia 1520', 'Maecenas laoreet ut purus nec dictum. Pellentesque condimentum nunc tellus, sed consectetur nulla efficitur nec. Nulla blandit a lorem at laoreet.', '', 'ab0a56a55b02391414084f8d77c85a0d.jpg;', 1, 400, ".date("U").", 1, 0, 0, 0),
(7, 1, 'HTC 10', 'Nullam a velit feugiat, eleifend est vel, egestas arcu. Nulla viverra bibendum eros, ac efficitur diam rutrum in. Vestibulum vel magna vulputate, porta felis vitae, egestas purus.', '', '621c7486aef3fc9c3ebc3ef535c7f932.jpg;', 1, 700, ".date("U").", 1, 0, 0, 0),
(8, 1, 'Google Pixel', 'Morbi id sapien sed diam laoreet porttitor porta quis velit. Cras tortor ipsum, auctor sit amet sem ac, viverra feugiat velit.', '', '3f61bef77de2589f1d8659b3ba559916.jpg;4c77d7f1a2d165b9b7c3046374ad58a0.jpg;', 1, 500, ".date("U").", 1, 0, 0, 0),
(9, 1, 'Samsung Galaxy S6 Edge', 'Quisque lorem dolor, tempor non enim et, condimentum ultrices dolor. Sed interdum mi vitae tempor dignissim.', '', 'f138cb1d03ffa62618545ac621cc98be.jpg;', 1, 900, ".date("U").", 1, 0, 0, 0);


INSERT INTO `nodes_product_data` (`id`, `cat_id`, `value`, `url`) VALUES
(1, 1, 'Apple', 'apple'),
(2, 1, 'Samsung', 'samsung'),
(3, 1, 'HTC', 'htc'),
(4, 1, 'Nokia', 'nokia'),
(5, 1, 'Google', 'google'),
(6, 2, 'Black', ''),
(7, 2, 'Silver', ''),
(8, 2, 'Gray', ''),
(9, 2, 'White', ''),
(10, 3, '1440 x 2560', ''),
(11, 3, '1080 x 1920', '');


INSERT INTO `nodes_product_property` (`id`, `cat_id`, `value`) VALUES
(2, 0, 'Color'),
(3, 0, 'Resolution');


INSERT INTO `nodes_property_data` (`id`, `product_id`, `property_id`, `data_id`) VALUES
(32, 1, 2, 9),
(29, 2, 2, 9),
(35, 3, 2, 8),
(38, 4, 2, 7),
(41, 5, 2, 6),
(44, 6, 2, 6),
(31, 1, 1, 1),
(28, 2, 1, 1),
(34, 3, 1, 2),
(37, 4, 1, 2),
(40, 5, 1, 3),
(43, 6, 1, 4),
(27, 7, 3, 10),
(26, 7, 2, 8),
(25, 7, 1, 3),
(16, 8, 1, 5),
(17, 8, 2, 9),
(18, 8, 3, 11),
(19, 9, 1, 2),
(20, 9, 2, 6),
(21, 9, 3, 10),
(30, 2, 3, 10),
(33, 1, 3, 11),
(36, 3, 3, 10),
(39, 4, 3, 10),
(42, 5, 3, 11),
(45, 6, 3, 11);


INSERT INTO `nodes_aframe` (`id`, `caption`, `text`, `url`, `image`, `file`, `lang`) VALUES
(1, 'Panoramas', 'Multiresolution interactive 360 panorama viewer.', 'panorama', '/img/vr/pano-preview.jpg', 'panorama.php', 'en'),
(2, 'Audio Visualizer', 'Virtual Reality\'s visualizer of audio! Experience music in new ways.', 'visualizer', '/img/vr/visualizer-preview.jpg', 'visualizer.php', 'en'),
(3, 'Multiplayer', 'Nodes Studio & A-Frame Frameworks multiplayer demonstration scene.', 'multiplayer ', '/img/vr/multiplayer-preview.jpg', 'multiplayer.php', 'en'),
(4, 'Панорамы', 'Интерактивный просмотрщик 360 панорам с мультиразрешением.', 'panorama', '/img/vr/pano-preview.jpg', 'panorama.php', 'ru'),
(5, 'Аудио Визуализатор', 'Визуализатор аудио в формате виртуальной реальности!', 'visualizer', '/img/vr/visualizer-preview.jpg', 'visualizer.php', 'ru'),
(6, 'Мультиплеер', 'Nodes Studio & A-Frame многопользовательская сцена', 'multiplayer ', '/img/vr/multiplayer-preview.jpg', 'multiplayer.php', 'ru');

INSERT INTO `nodes_server_object` (`object_id`, `id`, `scene_id`, `type`, `collada-model`, `position`, `rotation`, `scale`, `nodes-click`) VALUES
(1, 'green-dummy-object', 1, 1, '#green-dummy-model', '0 1.5 -10', '0 180 0', '1 1 1', 'event:custom_function'),
(2, 'red-dummy-object', 1, 1, '#red-dummy-model', '0 1.5 10', '0 0 0', '1 1 1', 'event:custom_function'),
(3, 'blue-dummy-object', 1, 1, '#blue-dummy-model', '10 1.5 0', '0 90 0', '1 1 1', 'event:custom_function'),
(4, 'purple-dummy-object', 1, 1, '#purple-dummy-model', '-10 1.5 0', '0 270 0', '1 1 1', 'event:custom_function');

INSERT INTO `nodes_server_scene` (`id`, `max_radius`, `radius`, `terrain`) VALUES (1, 600, 50, '{\"1\": [ [0, 0, 3], [0, 50, -1], [50, 0, 2], [50, 0, 2], [50, 50, -9.6666666666667], [0, 50, -1] ] , \"2\": [ [0, 50, -1], [0, 100, 5], [50, 50, -9.6666666666667], [50, 50, -9.6666666666667], [50, 100, 71.611111111111], [0, 100, 5] ] , \"3\": [ [0, 100, 5], [0, 150, 9], [50, 100, 71.611111111111], [50, 100, 71.611111111111], [50, 150, 8.537037037037], [0, 150, 9] ] , \"4\": [ [0, 150, 9], [0, 200, 6], [50, 150, 8.537037037037], [50, 150, 8.537037037037], [50, 200, 17.345679012346], [0, 200, 6] ] , \"5\": [ [0, 200, 6], [0, 250, 10], [50, 200, 17.345679012346], [50, 200, 17.345679012346], [50, 250, 26.115226337449], [0, 250, 10] ] , \"6\": [ [0, 250, 10], [0, 300, 9], [50, 250, 26.115226337449], [50, 250, 26.115226337449], [50, 300, 32.03840877915], [0, 300, 9] ] , \"7\": [ [0, 300, 9], [0, 350, 10], [50, 300, 32.03840877915], [50, 300, 32.03840877915], [50, 350, 81.012802926383], [0, 350, 10] ] , \"8\": [ [0, 350, 10], [0, 400, 8], [50, 350, 81.012802926383], [50, 350, 81.012802926383], [50, 400, 75.504267642128], [0, 400, 8] ] , \"9\": [ [0, 400, 8], [0, 450, 5], [50, 400, 75.504267642128], [50, 400, 75.504267642128], [50, 450, 48.168089214043], [0, 450, 5] ] , \"10\": [ [0, 450, 5], [0, 500, -3], [50, 450, 48.168089214043], [50, 450, 48.168089214043], [50, 500, 36.222696404681], [0, 500, -3] ] , \"11\": [ [0, 500, -3], [0, 550, -2], [50, 500, 36.222696404681], [50, 500, 36.222696404681], [50, 550, -5.592434531773], [0, 550, -2] ] , \"12\": [ [0, 550, -2], [0, 600, -1], [50, 550, -5.592434531773], [50, 550, -5.592434531773], [50, 600, -8.3641448439243], [0, 600, -1] ] , \"13\": [ [0, 600, -1], [0, 650, 6], [50, 600, -8.3641448439243], [50, 600, -8.3641448439243], [50, 650, -12.788048281308], [0, 650, 6] ] , \"14\": [ [0, 650, 6], [0, 700, 1], [50, 650, -12.788048281308], [50, 650, -12.788048281308], [50, 700, -13.929349427103], [0, 700, 1] ] , \"15\": [ [0, 700, 1], [0, 750, 9], [50, 700, -13.929349427103], [50, 700, -13.929349427103], [50, 750, -3.9764498090342], [0, 750, 9] ] , \"16\": [ [0, 750, 9], [0, 800, 10], [50, 750, -3.9764498090342], [50, 750, -3.9764498090342], [50, 800, 5.0078500636553], [0, 800, 10] ] , \"17\": [ [0, 800, 10], [0, 850, -4], [50, 800, 5.0078500636553], [50, 800, 5.0078500636553], [50, 850, 8.6692833545518], [0, 850, -4] ] , \"18\": [ [0, 850, -4], [0, 900, 1], [50, 850, 8.6692833545518], [50, 850, 8.6692833545518], [50, 900, 17.723094451517], [0, 900, 1] ] , \"19\": [ [0, 900, 1], [0, 950, 7], [50, 900, 17.723094451517], [50, 900, 17.723094451517], [50, 950, 21.907698150506], [0, 950, 7] ] , \"20\": [ [0, 950, 7], [0, 1000, -4], [50, 950, 21.907698150506], [50, 950, 21.907698150506], [50, 1000, 6.8025660501686], [0, 1000, -4] ] , \"21\": [ [0, 1000, -4], [0, 1050, 1], [50, 1000, 6.8025660501686], [50, 1000, 6.8025660501686], [50, 1050, 1.9341886833895], [0, 1050, 1] ] , \"22\": [ [0, 1050, 1], [0, 1100, -4], [50, 1050, 1.9341886833895], [50, 1050, 1.9341886833895], [50, 1100, -0.52193710553682], [0, 1100, -4] ] , \"23\": [ [0, 1100, -4], [0, 1150, -4], [50, 1100, -0.52193710553682], [50, 1100, -0.52193710553682], [50, 1150, -2.8406457018456], [0, 1150, -4] ] , \"24\": [ [0, 1150, -4], [0, 1200, 5], [50, 1150, -2.8406457018456], [50, 1150, -2.8406457018456], [50, 1200, -0.4468819006152], [0, 1200, 5] ] , \"25\": [ [50, 0, 2], [50, 50, -9.6666666666667], [100, 0, 8], [100, 0, 8], [100, 50, 28.111111111111], [50, 50, -9.6666666666667] ] , \"26\": [ [50, 50, -9.6666666666667], [50, 100, 71.611111111111], [100, 50, 28.111111111111], [100, 50, 28.111111111111], [100, 100, 30.018518518519], [50, 100, 71.611111111111] ] , \"27\": [ [50, 100, 71.611111111111], [50, 150, 8.537037037037], [100, 100, 30.018518518519], [100, 100, 30.018518518519], [100, 150, 52.555555555556], [50, 150, 8.537037037037] ] , \"28\": [ [50, 150, 8.537037037037], [50, 200, 17.345679012346], [100, 150, 52.555555555556], [100, 150, 52.555555555556], [100, 200, 8.1460905349794], [50, 200, 17.345679012346] ] , \"29\": [ [50, 200, 17.345679012346], [50, 250, 26.115226337449], [100, 200, 8.1460905349794], [100, 200, 8.1460905349794], [100, 250, 5.8689986282579], [50, 250, 26.115226337449] ] , \"30\": [ [50, 250, 26.115226337449], [50, 300, 32.03840877915], [100, 250, 5.8689986282579], [100, 250, 5.8689986282579], [100, 300, 64.007544581619], [50, 300, 32.03840877915] ] , \"31\": [ [50, 300, 32.03840877915], [50, 350, 81.012802926383], [100, 300, 64.007544581619], [100, 300, 64.007544581619], [100, 350, 64.01958542905], [50, 350, 81.012802926383] ] , \"32\": [ [50, 350, 81.012802926383], [50, 400, 75.504267642128], [100, 350, 64.01958542905], [100, 350, 64.01958542905], [100, 400, 61.845551999187], [50, 400, 75.504267642128] ] , \"33\": [ [50, 400, 75.504267642128], [50, 450, 48.168089214043], [100, 400, 61.845551999187], [100, 400, 61.845551999187], [100, 450, 83.505969618452], [50, 450, 48.168089214043] ] , \"34\": [ [50, 450, 48.168089214043], [50, 500, 36.222696404681], [100, 450, 83.505969618452], [100, 450, 83.505969618452], [100, 500, 47.965585079059], [50, 500, 36.222696404681] ] , \"35\": [ [50, 500, 36.222696404681], [50, 550, -5.592434531773], [100, 500, 47.965585079059], [100, 500, 47.965585079059], [100, 550, 9.6986156506555], [50, 550, -5.592434531773] ] , \"36\": [ [50, 550, -5.592434531773], [50, 600, -8.3641448439243], [100, 550, 9.6986156506555], [100, 550, 9.6986156506555], [100, 600, -1.4193212416806], [50, 600, -8.3641448439243] ] , \"37\": [ [50, 600, -8.3641448439243], [50, 650, -12.788048281308], [100, 600, -1.4193212416806], [100, 600, -1.4193212416806], [100, 650, 11.976161877696], [50, 650, -12.788048281308] ] , \"38\": [ [50, 650, -12.788048281308], [50, 700, -13.929349427103], [100, 650, 11.976161877696], [100, 650, 11.976161877696], [100, 700, -18.247078610238], [50, 700, -13.929349427103] ] , \"39\": [ [50, 700, -13.929349427103], [50, 750, -3.9764498090342], [100, 700, -18.247078610238], [100, 700, -18.247078610238], [100, 750, 1.9490407178749], [50, 750, -3.9764498090342] ] , \"40\": [ [50, 750, -3.9764498090342], [50, 800, 5.0078500636553], [100, 750, 1.9490407178749], [100, 750, 1.9490407178749], [100, 800, 12.993480324165], [50, 800, 5.0078500636553] ] , \"41\": [ [50, 800, 5.0078500636553], [50, 850, 8.6692833545518], [100, 800, 12.993480324165], [100, 800, 12.993480324165], [100, 850, 17.223537914124], [50, 850, 8.6692833545518] ] , \"42\": [ [50, 850, 8.6692833545518], [50, 900, 17.723094451517], [100, 850, 17.223537914124], [100, 850, 17.223537914124], [100, 900, 29.871971906731], [50, 900, 17.723094451517] ] , \"43\": [ [50, 900, 17.723094451517], [50, 950, 21.907698150506], [100, 900, 29.871971906731], [100, 900, 29.871971906731], [100, 950, 21.667588169585], [50, 950, 21.907698150506] ] , \"44\": [ [50, 950, 21.907698150506], [50, 1000, 6.8025660501686], [100, 950, 21.667588169585], [100, 950, 21.667588169585], [100, 1000, 16.125950790086], [50, 1000, 6.8025660501686] ] , \"45\": [ [50, 1000, 6.8025660501686], [50, 1050, 1.9341886833895], [100, 1000, 16.125950790086], [100, 1000, 16.125950790086], [100, 1050, 8.4542351745481], [50, 1050, 1.9341886833895] ] , \"46\": [ [50, 1050, 1.9341886833895], [50, 1100, -0.52193710553682], [100, 1050, 8.4542351745481], [100, 1050, 8.4542351745481], [100, 1100, 3.288828917467], [50, 1100, -0.52193710553682] ] , \"47\": [ [50, 1100, -0.52193710553682], [50, 1150, -2.8406457018456], [100, 1100, 3.288828917467], [100, 1100, 3.288828917467], [100, 1150, 3.4754153700282], [50, 1150, -2.8406457018456] ] , \"48\": [ [50, 1150, -2.8406457018456], [50, 1200, -0.4468819006152], [100, 1150, 3.4754153700282], [100, 1150, 3.4754153700282], [100, 1200, -1.2707040774775], [50, 1200, -0.4468819006152] ] , \"49\": [ [100, 0, 8], [100, 50, 28.111111111111], [150, 0, 2], [150, 0, 2], [150, 50, 29.37037037037], [100, 50, 28.111111111111] ] , \"50\": [ [100, 50, 28.111111111111], [100, 100, 30.018518518519], [150, 50, 29.37037037037], [150, 50, 29.37037037037], [150, 100, 57.666666666667], [100, 100, 30.018518518519] ] , \"51\": [ [100, 100, 30.018518518519], [100, 150, 52.555555555556], [150, 100, 57.666666666667], [150, 100, 57.666666666667], [150, 150, 22.746913580247], [100, 150, 52.555555555556] ] , \"52\": [ [100, 150, 52.555555555556], [100, 200, 8.1460905349794], [150, 150, 22.746913580247], [150, 150, 22.746913580247], [150, 200, 2.3161865569273], [100, 200, 8.1460905349794] ] , \"53\": [ [100, 200, 8.1460905349794], [100, 250, 5.8689986282579], [150, 200, 2.3161865569273], [150, 200, 2.3161865569273], [150, 250, 0.11042524005487], [100, 250, 5.8689986282579] ] , \"54\": [ [100, 250, 5.8689986282579], [100, 300, 64.007544581619], [150, 250, 0.11042524005487], [150, 250, 0.11042524005487], [150, 300, -1.6710105166895], [100, 300, 64.007544581619] ] , \"55\": [ [100, 300, 64.007544581619], [100, 350, 64.01958542905], [150, 300, -1.6710105166895], [150, 300, -1.6710105166895], [150, 350, 25.78537316466], [100, 350, 64.01958542905] ] , \"56\": [ [100, 350, 64.01958542905], [100, 400, 61.845551999187], [150, 350, 25.78537316466], [150, 350, 25.78537316466], [150, 400, 35.383503530966], [100, 400, 61.845551999187] ] , \"57\": [ [100, 400, 61.845551999187], [100, 450, 83.505969618452], [150, 400, 35.383503530966], [150, 400, 35.383503530966], [150, 450, 94.245008382868], [100, 450, 83.505969618452] ] , \"58\": [ [100, 450, 83.505969618452], [100, 500, 47.965585079059], [150, 450, 94.245008382868], [150, 450, 94.245008382868], [150, 500, 75.238854360127], [100, 500, 47.965585079059] ] , \"59\": [ [100, 500, 47.965585079059], [100, 550, 9.6986156506555], [150, 500, 75.238854360127], [150, 500, 75.238854360127], [150, 550, 59.30101836328], [100, 550, 9.6986156506555] ] , \"60\": [ [100, 550, 9.6986156506555], [100, 600, -1.4193212416806], [150, 550, 59.30101836328], [150, 550, 59.30101836328], [150, 600, 42.026770924085], [100, 600, -1.4193212416806] ] , \"61\": [ [100, 600, -1.4193212416806], [100, 650, 11.976161877696], [150, 600, 42.026770924085], [150, 600, 42.026770924085], [150, 650, 24.1945371867], [100, 650, 11.976161877696] ] , \"62\": [ [100, 650, 11.976161877696], [100, 700, -18.247078610238], [150, 650, 24.1945371867], [150, 650, 24.1945371867], [150, 700, 8.3078734847191], [100, 700, -18.247078610238] ] , \"63\": [ [100, 700, -18.247078610238], [100, 750, 1.9490407178749], [150, 700, 8.3078734847191], [150, 700, 8.3078734847191], [150, 750, 13.336611864119], [100, 750, 1.9490407178749] ] , \"64\": [ [100, 750, 1.9490407178749], [100, 800, 12.993480324165], [150, 750, 13.336611864119], [150, 750, 13.336611864119], [150, 800, 8.5930443020529], [100, 800, 12.993480324165] ] , \"65\": [ [100, 800, 12.993480324165], [100, 850, 17.223537914124], [150, 800, 8.5930443020529], [150, 800, 8.5930443020529], [150, 850, 10.936687513447], [100, 850, 17.223537914124] ] , \"66\": [ [100, 850, 17.223537914124], [100, 900, 29.871971906731], [150, 850, 10.936687513447], [150, 850, 10.936687513447], [150, 900, 18.844065778101], [100, 900, 29.871971906731] ] , \"67\": [ [100, 900, 29.871971906731], [100, 950, 21.667588169585], [150, 900, 18.844065778101], [150, 900, 18.844065778101], [150, 950, 22.794541951472], [100, 950, 21.667588169585] ] , \"68\": [ [100, 950, 21.667588169585], [100, 1000, 16.125950790086], [150, 950, 22.794541951472], [150, 950, 22.794541951472], [150, 1000, 20.362693637048], [100, 1000, 16.125950790086] ] , \"69\": [ [100, 1000, 16.125950790086], [100, 1050, 8.4542351745481], [150, 1000, 20.362693637048], [150, 1000, 20.362693637048], [150, 1050, 14.980959867227], [100, 1050, 8.4542351745481] ] , \"70\": [ [100, 1050, 8.4542351745481], [100, 1100, 3.288828917467], [150, 1050, 14.980959867227], [150, 1050, 14.980959867227], [150, 1100, 9.7413413197475], [100, 1100, 3.288828917467] ] , \"71\": [ [100, 1100, 3.288828917467], [100, 1150, 3.4754153700282], [150, 1100, 9.7413413197475], [150, 1100, 9.7413413197475], [150, 1150, 10.501861869081], [100, 1150, 3.4754153700282] ] , \"72\": [ [100, 1150, 3.4754153700282], [100, 1200, -1.2707040774775], [150, 1150, 10.501861869081], [150, 1150, 10.501861869081], [150, 1200, 6.7355243872105], [100, 1200, -1.2707040774775] ] , \"73\": [ [150, 0, 2], [150, 50, 29.37037037037], [200, 0, -2], [200, 0, -2], [200, 50, 31.956790123457], [150, 50, 29.37037037037] ] , \"74\": [ [150, 50, 29.37037037037], [150, 100, 57.666666666667], [200, 50, 31.956790123457], [200, 50, 31.956790123457], [200, 100, 81.664609053498], [150, 100, 57.666666666667] ] , \"75\": [ [150, 100, 57.666666666667], [150, 150, 22.746913580247], [200, 100, 81.664609053498], [200, 100, 81.664609053498], [200, 150, 76.692729766804], [150, 150, 22.746913580247] ] , \"76\": [ [150, 150, 22.746913580247], [150, 200, 2.3161865569273], [200, 150, 76.692729766804], [200, 150, 76.692729766804], [200, 200, 68.585276634659], [150, 200, 2.3161865569273] ] , \"77\": [ [150, 200, 2.3161865569273], [150, 250, 0.11042524005487], [200, 200, 68.585276634659], [200, 200, 68.585276634659], [200, 250, 53.670629477214], [150, 250, 0.11042524005487] ] , \"78\": [ [150, 250, 0.11042524005487], [150, 300, -1.6710105166895], [200, 250, 53.670629477214], [200, 250, 53.670629477214], [200, 300, 59.370014733526], [150, 300, -1.6710105166895] ] , \"79\": [ [150, 300, -1.6710105166895], [150, 350, 25.78537316466], [200, 300, 59.370014733526], [200, 300, 59.370014733526], [200, 350, 42.994792460499], [150, 350, 25.78537316466] ] , \"80\": [ [150, 350, 25.78537316466], [150, 400, 35.383503530966], [200, 350, 42.994792460499], [200, 350, 42.994792460499], [200, 400, 32.721223052042], [150, 400, 35.383503530966] ] , \"81\": [ [150, 400, 35.383503530966], [150, 450, 94.245008382868], [200, 400, 32.721223052042], [200, 400, 32.721223052042], [200, 450, 68.783244988625], [150, 450, 94.245008382868] ] , \"82\": [ [150, 450, 94.245008382868], [150, 500, 75.238854360127], [200, 450, 68.783244988625], [200, 450, 68.783244988625], [200, 500, 62.755702577207], [150, 500, 75.238854360127] ] , \"83\": [ [150, 500, 75.238854360127], [150, 550, 59.30101836328], [200, 500, 62.755702577207], [200, 500, 62.755702577207], [200, 550, 71.765191766871], [150, 550, 59.30101836328] ] , \"84\": [ [150, 550, 59.30101836328], [150, 600, 42.026770924085], [200, 550, 71.765191766871], [200, 550, 71.765191766871], [200, 600, 68.364327018079], [150, 600, 42.026770924085] ] , \"85\": [ [150, 600, 42.026770924085], [150, 650, 24.1945371867], [200, 600, 68.364327018079], [200, 600, 68.364327018079], [200, 650, 50.695211709621], [150, 650, 24.1945371867] ] , \"86\": [ [150, 650, 24.1945371867], [150, 700, 8.3078734847191], [200, 650, 50.695211709621], [200, 650, 50.695211709621], [200, 700, 31.73254079368], [150, 700, 8.3078734847191] ] , \"87\": [ [150, 700, 8.3078734847191], [150, 750, 13.336611864119], [200, 700, 31.73254079368], [200, 700, 31.73254079368], [200, 750, 24.459008714173], [150, 750, 13.336611864119] ] , \"88\": [ [150, 750, 13.336611864119], [150, 800, 8.5930443020529], [200, 750, 24.459008714173], [200, 750, 24.459008714173], [200, 800, 10.796221626781], [150, 800, 8.5930443020529] ] , \"89\": [ [150, 800, 8.5930443020529], [150, 850, 10.936687513447], [200, 800, 10.796221626781], [200, 800, 10.796221626781], [200, 850, 9.6086511474272], [150, 850, 10.936687513447] ] , \"90\": [ [150, 850, 10.936687513447], [150, 900, 18.844065778101], [200, 850, 9.6086511474272], [200, 850, 9.6086511474272], [200, 900, 12.463134812992], [150, 900, 18.844065778101] ] , \"91\": [ [150, 900, 18.844065778101], [150, 950, 22.794541951472], [200, 900, 12.463134812992], [200, 900, 12.463134812992], [200, 950, 19.533914180855], [150, 950, 22.794541951472] ] , \"92\": [ [150, 950, 22.794541951472], [150, 1000, 20.362693637048], [200, 950, 19.533914180855], [200, 950, 19.533914180855], [200, 1000, 20.897049923125], [150, 1000, 20.362693637048] ] , \"93\": [ [150, 1000, 20.362693637048], [150, 1050, 14.980959867227], [200, 1000, 20.897049923125], [200, 1000, 20.897049923125], [200, 1050, 21.413567809133], [150, 1050, 14.980959867227] ] , \"94\": [ [150, 1050, 14.980959867227], [150, 1100, 9.7413413197475], [200, 1050, 21.413567809133], [200, 1050, 21.413567809133], [200, 1100, 15.045289665369], [150, 1100, 9.7413413197475] ] , \"95\": [ [150, 1100, 9.7413413197475], [150, 1150, 10.501861869081], [200, 1100, 15.045289665369], [200, 1100, 15.045289665369], [200, 1150, 24.262830951399], [150, 1150, 10.501861869081] ] , \"96\": [ [150, 1150, 10.501861869081], [150, 1200, 6.7355243872105], [200, 1150, 24.262830951399], [200, 1150, 24.262830951399], [200, 1200, 19.833405735897], [150, 1200, 6.7355243872105] ] , \"97\": [ [200, 0, -2], [200, 50, 31.956790123457], [250, 0, 9], [250, 0, 9], [250, 50, -17.014403292181], [200, 50, 31.956790123457] ] , \"98\": [ [200, 50, 31.956790123457], [200, 100, 81.664609053498], [250, 50, -17.014403292181], [250, 50, -17.014403292181], [250, 100, 49.202331961591], [200, 100, 81.664609053498] ] , \"99\": [ [200, 100, 81.664609053498], [200, 150, 76.692729766804], [250, 100, 49.202331961591], [250, 100, 49.202331961591], [250, 150, 53.186556927298], [200, 150, 76.692729766804] ] , \"100\": [ [200, 150, 76.692729766804], [200, 200, 68.585276634659], [250, 150, 53.186556927298], [250, 150, 53.186556927298], [250, 200, 78.65485444292], [200, 200, 68.585276634659] ] , \"101\": [ [200, 200, 68.585276634659], [200, 250, 53.670629477214], [250, 200, 78.65485444292], [250, 200, 78.65485444292], [250, 250, 50.636920184931], [200, 250, 53.670629477214] ] , \"102\": [ [200, 250, 53.670629477214], [200, 300, 59.370014733526], [250, 250, 50.636920184931], [250, 250, 50.636920184931], [250, 300, 84.892521465224], [200, 300, 59.370014733526] ] , \"103\": [ [200, 300, 59.370014733526], [200, 350, 42.994792460499], [250, 300, 84.892521465224], [250, 300, 84.892521465224], [250, 350, 56.419109553083], [200, 350, 42.994792460499] ] , \"104\": [ [200, 350, 42.994792460499], [200, 400, 32.721223052042], [250, 350, 56.419109553083], [250, 350, 56.419109553083], [250, 400, 51.378375021874], [200, 400, 32.721223052042] ] , \"105\": [ [200, 400, 32.721223052042], [200, 450, 68.783244988625], [250, 400, 51.378375021874], [250, 400, 51.378375021874], [250, 450, 72.62761435418], [200, 450, 68.783244988625] ] , \"106\": [ [200, 450, 68.783244988625], [200, 500, 62.755702577207], [250, 450, 72.62761435418], [250, 450, 72.62761435418], [250, 500, 56.055520640004], [200, 500, 62.755702577207] ] , \"107\": [ [200, 500, 62.755702577207], [200, 550, 71.765191766871], [250, 500, 56.055520640004], [250, 500, 56.055520640004], [250, 550, 54.192138328027], [200, 550, 71.765191766871] ] , \"108\": [ [200, 550, 71.765191766871], [200, 600, 68.364327018079], [250, 550, 54.192138328027], [250, 550, 54.192138328027], [250, 600, 74.107219037659], [200, 600, 68.364327018079] ] , \"109\": [ [200, 600, 68.364327018079], [200, 650, 50.695211709621], [250, 600, 74.107219037659], [250, 600, 74.107219037659], [250, 650, 59.38891925512], [200, 650, 50.695211709621] ] , \"110\": [ [200, 650, 50.695211709621], [200, 700, 31.73254079368], [250, 650, 59.38891925512], [250, 650, 59.38891925512], [250, 700, 41.43889058614], [200, 700, 31.73254079368] ] , \"111\": [ [200, 700, 31.73254079368], [200, 750, 24.459008714173], [250, 700, 41.43889058614], [250, 700, 41.43889058614], [250, 750, 39.876813364664], [200, 750, 24.459008714173] ] , \"112\": [ [200, 750, 24.459008714173], [200, 800, 10.796221626781], [250, 750, 39.876813364664], [250, 750, 39.876813364664], [250, 800, 26.044014568539], [200, 800, 10.796221626781] ] , \"113\": [ [200, 800, 10.796221626781], [200, 850, 9.6086511474272], [250, 800, 26.044014568539], [250, 800, 26.044014568539], [250, 850, 19.482962447583], [200, 850, 9.6086511474272] ] , \"114\": [ [200, 850, 9.6086511474272], [200, 900, 12.463134812992], [250, 850, 19.482962447583], [250, 850, 19.482962447583], [250, 900, 14.851582802667], [200, 900, 12.463134812992] ] , \"115\": [ [200, 900, 12.463134812992], [200, 950, 19.533914180855], [250, 900, 14.851582802667], [250, 900, 14.851582802667], [250, 950, 15.616210598838], [200, 950, 19.533914180855] ] , \"116\": [ [200, 950, 19.533914180855], [200, 1000, 20.897049923125], [250, 950, 15.616210598838], [250, 950, 15.616210598838], [250, 1000, 20.015724900939], [200, 1000, 20.897049923125] ] , \"117\": [ [200, 1000, 20.897049923125], [200, 1050, 21.413567809133], [250, 1000, 20.015724900939], [250, 1000, 20.015724900939], [250, 1050, 18.775447544399], [200, 1050, 21.413567809133] ] , \"118\": [ [200, 1050, 21.413567809133], [200, 1100, 15.045289665369], [250, 1050, 18.775447544399], [250, 1050, 18.775447544399], [250, 1100, 13.911435006301], [200, 1100, 15.045289665369] ] , \"119\": [ [200, 1100, 15.045289665369], [200, 1150, 24.262830951399], [250, 1100, 13.911435006301], [250, 1100, 13.911435006301], [250, 1150, 19.07318520769], [200, 1150, 24.262830951399] ] , \"120\": [ [200, 1150, 24.262830951399], [200, 1200, 19.833405735897], [250, 1150, 19.07318520769], [250, 1150, 19.07318520769], [250, 1200, 22.723140631662], [200, 1200, 19.833405735897] ] , \"121\": [ [250, 0, 9], [250, 50, -17.014403292181], [300, 0, 5], [300, 0, 5], [300, 50, -20.838134430727], [250, 50, -17.014403292181] ] , \"122\": [ [250, 50, -17.014403292181], [250, 100, 49.202331961591], [300, 50, -20.838134430727], [300, 50, -20.838134430727], [300, 100, 27.783264746228], [250, 100, 49.202331961591] ] , \"123\": [ [250, 100, 49.202331961591], [250, 150, 53.186556927298], [300, 100, 27.783264746228], [300, 100, 27.783264746228], [300, 150, 65.890717878372], [250, 150, 53.186556927298] ] , \"124\": [ [250, 150, 53.186556927298], [250, 200, 78.65485444292], [300, 150, 65.890717878372], [300, 150, 65.890717878372], [300, 200, 44.91070974953], [250, 200, 78.65485444292] ] , \"125\": [ [250, 200, 78.65485444292], [250, 250, 50.636920184931], [300, 200, 44.91070974953], [300, 200, 44.91070974953], [300, 250, 77.567494792461], [250, 250, 50.636920184931] ] , \"126\": [ [250, 250, 50.636920184931], [250, 300, 84.892521465224], [300, 250, 77.567494792461], [300, 250, 77.567494792461], [300, 300, 73.032312147538], [250, 300, 84.892521465224] ] , \"127\": [ [250, 300, 84.892521465224], [250, 350, 56.419109553083], [300, 300, 73.032312147538], [300, 300, 73.032312147538], [300, 350, 56.781314388615], [250, 350, 56.419109553083] ] , \"128\": [ [250, 350, 56.419109553083], [250, 400, 51.378375021874], [300, 350, 56.781314388615], [300, 350, 56.781314388615], [300, 400, 56.526266321191], [250, 400, 51.378375021874] ] , \"129\": [ [250, 400, 51.378375021874], [250, 450, 72.62761435418], [300, 400, 56.526266321191], [300, 400, 56.526266321191], [300, 450, 60.177418565749], [250, 450, 72.62761435418] ] , \"130\": [ [250, 450, 72.62761435418], [250, 500, 56.055520640004], [300, 450, 60.177418565749], [300, 450, 60.177418565749], [300, 500, 54.953517853311], [250, 500, 56.055520640004] ] , \"131\": [ [250, 500, 56.055520640004], [250, 550, 54.192138328027], [300, 500, 54.953517853311], [300, 500, 54.953517853311], [300, 550, 51.567058940448], [250, 550, 54.192138328027] ] , \"132\": [ [250, 550, 54.192138328027], [250, 600, 74.107219037659], [300, 550, 51.567058940448], [300, 550, 51.567058940448], [300, 600, 56.955472102045], [250, 600, 74.107219037659] ] , \"133\": [ [250, 600, 74.107219037659], [250, 650, 59.38891925512], [300, 600, 56.955472102045], [300, 600, 56.955472102045], [300, 650, 57.650536798274], [250, 650, 59.38891925512] ] , \"134\": [ [250, 650, 59.38891925512], [250, 700, 41.43889058614], [300, 650, 57.650536798274], [300, 650, 57.650536798274], [300, 700, 47.492782213178], [250, 700, 41.43889058614] ] , \"135\": [ [250, 700, 41.43889058614], [250, 750, 39.876813364664], [300, 700, 47.492782213178], [300, 700, 47.492782213178], [300, 750, 38.436162054661], [250, 750, 39.876813364664] ] , \"136\": [ [250, 750, 39.876813364664], [250, 800, 26.044014568539], [300, 750, 38.436162054661], [300, 750, 38.436162054661], [300, 800, 35.452329995955], [250, 800, 26.044014568539] ] , \"137\": [ [250, 800, 26.044014568539], [250, 850, 19.482962447583], [300, 800, 35.452329995955], [300, 800, 35.452329995955], [300, 850, 27.326435670692], [250, 850, 19.482962447583] ] , \"138\": [ [250, 850, 19.482962447583], [250, 900, 14.851582802667], [300, 850, 27.326435670692], [300, 850, 27.326435670692], [300, 900, 20.553660306981], [250, 900, 14.851582802667] ] , \"139\": [ [250, 900, 14.851582802667], [250, 950, 15.616210598838], [300, 900, 20.553660306981], [300, 900, 20.553660306981], [300, 950, 15.340484569495], [250, 950, 15.616210598838] ] , \"140\": [ [250, 950, 15.616210598838], [250, 1000, 20.015724900939], [300, 950, 15.340484569495], [300, 950, 15.340484569495], [300, 1000, 14.657473356424], [250, 1000, 20.015724900939] ] , \"141\": [ [250, 1000, 20.015724900939], [250, 1050, 18.775447544399], [300, 1000, 14.657473356424], [300, 1000, 14.657473356424], [300, 1050, 14.316215267254], [250, 1050, 18.775447544399] ] , \"142\": [ [250, 1050, 18.775447544399], [250, 1100, 13.911435006301], [300, 1050, 14.316215267254], [300, 1050, 14.316215267254], [300, 1100, 22.334365939318], [250, 1100, 13.911435006301] ] , \"143\": [ [250, 1100, 13.911435006301], [250, 1150, 19.07318520769], [300, 1100, 22.334365939318], [300, 1100, 22.334365939318], [300, 1150, 22.60632871777], [250, 1150, 19.07318520769] ] , \"144\": [ [250, 1150, 19.07318520769], [250, 1200, 22.723140631662], [300, 1150, 22.60632871777], [300, 1150, 22.60632871777], [300, 1200, 34.46755151904], [250, 1200, 22.723140631662] ] , \"145\": [ [300, 0, 5], [300, 50, -20.838134430727], [350, 0, -1], [350, 0, -1], [350, 50, -21.612711476909], [300, 50, -20.838134430727] ] , \"146\": [ [300, 50, -20.838134430727], [300, 100, 27.783264746228], [350, 50, -21.612711476909], [350, 50, -21.612711476909], [350, 100, -24.889193720469], [300, 100, 27.783264746228] ] , \"147\": [ [300, 100, 27.783264746228], [300, 150, 65.890717878372], [350, 100, -24.889193720469], [350, 100, -24.889193720469], [350, 150, 53.261596301377], [300, 150, 65.890717878372] ] , \"148\": [ [300, 150, 65.890717878372], [300, 200, 44.91070974953], [350, 150, 53.261596301377], [350, 150, 53.261596301377], [350, 200, 102.35434130976], [300, 200, 44.91070974953] ] , \"149\": [ [300, 200, 44.91070974953], [300, 250, 77.567494792461], [350, 200, 102.35434130976], [350, 200, 102.35434130976], [350, 250, 64.944181950583], [300, 250, 77.567494792461] ] , \"150\": [ [300, 250, 77.567494792461], [300, 300, 73.032312147538], [350, 250, 64.944181950583], [350, 250, 64.944181950583], [350, 300, 86.514662963527], [300, 300, 73.032312147538] ] , \"151\": [ [300, 300, 73.032312147538], [300, 350, 56.781314388615], [350, 300, 86.514662963527], [350, 300, 86.514662963527], [350, 350, 3], [300, 350, 56.781314388615] ] , \"152\": [ [300, 350, 56.781314388615], [300, 400, 56.526266321191], [350, 350, 3], [350, 350, 3], [350, 400, 0], [300, 400, 56.526266321191] ] , \"153\": [ [300, 400, 56.526266321191], [300, 450, 60.177418565749], [350, 400, 0], [350, 400, 0], [350, 450, -2], [300, 450, 60.177418565749] ] , \"154\": [ [300, 450, 60.177418565749], [300, 500, 54.953517853311], [350, 450, -2], [350, 450, -2], [350, 500, -3], [300, 500, 54.953517853311] ] , \"155\": [ [300, 500, 54.953517853311], [300, 550, 51.567058940448], [350, 500, -3], [350, 500, -3], [350, 550, -3], [300, 550, 51.567058940448] ] , \"156\": [ [300, 550, 51.567058940448], [300, 600, 56.955472102045], [350, 550, -3], [350, 550, -3], [350, 600, 0], [300, 600, 56.955472102045] ] , \"157\": [ [300, 600, 56.955472102045], [300, 650, 57.650536798274], [350, 600, 0], [350, 600, 0], [350, 650, 8], [300, 650, 57.650536798274] ] , \"158\": [ [300, 650, 57.650536798274], [300, 700, 47.492782213178], [350, 650, 8], [350, 650, 8], [350, 700, 1], [300, 700, 47.492782213178] ] , \"159\": [ [300, 700, 47.492782213178], [300, 750, 38.436162054661], [350, 700, 1], [350, 700, 1], [350, 750, 7], [300, 750, 38.436162054661] ] , \"160\": [ [300, 750, 38.436162054661], [300, 800, 35.452329995955], [350, 750, 7], [350, 750, 7], [350, 800, 6], [300, 800, 35.452329995955] ] , \"161\": [ [300, 800, 35.452329995955], [300, 850, 27.326435670692], [350, 800, 6], [350, 800, 6], [350, 850, 7], [300, 850, 27.326435670692] ] , \"162\": [ [300, 850, 27.326435670692], [300, 900, 20.553660306981], [350, 850, 7], [350, 850, 7], [350, 900, 17.793365325891], [300, 900, 20.553660306981] ] , \"163\": [ [300, 900, 20.553660306981], [300, 950, 15.340484569495], [350, 900, 17.793365325891], [350, 900, 17.793365325891], [350, 950, 18.562503400789], [300, 950, 15.340484569495] ] , \"164\": [ [300, 950, 15.340484569495], [300, 1000, 14.657473356424], [350, 950, 18.562503400789], [350, 950, 18.562503400789], [350, 1000, 19.186820442236], [300, 1000, 14.657473356424] ] , \"165\": [ [300, 1000, 14.657473356424], [300, 1050, 14.316215267254], [350, 1000, 19.186820442236], [350, 1000, 19.186820442236], [350, 1050, 16.720169688638], [300, 1050, 14.316215267254] ] , \"166\": [ [300, 1050, 14.316215267254], [300, 1100, 22.334365939318], [350, 1050, 16.720169688638], [350, 1050, 16.720169688638], [350, 1100, 30.290250298404], [300, 1100, 22.334365939318] ] , \"167\": [ [300, 1100, 22.334365939318], [300, 1150, 22.60632871777], [350, 1100, 30.290250298404], [350, 1100, 30.290250298404], [350, 1150, 21.07698165183], [300, 1150, 22.60632871777] ] , \"168\": [ [300, 1150, 22.60632871777], [300, 1200, 34.46755151904], [350, 1150, 21.07698165183], [350, 1150, 21.07698165183], [350, 1200, 52.883620629547], [300, 1200, 34.46755151904] ] , \"169\": [ [350, 0, -1], [350, 50, -21.612711476909], [400, 0, 2], [400, 0, 2], [400, 50, 10.629096174364], [350, 50, -21.612711476909] ] , \"170\": [ [350, 50, -21.612711476909], [350, 100, -24.889193720469], [400, 50, 10.629096174364], [400, 50, 10.629096174364], [400, 100, -35.290936341005], [350, 100, -24.889193720469] ] , \"171\": [ [350, 100, -24.889193720469], [350, 150, 53.261596301377], [400, 100, -35.290936341005], [400, 100, -35.290936341005], [400, 150, -21.806177920033], [350, 150, 53.261596301377] ] , \"172\": [ [350, 150, 53.261596301377], [350, 200, 102.35434130976], [400, 150, -21.806177920033], [400, 150, -21.806177920033], [400, 200, 36.603253230368], [350, 200, 102.35434130976] ] , \"173\": [ [350, 200, 102.35434130976], [350, 250, 64.944181950583], [400, 200, 36.603253230368], [400, 200, 36.603253230368], [400, 250, 49.633925496904], [350, 250, 64.944181950583] ] , \"174\": [ [350, 250, 64.944181950583], [350, 300, 86.514662963527], [400, 250, 49.633925496904], [400, 250, 49.633925496904], [400, 300, 58.697590137005], [350, 300, 86.514662963527] ] , \"175\": [ [350, 300, 86.514662963527], [350, 350, 3], [400, 300, 58.697590137005], [400, 300, 58.697590137005], [400, 350, 1], [350, 350, 3] ] , \"176\": [ [350, 350, 3], [350, 400, 0], [400, 350, 1], [400, 350, 1], [400, 400, 3], [350, 400, 0] ] , \"177\": [ [350, 400, 0], [350, 450, -2], [400, 400, 3], [400, 400, 3], [400, 450, 9], [350, 450, -2] ] , \"178\": [ [350, 450, -2], [350, 500, -3], [400, 450, 9], [400, 450, 9], [400, 500, -3], [350, 500, -3] ] , \"179\": [ [350, 500, -3], [350, 550, -3], [400, 500, -3], [400, 500, -3], [400, 550, 6], [350, 550, -3] ] , \"180\": [ [350, 550, -3], [350, 600, 0], [400, 550, 6], [400, 550, 6], [400, 600, 5], [350, 600, 0] ] , \"181\": [ [350, 600, 0], [350, 650, 8], [400, 600, 5], [400, 600, 5], [400, 650, 6], [350, 650, 8] ] , \"182\": [ [350, 650, 8], [350, 700, 1], [400, 650, 6], [400, 650, 6], [400, 700, -3], [350, 700, 1] ] , \"183\": [ [350, 700, 1], [350, 750, 7], [400, 700, -3], [400, 700, -3], [400, 750, 6], [350, 750, 7] ] , \"184\": [ [350, 750, 7], [350, 800, 6], [400, 750, 6], [400, 750, 6], [400, 800, -4], [350, 800, 6] ] , \"185\": [ [350, 800, 6], [350, 850, 7], [400, 800, -4], [400, 800, -4], [400, 850, 2], [350, 850, 7] ] , \"186\": [ [350, 850, 7], [350, 900, 17.793365325891], [400, 850, 2], [400, 850, 2], [400, 900, 9.2644551086303], [350, 900, 17.793365325891] ] , \"187\": [ [350, 900, 17.793365325891], [350, 950, 18.562503400789], [400, 900, 9.2644551086303], [400, 900, 9.2644551086303], [400, 950, 10.20677461177], [350, 950, 18.562503400789] ] , \"188\": [ [350, 950, 18.562503400789], [350, 1000, 19.186820442236], [400, 950, 10.20677461177], [400, 950, 10.20677461177], [400, 1000, 15.318699484932], [350, 1000, 19.186820442236] ] , \"189\": [ [350, 1000, 19.186820442236], [350, 1050, 16.720169688638], [400, 1000, 15.318699484932], [400, 1000, 15.318699484932], [400, 1050, 23.741896538602], [350, 1050, 16.720169688638] ] , \"190\": [ [350, 1050, 16.720169688638], [350, 1100, 30.290250298404], [400, 1050, 23.741896538602], [400, 1050, 23.741896538602], [400, 1100, 34.584105508548], [350, 1100, 30.290250298404] ] , \"191\": [ [350, 1100, 30.290250298404], [350, 1150, 21.07698165183], [400, 1100, 34.584105508548], [400, 1100, 34.584105508548], [400, 1150, 26.317112486261], [350, 1150, 21.07698165183] ] , \"192\": [ [350, 1150, 21.07698165183], [350, 1200, 52.883620629547], [400, 1150, 26.317112486261], [400, 1150, 26.317112486261], [400, 1200, 60.092571589213], [350, 1200, 52.883620629547] ] , \"193\": [ [400, 0, 2], [400, 50, 10.629096174364], [450, 0, 1], [450, 0, 1], [450, 50, -11.790301275212], [400, 50, 10.629096174364] ] , \"194\": [ [400, 50, 10.629096174364], [400, 100, -35.290936341005], [450, 50, -11.790301275212], [450, 50, -11.790301275212], [450, 100, 0.84928618604887], [400, 100, -35.290936341005] ] , \"195\": [ [400, 100, -35.290936341005], [400, 150, -21.806177920033], [450, 100, 0.84928618604887], [450, 100, 0.84928618604887], [450, 150, 15.250723975004], [400, 150, -21.806177920033] ] , \"196\": [ [400, 150, -21.806177920033], [400, 200, 36.603253230368], [450, 150, 15.250723975004], [450, 150, 15.250723975004], [450, 200, 11.849266428446], [400, 200, 36.603253230368] ] , \"197\": [ [400, 200, 36.603253230368], [400, 250, 49.633925496904], [450, 200, 11.849266428446], [450, 200, 11.849266428446], [450, 250, 32.695481718573], [400, 250, 49.633925496904] ] , \"198\": [ [400, 250, 49.633925496904], [400, 300, 58.697590137005], [450, 250, 32.695481718573], [450, 250, 32.695481718573], [450, 300, 44.008999117494], [400, 300, 58.697590137005] ] , \"199\": [ [400, 300, 58.697590137005], [400, 350, 1], [450, 300, 44.008999117494], [450, 300, 44.008999117494], [450, 350, 2], [400, 350, 1] ] , \"200\": [ [400, 350, 1], [400, 400, 3], [450, 350, 2], [450, 350, 2], [450, 400, 8], [400, 400, 3] ] , \"201\": [ [400, 400, 3], [400, 450, 9], [450, 400, 8], [450, 400, 8], [450, 450, -3], [400, 450, 9] ] , \"202\": [ [400, 450, 9], [400, 500, -3], [450, 450, -3], [450, 450, -3], [450, 500, 8], [400, 500, -3] ] , \"203\": [ [400, 500, -3], [400, 550, 6], [450, 500, 8], [450, 500, 8], [450, 550, -1], [400, 550, 6] ] , \"204\": [ [400, 550, 6], [400, 600, 5], [450, 550, -1], [450, 550, -1], [450, 600, 3], [400, 600, 5] ] , \"205\": [ [400, 600, 5], [400, 650, 6], [450, 600, 3], [450, 600, 3], [450, 650, 6], [400, 650, 6] ] , \"206\": [ [400, 650, 6], [400, 700, -3], [450, 650, 6], [450, 650, 6], [450, 700, 1], [400, 700, -3] ] , \"207\": [ [400, 700, -3], [400, 750, 6], [450, 700, 1], [450, 700, 1], [450, 750, 5], [400, 750, 6] ] , \"208\": [ [400, 750, 6], [400, 800, -4], [450, 750, 5], [450, 750, 5], [450, 800, 2], [400, 800, -4] ] , \"209\": [ [400, 800, -4], [400, 850, 2], [450, 800, 2], [450, 800, 2], [450, 850, 0], [400, 850, 2] ] , \"210\": [ [400, 850, 2], [400, 900, 9.2644551086303], [450, 850, 0], [450, 850, 0], [450, 900, 16.754818369543], [400, 900, 9.2644551086303] ] , \"211\": [ [400, 900, 9.2644551086303], [400, 950, 10.20677461177], [450, 900, 16.754818369543], [450, 900, 16.754818369543], [450, 950, 6.0753493633146], [400, 950, 10.20677461177] ] , \"212\": [ [400, 950, 10.20677461177], [400, 1000, 15.318699484932], [450, 950, 6.0753493633146], [450, 950, 6.0753493633146], [450, 1000, 28.033607820006], [400, 1000, 15.318699484932] ] , \"213\": [ [400, 1000, 15.318699484932], [400, 1050, 23.741896538602], [450, 1000, 28.033607820006], [450, 1000, 28.033607820006], [450, 1050, 24.364734614513], [400, 1050, 23.741896538602] ] , \"214\": [ [400, 1050, 23.741896538602], [400, 1100, 34.584105508548], [450, 1050, 24.364734614513], [450, 1050, 24.364734614513], [450, 1100, 47.396912220554], [400, 1100, 34.584105508548] ] , \"215\": [ [400, 1100, 34.584105508548], [400, 1150, 26.317112486261], [450, 1100, 47.396912220554], [450, 1100, 47.396912220554], [450, 1150, 64.099376738454], [400, 1150, 26.317112486261] ] , \"216\": [ [400, 1150, 26.317112486261], [400, 1200, 60.092571589213], [450, 1150, 64.099376738454], [450, 1150, 64.099376738454], [450, 1200, 77.169686937976], [400, 1200, 60.092571589213] ] , \"217\": [ [450, 0, 1], [450, 50, -11.790301275212], [500, 0, 2], [500, 0, 2], [500, 50, 3.5698995749293], [450, 50, -11.790301275212] ] , \"218\": [ [450, 50, -11.790301275212], [450, 100, 0.84928618604887], [500, 50, 3.5698995749293], [500, 50, 3.5698995749293], [500, 100, 1.5429614952554], [450, 100, 0.84928618604887] ] , \"219\": [ [450, 100, 0.84928618604887], [450, 150, 15.250723975004], [500, 100, 1.5429614952554], [500, 100, 1.5429614952554], [500, 150, 22.380990552103], [450, 150, 15.250723975004] ] , \"220\": [ [450, 150, 15.250723975004], [450, 200, 11.849266428446], [500, 150, 22.380990552103], [500, 150, 22.380990552103], [500, 200, 19.826993651851], [450, 200, 11.849266428446] ] , \"221\": [ [450, 200, 11.849266428446], [450, 250, 32.695481718573], [500, 200, 19.826993651851], [500, 200, 19.826993651851], [500, 250, 13.95724726629], [450, 250, 32.695481718573] ] , \"222\": [ [450, 250, 32.695481718573], [450, 300, 44.008999117494], [500, 250, 13.95724726629], [500, 250, 13.95724726629], [500, 300, 23.553909367452], [450, 300, 44.008999117494] ] , \"223\": [ [450, 300, 44.008999117494], [450, 350, 2], [500, 300, 23.553909367452], [500, 300, 23.553909367452], [500, 350, 3], [450, 350, 2] ] , \"224\": [ [450, 350, 2], [450, 400, 8], [500, 350, 3], [500, 350, 3], [500, 400, 3], [450, 400, 8] ] , \"225\": [ [450, 400, 8], [450, 450, -3], [500, 400, 3], [500, 400, 3], [500, 450, 5], [450, 450, -3] ] , \"226\": [ [450, 450, -3], [450, 500, 8], [500, 450, 5], [500, 450, 5], [500, 500, 10], [450, 500, 8] ] , \"227\": [ [450, 500, 8], [450, 550, -1], [500, 500, 10], [500, 500, 10], [500, 550, 7], [450, 550, -1] ] , \"228\": [ [450, 550, -1], [450, 600, 3], [500, 550, 7], [500, 550, 7], [500, 600, -1], [450, 600, 3] ] , \"229\": [ [450, 600, 3], [450, 650, 6], [500, 600, -1], [500, 600, -1], [500, 650, 2], [450, 650, 6] ] , \"230\": [ [450, 650, 6], [450, 700, 1], [500, 650, 2], [500, 650, 2], [500, 700, -2], [450, 700, 1] ] , \"231\": [ [450, 700, 1], [450, 750, 5], [500, 700, -2], [500, 700, -2], [500, 750, 10], [450, 750, 5] ] , \"232\": [ [450, 750, 5], [450, 800, 2], [500, 750, 10], [500, 750, 10], [500, 800, -4], [450, 800, 2] ] , \"233\": [ [450, 800, 2], [450, 850, 0], [500, 800, -4], [500, 800, -4], [500, 850, 7], [450, 850, 0] ] , \"234\": [ [450, 850, 0], [450, 900, 16.754818369543], [500, 850, 7], [500, 850, 7], [500, 900, 15.918272789848], [450, 900, 16.754818369543] ] , \"235\": [ [450, 900, 16.754818369543], [450, 950, 6.0753493633146], [500, 900, 15.918272789848], [500, 900, 15.918272789848], [500, 950, 27.082813507569], [450, 950, 6.0753493633146] ] , \"236\": [ [450, 950, 6.0753493633146], [450, 1000, 28.033607820006], [500, 950, 27.082813507569], [500, 950, 27.082813507569], [500, 1000, 29.397256896963], [450, 1000, 28.033607820006] ] , \"237\": [ [450, 1000, 28.033607820006], [450, 1050, 24.364734614513], [500, 1000, 29.397256896963], [500, 1000, 29.397256896963], [500, 1050, 27.265199777161], [450, 1050, 24.364734614513] ] , \"238\": [ [450, 1050, 24.364734614513], [450, 1100, 47.396912220554], [500, 1050, 27.265199777161], [500, 1050, 27.265199777161], [500, 1100, 27.675615537409], [450, 1100, 47.396912220554] ] , \"239\": [ [450, 1100, 47.396912220554], [450, 1150, 64.099376738454], [500, 1100, 27.675615537409], [500, 1100, 27.675615537409], [500, 1150, 46.390634832139], [450, 1150, 64.099376738454] ] , \"240\": [ [450, 1150, 64.099376738454], [450, 1200, 77.169686937976], [500, 1150, 46.390634832139], [500, 1150, 46.390634832139], [500, 1200, 87.55323283619], [450, 1200, 77.169686937976] ] , \"241\": [ [500, 0, 2], [500, 50, 3.5698995749293], [550, 0, 0], [550, 0, 0], [550, 50, 9.8566331916431], [500, 50, 3.5698995749293] ] , \"242\": [ [500, 50, 3.5698995749293], [500, 100, 1.5429614952554], [550, 50, 9.8566331916431], [550, 50, 9.8566331916431], [550, 100, -13.343501912724], [500, 100, 1.5429614952554] ] , \"243\": [ [500, 100, 1.5429614952554], [500, 150, 22.380990552103], [550, 100, -13.343501912724], [550, 100, -13.343501912724], [550, 150, 3.5268167115446], [500, 150, 22.380990552103] ] , \"244\": [ [500, 150, 22.380990552103], [500, 200, 19.826993651851], [550, 150, 3.5268167115446], [550, 150, 3.5268167115446], [550, 200, 22.744933638499], [500, 200, 19.826993651851] ] , \"245\": [ [500, 200, 19.826993651851], [500, 250, 13.95724726629], [550, 200, 22.744933638499], [550, 200, 22.744933638499], [550, 250, 10.843058185547], [500, 250, 13.95724726629] ] , \"246\": [ [500, 250, 13.95724726629], [500, 300, 23.553909367452], [550, 250, 10.843058185547], [550, 250, 10.843058185547], [550, 300, 23.11807160643], [500, 300, 23.553909367452] ] , \"247\": [ [500, 300, 23.553909367452], [500, 350, 3], [550, 300, 23.11807160643], [550, 300, 23.11807160643], [550, 350, 1], [500, 350, 3] ] , \"248\": [ [500, 350, 3], [500, 400, 3], [550, 350, 1], [550, 350, 1], [550, 400, -2], [500, 400, 3] ] , \"249\": [ [500, 400, 3], [500, 450, 5], [550, 400, -2], [550, 400, -2], [550, 450, -2], [500, 450, 5] ] , \"250\": [ [500, 450, 5], [500, 500, 10], [550, 450, -2], [550, 450, -2], [550, 500, 0], [500, 500, 10] ] , \"251\": [ [500, 500, 10], [500, 550, 7], [550, 500, 0], [550, 500, 0], [550, 550, -5], [500, 550, 7] ] , \"252\": [ [500, 550, 7], [500, 600, -1], [550, 550, -5], [550, 550, -5], [550, 600, -1], [500, 600, -1] ] , \"253\": [ [500, 600, -1], [500, 650, 2], [550, 600, -1], [550, 600, -1], [550, 650, -5], [500, 650, 2] ] , \"254\": [ [500, 650, 2], [500, 700, -2], [550, 650, -5], [550, 650, -5], [550, 700, 4], [500, 700, -2] ] , \"255\": [ [500, 700, -2], [500, 750, 10], [550, 700, 4], [550, 700, 4], [550, 750, -3], [500, 750, 10] ] , \"256\": [ [500, 750, 10], [500, 800, -4], [550, 750, -3], [550, 750, -3], [550, 800, 10], [500, 800, -4] ] , \"257\": [ [500, 800, -4], [500, 850, 7], [550, 800, 10], [550, 800, 10], [550, 850, -3], [500, 850, 7] ] , \"258\": [ [500, 850, 7], [500, 900, 15.918272789848], [550, 850, -3], [550, 850, -3], [550, 900, 16.639424263283], [500, 900, 15.918272789848] ] , \"259\": [ [500, 900, 15.918272789848], [500, 950, 27.082813507569], [550, 900, 16.639424263283], [550, 900, 16.639424263283], [550, 950, 12.8801701869], [500, 950, 27.082813507569] ] , \"260\": [ [500, 950, 27.082813507569], [500, 1000, 29.397256896963], [550, 950, 12.8801701869], [550, 950, 12.8801701869], [550, 1000, 18.453413530477], [500, 1000, 29.397256896963] ] , \"261\": [ [500, 1000, 29.397256896963], [500, 1050, 27.265199777161], [550, 1000, 18.453413530477], [550, 1000, 18.453413530477], [550, 1050, 19.7052900682], [500, 1050, 27.265199777161] ] , \"262\": [ [500, 1050, 27.265199777161], [500, 1100, 27.675615537409], [550, 1050, 19.7052900682], [550, 1050, 19.7052900682], [550, 1100, 11.38203512759], [500, 1100, 27.675615537409] ] , \"263\": [ [500, 1100, 27.675615537409], [500, 1150, 46.390634832139], [550, 1100, 11.38203512759], [550, 1100, 11.38203512759], [550, 1150, 31.816095165713], [500, 1150, 46.390634832139] ] , \"264\": [ [500, 1150, 46.390634832139], [500, 1200, 87.55323283619], [550, 1150, 31.816095165713], [550, 1150, 31.816095165713], [550, 1200, 40.586654278014], [500, 1200, 87.55323283619] ] , \"265\": [ [550, 0, 0], [550, 50, 9.8566331916431], [600, 0, -3], [600, 0, -3], [600, 50, -5.047788936119], [550, 50, 9.8566331916431] ] , \"266\": [ [550, 50, 9.8566331916431], [550, 100, -13.343501912724], [600, 50, -5.047788936119], [600, 50, -5.047788936119], [600, 100, 18.821780780933], [550, 100, -13.343501912724] ] , \"267\": [ [550, 100, -13.343501912724], [550, 150, 3.5268167115446], [600, 100, 18.821780780933], [600, 100, 18.821780780933], [600, 150, 22.501698526585], [550, 150, 3.5268167115446] ] , \"268\": [ [550, 150, 3.5268167115446], [550, 200, 22.744933638499], [600, 150, 22.501698526585], [600, 150, 22.501698526585], [600, 200, 6.9244829588762], [550, 200, 22.744933638499] ] , \"269\": [ [550, 200, 22.744933638499], [550, 250, 10.843058185547], [600, 200, 6.9244829588762], [600, 200, 6.9244829588762], [600, 250, 19.337491594307], [550, 250, 10.843058185547] ] , \"270\": [ [550, 250, 10.843058185547], [550, 300, 23.11807160643], [600, 250, 19.337491594307], [600, 250, 19.337491594307], [600, 300, 22.766207128761], [550, 300, 23.11807160643] ] , \"271\": [ [550, 300, 23.11807160643], [550, 350, 1], [600, 300, 22.766207128761], [600, 300, 22.766207128761], [600, 350, 7], [550, 350, 1] ] , \"272\": [ [550, 350, 1], [550, 400, -2], [600, 350, 7], [600, 350, 7], [600, 400, 1], [550, 400, -2] ] , \"273\": [ [550, 400, -2], [550, 450, -2], [600, 400, 1], [600, 400, 1], [600, 450, 8], [550, 450, -2] ] , \"274\": [ [550, 450, -2], [550, 500, 0], [600, 450, 8], [600, 450, 8], [600, 500, -1], [550, 500, 0] ] , \"275\": [ [550, 500, 0], [550, 550, -5], [600, 500, -1], [600, 500, -1], [600, 550, 8], [550, 550, -5] ] , \"276\": [ [550, 550, -5], [550, 600, -1], [600, 550, 8], [600, 550, 8], [600, 600, 9], [550, 600, -1] ] , \"277\": [ [550, 600, -1], [550, 650, -5], [600, 600, 9], [600, 600, 9], [600, 650, -2], [550, 650, -5] ] , \"278\": [ [550, 650, -5], [550, 700, 4], [600, 650, -2], [600, 650, -2], [600, 700, 2], [550, 700, 4] ] , \"279\": [ [550, 700, 4], [550, 750, -3], [600, 700, 2], [600, 700, 2], [600, 750, 3], [550, 750, -3] ] , \"280\": [ [550, 750, -3], [550, 800, 10], [600, 750, 3], [600, 750, 3], [600, 800, 3], [550, 800, 10] ] , \"281\": [ [550, 800, 10], [550, 850, -3], [600, 800, 3], [600, 800, 3], [600, 850, -3], [550, 850, -3] ] , \"282\": [ [550, 850, -3], [550, 900, 16.639424263283], [600, 850, -3], [600, 850, -3], [600, 900, -6.4535252455725], [550, 900, 16.639424263283] ] , \"283\": [ [550, 900, 16.639424263283], [550, 950, 12.8801701869], [600, 900, -6.4535252455725], [600, 900, -6.4535252455725], [600, 950, 10.022023068203], [550, 950, 12.8801701869] ] , \"284\": [ [550, 950, 12.8801701869], [550, 1000, 18.453413530477], [600, 950, 10.022023068203], [600, 950, 10.022023068203], [600, 1000, 23.118535595193], [550, 1000, 18.453413530477] ] , \"285\": [ [550, 1000, 18.453413530477], [550, 1050, 19.7052900682], [600, 1000, 23.118535595193], [600, 1000, 23.118535595193], [600, 1050, 26.425746397957], [550, 1050, 19.7052900682] ] , \"286\": [ [550, 1050, 19.7052900682], [550, 1100, 11.38203512759], [600, 1050, 26.425746397957], [600, 1050, 26.425746397957], [600, 1100, 5.837690531249], [550, 1100, 11.38203512759] ] , \"287\": [ [550, 1100, 11.38203512759], [550, 1150, 31.816095165713], [600, 1100, 5.837690531249], [600, 1100, 5.837690531249], [600, 1150, 23.678606941517], [550, 1150, 31.816095165713] ] , \"288\": [ [550, 1150, 31.816095165713], [550, 1200, 40.586654278014], [600, 1150, 23.678606941517], [600, 1150, 23.678606941517], [600, 1200, 38.027118795081], [550, 1200, 40.586654278014] ] , \"289\": [ [600, 0, -3], [600, 50, -5.047788936119], [650, 0, 5], [650, 0, 5], [650, 50, 15.650737021294], [600, 50, -5.047788936119] ] , \"290\": [ [600, 50, -5.047788936119], [600, 100, 18.821780780933], [650, 50, 15.650737021294], [650, 50, 15.650737021294], [650, 100, 15.808242955369], [600, 100, 18.821780780933] ] , \"291\": [ [600, 100, 18.821780780933], [600, 150, 22.501698526585], [650, 100, 15.808242955369], [650, 100, 15.808242955369], [650, 150, 28.377240754296], [600, 150, 22.501698526585] ] , \"292\": [ [600, 150, 22.501698526585], [600, 200, 6.9244829588762], [650, 150, 28.377240754296], [650, 150, 28.377240754296], [650, 200, 13.434474079919], [600, 200, 6.9244829588762] ] , \"293\": [ [600, 200, 6.9244829588762], [600, 250, 19.337491594307], [650, 200, 13.434474079919], [650, 200, 13.434474079919], [650, 250, 23.232149544368], [600, 250, 19.337491594307] ] , \"294\": [ [600, 250, 19.337491594307], [600, 300, 22.766207128761], [650, 250, 23.232149544368], [650, 250, 23.232149544368], [650, 300, 15.945282755812], [600, 300, 22.766207128761] ] , \"295\": [ [600, 300, 22.766207128761], [600, 350, 7], [650, 300, 15.945282755812], [650, 300, 15.945282755812], [650, 350, -4], [600, 350, 7] ] , \"296\": [ [600, 350, 7], [600, 400, 1], [650, 350, -4], [650, 350, -4], [650, 400, 4], [600, 400, 1] ] , \"297\": [ [600, 400, 1], [600, 450, 8], [650, 400, 4], [650, 400, 4], [650, 450, 0], [600, 450, 8] ] , \"298\": [ [600, 450, 8], [600, 500, -1], [650, 450, 0], [650, 450, 0], [650, 500, 10], [600, 500, -1] ] , \"299\": [ [600, 500, -1], [600, 550, 8], [650, 500, 10], [650, 500, 10], [650, 550, 3], [600, 550, 8] ] , \"300\": [ [600, 550, 8], [600, 600, 9], [650, 550, 3], [650, 550, 3], [650, 600, -2], [600, 600, 9] ] , \"301\": [ [600, 600, 9], [600, 650, -2], [650, 600, -2], [650, 600, -2], [650, 650, -2], [600, 650, -2] ] , \"302\": [ [600, 650, -2], [600, 700, 2], [650, 650, -2], [650, 650, -2], [650, 700, 10], [600, 700, 2] ] , \"303\": [ [600, 700, 2], [600, 750, 3], [650, 700, 10], [650, 700, 10], [650, 750, -4], [600, 750, 3] ] , \"304\": [ [600, 750, 3], [600, 800, 3], [650, 750, -4], [650, 750, -4], [650, 800, 1], [600, 800, 3] ] , \"305\": [ [600, 800, 3], [600, 850, -3], [650, 800, 1], [650, 800, 1], [650, 850, -2], [600, 850, -3] ] , \"306\": [ [600, 850, -3], [600, 900, -6.4535252455725], [650, 850, -2], [650, 850, -2], [650, 900, -9.6511750818575], [600, 900, -6.4535252455725] ] , \"307\": [ [600, 900, -6.4535252455725], [600, 950, 10.022023068203], [650, 900, -9.6511750818575], [650, 900, -9.6511750818575], [650, 950, -2.0275590864089], [600, 950, 10.022023068203] ] , \"308\": [ [600, 950, 10.022023068203], [600, 1000, 23.118535595193], [650, 950, -2.0275590864089], [650, 950, -2.0275590864089], [650, 1000, 20.870999858996], [600, 1000, 23.118535595193] ] , \"309\": [ [600, 1000, 23.118535595193], [600, 1050, 26.425746397957], [650, 1000, 20.870999858996], [650, 1000, 20.870999858996], [650, 1050, 26.805093950715], [600, 1050, 26.425746397957] ] , \"310\": [ [600, 1050, 26.425746397957], [600, 1100, 5.837690531249], [650, 1050, 26.805093950715], [650, 1050, 26.805093950715], [650, 1100, 23.356176959974], [600, 1100, 5.837690531249] ] , \"311\": [ [600, 1100, 5.837690531249], [600, 1150, 23.678606941517], [650, 1100, 23.356176959974], [650, 1100, 23.356176959974], [650, 1150, 5.6241581442467], [600, 1150, 23.678606941517] ] , \"312\": [ [600, 1150, 23.678606941517], [600, 1200, 38.027118795081], [650, 1150, 5.6241581442467], [650, 1150, 5.6241581442467], [650, 1200, 37.609961293615], [600, 1200, 38.027118795081] ] , \"313\": [ [650, 0, 5], [650, 50, 15.650737021294], [700, 0, 4], [700, 0, 4], [700, 50, 20.216912340431], [650, 50, 15.650737021294] ] , \"314\": [ [650, 50, 15.650737021294], [650, 100, 15.808242955369], [700, 50, 20.216912340431], [700, 50, 20.216912340431], [700, 100, 18.558630772365], [650, 100, 15.808242955369] ] , \"315\": [ [650, 100, 15.808242955369], [650, 150, 28.377240754296], [700, 100, 18.558630772365], [700, 100, 18.558630772365], [700, 150, 12.748038160677], [650, 150, 28.377240754296] ] , \"316\": [ [650, 150, 28.377240754296], [650, 200, 13.434474079919], [700, 150, 12.748038160677], [700, 150, 12.748038160677], [700, 200, 17.18658433163], [650, 200, 13.434474079919] ] , \"317\": [ [650, 200, 13.434474079919], [650, 250, 23.232149544368], [700, 200, 17.18658433163], [700, 200, 17.18658433163], [700, 250, 14.617735985306], [650, 250, 23.232149544368] ] , \"318\": [ [650, 250, 23.232149544368], [650, 300, 15.945282755812], [700, 250, 14.617735985306], [700, 250, 14.617735985306], [700, 300, 21.265056095162], [650, 300, 15.945282755812] ] , \"319\": [ [650, 300, 15.945282755812], [650, 350, -4], [700, 300, 21.265056095162], [700, 300, 21.265056095162], [700, 350, 7], [650, 350, -4] ] , \"320\": [ [650, 350, -4], [650, 400, 4], [700, 350, 7], [700, 350, 7], [700, 400, -2], [650, 400, 4] ] , \"321\": [ [650, 400, 4], [650, 450, 0], [700, 400, -2], [700, 400, -2], [700, 450, -3], [650, 450, 0] ] , \"322\": [ [650, 450, 0], [650, 500, 10], [700, 450, -3], [700, 450, -3], [700, 500, -1], [650, 500, 10] ] , \"323\": [ [650, 500, 10], [650, 550, 3], [700, 500, -1], [700, 500, -1], [700, 550, 9], [650, 550, 3] ] , \"324\": [ [650, 550, 3], [650, 600, -2], [700, 550, 9], [700, 550, 9], [700, 600, 8], [650, 600, -2] ] , \"325\": [ [650, 600, -2], [650, 650, -2], [700, 600, 8], [700, 600, 8], [700, 650, -1], [650, 650, -2] ] , \"326\": [ [650, 650, -2], [650, 700, 10], [700, 650, -1], [700, 650, -1], [700, 700, 5], [650, 700, 10] ] , \"327\": [ [650, 700, 10], [650, 750, -4], [700, 700, 5], [700, 700, 5], [700, 750, -5], [650, 750, -4] ] , \"328\": [ [650, 750, -4], [650, 800, 1], [700, 750, -5], [700, 750, -5], [700, 800, -1], [650, 800, 1] ] , \"329\": [ [650, 800, 1], [650, 850, -2], [700, 800, -1], [700, 800, -1], [700, 850, 1], [650, 850, -2] ] , \"330\": [ [650, 850, -2], [650, 900, -9.6511750818575], [700, 850, 1], [700, 850, 1], [700, 900, 11.116274972714], [650, 900, -9.6511750818575] ] , \"331\": [ [650, 900, -9.6511750818575], [650, 950, -2.0275590864089], [700, 900, 11.116274972714], [700, 900, 11.116274972714], [700, 950, 7.3125136014826], [650, 950, -2.0275590864089] ] , \"332\": [ [650, 950, -2.0275590864089], [650, 1000, 20.870999858996], [700, 950, 7.3125136014826], [700, 950, 7.3125136014826], [700, 1000, 55.38531812469], [650, 1000, 20.870999858996] ] , \"333\": [ [650, 1000, 20.870999858996], [650, 1050, 26.805093950715], [700, 1000, 55.38531812469], [700, 1000, 55.38531812469], [700, 1050, 67.353803978134], [650, 1050, 26.805093950715] ] , \"334\": [ [650, 1050, 26.805093950715], [650, 1100, 23.356176959974], [700, 1050, 67.353803978134], [700, 1050, 67.353803978134], [700, 1100, 39.171691629608], [650, 1100, 23.356176959974] ] , \"335\": [ [650, 1100, 23.356176959974], [650, 1150, 5.6241581442467], [700, 1100, 39.171691629608], [700, 1100, 39.171691629608], [700, 1150, 3.2173422446094], [650, 1150, 5.6241581442467] ] , \"336\": [ [650, 1150, 5.6241581442467], [650, 1200, 37.609961293615], [700, 1150, 3.2173422446094], [700, 1150, 3.2173422446094], [700, 1200, 6.1504872274904], [650, 1200, 37.609961293615] ] , \"337\": [ [700, 0, 4], [700, 50, 20.216912340431], [750, 0, -4], [750, 0, -4], [750, 50, 10.738970780144], [700, 50, 20.216912340431] ] , \"338\": [ [700, 50, 20.216912340431], [700, 100, 18.558630772365], [750, 50, 10.738970780144], [750, 50, 10.738970780144], [750, 100, 23.504837964313], [700, 100, 18.558630772365] ] , \"339\": [ [700, 100, 18.558630772365], [700, 150, 12.748038160677], [750, 100, 23.504837964313], [750, 100, 23.504837964313], [750, 150, 12.270502299118], [700, 150, 12.748038160677] ] , \"340\": [ [700, 150, 12.748038160677], [700, 200, 17.18658433163], [750, 150, 12.270502299118], [750, 150, 12.270502299118], [750, 200, 15.735041597142], [700, 200, 17.18658433163] ] , \"341\": [ [700, 200, 17.18658433163], [700, 250, 14.617735985306], [750, 200, 15.735041597142], [750, 200, 15.735041597142], [750, 250, 11.179787304693], [700, 250, 14.617735985306] ] , \"342\": [ [700, 250, 14.617735985306], [700, 300, 21.265056095162], [750, 250, 11.179787304693], [750, 250, 11.179787304693], [750, 300, 14.18752646172], [700, 300, 21.265056095162] ] , \"343\": [ [700, 300, 21.265056095162], [700, 350, 7], [750, 300, 14.18752646172], [750, 300, 14.18752646172], [750, 350, 6], [700, 350, 7] ] , \"344\": [ [700, 350, 7], [700, 400, -2], [750, 350, 6], [750, 350, 6], [750, 400, 3], [700, 400, -2] ] , \"345\": [ [700, 400, -2], [700, 450, -3], [750, 400, 3], [750, 400, 3], [750, 450, -5], [700, 450, -3] ] , \"346\": [ [700, 450, -3], [700, 500, -1], [750, 450, -5], [750, 450, -5], [750, 500, 5], [700, 500, -1] ] , \"347\": [ [700, 500, -1], [700, 550, 9], [750, 500, 5], [750, 500, 5], [750, 550, 5], [700, 550, 9] ] , \"348\": [ [700, 550, 9], [700, 600, 8], [750, 550, 5], [750, 550, 5], [750, 600, 2], [700, 600, 8] ] , \"349\": [ [700, 600, 8], [700, 650, -1], [750, 600, 2], [750, 600, 2], [750, 650, 0], [700, 650, -1] ] , \"350\": [ [700, 650, -1], [700, 700, 5], [750, 650, 0], [750, 650, 0], [750, 700, 8], [700, 700, 5] ] , \"351\": [ [700, 700, 5], [700, 750, -5], [750, 700, 8], [750, 700, 8], [750, 750, 7], [700, 750, -5] ] , \"352\": [ [700, 750, -5], [700, 800, -1], [750, 750, 7], [750, 750, 7], [750, 800, 1], [700, 800, -1] ] , \"353\": [ [700, 800, -1], [700, 850, 1], [750, 800, 1], [750, 800, 1], [750, 850, -5], [700, 850, 1] ] , \"354\": [ [700, 850, 1], [700, 900, 11.116274972714], [750, 850, -5], [750, 850, -5], [750, 900, 20.372091657571], [700, 900, 11.116274972714] ] , \"355\": [ [700, 900, 11.116274972714], [700, 950, 7.3125136014826], [750, 900, 20.372091657571], [750, 900, 20.372091657571], [750, 950, 16.266960077256], [700, 950, 7.3125136014826] ] , \"356\": [ [700, 950, 7.3125136014826], [700, 1000, 55.38531812469], [750, 950, 16.266960077256], [750, 950, 16.266960077256], [750, 1000, 18.988263934476], [700, 1000, 55.38531812469] ] , \"357\": [ [700, 1000, 55.38531812469], [700, 1050, 67.353803978134], [750, 1000, 18.988263934476], [750, 1000, 18.988263934476], [750, 1050, 49.242462012433], [700, 1050, 67.353803978134] ] , \"358\": [ [700, 1050, 67.353803978134], [700, 1100, 39.171691629608], [750, 1050, 49.242462012433], [750, 1050, 49.242462012433], [750, 1100, 97.422652540058], [700, 1100, 39.171691629608] ] , \"359\": [ [700, 1100, 39.171691629608], [700, 1150, 3.2173422446094], [750, 1100, 97.422652540058], [750, 1100, 97.422652540058], [750, 1150, 48.937228804758], [700, 1150, 3.2173422446094] ] , \"360\": [ [700, 1150, 3.2173422446094], [700, 1200, 6.1504872274904], [750, 1150, 48.937228804758], [750, 1150, 48.937228804758], [750, 1200, 51.935019425619], [700, 1200, 6.1504872274904] ] , \"361\": [ [750, 0, -4], [750, 50, 10.738970780144], [800, 0, 3], [800, 0, 3], [800, 50, -3.7536764066188], [750, 50, 10.738970780144] ] , \"362\": [ [750, 50, 10.738970780144], [750, 100, 23.504837964313], [800, 50, -3.7536764066188], [800, 50, -3.7536764066188], [800, 100, 18.163377445946], [750, 100, 23.504837964313] ] , \"363\": [ [750, 100, 23.504837964313], [750, 150, 12.270502299118], [800, 100, 18.163377445946], [800, 100, 18.163377445946], [800, 150, 19.646239236459], [750, 150, 12.270502299118] ] , \"364\": [ [750, 150, 12.270502299118], [750, 200, 15.735041597142], [800, 150, 19.646239236459], [800, 150, 19.646239236459], [800, 200, 15.21726104424], [750, 200, 15.735041597142] ] , \"365\": [ [750, 200, 15.735041597142], [750, 250, 11.179787304693], [800, 200, 15.21726104424], [800, 200, 15.21726104424], [800, 250, 19.544029982025], [750, 250, 11.179787304693] ] , \"366\": [ [750, 250, 11.179787304693], [750, 300, 14.18752646172], [800, 250, 19.544029982025], [800, 250, 19.544029982025], [800, 300, 12.303781249479], [750, 300, 14.18752646172] ] , \"367\": [ [750, 300, 14.18752646172], [750, 350, 6], [800, 300, 12.303781249479], [800, 300, 12.303781249479], [800, 350, 1], [750, 350, 6] ] , \"368\": [ [750, 350, 6], [750, 400, 3], [800, 350, 1], [800, 350, 1], [800, 400, 0], [750, 400, 3] ] , \"369\": [ [750, 400, 3], [750, 450, -5], [800, 400, 0], [800, 400, 0], [800, 450, 10], [750, 450, -5] ] , \"370\": [ [750, 450, -5], [750, 500, 5], [800, 450, 10], [800, 450, 10], [800, 500, 5], [750, 500, 5] ] , \"371\": [ [750, 500, 5], [750, 550, 5], [800, 500, 5], [800, 500, 5], [800, 550, 3], [750, 550, 5] ] , \"372\": [ [750, 550, 5], [750, 600, 2], [800, 550, 3], [800, 550, 3], [800, 600, -5], [750, 600, 2] ] , \"373\": [ [750, 600, 2], [750, 650, 0], [800, 600, -5], [800, 600, -5], [800, 650, -1], [750, 650, 0] ] , \"374\": [ [750, 650, 0], [750, 700, 8], [800, 650, -1], [800, 650, -1], [800, 700, 8], [750, 700, 8] ] , \"375\": [ [750, 700, 8], [750, 750, 7], [800, 700, 8], [800, 700, 8], [800, 750, 1], [750, 750, 7] ] , \"376\": [ [750, 750, 7], [750, 800, 1], [800, 750, 1], [800, 750, 1], [800, 800, 6], [750, 800, 1] ] , \"377\": [ [750, 800, 1], [750, 850, -5], [800, 800, 6], [800, 800, 6], [800, 850, 7], [750, 850, -5] ] , \"378\": [ [750, 850, -5], [750, 900, 20.372091657571], [800, 850, 7], [800, 850, 7], [800, 900, -9.2093027808095], [750, 900, 20.372091657571] ] , \"379\": [ [750, 900, 20.372091657571], [750, 950, 16.266960077256], [800, 900, -9.2093027808095], [800, 900, -9.2093027808095], [800, 950, -1.8567503486607], [750, 950, 16.266960077256] ] , \"380\": [ [750, 950, 16.266960077256], [750, 1000, 18.988263934476], [800, 950, -1.8567503486607], [800, 950, -1.8567503486607], [800, 1000, -2.8671754456428], [750, 1000, 18.988263934476] ] , \"381\": [ [750, 1000, 18.988263934476], [750, 1050, 49.242462012433], [800, 1000, -2.8671754456428], [800, 1000, -2.8671754456428], [800, 1050, 36.954516833756], [750, 1050, 49.242462012433] ] , \"382\": [ [750, 1050, 49.242462012433], [750, 1100, 97.422652540058], [800, 1050, 36.954516833756], [800, 1050, 36.954516833756], [800, 1100, 89.206543795416], [750, 1100, 97.422652540058] ] , \"383\": [ [750, 1100, 97.422652540058], [750, 1150, 48.937228804758], [800, 1100, 89.206543795416], [800, 1100, 89.206543795416], [800, 1150, 81.022141713411], [750, 1150, 48.937228804758] ] , \"384\": [ [750, 1150, 48.937228804758], [750, 1200, 51.935019425619], [800, 1150, 81.022141713411], [800, 1150, 81.022141713411], [800, 1200, 52.631463314596], [750, 1200, 51.935019425619] ] , \"385\": [ [800, 0, 3], [800, 50, -3.7536764066188], [850, 0, -3], [850, 0, -3], [850, 50, 22.748774531127], [800, 50, -3.7536764066188] ] , \"386\": [ [800, 50, -3.7536764066188], [800, 100, 18.163377445946], [850, 50, 22.748774531127], [850, 50, 22.748774531127], [850, 100, 4.0528251901515], [800, 100, 18.163377445946] ] , \"387\": [ [800, 100, 18.163377445946], [800, 150, 19.646239236459], [850, 100, 4.0528251901515], [850, 100, 4.0528251901515], [850, 150, 15.954147290852], [800, 150, 19.646239236459] ] , \"388\": [ [800, 150, 19.646239236459], [800, 200, 15.21726104424], [850, 150, 15.954147290852], [850, 150, 15.954147290852], [850, 200, 24.439215857184], [800, 200, 15.21726104424] ] , \"389\": [ [800, 200, 15.21726104424], [800, 250, 19.544029982025], [850, 200, 24.439215857184], [850, 200, 24.439215857184], [850, 250, 23.066835627816], [800, 250, 19.544029982025] ] , \"390\": [ [800, 250, 19.544029982025], [800, 300, 12.303781249479], [850, 250, 23.066835627816], [850, 250, 23.066835627816], [850, 300, 17.971548953107], [800, 300, 12.303781249479] ] , \"391\": [ [800, 300, 12.303781249479], [800, 350, 1], [850, 300, 17.971548953107], [850, 300, 17.971548953107], [850, 350, -3], [800, 350, 1] ] , \"392\": [ [800, 350, 1], [800, 400, 0], [850, 350, -3], [850, 350, -3], [850, 400, 0], [800, 400, 0] ] , \"393\": [ [800, 400, 0], [800, 450, 10], [850, 400, 0], [850, 400, 0], [850, 450, 1], [800, 450, 10] ] , \"394\": [ [800, 450, 10], [800, 500, 5], [850, 450, 1], [850, 450, 1], [850, 500, 7], [800, 500, 5] ] , \"395\": [ [800, 500, 5], [800, 550, 3], [850, 500, 7], [850, 500, 7], [850, 550, 5], [800, 550, 3] ] , \"396\": [ [800, 550, 3], [800, 600, -5], [850, 550, 5], [850, 550, 5], [850, 600, 7], [800, 600, -5] ] , \"397\": [ [800, 600, -5], [800, 650, -1], [850, 600, 7], [850, 600, 7], [850, 650, -5], [800, 650, -1] ] , \"398\": [ [800, 650, -1], [800, 700, 8], [850, 650, -5], [850, 650, -5], [850, 700, -3], [800, 700, 8] ] , \"399\": [ [800, 700, 8], [800, 750, 1], [850, 700, -3], [850, 700, -3], [850, 750, 8], [800, 750, 1] ] , \"400\": [ [800, 750, 1], [800, 800, 6], [850, 750, 8], [850, 750, 8], [850, 800, -4], [800, 800, 6] ] , \"401\": [ [800, 800, 6], [800, 850, 7], [850, 800, -4], [850, 800, -4], [850, 850, 6], [800, 850, 7] ] , \"402\": [ [800, 850, 7], [800, 900, -9.2093027808095], [850, 850, 6], [850, 850, 6], [850, 900, -17.069767593603], [800, 900, -9.2093027808095] ] , \"403\": [ [800, 900, -9.2093027808095], [800, 950, -1.8567503486607], [850, 900, -17.069767593603], [850, 900, -17.069767593603], [850, 950, 22.621393092309], [800, 950, -1.8567503486607] ] , \"404\": [ [800, 950, -1.8567503486607], [800, 1000, -2.8671754456428], [850, 950, 22.621393092309], [850, 950, 22.621393092309], [850, 1000, -13.534177567332], [800, 1000, -2.8671754456428] ] , \"405\": [ [800, 1000, -2.8671754456428], [800, 1050, 36.954516833756], [850, 1000, -13.534177567332], [850, 1000, -13.534177567332], [850, 1050, 30.18438794026], [800, 1050, 36.954516833756] ] , \"406\": [ [800, 1050, 36.954516833756], [800, 1100, 89.206543795416], [850, 1050, 30.18438794026], [850, 1050, 30.18438794026], [850, 1100, 54.615149523144], [800, 1100, 89.206543795416] ] , \"407\": [ [800, 1100, 89.206543795416], [800, 1150, 81.022141713411], [850, 1100, 54.615149523144], [850, 1100, 54.615149523144], [850, 1150, 74.947945010657], [800, 1150, 81.022141713411] ] , \"408\": [ [800, 1150, 81.022141713411], [800, 1200, 52.631463314596], [850, 1150, 74.947945010657], [850, 1150, 74.947945010657], [850, 1200, 61.033850012888], [800, 1200, 52.631463314596] ] , \"409\": [ [850, 0, -3], [850, 50, 22.748774531127], [900, 0, -5], [900, 0, -5], [900, 50, 20.749591510376], [850, 50, 22.748774531127] ] , \"410\": [ [850, 50, 22.748774531127], [850, 100, 4.0528251901515], [900, 50, 20.749591510376], [900, 50, 20.749591510376], [900, 100, 21.183730410551], [850, 100, 4.0528251901515] ] , \"411\": [ [850, 100, 4.0528251901515], [850, 150, 15.954147290852], [900, 100, 21.183730410551], [900, 100, 21.183730410551], [900, 150, 17.230234297185], [850, 150, 15.954147290852] ] , \"412\": [ [850, 150, 15.954147290852], [850, 200, 24.439215857184], [900, 150, 17.230234297185], [900, 150, 17.230234297185], [900, 200, 20.87453248174], [850, 200, 24.439215857184] ] , \"413\": [ [850, 200, 24.439215857184], [850, 250, 23.066835627816], [900, 200, 20.87453248174], [900, 200, 20.87453248174], [900, 250, 25.46019465558], [850, 250, 23.066835627816] ] , \"414\": [ [850, 250, 23.066835627816], [850, 300, 17.971548953107], [900, 250, 25.46019465558], [900, 250, 25.46019465558], [900, 300, 22.166193078834], [850, 300, 17.971548953107] ] , \"415\": [ [850, 300, 17.971548953107], [850, 350, -3], [900, 300, 22.166193078834], [900, 300, 22.166193078834], [900, 350, 12.712580677314], [850, 350, -3] ] , \"416\": [ [850, 350, -3], [850, 400, 0], [900, 350, 12.712580677314], [900, 350, 12.712580677314], [900, 400, 5.5708602257712], [850, 400, 0] ] , \"417\": [ [850, 400, 0], [850, 450, 1], [900, 400, 5.5708602257712], [900, 400, 5.5708602257712], [900, 450, 11.190286741924], [850, 450, 1] ] , \"418\": [ [850, 450, 1], [850, 500, 7], [900, 450, 11.190286741924], [900, 450, 11.190286741924], [900, 500, 15.063428913975], [850, 500, 7] ] , \"419\": [ [850, 500, 7], [850, 550, 5], [900, 500, 15.063428913975], [900, 500, 15.063428913975], [900, 550, 10.687809637992], [850, 550, 5] ] , \"420\": [ [850, 550, 5], [850, 600, 7], [900, 550, 10.687809637992], [900, 550, 10.687809637992], [900, 600, -2.4373967873362], [850, 600, 7] ] , \"421\": [ [850, 600, 7], [850, 650, -5], [900, 600, -2.4373967873362], [900, 600, -2.4373967873362], [900, 650, -11.812465595779], [850, 650, -5] ] , \"422\": [ [850, 650, -5], [850, 700, -3], [900, 650, -11.812465595779], [900, 650, -11.812465595779], [900, 700, 6.7291781347404], [850, 700, -3] ] , \"423\": [ [850, 700, -3], [850, 750, 8], [900, 700, 6.7291781347404], [900, 700, 6.7291781347404], [900, 750, 39.909726044913], [850, 750, 8] ] , \"424\": [ [850, 750, 8], [850, 800, -4], [900, 750, 39.909726044913], [900, 750, 39.909726044913], [900, 800, 11.303242014971], [850, 800, -4] ] , \"425\": [ [850, 800, -4], [850, 850, 6], [900, 800, 11.303242014971], [900, 800, 11.303242014971], [900, 850, -13.898919328343], [850, 850, 6] ] , \"426\": [ [850, 850, 6], [850, 900, -17.069767593603], [900, 850, -13.898919328343], [900, 850, -13.898919328343], [900, 900, 15.677104359351], [850, 900, -17.069767593603] ] , \"427\": [ [850, 900, -17.069767593603], [850, 950, 22.621393092309], [900, 900, 15.677104359351], [900, 900, 15.677104359351], [900, 950, 11.409576619352], [850, 950, 22.621393092309] ] , \"428\": [ [850, 950, 22.621393092309], [850, 1000, -13.534177567332], [900, 950, 11.409576619352], [900, 950, 11.409576619352], [900, 1000, 6.8322640481099], [850, 1000, -13.534177567332] ] , \"429\": [ [850, 1000, -13.534177567332], [850, 1050, 30.18438794026], [900, 1000, 6.8322640481099], [900, 1000, 6.8322640481099], [900, 1050, 22.82749147368], [850, 1050, 30.18438794026] ] , \"430\": [ [850, 1050, 30.18438794026], [850, 1100, 54.615149523144], [900, 1050, 22.82749147368], [900, 1050, 22.82749147368], [900, 1100, 57.209009645695], [850, 1100, 54.615149523144] ] , \"431\": [ [850, 1100, 54.615149523144], [850, 1150, 74.947945010657], [900, 1100, 57.209009645695], [900, 1100, 57.209009645695], [900, 1150, 62.257368059832], [850, 1150, 74.947945010657] ] , \"432\": [ [850, 1150, 74.947945010657], [850, 1200, 61.033850012888], [900, 1150, 62.257368059832], [900, 1150, 62.257368059832], [900, 1200, 81.079721027792], [850, 1200, 61.033850012888] ] , \"433\": [ [900, 0, -5], [900, 50, 20.749591510376], [950, 0, -3], [950, 0, -3], [950, 50, 7.5831971701252], [900, 50, 20.749591510376] ] , \"434\": [ [900, 50, 20.749591510376], [900, 100, 21.183730410551], [950, 50, 7.5831971701252], [950, 50, 7.5831971701252], [950, 100, 25.005506363684], [900, 100, 21.183730410551] ] , \"435\": [ [900, 100, 21.183730410551], [900, 150, 17.230234297185], [950, 100, 25.005506363684], [950, 100, 25.005506363684], [950, 150, 19.80649035714], [900, 150, 17.230234297185] ] , \"436\": [ [900, 150, 17.230234297185], [900, 200, 20.87453248174], [950, 150, 19.80649035714], [950, 150, 19.80649035714], [950, 200, 18.137085712022], [900, 200, 20.87453248174] ] , \"437\": [ [900, 200, 20.87453248174], [900, 250, 25.46019465558], [950, 200, 18.137085712022], [950, 200, 18.137085712022], [950, 250, 21.490604283114], [900, 250, 25.46019465558] ] , \"438\": [ [900, 250, 25.46019465558], [900, 300, 22.166193078834], [950, 250, 21.490604283114], [950, 250, 21.490604283114], [950, 300, 23.705664005843], [900, 300, 22.166193078834] ] , \"439\": [ [900, 300, 22.166193078834], [900, 350, 12.712580677314], [950, 300, 23.705664005843], [950, 300, 23.705664005843], [950, 350, 22.528145920664], [900, 350, 12.712580677314] ] , \"440\": [ [900, 350, 12.712580677314], [900, 400, 5.5708602257712], [950, 350, 22.528145920664], [950, 350, 22.528145920664], [950, 400, 14.103862274583], [900, 400, 5.5708602257712] ] , \"441\": [ [900, 400, 5.5708602257712], [900, 450, 11.190286741924], [950, 400, 14.103862274583], [950, 400, 14.103862274583], [950, 450, 15.621669747426], [900, 450, 11.190286741924] ] , \"442\": [ [900, 450, 11.190286741924], [900, 500, 15.063428913975], [950, 450, 15.621669747426], [950, 450, 15.621669747426], [950, 500, 10.625128467775], [900, 500, 15.063428913975] ] , \"443\": [ [900, 500, 15.063428913975], [900, 550, 10.687809637992], [950, 500, 10.625128467775], [950, 500, 10.625128467775], [950, 550, 2.1254556732469], [900, 550, 10.687809637992] ] , \"444\": [ [900, 550, 10.687809637992], [900, 600, -2.4373967873362], [950, 550, 2.1254556732469], [950, 550, 2.1254556732469], [950, 600, 2.2919561746341], [900, 600, -2.4373967873362] ] , \"445\": [ [900, 600, -2.4373967873362], [900, 650, -11.812465595779], [950, 600, 2.2919561746341], [950, 600, 2.2919561746341], [950, 650, -2.6526354028269], [900, 650, -11.812465595779] ] , \"446\": [ [900, 650, -11.812465595779], [900, 700, 6.7291781347404], [950, 650, -2.6526354028269], [950, 650, -2.6526354028269], [950, 700, -10.078640954622], [900, 700, 6.7291781347404] ] , \"447\": [ [900, 700, 6.7291781347404], [900, 750, 39.909726044913], [950, 700, -10.078640954622], [950, 700, -10.078640954622], [950, 750, -1.1465789249893], [900, 750, 39.909726044913] ] , \"448\": [ [900, 750, 39.909726044913], [900, 800, 11.303242014971], [950, 750, -1.1465789249893], [950, 750, -1.1465789249893], [950, 800, 53.355463044965], [900, 800, 11.303242014971] ] , \"449\": [ [900, 800, 11.303242014971], [900, 850, -13.898919328343], [950, 800, 53.355463044965], [950, 800, 53.355463044965], [950, 850, 0.91992857719778], [900, 850, -13.898919328343] ] , \"450\": [ [900, 850, -13.898919328343], [900, 900, 15.677104359351], [950, 850, 0.91992857719778], [950, 850, 0.91992857719778], [950, 900, -16.433962130598], [900, 900, 15.677104359351] ] , \"451\": [ [900, 900, 15.677104359351], [900, 950, 11.409576619352], [950, 900, -16.433962130598], [950, 900, -16.433962130598], [950, 950, -19.782427050631], [900, 950, 11.409576619352] ] , \"452\": [ [900, 950, 11.409576619352], [900, 1000, 6.8322640481099], [950, 950, -19.782427050631], [950, 950, -19.782427050631], [950, 1000, -0.51352879438975], [900, 1000, 6.8322640481099] ] , \"453\": [ [900, 1000, 6.8322640481099], [900, 1050, 22.82749147368], [950, 1000, -0.51352879438975], [950, 1000, -0.51352879438975], [950, 1050, 9.7154089091332], [900, 1050, 22.82749147368] ] , \"454\": [ [900, 1050, 22.82749147368], [900, 1100, 57.209009645695], [950, 1050, 9.7154089091332], [950, 1050, 9.7154089091332], [950, 1100, 4.4173033428358], [900, 1100, 57.209009645695] ] , \"455\": [ [900, 1100, 57.209009645695], [900, 1150, 62.257368059832], [950, 1100, 4.4173033428358], [950, 1100, 4.4173033428358], [950, 1150, 17.294560349454], [900, 1150, 62.257368059832] ] , \"456\": [ [900, 1150, 62.257368059832], [900, 1200, 81.079721027792], [950, 1150, 17.294560349454], [950, 1150, 17.294560349454], [950, 1200, 56.710549812359], [900, 1200, 81.079721027792] ] , \"457\": [ [950, 0, -3], [950, 50, 7.5831971701252], [1000, 0, 8], [1000, 0, 8], [1000, 50, -0.30560094329159], [950, 50, 7.5831971701252] ] , \"458\": [ [950, 50, 7.5831971701252], [950, 100, 25.005506363684], [1000, 50, -0.30560094329159], [1000, 50, -0.30560094329159], [1000, 100, 12.094367530173], [950, 100, 25.005506363684] ] , \"459\": [ [950, 100, 25.005506363684], [950, 150, 19.80649035714], [1000, 100, 12.094367530173], [1000, 100, 12.094367530173], [1000, 150, 17.302121416999], [950, 150, 19.80649035714] ] , \"460\": [ [950, 150, 19.80649035714], [950, 200, 18.137085712022], [1000, 150, 17.302121416999], [1000, 150, 17.302121416999], [1000, 200, 18.415232495387], [950, 200, 18.137085712022] ] , \"461\": [ [950, 200, 18.137085712022], [950, 250, 21.490604283114], [1000, 200, 18.415232495387], [1000, 200, 18.415232495387], [1000, 250, 19.514307496841], [950, 250, 21.490604283114] ] , \"462\": [ [950, 250, 21.490604283114], [950, 300, 23.705664005843], [1000, 250, 19.514307496841], [1000, 250, 19.514307496841], [1000, 300, 19.570191928599], [950, 300, 23.705664005843] ] , \"463\": [ [950, 300, 23.705664005843], [950, 350, 22.528145920664], [1000, 300, 19.570191928599], [1000, 300, 19.570191928599], [1000, 350, 24.934667285035], [950, 350, 22.528145920664] ] , \"464\": [ [950, 350, 22.528145920664], [950, 400, 14.103862274583], [1000, 350, 24.934667285035], [1000, 350, 24.934667285035], [1000, 400, 14.522225160094], [950, 400, 14.103862274583] ] , \"465\": [ [950, 400, 14.103862274583], [950, 450, 15.621669747426], [1000, 400, 14.522225160094], [1000, 400, 14.522225160094], [1000, 450, 30.582585727368], [950, 450, 15.621669747426] ] , \"466\": [ [950, 450, 15.621669747426], [950, 500, 10.625128467775], [1000, 450, 30.582585727368], [1000, 450, 30.582585727368], [1000, 500, 40.943127980856], [950, 500, 10.625128467775] ] , \"467\": [ [950, 500, 10.625128467775], [950, 550, 2.1254556732469], [1000, 500, 40.943127980856], [1000, 500, 40.943127980856], [1000, 550, 9.7312373739592], [950, 550, 2.1254556732469] ] , \"468\": [ [950, 550, 2.1254556732469], [950, 600, 2.2919561746341], [1000, 550, 9.7312373739592], [1000, 550, 9.7312373739592], [1000, 600, 2.0495497406134], [950, 600, 2.2919561746341] ] , \"469\": [ [950, 600, 2.2919561746341], [950, 650, -2.6526354028269], [1000, 600, 2.0495497406134], [1000, 600, 2.0495497406134], [1000, 650, 2.0629568374735], [950, 650, -2.6526354028269] ] , \"470\": [ [950, 650, -2.6526354028269], [950, 700, -10.078640954622], [1000, 650, 2.0629568374735], [1000, 650, 2.0629568374735], [1000, 700, 19.777226826675], [950, 700, -10.078640954622] ] , \"471\": [ [950, 700, -10.078640954622], [950, 750, -1.1465789249893], [1000, 700, 19.777226826675], [1000, 700, 19.777226826675], [1000, 750, -8.1493310176454], [950, 750, -1.1465789249893] ] , \"472\": [ [950, 750, -1.1465789249893], [950, 800, 53.355463044965], [1000, 750, -8.1493310176454], [1000, 750, -8.1493310176454], [1000, 800, 34.686517700777], [950, 800, 53.355463044965] ] , \"473\": [ [950, 800, 53.355463044965], [950, 850, 0.91992857719778], [1000, 800, 34.686517700777], [1000, 800, 34.686517700777], [1000, 850, 55.653969774313], [950, 850, 0.91992857719778] ] , \"474\": [ [950, 850, 0.91992857719778], [950, 900, -16.433962130598], [1000, 850, 55.653969774313], [1000, 850, 55.653969774313], [1000, 900, -0.62002125969565], [950, 900, -16.433962130598] ] , \"475\": [ [950, 900, -16.433962130598], [950, 950, -19.782427050631], [1000, 900, -0.62002125969565], [1000, 900, -0.62002125969565], [1000, 950, -7.2788034803083], [950, 950, -19.782427050631] ] , \"476\": [ [950, 950, -19.782427050631], [950, 1000, -0.51352879438975], [1000, 950, -7.2788034803083], [1000, 950, -7.2788034803083], [1000, 1000, -22.52491977511], [950, 1000, -0.51352879438975] ] , \"477\": [ [950, 1000, -0.51352879438975], [950, 1050, 9.7154089091332], [1000, 1000, -22.52491977511], [1000, 1000, -22.52491977511], [1000, 1050, 60.725653446545], [950, 1050, 9.7154089091332] ] , \"478\": [ [950, 1050, 9.7154089091332], [950, 1100, 4.4173033428358], [1000, 1050, 60.725653446545], [1000, 1050, 60.725653446545], [1000, 1100, -2.0472114338288], [950, 1100, 4.4173033428358] ] , \"479\": [ [950, 1100, 4.4173033428358], [950, 1150, 17.294560349454], [1000, 1100, -2.0472114338288], [1000, 1100, -2.0472114338288], [1000, 1150, -9.2784492471796], [950, 1150, 17.294560349454] ] , \"480\": [ [950, 1150, 17.294560349454], [950, 1200, 56.710549812359], [1000, 1150, -9.2784492471796], [1000, 1150, -9.2784492471796], [1000, 1200, 11.575553638211], [950, 1200, 56.710549812359] ] , \"481\": [ [1000, 0, 8], [1000, 50, -0.30560094329159], [1050, 0, 10], [1050, 0, 10], [1050, 50, 6.5647996855695], [1000, 50, -0.30560094329159] ] , \"482\": [ [1000, 50, -0.30560094329159], [1000, 100, 12.094367530173], [1050, 50, 6.5647996855695], [1050, 50, 6.5647996855695], [1050, 100, 5.6178554241502], [1000, 100, 12.094367530173] ] , \"483\": [ [1000, 100, 12.094367530173], [1000, 150, 17.302121416999], [1050, 100, 5.6178554241502], [1050, 100, 5.6178554241502], [1050, 150, 11.671448123774], [1000, 150, 17.302121416999] ] , \"484\": [ [1000, 150, 17.302121416999], [1000, 200, 18.415232495387], [1050, 150, 11.671448123774], [1050, 150, 11.671448123774], [1050, 200, 14.12960067872], [1000, 200, 18.415232495387] ] , \"485\": [ [1000, 200, 18.415232495387], [1000, 250, 19.514307496841], [1050, 200, 14.12960067872], [1050, 200, 14.12960067872], [1050, 250, 19.019713556983], [1000, 250, 19.514307496841] ] , \"486\": [ [1000, 250, 19.514307496841], [1000, 300, 19.570191928599], [1050, 250, 19.019713556983], [1050, 250, 19.019713556983], [1050, 300, 16.368070994141], [1000, 300, 19.570191928599] ] , \"487\": [ [1000, 300, 19.570191928599], [1000, 350, 24.934667285035], [1050, 300, 16.368070994141], [1050, 300, 16.368070994141], [1050, 350, 17.624310069258], [1000, 350, 24.934667285035] ] , \"488\": [ [1000, 350, 24.934667285035], [1000, 400, 14.522225160094], [1050, 350, 17.624310069258], [1050, 350, 17.624310069258], [1050, 400, 21.527067504796], [1000, 400, 14.522225160094] ] , \"489\": [ [1000, 400, 14.522225160094], [1000, 450, 30.582585727368], [1050, 400, 21.527067504796], [1050, 400, 21.527067504796], [1050, 450, 32.210626130752], [1000, 450, 30.582585727368] ] , \"490\": [ [1000, 450, 30.582585727368], [1000, 500, 40.943127980856], [1050, 450, 32.210626130752], [1050, 450, 32.210626130752], [1050, 500, 31.078779946325], [1000, 500, 40.943127980856] ] , \"491\": [ [1000, 500, 40.943127980856], [1000, 550, 9.7312373739592], [1050, 500, 31.078779946325], [1050, 500, 31.078779946325], [1050, 550, 36.584381767047], [1000, 550, 9.7312373739592] ] , \"492\": [ [1000, 550, 9.7312373739592], [1000, 600, 2.0495497406134], [1050, 550, 36.584381767047], [1050, 550, 36.584381767047], [1050, 600, 1.1217229605398], [1000, 600, 2.0495497406134] ] , \"493\": [ [1000, 600, 2.0495497406134], [1000, 650, 2.0629568374735], [1050, 600, 1.1217229605398], [1050, 600, 1.1217229605398], [1050, 650, 21.744743179542], [1000, 650, 2.0629568374735] ] , \"494\": [ [1000, 650, 2.0629568374735], [1000, 700, 19.777226826675], [1050, 650, 21.744743179542], [1050, 650, 21.744743179542], [1050, 700, 62.194975614564], [1000, 700, 19.777226826675] ] , \"495\": [ [1000, 700, 19.777226826675], [1000, 750, -8.1493310176454], [1050, 700, 62.194975614564], [1050, 700, 62.194975614564], [1050, 750, 18.607623807864], [1000, 750, -8.1493310176454] ] , \"496\": [ [1000, 750, -8.1493310176454], [1000, 800, 34.686517700777], [1050, 750, 18.607623807864], [1050, 750, 18.607623807864], [1050, 800, 12.881603496999], [1000, 800, 34.686517700777] ] , \"497\": [ [1000, 800, 34.686517700777], [1000, 850, 55.653969774313], [1050, 800, 12.881603496999], [1050, 800, 12.881603496999], [1050, 850, 71.740696990696], [1000, 850, 55.653969774313] ] , \"498\": [ [1000, 850, 55.653969774313], [1000, 900, -0.62002125969565], [1050, 850, 71.740696990696], [1050, 850, 71.740696990696], [1050, 900, 27.258215168438], [1000, 900, -0.62002125969565] ] , \"499\": [ [1000, 900, -0.62002125969565], [1000, 950, -7.2788034803083], [1050, 900, 27.258215168438], [1050, 900, 27.258215168438], [1050, 950, 51.786463476145], [1000, 950, -7.2788034803083] ] , \"500\": [ [1000, 950, -7.2788034803083], [1000, 1000, -22.52491977511], [1050, 950, 51.786463476145], [1050, 950, 51.786463476145], [1050, 1000, 10.160913406909], [1000, 1000, -22.52491977511] ] , \"501\": [ [1000, 1000, -22.52491977511], [1000, 1050, 60.725653446545], [1050, 1000, 10.160913406909], [1050, 1000, 10.160913406909], [1050, 1050, -7.8794509738855], [1000, 1050, 60.725653446545] ] , \"502\": [ [1000, 1050, 60.725653446545], [1000, 1100, -2.0472114338288], [1050, 1050, -7.8794509738855], [1050, 1050, -7.8794509738855], [1050, 1100, 51.766330346277], [1000, 1100, -2.0472114338288] ] , \"503\": [ [1000, 1100, -2.0472114338288], [1000, 1150, -9.2784492471796], [1050, 1100, 51.766330346277], [1050, 1100, 51.766330346277], [1050, 1150, 40.146889888423], [1000, 1150, -9.2784492471796] ] , \"504\": [ [1000, 1150, -9.2784492471796], [1000, 1200, 11.575553638211], [1050, 1150, 40.146889888423], [1050, 1150, 40.146889888423], [1050, 1200, 84.147998093151], [1000, 1200, 11.575553638211] ] , \"505\": [ [1050, 0, 10], [1050, 50, 6.5647996855695], [1100, 0, -2], [1100, 0, -2], [1100, 50, 3.5215998951898], [1050, 50, 6.5647996855695] ] , \"506\": [ [1050, 50, 6.5647996855695], [1050, 100, 5.6178554241502], [1100, 50, 3.5215998951898], [1100, 50, 3.5215998951898], [1100, 100, 5.2347516683031], [1050, 100, 5.6178554241502] ] , \"507\": [ [1050, 100, 5.6178554241502], [1050, 150, 11.671448123774], [1100, 100, 5.2347516683031], [1100, 100, 5.2347516683031], [1100, 150, 8.0080184054091], [1050, 150, 11.671448123774] ] , \"508\": [ [1050, 150, 11.671448123774], [1050, 200, 14.12960067872], [1100, 150, 8.0080184054091], [1100, 150, 8.0080184054091], [1100, 200, 12.269689069301], [1050, 200, 14.12960067872] ] , \"509\": [ [1050, 200, 14.12960067872], [1050, 250, 19.019713556983], [1100, 200, 12.269689069301], [1100, 200, 12.269689069301], [1100, 250, 15.139667768335], [1050, 250, 19.019713556983] ] , \"510\": [ [1050, 250, 19.019713556983], [1050, 300, 16.368070994141], [1100, 250, 15.139667768335], [1100, 250, 15.139667768335], [1100, 300, 20.842484106486], [1050, 300, 16.368070994141] ] , \"511\": [ [1050, 300, 16.368070994141], [1050, 350, 17.624310069258], [1100, 300, 20.842484106486], [1100, 300, 20.842484106486], [1100, 350, 16.611621723295], [1050, 350, 17.624310069258] ] , \"512\": [ [1050, 350, 17.624310069258], [1050, 400, 21.527067504796], [1100, 350, 16.611621723295], [1100, 350, 16.611621723295], [1100, 400, 24.58766643245], [1050, 400, 21.527067504796] ] , \"513\": [ [1050, 400, 21.527067504796], [1050, 450, 32.210626130752], [1100, 400, 24.58766643245], [1100, 400, 24.58766643245], [1100, 450, 16.775120022666], [1050, 450, 32.210626130752] ] , \"514\": [ [1050, 450, 32.210626130752], [1050, 500, 31.078779946325], [1100, 450, 16.775120022666], [1100, 450, 16.775120022666], [1100, 500, 21.354842033248], [1050, 500, 31.078779946325] ] , \"515\": [ [1050, 500, 31.078779946325], [1050, 550, 36.584381767047], [1100, 500, 21.354842033248], [1100, 500, 21.354842033248], [1100, 550, 49.17266791554], [1050, 550, 36.584381767047] ] , \"516\": [ [1050, 550, 36.584381767047], [1050, 600, 1.1217229605398], [1100, 550, 49.17266791554], [1100, 550, 49.17266791554], [1100, 600, 28.959590881042], [1050, 600, 1.1217229605398] ] , \"517\": [ [1050, 600, 1.1217229605398], [1050, 650, 21.744743179542], [1100, 600, 28.959590881042], [1100, 600, 28.959590881042], [1100, 650, 33.775352340375], [1050, 650, 21.744743179542] ] , \"518\": [ [1050, 650, 21.744743179542], [1050, 700, 62.194975614564], [1100, 650, 33.775352340375], [1100, 650, 33.775352340375], [1100, 700, 45.238357044827], [1050, 700, 62.194975614564] ] , \"519\": [ [1050, 700, 62.194975614564], [1050, 750, 18.607623807864], [1100, 700, 45.238357044827], [1100, 700, 45.238357044827], [1100, 750, 22.513652155752], [1050, 750, 18.607623807864] ] , \"520\": [ [1050, 750, 18.607623807864], [1050, 800, 12.881603496999], [1100, 750, 22.513652155752], [1100, 750, 22.513652155752], [1100, 800, 13.334293153538], [1050, 800, 12.881603496999] ] , \"521\": [ [1050, 800, 12.881603496999], [1050, 850, 71.740696990696], [1100, 800, 13.334293153538], [1100, 800, 13.334293153538], [1100, 850, 62.652197880411], [1050, 850, 71.740696990696] ] , \"522\": [ [1050, 850, 71.740696990696], [1050, 900, 27.258215168438], [1100, 850, 62.652197880411], [1100, 850, 62.652197880411], [1100, 900, 83.217036679848], [1050, 900, 27.258215168438] ] , \"523\": [ [1050, 900, 27.258215168438], [1050, 950, 51.786463476145], [1100, 900, 83.217036679848], [1100, 900, 83.217036679848], [1100, 950, 71.087238441477], [1050, 950, 51.786463476145] ] , \"524\": [ [1050, 950, 51.786463476145], [1050, 1000, 10.160913406909], [1100, 950, 71.087238441477], [1100, 950, 71.087238441477], [1100, 1000, 20.344871774843], [1050, 1000, 10.160913406909] ] , \"525\": [ [1050, 1000, 10.160913406909], [1050, 1050, -7.8794509738855], [1100, 1000, 20.344871774843], [1100, 1000, 20.344871774843], [1100, 1050, -17.791221930711], [1050, 1050, -7.8794509738855] ] , \"526\": [ [1050, 1050, -7.8794509738855], [1050, 1100, 51.766330346277], [1100, 1050, -17.791221930711], [1100, 1050, -17.791221930711], [1100, 1100, -14.634780852773], [1050, 1100, 51.766330346277] ] , \"527\": [ [1050, 1100, 51.766330346277], [1050, 1150, 40.146889888423], [1100, 1100, -14.634780852773], [1100, 1100, -14.634780852773], [1100, 1150, -9.2405202060246], [1050, 1150, 40.146889888423] ] , \"528\": [ [1050, 1150, 40.146889888423], [1050, 1200, 84.147998093151], [1100, 1150, -9.2405202060246], [1100, 1150, -9.2405202060246], [1100, 1200, 86.01812259185], [1050, 1200, 84.147998093151] ] , \"529\": [ [1100, 0, -2], [1100, 50, 3.5215998951898], [1150, 0, 6], [1150, 0, 6], [1150, 50, 2.5071999650633], [1100, 50, 3.5215998951898] ] , \"530\": [ [1100, 50, 3.5215998951898], [1100, 100, 5.2347516683031], [1150, 50, 2.5071999650633], [1150, 50, 2.5071999650633], [1150, 100, 6.9211838428521], [1100, 100, 5.2347516683031] ] , \"531\": [ [1100, 100, 5.2347516683031], [1100, 150, 8.0080184054091], [1150, 100, 6.9211838428521], [1150, 100, 6.9211838428521], [1150, 150, 7.3879846388548], [1100, 150, 8.0080184054091] ] , \"532\": [ [1100, 150, 8.0080184054091], [1100, 200, 12.269689069301], [1150, 150, 7.3879846388548], [1150, 150, 7.3879846388548], [1150, 200, 4.7218973711883], [1100, 200, 12.269689069301] ] , \"533\": [ [1100, 200, 12.269689069301], [1100, 250, 15.139667768335], [1150, 200, 4.7218973711883], [1150, 200, 4.7218973711883], [1150, 250, 10.043751402941], [1100, 250, 15.139667768335] ] , \"534\": [ [1100, 250, 15.139667768335], [1100, 300, 20.842484106486], [1150, 250, 10.043751402941], [1150, 250, 10.043751402941], [1150, 300, 28.675301092587], [1100, 300, 20.842484106486] ] , \"535\": [ [1100, 300, 20.842484106486], [1100, 350, 16.611621723295], [1150, 300, 28.675301092587], [1150, 300, 28.675301092587], [1150, 350, 20.04313564079], [1100, 350, 16.611621723295] ] , \"536\": [ [1100, 350, 16.611621723295], [1100, 400, 24.58766643245], [1150, 350, 20.04313564079], [1150, 350, 20.04313564079], [1150, 400, 11.080807932178], [1100, 400, 24.58766643245] ] , \"537\": [ [1100, 400, 24.58766643245], [1100, 450, 16.775120022666], [1150, 400, 11.080807932178], [1150, 400, 11.080807932178], [1150, 450, 42.814531462431], [1100, 450, 16.775120022666] ] , \"538\": [ [1100, 450, 16.775120022666], [1100, 500, 21.354842033248], [1150, 450, 42.814531462431], [1150, 450, 42.814531462431], [1150, 500, 25.481497839448], [1100, 500, 21.354842033248] ] , \"539\": [ [1100, 500, 21.354842033248], [1100, 550, 49.17266791554], [1150, 500, 25.481497839448], [1150, 500, 25.481497839448], [1150, 550, 60.336335929412], [1100, 550, 49.17266791554] ] , \"540\": [ [1100, 550, 49.17266791554], [1100, 600, 28.959590881042], [1150, 550, 60.336335929412], [1150, 550, 60.336335929412], [1150, 600, 55.322864908665], [1100, 600, 28.959590881042] ] , \"541\": [ [1100, 600, 28.959590881042], [1100, 650, 33.775352340375], [1150, 600, 55.322864908665], [1150, 600, 55.322864908665], [1150, 650, 23.352602710027], [1100, 650, 33.775352340375] ] , \"542\": [ [1100, 650, 33.775352340375], [1100, 700, 45.238357044827], [1150, 650, 23.352602710027], [1150, 650, 23.352602710027], [1150, 700, 34.122104031743], [1100, 700, 45.238357044827] ] , \"543\": [ [1100, 700, 45.238357044827], [1100, 750, 22.513652155752], [1150, 700, 34.122104031743], [1150, 700, 34.122104031743], [1150, 750, 24.624704410774], [1100, 750, 22.513652155752] ] , \"544\": [ [1100, 750, 22.513652155752], [1100, 800, 13.334293153538], [1150, 750, 24.624704410774], [1150, 750, 24.624704410774], [1150, 800, 5.1575499066879], [1100, 800, 13.334293153538] ] , \"545\": [ [1100, 800, 13.334293153538], [1100, 850, 62.652197880411], [1150, 800, 5.1575499066879], [1150, 800, 5.1575499066879], [1150, 850, 64.381346980212], [1100, 850, 62.652197880411] ] , \"546\": [ [1100, 850, 62.652197880411], [1100, 900, 83.217036679848], [1150, 850, 64.381346980212], [1150, 850, 64.381346980212], [1150, 900, 55.916860513491], [1100, 900, 83.217036679848] ] , \"547\": [ [1100, 900, 83.217036679848], [1100, 950, 71.087238441477], [1150, 900, 55.916860513491], [1150, 900, 55.916860513491], [1150, 950, 97.073711878272], [1100, 950, 71.087238441477] ] , \"548\": [ [1100, 950, 71.087238441477], [1100, 1000, 20.344871774843], [1150, 950, 97.073711878272], [1150, 950, 97.073711878272], [1150, 1000, 94.501940698197], [1100, 1000, 20.344871774843] ] , \"549\": [ [1100, 1000, 20.344871774843], [1100, 1050, -17.791221930711], [1150, 1000, 94.501940698197], [1150, 1000, 94.501940698197], [1150, 1050, 9.0185301807766], [1100, 1050, -17.791221930711] ] , \"550\": [ [1100, 1050, -17.791221930711], [1100, 1100, -14.634780852773], [1150, 1050, 9.0185301807766], [1150, 1050, 9.0185301807766], [1150, 1100, -7.8024908675692], [1100, 1100, -14.634780852773] ] , \"551\": [ [1100, 1100, -14.634780852773], [1100, 1150, -9.2405202060246], [1150, 1100, -7.8024908675692], [1150, 1100, -7.8024908675692], [1150, 1150, 15.107402691211], [1100, 1150, -9.2405202060246] ] , \"552\": [ [1100, 1150, -9.2405202060246], [1100, 1200, 86.01812259185], [1150, 1150, 15.107402691211], [1150, 1150, 15.107402691211], [1150, 1200, 38.295001692345], [1100, 1200, 86.01812259185] ] , \"553\": [ [1150, 0, 6], [1150, 50, 2.5071999650633], [1200, 0, 9], [1200, 0, 9], [1200, 50, 5.5023999883544], [1150, 50, 2.5071999650633] ] , \"554\": [ [1150, 50, 2.5071999650633], [1150, 100, 6.9211838428521], [1200, 50, 5.5023999883544], [1200, 50, 5.5023999883544], [1200, 100, 6.9769279320899], [1150, 100, 6.9211838428521] ] , \"555\": [ [1150, 100, 6.9211838428521], [1150, 150, 7.3879846388548], [1200, 100, 6.9769279320899], [1200, 100, 6.9769279320899], [1200, 150, 12.595365471266], [1150, 150, 7.3879846388548] ] , \"556\": [ [1150, 150, 7.3879846388548], [1150, 200, 4.7218973711883], [1200, 150, 12.595365471266], [1200, 150, 12.595365471266], [1200, 200, 12.23508249377], [1150, 200, 4.7218973711883] ] , \"557\": [ [1150, 200, 4.7218973711883], [1150, 250, 10.043751402941], [1200, 200, 12.23508249377], [1200, 200, 12.23508249377], [1200, 250, 19.000243755966], [1150, 250, 10.043751402941] ] , \"558\": [ [1150, 250, 10.043751402941], [1150, 300, 28.675301092587], [1200, 250, 19.000243755966], [1200, 250, 19.000243755966], [1200, 300, 17.239765417165], [1150, 300, 28.675301092587] ] , \"559\": [ [1150, 300, 28.675301092587], [1150, 350, 20.04313564079], [1200, 300, 17.239765417165], [1200, 300, 17.239765417165], [1200, 350, 21.986067383514], [1150, 350, 20.04313564079] ] , \"560\": [ [1150, 350, 20.04313564079], [1150, 400, 11.080807932178], [1200, 350, 21.986067383514], [1200, 350, 21.986067383514], [1200, 400, 5.7033369854939], [1150, 400, 11.080807932178] ] , \"561\": [ [1150, 400, 11.080807932178], [1150, 450, 42.814531462431], [1200, 400, 5.7033369854939], [1200, 400, 5.7033369854939], [1200, 450, 9.3662254600345], [1150, 450, 42.814531462431] ] , \"562\": [ [1150, 450, 42.814531462431], [1150, 500, 25.481497839448], [1200, 450, 9.3662254600345], [1200, 450, 9.3662254600345], [1200, 500, 47.554084920638], [1150, 500, 25.481497839448] ] , \"563\": [ [1150, 500, 25.481497839448], [1150, 550, 60.336335929412], [1200, 500, 47.554084920638], [1200, 500, 47.554084920638], [1200, 550, 27.957306229833], [1150, 550, 60.336335929412] ] , \"564\": [ [1150, 550, 60.336335929412], [1150, 600, 55.322864908665], [1200, 550, 27.957306229833], [1200, 550, 27.957306229833], [1200, 600, 33.872169022637], [1150, 600, 55.322864908665] ] , \"565\": [ [1150, 600, 55.322864908665], [1150, 650, 23.352602710027], [1200, 600, 33.872169022637], [1200, 600, 33.872169022637], [1200, 650, 20.18254554711], [1150, 650, 23.352602710027] ] , \"566\": [ [1150, 650, 23.352602710027], [1150, 700, 34.122104031743], [1200, 650, 20.18254554711], [1200, 650, 20.18254554711], [1200, 700, 25.88575076296], [1150, 700, 34.122104031743] ] , \"567\": [ [1150, 700, 34.122104031743], [1150, 750, 24.624704410774], [1200, 700, 25.88575076296], [1200, 700, 25.88575076296], [1200, 750, 63.210853068492], [1150, 750, 24.624704410774] ] , \"568\": [ [1150, 750, 24.624704410774], [1150, 800, 5.1575499066879], [1200, 750, 63.210853068492], [1200, 750, 63.210853068492], [1200, 800, 78.997702461985], [1150, 800, 5.1575499066879] ] , \"569\": [ [1150, 800, 5.1575499066879], [1150, 850, 64.381346980212], [1200, 800, 78.997702461985], [1200, 800, 78.997702461985], [1200, 850, 49.512199782962], [1150, 850, 64.381346980212] ] , \"570\": [ [1150, 850, 64.381346980212], [1150, 900, 55.916860513491], [1200, 850, 49.512199782962], [1200, 850, 49.512199782962], [1200, 900, 59.603469092222], [1150, 900, 55.916860513491] ] , \"571\": [ [1150, 900, 55.916860513491], [1150, 950, 97.073711878272], [1200, 900, 59.603469092222], [1200, 900, 59.603469092222], [1200, 950, 48.698013827995], [1150, 950, 97.073711878272] ] , \"572\": [ [1150, 950, 97.073711878272], [1150, 1000, 94.501940698197], [1200, 950, 48.698013827995], [1200, 950, 48.698013827995], [1200, 1000, 50.091222134821], [1150, 1000, 94.501940698197] ] , \"573\": [ [1150, 1000, 94.501940698197], [1150, 1050, 9.0185301807766], [1200, 1000, 50.091222134821], [1200, 1000, 50.091222134821], [1200, 1050, 86.203897671265], [1150, 1050, 9.0185301807766] ] , \"574\": [ [1150, 1050, 9.0185301807766], [1150, 1100, -7.8024908675692], [1200, 1050, 86.203897671265], [1200, 1050, 86.203897671265], [1200, 1100, 54.806645661491], [1150, 1100, -7.8024908675692] ] , \"575\": [ [1150, 1100, -7.8024908675692], [1150, 1150, 15.107402691211], [1200, 1100, 54.806645661491], [1200, 1100, 54.806645661491], [1200, 1150, 13.037185828378], [1150, 1150, 15.107402691211] ] , \"576\": [ [1150, 1150, 15.107402691211], [1150, 1200, 38.295001692345], [1200, 1150, 13.037185828378], [1200, 1150, 13.037185828378], [1200, 1200, 6.1465300706446], [1150, 1200, 38.295001692345] ]}');

INSERT INTO `nodes_vr_level` (`id`, `project_id`, `name`, `text`, `image`, `rotation`, `scale`) VALUES
(1, 1, 'Level 1', '', '', 0, 1);

INSERT INTO `nodes_vr_link` (`id`, `project_id`, `level_id`, `scene_id`, `url`, `position`, `scale`) VALUES
(1, 1, 1, 1, 'https://www.google.com/streetview/#disney-parks-resorts/magic-kingdom-theme-park', '63.40730220987402 12.19268584598054 -76.95365584629462', '10 10 10');


INSERT INTO `nodes_vr_navigation` (`id`, `project_id`, `level_id`, `scene_id`, `target`, `position`, `scale`) VALUES
(1, 1, 1, 2, 1, '-44.205586857763734 10.596400647944476 89.4980964592546', '10 10 10'),
(2, 1, 1, 1, 2, '-98.17099845635533 18.55431146442364 -12.951148738090106', '10 10 10');


INSERT INTO `nodes_vr_object` (`id`, `scene_id`, `level_id`, `project_id`, `text`, `width`, `height`, `color`, `base64`, `position`, `rotation`, `scale`) VALUES
(1, 1, 1, 1, 'Demo message', 252, 85, 'white', 'iVBORw0KGgoAAAANSUhEUgAAAPwAAABVCAIAAADi2h4WAAALqElEQVR4nO2bf0xTVxvHH0pHSqmuVMQOyFq3qkgaICzbMtQmMEfIgrr4A5EoEjFGk4bxByPGEOPI4hZDYqPsh8ZshmwMG+cPNKYhUYlBJWgI8eeFVVa1KmlIxU7rFa+97x/n3cl929K1pS3kPc/nr/Kcb+99znO/Pfeccy9JoigCgrCEbLoTQJBEg6ZHmANNjzAHmh5hDjQ9whxoeoQ50PQIc6DpEeZA0yPMgaZHmANNjzAHmh5hDjQ9whxoeoQ50PQIc6DpEeZA0yPMgaZHmANNjzAHmh5hDjQ9whxoeoQ50PQIc6DpEeZA0yPMgaZHmANNjzAHmh5hDjQ9whxxN/3p06eT/pfk5OT09PQFCxasW7fu0KFDf//9d7xzQBAp0zDS+3y+8fFxu91+/Pjx7du36/X6gwcPJj4NhFkSZ3qe5wVBEATB4/Fcv369o6OjqalJpVK53e76+vrNmzcnLBOEdcQ4c+rUKXIiQRACWx0OR0VFBRHs2bMn3skgiCiK07yQ1el0Z86cKS0tBYCWlpZ79+5Nbz4IC8yI3RuLxSKXy30+X2tra2Drq1evDh48uGzZsjlz5syaNevjjz+uq6sbGhoKVO7YsSMpKenYsWNv3rz58ccflyxZkp6enpqaOn/+/Lq6urt37xLZ1atXN23apNPpUlNTdTrdZ599dvr06RDp/fLLLxs2bCgoKEhNTZ03b96nn3566NCh169fh9m7eGQVfk0A4OLFi2vWrFm8eHFaWtqcOXMKCgo2bdp08uTJly9fRiEDgPPnz9fV1X3yySfvvPNOWlra4sWLV6xYcf78+aBnf/369f79+0tKSrKzs5OCcezYsai7FiXxvpWEnt5QqqurASAnJ8cv7nA4cnNzyRFkMplKpSKfFQqFxWLxE2/fvh0A6uvrTSYTlclk//1hK5XK3t7elpYWaYTWobGxMTArh8NB7kL0aPSz0Wh88OBBOBWIR1bh16ShoYEq1Wq1XC6nB+/r64tUJoqitCBKpTIjI4P+2dTU5Hf2sbExkqpKpTKZTOXl5VlZWUScmZlZWVnZ1NTEcVx0XYuamWL6ffv2EZnD4aBBQRCMRiMA5Obm2mw2r9criqLT6TSbzcQiNptNehBiL0JDQ4PdbhdF0ev1tre3k/Kp1WoAMBgMNpvN4/GIojgyMvLFF18AgFwuv3XrlvRoExMTBoMBALRardVqHR0dJWc/evSoRqMBgPz8/NCdikdWEdWku7ubGO7XX3/leZ58fXh4uLW1taamJlIZobW11WQyHTlyxOl0ksjY2NiWLVuITQcGBqRiMpYVFRVR8cTEhNlsJr0eHx+PumtTYaaY/sSJE0TW29tLgz/99BMA5OTk+FVHFMVt27YRo0xMTNAgtdfOnTv99Hv37iVNarXa7XZLm7xer1arBYBvvvlGGv/uu+8AQKPRELtLGRkZIX5ta2v7twLEOKuIatLc3AwAGzduDJ1hmLIQCIJARuh9+/bRoMfjCbymoijyPE+61t7eHnXXpsJMMf2NGzeIrKuriwZJaY4cORKoHx4eJvrr16/TILFXSkpKoE0HBgYmc54oipWVlQCwZcsWaXD27NmBnqPs3LmT/CRCdCoeWUVUkwMHDgCA0WgM7ZUwZaEhg/3WrVtppK+vj9ysAg9bVlYGALt27ZIGI73cUTMjFrIAQGe0KSkp5MOTJ09GR0cBYPny5YH69957jyg5jvNr0uv18+bN8wvSqWRRUVHg0Uir0+mkkb/++osMVGvXrg2aMLlxu93u+/fvh+xZLLOKtCbl5eVKpfLWrVsmk+ny5cuT5RamLDSZmZkAMD4+TiNutxsA5HL5W2+95ScmqyPpGinqyx0FM8X0pEAAQBdGdrudfNDr9YFLfjJ+SGUU6iQpdGWWk5MzWavP56MROrS8++67QROmcaoMTUyyirQmCxYsOHv27OzZs/v6+pYuXVpQUPD999+/ePHC70Rhyih//vnn119/vWHDhmXLlul0OrIZRWaDUhYuXAgAPM/fvn3br4nc2PPz86Pu2lSQ/7skIQwODgKATCbT6/Uk4nA4SITUbjLIKlCKdD8hEOneSAjI+KpSqVJTU4MK3n77bYVCwfO8dCQOQUyyiqImJSUlIyMjFovl559/vnHjhtlsbm5uNpvNu3fvlg7AYcqePHlSVVV16dIlAFAoFFlZWXq9XqPRKJXKgYEBv2H4/fffLy4uvnLlitlsPnHiRHp6Ool/++23Dodj4cKFZJITddeiZ+ozpNCEOadfuXIlAOTl5dEI2VKQyWTPnz8P81xk9rx27drAprGxMZLG4OBgYGtjYyMALF++nEbowprsIQRC7+Pd3d0JyyqKmlAEQejs7CwuLiZnLCoqCtq1EDK6/2gwGKxWq9/Xg3aT4ziy05WRkbFy5crq6mqyP5ORkdHf3y9VTqVrkTIjpjdDQ0Nnz54FgNWrV9NgXl4eAPh8vjt37iQ+JTp7ISNQIA8ePCAfQo9MsWUqNUlOTl6/fv3ly5c7OzsVCsXAwIDFYolI1tXVxXFcSkpKT0/PunXr/O6BQe94ixYt6uvrUyqV4+PjPT09586dUygUDQ0Nw8PDH374Yay6FinTb/pXr17V1NT4fD6lUllfX0/j2dnZZJDo6OhIfFYffPABmYV3dnYGFZCs1Gq1TqdLWFYxqcn69evJqEx2V8KXkQ+FhYXZ2dl+4jdv3pAJqh/Pnj2rrKwkv5Nnz549ffr02rVr+/fvp1MdSiIv9zSb/uHDh6Wlpf39/QBw4MCBuXPnSlv37NkDAG1tbVevXk18brW1tSSrR48e+TU9fPiwra0NAMgOdyKJSU3I4phsEYYvI6Z0uVyByr179wYd6bu6ugYHBysrK5csWfKvWSXucsd7/kTn9PTVYvIyvdVqra2tpRuUzc3NQb/+0UcfAYBcLm9qaurv7/d4PF6v126322w2s9lcWloqFcdw9iyKIs/z5J6bmZlptVpdLpcoii6Xq6Ojg6xKc3Nzw9nYjm1WYiQ1aWlp2bp1a09PD33c8/z588OHD5O9wuPHj0ckI1NQAKivrydPjgVB4DiupqZGJpMRsV83yROAvLy8kZGRcJ5eR3S5oyZxpp8Mg8Fw6tSpyb7ucrmqqqpCfFcqjrm9nE4nuQwE+ioIAJSVlUX07k0Mswq/JuQIBLVanZGRQZ+HmM3mSGXiP68VAIBMJtNqtXTMqq6uJs9T/br5+PFjuh1HUSgUubm5Gzdu9HvDIqKuTYVp2LJMSUnRaDRarba4uLisrKyioiI5OXky8dy5c3///ffa2lqr1Xrnzh2O4wRByMzMzMnJ+fzzz6UL33iQnZ3d29trtVqPHj3KcZzb7c7PzzcajWVlZdP4Xy/h16SxsVGr1XZ1dTmdztHRUZ/Pl5eXZzQazWazdL4RpgwAfvvtt/Ly8sOHD9vtdrfbrdFoioqKtm3btmrVqsDNeAAYHBzU6/V+mwE8z3Mcx3FcZ2dnd3d3SUlJFF2bEjH56SBIIGTBU1hYaLPZXC4Xz/M8z3u93sePH1+4cGHp0qUQu8E7IpJEUYzNrwdBJNy8ebOwsFCpVNrt9sD3LwBgaGiI7Pq73e7AzZy4Mv1blsj/Jb29vT6fr7CwMKjj4Z8Xb+RyOV0YJAw0PRIXyPsC5B2yoLS3twNAfn5+Wlpa4tICADQ9EidMJpNCobDb7V9++eXTp0+lTffu3fvqq692794tk8l27dqV+NxwTo/Eiz/++KOqqkoQBIVCYTAYNBqNx+MZHR0lw79KpbJYLHV1dYlPDE2PxJH79+//8MMPPT09TqdzbGxMo9FkZWVlZWVVVFRUV1fPmjVrWrJC0yPMgXN6hDnQ9AhzoOkR5kDTI8yBpkeYA02PMAeaHmEOND3CHGh6hDnQ9AhzoOkR5kDTI8yBpkeYA02PMAeaHmEOND3CHGh6hDnQ9AhzoOkR5kDTI8yBpkeYA02PMAeaHmEOND3CHGh6hDnQ9AhzoOkR5vgPpfqrVHTHxL8AAAAASUVORK5CYII=', '-34.71028662025142 15.776599215128522 -93.11433634368225', '30 30 0', '10 10 10');


INSERT INTO `nodes_vr_project` (`id`, `name`, `url`, `text`) VALUES
(1, 'Test', '', '');


INSERT INTO `nodes_vr_scene` (`id`, `project_id`, `level_id`, `name`, `position`, `rotation`, `lat`, `lng`, `top`, `left`, `height`, `floor_position`, `floor_radius`, `logo_size`) VALUES
(1, 1, 1, 'Scene №1', '0 3 0', '0 0 0', 0, 0, 0, 0, 0, '-30 -2 -10', 20, 4),
(2, 1, 1, 'Scene №2', '0 3 0', '0 0 0', 0, 0, 0, 0, 0, '-20 -2 30', 20, 4);
";
            $arr = explode(";
", $query);
            $flag = 0;
            foreach($arr as $a){
                $a = trim($a);
                if(!empty($a)){
                    @mysqli_query($link, "SET NAMES utf8");
                    mysqli_query($link, $a) or die(mysqli_error($link));
                    $flag++;
                }
            }
            require_once("engine/core/file.php");
            file::copy($_SERVER["DOCUMENT_ROOT"].$_SERVER["DIR"]."/temp/img", $_SERVER["DOCUMENT_ROOT"].$_SERVER["DIR"]."/img");
            file::copy($_SERVER["DOCUMENT_ROOT"].$_SERVER["DIR"]."/temp/res", $_SERVER["DOCUMENT_ROOT"].$_SERVER["DIR"]."/res");
            unlink($_SERVER["DOCUMENT_ROOT"].$_SERVER["DIR"]."/temp/img");
            unlink($_SERVER["DOCUMENT_ROOT"].$_SERVER["DIR"]."/temp/res");
            $output .= 'Ok.<br/>';
        }
        if($_POST["protocol"] == "https"){
            $file = fopen("https.htaccess", "r");
            $data = fread($file, filesize("https.htaccess"));
            fclose($file);
            $file = fopen(".htaccess", "w");
            fwrite($file, $data);
            fclose($file);
        }
        $query = 'SELECT * FROM `nodes_user` WHERE `id` = "1"';
        @mysqli_query($link, "SET NAMES utf8");
        $res = mysqli_query($link, $query) or die(mysqli_error($link));
        $data = mysqli_fetch_array($res);
        unset($data["pass"]);
        unset($data[5]);
        unset($data["token"]);
        unset($data[9]);
        $_SESSION["user"] = $data;
        $_SESSION["Lang"] =  strtolower ($_POST["language"]);
        $new_version = file_get_contents("http://nodes-studio.com/source/updater.php?version=1");
        if(empty($new_version)) $new_version = "-1";
        return($output.'
Installation completed!
<br/><center>
<input type="button" class="btn" style="width: 280px;" value="Update Engine" onClick=\'new_update();\' /><br/><br/>
<a href="'.$_SERVER["DIR"].'/"><input type="button" class="btn" style="width: 280px;" value="Main Page" /></a><br/><br/>
<a href="'.$_SERVER["DIR"].'/admin"><input type="button" class="btn" style="width: 280px;" value="Admin Page" /></a><br/><br/>
<script>
function new_update(){
    jQuery("#content").animate({opacity: 0}, 300);
    document.getElementById("post_form").submit();
}
</script>
<form method="POST" id="post_form" action="'.$_SERVER["DIR"].'/install.php">
    <input type="hidden" name="version" value="'.intval($new_version).'" />
</form>
    ');
    }else{
        return($output."Error.<br/>Installation aborted.".$error_output);
    }
}else if(!empty($_POST["version"])){
    if(intval($_POST["version"])>0){
        $fout = ' ';
        require_once("engine/code/update.php");
        $fout .= '<br/><center>
            <a href="'.$_SERVER["DIR"].'/"><input type="button" class="btn" style="width: 280px;" value="Main page" /></a><br/><br/>
            <a href="'.$_SERVER["DIR"].'/admin"><input type="button" class="btn" style="width: 280px;" value="Admin page" /></a><br/><br/>
            </center>';
        return $fout;
    }else{
        return('<script>window.location = "'.$_SERVER["DIR"].'/";</script>');
    }
}else{
    $options = '<option value="1" disabled>Yes</option><option value="0" selected>No</option>';   
    if(function_exists('eval')&&function_exists('base64_decode')&&function_exists('base64_encode')){
        $options = '<option value="1" selected>Yes</option><option value="0">No</option>';
    }
    if(is_dir($_SERVER["DOCUMENT_ROOT"].$_SERVER["DIR"]."/temp/img")){
        $options1 = '<option value="1">Yes</option><option value="0">No</option>';  
    }else{
        $options1 = '<option value="1" disabled>Yes</option><option value="0" selected>No</option>';
    }
    $output = '<div style="float:right; text-align:center;" class=\'right-center\'>
    <img itemprop="logo" src="'.$_SERVER["DIR"].'/img/cms/nodes_studio.png" style="width: 100%; max-width: 395px;" />
    <br/>
</div> 
<div style="clear:left;"></div>
<div style="float:left; text-align: left; padding-left: 10%; padding-top: 5px; white-space:nowrap; line-height: 2;">
    <form method="POST" id="post_form">
    <div style="width: 110px; float: left; margin-right: 10px;">Mysql server</div> <input id="server" class="input" type="text" required="required" name="mysql_server" value="localhost" ><br/>
    <div style="width: 110px; float: left; margin-right: 10px;">Mysql login</div> <input id="login" class="input" type="text" required="required" name="mysql_login" value="root" ><br/>
    <div style="width: 110px; float: left; margin-right: 10px;">Mysql pass</div> <input id="pass" class="input" type="text" name="mysql_pass" ><br/>
    <div style="width: 110px; float: left; margin-right: 10px;">Mysql DB</div> <input id="db" class="input" type="text" required="required" name="mysql_db" value="" ><br/>
    <div style="width: 110px; float: left; margin-right: 10px;">Admin name</div> <input class="input" required="required" type="text" name="admin_name" value="Admin" ><br/>
    <div style="width: 110px; float: left; margin-right: 10px;">Admin email</div> <input class="input" required="required" type="text" name="admin_email" ><br/>
    <div style="width: 110px; float: left; margin-right: 10px;">Admin pass</div> <input class="input" required="required" type="text" name="admin_pass" ><br/>
    <div style="width: 110px; float: left; margin-right: 10px;">Site protocol</div> <select class="input" style="width:160px;" required="required" type="text" name="protocol"> <option value="http">http</option> <option value="https">https</option> </select><br/>
    <div style="width: 110px; float: left; margin-right: 10px;">Site name</div> <input class="input" required="required" type="text" name="name" value="Nodes Studio" ><br/>
    <div style="width: 110px; float: left; margin-right: 10px;">Site description</div> <input class="input" required="required" type="text" name="description" value="WebVR Framework" ><br/>
    <div style="width: 110px; float: left; margin-right: 10px;">Site language</div> <input class="input" required="required" type="text" name="language" value="en" ><br/>
    <div style="width: 110px; float: left; margin-right: 10px;">Site languages</div> <input class="input" required="required" type="text" name="languages" value="en;ru;" ><br/>
    <div style="width: 210px; float: left; margin-right: 10px;">Base64 encode config file</div> <select class="input" type="text" name="encoding">'.$options.'</select><br/>
    <div style="width: 210px; float: left; margin-right: 10px;">Publicate demo information</div> <select class="input" type="text" name="temp">'.$options1.'</select><br/>
    </form><br/>
</div>
<div style=\'width: 100%; max-width: 395px; text-align: center; float:right;\' class=\'right-center\'>
    <input id="install_now" type="button" class="btn" onClick=\'check_connection();\' value="Install Now" style="width: 280px;" />
    <br/><br/>
</div>
<div style="clear:both;"></div>';
}return $output;
}
$session_lifetime = 2592000;
session_set_cookie_params($session_lifetime, '/', '.'.$_SERVER["HTTP_HOST"]);
session_name('token');
session_start();
if(empty($_SESSION["Lang"])) $_SESSION["Lang"] = "en";
if(!empty($_POST["mysql_test"])){
    $link = mysqli_connect($_POST["server"], 
        $_POST["login"],
        $_POST["pass"]);
    if($link){
        if(mysqli_select_db($link, $_POST["db"])){ 
            if(!empty($_SERVER["HTTP_HOST"])&&
                !empty($_SERVER["DOCUMENT_ROOT"])) die('2');
            else die('1');
        }
    }die('0');
}else{
    $output = output();
}
?><!DOCTYPE html>
<html lang="en" style="background: url(<?php echo $_SERVER["DIR"]; ?>/img/load.gif) no-repeat center center fixed;">
<script language="JavaScript" type="text/javascript">new Image().src = "<?php echo $_SERVER["DIR"]; ?>/img/load.gif";</script>
<head>
<title>Nodes Studio - Framework Setup</title>
<meta charset="UTF-8" />
<meta http-equiv="content-type" content="text/html; charset=utf-8" />
<meta name="description" content="Nodes Studio - WebVR Framework" />
<meta property="og:description" content="Nodes Studio - WebVR Framework" />
<link href="<?php echo $_SERVER["DIR"]; ?>/template/nodes.css" rel="stylesheet" type="text/css" />
<link href="<?php echo $_SERVER["DIR"]; ?>/template/default/template.css" rel="stylesheet" type="text/css" />
<?php
require_once('engine/nodes/meta.php');
echo $fout;
?>
</head>
<body class="nodes">
    <div style="position:absolute; top: 0px; text-align: center; left: 0px; right: 0px; line-height: 1.0; 
         height: 80px; display: flex; align-items: center; border: 0px solid;">
        <h1 style="font-size: 32px; margin: auto;">Welcome to <nobr>Nodes Studio.</nobr></h1>
    </div>
<div id="content">
<!-- content -->
<section id="contentSection">
<div class="container">
<div id="mainSection" style="padding-top: 0px; line-height: 1.9; text-align: left; max-width: 900px; margin: 0px auto;">
<?php echo $output; ?>
</div>
</div>
</section>
<!-- /content -->
</div>
    <script src="<?php echo $_SERVER["DIR"]; ?>/script/jquery.js" type="text/javascript"></script>
    <script>
    function check_connection(){
        jQuery.ajax({
            type: "POST",
            data: { 
                "mysql_test" : "1", 
                "server" : jQuery('#server').val(), 
                "login" : jQuery('#login').val(), 
                "pass" : jQuery('#pass').val(), 
                "db" : jQuery('#db').val()
            },
            success: function(data){ 
                if(data=="2"){
                    document.getElementById("content").style.display = "none"; 
                    document.getElementById("post_form").submit();  
                }else if(data=="1"){ 
                    alert("$_SERVER variables is not defined");
                }else alert("Error. MySQL connection is not established");
            }
        });
    }
    </script>
</body>
<script language="JavaScript" type="text/javascript">
function display(){ if(!window.jQuery) setTimeout(function(){ document.body.style.opacity = "1";}, 1000); 
else jQuery("html, body").animate({opacity: 1}, 1000); }var tm = setTimeout(display, 5000); window.onload = function(){ clearTimeout(tm); display(); 
if(!window.jQuery) document.write(unescape('<script type="text/javascript" src="<?php echo $_SERVER["DIR"]; ?>/script/jquery.js">%3C/script%3E')); };</script>
</html>