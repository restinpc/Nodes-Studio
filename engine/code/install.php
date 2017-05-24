<?php
/**
* Framework Installer.
* @path /engine/code/install.php
*
* @name    Nodes Studio    @version 2.0.3
* @author  Ripak Forzaken  <developing@nodes-tech.ru>
* @license http://www.apache.org/licenses/LICENSE-2.0 GNU Public License
*/
function output(){
$output = '';
if(!empty($_POST["mysql_server"])){
    $error_output = '<br/><br/>'
    . '<center>'
    . '<a href="'.$_SERVER["DIR"].'/install.php"><input type="button" value="Reinstall site" class="btn" style="width: 280px;" /></a><br/><br/>'
    . '</center>';
    $flag = 0;
    $output .= 'Checking MySQL connection.. ';
    if(mysql_connect($_POST["mysql_server"], 
        $_POST["mysql_login"],
        $_POST["mysql_pass"])){
        if(mysql_select_db($_POST["mysql_db"]))
            $flag = 1;
    }if($flag){
        $output .= "Ok.<br/>";
        $query = "DROP TABLE IF EXISTS 
        `nodes_agent`,
        `nodes_attendance`,
        `nodes_backend`,
        `nodes_cache`,
        `nodes_catalog`,
        `nodes_comment`,
        `nodes_config`,
        `nodes_content`,
        `nodes_error`,
        `nodes_image`,
        `nodes_inbox`,
        `nodes_language`,
        `nodes_log`,
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
        `nodes_shipping`,
        `nodes_transaction`,
        `nodes_user`,
        `nodes_user_outbox`;


CREATE TABLE IF NOT EXISTS `nodes_user` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `name` varchar(25) NOT NULL,
  `photo` varchar(400) NOT NULL,
  `url` varchar(400) NOT NULL,
  `email` varchar(400) NOT NULL,
  `pass` varchar(32) NOT NULL,
  `balance` double NOT NULL,
  `ip` varchar(20) NOT NULL,
  `ban` tinyint(1) NOT NULL,
  `online` int(11) NOT NULL,
  `rating` int(11) NOT NULL DEFAULT '0',
  `votes` int(11) NOT NULL DEFAULT '0',
  `token` varchar(32) NOT NULL,
  `confirm` tinyint(1) NOT NULL,
  `code` int(4) NOT NULL,
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
('name', '".mysql_real_escape_string($_POST["name"])."', 'Site name', 'string'),
('description', '".mysql_real_escape_string($_POST["description"])."', 'Description', 'string'),
('email', '".mysql_real_escape_string($_POST["admin_email"])."', 'Site email', 'string'),
('language', '".mysql_real_escape_string($_POST["language"])."', 'Site language', 'string'),
('languages', '".mysql_real_escape_string(str_replace("'", "\'", $_POST["languages"]))."', 'Available languages', 'string'),
('image', '".$_SERVER["PUBLIC_URL"]."/img/cms/nodes_studio.png', 'Site image', 'string'),
('email_image', '".$_SERVER["PUBLIC_URL"]."/img/logo.png', 'Email header image', 'string');


INSERT INTO `nodes_user` (`name`, `photo`, `url`, `email`, `pass`, `balance`, `ip`, `ban`, `online`, `token`, `confirm`, `code`, `bulk_ignore`) VALUES
('".mysql_real_escape_string(str_replace("'", "\'", $_POST["admin_name"]))."', 'admin.jpg', '', '".htmlspecialchars($_POST["admin_email"])."', '".md5(trim(strtolower($_POST["admin_pass"])))."', 0, '', -1, 0, '', 1, 0, 0);
     

INSERT INTO `nodes_config` (`name`, `value`, `text`, `type`) VALUES
('template', 'default', 'Template', 'string'),
('default', 'content.php', 'System', 'string'),
('debug', '0', 'Debug mode', 'bool'),
('cron', '1', 'jQuery cron', 'bool'),
('compress', '1', 'Compress HTML', 'bool'),
('sandbox', '1', 'Sandbox payment mode', 'bool'),
('autoupdate', '1', 'Engine auto-update', 'bool'),
('autobackup', '1', 'Auto backup', 'bool'),
('pattern_color', '1', 'Adaptive interface color', 'bool'),
('catch_patterns', '1', 'Behavioral monitoring', 'bool'),
('backup_files', '0', 'Backup files', 'bool'),
('free_message', '0', 'Messages between users', 'bool'),
('daily_report', '1', 'Daily report to email', 'bool'),
('send_comments_email', '1', 'Email admin on comment', 'bool'),
('send_registration_email', '1', 'Email user on sign up', 'bool'),
('send_message_email', '1', 'Email user on message', 'bool'),
('send_paypal_email', '1', 'Email user on payment', 'bool'),
('paypal_test', '1', 'PayPal test mode', 'bool'),
('paypal_id', '', 'PayPal user ID', 'string'),
('paypal_description', '', 'PayPal description', 'string'),
('vk_link', 'https://vk.com', 'VK page URL', 'string'),
('vk_id', '', 'VK client ID', 'string'),
('fb_link', 'https://facebook.com', 'Facebook page URL', 'string'),
('fb_id', '', 'Facebook client ID', 'string'),
('fb_secret', '', 'Facebook client secret', 'string'),
('tw_link', 'https://twitter.com/', 'Twitter page URL', 'string'),
('tw_key', '', 'Twitter consumer key', 'string'),
('tw_secret', '', 'Twitter consumer secret', 'string'),
('gp_link', 'https://plus.google.com', 'Google+ page URL', 'string'),
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
('cron_session', '0', 'System', 'int'),
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
  PRIMARY KEY (`id`)
) ENGINE=MyISAM  DEFAULT CHARSET=utf8 AUTO_INCREMENT=1 ;


CREATE TABLE IF NOT EXISTS `nodes_backend` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `mode` varchar(400) DEFAULT NULL,
  `file` varchar(400) DEFAULT NULL,
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
('profile', 'profile.php');


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
  PRIMARY KEY (`id`)
) ENGINE=MyISAM  DEFAULT CHARSET=utf8 AUTO_INCREMENT=1 ;


CREATE TABLE IF NOT EXISTS `nodes_catalog` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `caption` varchar(400) NOT NULL,
  `text` mediumtext NOT NULL,
  `url` varchar(400) NOT NULL,
  `img` varchar(100) NOT NULL,
  `visible` tinyint(1) NOT NULL,
  `lang` varchar(3) NOT NULL,
  `order` int(11) NOT NULL,
  `date` int(11) NOT NULL DEFAULT '0',
  `public_date` int(11) NOT NULL DEFAULT '0',
  PRIMARY KEY (`id`)
) ENGINE=MyISAM  DEFAULT CHARSET=utf8 AUTO_INCREMENT=13 ;


CREATE TABLE IF NOT EXISTS `nodes_comment` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `url` text NOT NULL,
  `reply` int(11) NOT NULL,
  `user_id` int(11) NOT NULL,
  `text` text NOT NULL,
  `date` int(11) NOT NULL DEFAULT '0',
  PRIMARY KEY (`id`)
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
  PRIMARY KEY (`id`)
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
  PRIMARY KEY (`id`)
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
  PRIMARY KEY (`id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8 AUTO_INCREMENT=1 ;


CREATE TABLE IF NOT EXISTS `nodes_log` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `action` int(11) NOT NULL,
  `user_id` int(11) NOT NULL,
  `ip` varchar(20) NOT NULL,
  `date` int(11) NOT NULL,
  `details` text NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8 AUTO_INCREMENT=1 ;


CREATE TABLE IF NOT EXISTS `nodes_meta` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `url` varchar(400) NOT NULL,
  `lang` varchar(4) NOT NULL,
  `title` varchar(100) NOT NULL,
  `description` varchar(200) NOT NULL,
  `keywords` varchar(300) NOT NULL,
  `mode` int(1) NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8 AUTO_INCREMENT=1 ;


CREATE TABLE IF NOT EXISTS `nodes_order` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `user_id` int(11) NOT NULL,
  `date` int(11) NOT NULL,
  `status` int(11) NOT NULL,
  `shipping` int(11) NOT NULL,
  `payment` int(1) NOT NULL,
  PRIMARY KEY (`id`)
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
  PRIMARY KEY (`id`)
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
  PRIMARY KEY (`id`)
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
  PRIMARY KEY (`id`)
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
  PRIMARY KEY (`id`)
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
  PRIMARY KEY (`id`)
) ENGINE=MyISAM  DEFAULT CHARSET=utf8 AUTO_INCREMENT=1 ;


INSERT INTO `nodes_shipping` (`id`, `user_id`, `fname`, `lname`, `country`, `state`, `city`, `zip`, `street1`, `street2`, `phone`) VALUES
(1, 1, '', '', 'United States', 'Columbia D.C', 'Washington', '20001', 'District of Columbia', '', '+1234567890');


CREATE TABLE IF NOT EXISTS `nodes_transaction` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `user_id` int(11) NOT NULL,
  `order_id` int(11) NOT NULL,
  `txn_id` varchar(40) NOT NULL,
  `amount` double NOT NULL,
  `status` int(11) NOT NULL,
  `date` int(11) NOT NULL,
  `payment_date` varchar(100) NOT NULL,
  `comment` text NOT NULL,
  `ip` varchar(40) NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8 AUTO_INCREMENT=1 ;


CREATE TABLE IF NOT EXISTS `nodes_user_outbox` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `user_id` int(11) NOT NULL,
  `outbox_id` int(11) NOT NULL,
  `date` int(11) NOT NULL,
  `status` tinyint(4) NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8 AUTO_INCREMENT=1 ;


CREATE TABLE IF NOT EXISTS `nodes_agent` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `name` text NOT NULL,
  `bot` tinyint(1) NOT NULL DEFAULT '0',
  PRIMARY KEY (`id`)
) ENGINE=MyISAM  DEFAULT CHARSET=utf8 AUTO_INCREMENT=1 ;


INSERT INTO `nodes_agent` (`id`, `name`, `bot`) VALUES
(1, 'a.pr-cy.ru', 1),
(2, 'AdsBot-Google (+http://www.google.com/adsbot.html)', 1),
(3, 'AdsBot-Google-Mobile (+http://www.google.com/mobile/adsbot.html) Mozilla (iPhone; U; CPU iPhone OS 3 0 like Mac OS X) AppleWebKit (KHTML, like Gecko) Mobile Safari', 1),
(4, 'Apache-HttpClient/4.5 (Java/1.8.0_60)', 1),
(5, 'eSyndiCat Bot', 1),
(6, 'facebookexternalhit/1.1', 1),
(7, 'facebookexternalhit/1.1 (+http://www.facebook.com/externalhit_uatext.php)', 1),
(8, 'Google favicon', 1),
(9, 'Googlebot-Image/1.0', 1),
(10, 'GuzzleHttp/6.1.0 curl/7.26.0 PHP/5.5.29-1~dotdeb+7.1', 1),
(11, 'GuzzleHttp/6.1.0 curl/7.35.0 PHP/5.6.14-1+deb.sury.org~trusty+1', 1),
(12, 'Java/1.4.1_04', 1),
(13, 'Java/1.8.0_60', 1),
(14, 'LinksMasterRoBot/0.01 (http://www.linksmaster.ru)', 1),
(15, 'LinkStats Bot', 1),
(16, 'ltx71 - (http://ltx71.com/)', 1),
(17, 'Mozilla/5.0 (compatible; AhrefsBot/5.0; +http://ahrefs.com/robot/)', 1),
(18, 'Mozilla/5.0 (compatible; archive.org_bot; Wayback Machine Live Record; +http://archive.org/details/archive.org_bot)', 1),
(19, 'Mozilla/5.0 (compatible; Baiduspider/2.0; +http://www.baidu.com/search/spider.html)', 1),
(20, 'Mozilla/5.0 (compatible; bingbot/2.0; +http://www.bing.com/bingbot.htm)', 1),
(21, 'Mozilla/5.0 (compatible; CNCat/4.2; +http://www.cn-software.com/en/cncat/robot/)', 1),
(22, 'Mozilla/5.0 (compatible; CNCat/4.2; +http://www.vipwords.com/en/cncat/robot/)', 1),
(23, 'Mozilla/5.0 (compatible; DeuSu/5.0.2; +https://deusu.de/robot.html)', 1),
(24, 'Mozilla/5.0 (compatible; DotBot/1.1; http://www.opensiteexplorer.org/dotbot, help@moz.com)', 1),
(25, 'Mozilla/5.0 (compatible; Googlebot/2.1; +http://www.google.com/bot.html)', 1),
(26, 'Mozilla/5.0 (compatible; Googlebot/2.1; +http://www.google.com/bot.html)/1.8 (InfoSeek crawler; http://www.infoseek.com; crawler@infoseek.com)', 1),
(27, 'Mozilla/5.0 (compatible; Google-Site-Verification/1.0)', 1),
(28, 'Mozilla/5.0 (compatible; GrapeshotCrawler/2.0; +http://www.grapeshot.co.uk/crawler.php)', 1),
(29, 'Mozilla/5.0 (compatible; linkdexbot/2.2; +http://www.linkdex.com/bots/)', 1),
(30, 'Mozilla/5.0 (compatible; LinkpadBot/1.06; +http://www.linkpad.ru)', 1),
(31, 'Mozilla/5.0 (compatible; Linux x86_64; Mail.RU_Bot/2.0; +http://go.mail.ru/help/robots)', 1),
(32, 'Mozilla/5.0 (compatible; Linux x86_64; Mail.RU_Bot/Fast/2.0; +http://go.mail.ru/help/robots)', 1),
(33, 'Mozilla/5.0 (compatible; meanpathbot/1.0; +http://www.meanpath.com/meanpathbot.html)', 1),
(34, 'Mozilla/5.0 (compatible; MegaIndex.ru/2.0; +http://megaindex.com/crawler)', 1),
(35, 'Mozilla/5.0 (compatible; MJ12bot/v1.4.5; http://www.majestic12.co.uk/bot.php?+)', 1),
(36, 'Mozilla/5.0 (compatible; NetSeer crawler/2.0; +http://www.netseer.com/crawler.html; crawler@netseer.com)', 1),
(37, 'Mozilla/5.0 (compatible; openstat.ru/Bot)', 1),
(38, 'Mozilla/5.0 (compatible; SemrushBot/0.99~bl; +http://www.semrush.com/bot.html)', 1),
(39, 'Mozilla/5.0 (compatible; SputnikFaviconBot/1.2; +http://corp.sputnik.ru/webmaster)', 1),
(40, 'Mozilla/5.0 (compatible; statdom.ru/Bot; +http://statdom.ru/bot.html)', 1),
(41, 'Mozilla/5.0 (compatible; StatOnlineRuBot/1.0)', 1),
(42, 'Mozilla/5.0 (compatible; vkShare; +http://vk.com/dev/Share)', 1),
(43, 'Mozilla/5.0 (compatible; WebArtexBot; +http://webartex.ru/)', 1),
(44, 'Mozilla/5.0 (compatible; Web-Monitoring/1.0; +http://monoid.nic.ru/)', 1),
(45, 'Mozilla/5.0 (compatible; YaDirectFetcher/1.0; +http://yandex.com/bots)', 1),
(46, 'Mozilla/5.0 (compatible; YaDirectFetcher/1.0; Dyatel; +http://yandex.com/bots)', 1),
(47, 'Mozilla/5.0 (compatible; Yahoo! Slurp; http://help.yahoo.com/help/us/ysearch/slurp)', 1),
(48, 'Mozilla/5.0 (compatible; YandexBot/3.0; +http://yandex.com/bots)', 1),
(49, 'Mozilla/5.0 (compatible; YandexDirect/3.0; +http://yandex.com/bots)', 1),
(50, 'Mozilla/5.0 (compatible; YandexImages/3.0; +http://yandex.com/bots)', 1),
(51, 'Mozilla/5.0 (compatible; YandexMetrika/2.0; +http://yandex.com/bots DEV)', 1),
(52, 'Mozilla/5.0 (compatible; YandexMetrika/2.0; +http://yandex.com/bots mtmon01e.yandex.ru)', 1),
(53, 'Mozilla/5.0 (compatible; YandexMetrika/2.0; +http://yandex.com/bots mtmon01g.yandex.ru)', 1),
(54, 'Mozilla/5.0 (compatible; YandexMetrika/2.0; +http://yandex.com/bots mtmon01i.yandex.ru)', 1),
(55, 'Mozilla/5.0 (compatible; YandexMetrika/2.0; +http://yandex.com/bots mtweb01t.yandex.ru)', 1),
(56, 'Mozilla/5.0 (compatible; YandexMetrika/2.0; +http://yandex.com/bots)', 1),
(57, 'Mozilla/5.0 (compatible; YandexMetrika/3.0; +http://yandex.com/bots)', 1),
(58, 'Mozilla/5.0 (compatible; YandexWebmaster/2.0; +http://yandex.com/bots)', 1),
(59, 'Mozilla/5.0 (compatible; YandexWebmaster/2.0; +http://yandex.com/bots)', 1),
(60, 'Mozilla/5.0 (iPhone; CPU iPhone OS 8_1 like Mac OS X) AppleWebKit/600.1.4 (KHTML, like Gecko) Version/8.0 Mobile/12B411 Safari/600.1.4 (compatible; YandexMobileBot/3.0; +http://yandex.com/bots)', 1),
(61, 'Mozilla/5.0 (iPhone; CPU iPhone OS 8_3 like Mac OS X) AppleWebKit/600.1.4 (KHTML, like Gecko) Version/8.0 Mobile/12F70 Safari/600.1.4 (compatible; Googlebot/2.1; +http://www.google.com/bot.html)', 1),
(62, 'Mozilla/5.0 (Windows NT 6.2; WOW64) Runet-Research-Crawler (itrack.ru/research/cmsrate; rating@itrack.ru)', 1),
(63, 'Mozilla/5.0 (Windows NT 6.2; WOW64) Runet-Research-Crawler (itrack.ru/research/cmsrate; rating@itrack.ru)', 1),
(64, 'Mozilla/5.0 (Windows; U; Windows NT 5.1; en; rv:1.9.0.13) Gecko/2009073022 Firefox/3.5.2 (.NET CLR 3.5.30729) SurveyBot/2.3 (DomainTools)', 1),
(65, 'Mozilla/5.0 (Windows; U; Windows NT 6.0; en-GB; rv:1.0; trendictionbot0.5.0; trendiction search; http://www.trendiction.de/bot; please let us know of any problems; web at trendiction.com) Gecko/20071127 Firefox/3.0.0.11', 1),
(66, 'Netcat Bot', 1),
(67, 'netEstate NE Crawler (+http://www.website-datenbank.de/)', 1),
(68, 'OdklBot/1.0 (klass@odnoklassniki.ru)', 1),
(69, 'parser3', 1),
(70, 'PEAR HTTP_Request class ( http://pear.php.net/ )', 1),
(71, 'pr-cy.ru Screenshot Bot', 1),
(72, 'python-requests/2.8.1', 1),
(73, 'Riddler (http://riddler.io/about)', 1),
(74, 'rogerbot/1.0 (http://moz.com/help/pro/what-is-rogerbot-, rogerbot-wherecat@moz.com)', 1),
(75, 'RookeeBot', 1),
(76, 'SafeDNS search bot/Nutch-1.9 (https://www.safedns.com/searchbot; support [at] safedns [dot] com)', 1),
(77, 'SeopultContentAnalyzer/1.0', 1),
(78, 'Validator.nu/LV http://validator.w3.org/services', 1),
(79, 'W3C_Validator/1.3 http://validator.w3.org/services', 1),
(80, 'W3C_Validator/1.3 libwww-perl/6.05', 1),
(81, 'Websquash.com (Add url robot)', 1),
(82, 'Who.is Bot', 1),
(83, 'Y!J-ASR/0.1 crawler (http://www.yahoo-help.jp/app/answers/detail/p/595/a_id/42716/)', 1),
(84, 'Yandex/1.01.001 (compatible; Win16; I)', 1),
(85, 'Nodes Studio 2.0', 1),
(86, 'Mozilla/5.0 (compatible; AhrefsBot/5.2; +http://ahrefs.com/robot/)', 1),
(87, 'Googlebot/2.1 (+http://www.google.com/bot.html)', 1),
(88, 'CRAZYWEBCRAWLER 0.9.10, http://www.crazywebcrawler.com', 1),
(89, 'C-T bot', 1),
(90, 'Mozilla/5.0 (compatible; Uptimebot/1.0; +http://www.uptime.com/uptimebot)', 1),
(91, 'Virusdie crawler/2.1', 1),
(92, 'Mozilla/5.0 (compatible; Google-Structured-Data-Testing-Tool +https://search.google.com/structured-data/testing-tool)', 1),
(93, 'Mozilla/5.0 (compatible; YandexPagechecker/2.0; +http://yandex.com/bots)', 1),
(94, 'GuzzleHttp/6.1.0 curl/7.38.0 PHP/7.0.13-1~dotdeb+8.1', 1);


CREATE TABLE IF NOT EXISTS `nodes_language` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `name` text NOT NULL,
  `lang` varchar(3) NOT NULL,
  `value` text NOT NULL,
  PRIMARY KEY (`id`)
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
(968, 'Submitted on', 'ru', 'Опубликовано');

";      $arr = explode(";
", $query);
    $flag = 0;
        foreach($arr as $a){
            $a = trim($a);
            if(!empty($a)){
                @mysql_query("SET NAMES utf8");
                mysql_query($a) or die(mysql_error());
            }
        }
        $output .= 'Generation config.php.. ';
        $fname = "engine/nodes/config.php";
        $fname = fopen($fname, 'w') or die("Error. Can't open file engine/nodes/config.php");
        $source = '/**'."\n".'
* Framework config file'."\n".'
*/'."\n".'
$_SERVER["config"] = array('."\n".'
    "name" => "'. mysql_real_escape_string($_POST["name"]).'",'."\n".'
    "sql_server" => "'. mysql_real_escape_string($_POST["mysql_server"]).'",'."\n".'
    "sql_login" => "'. mysql_real_escape_string($_POST["mysql_login"]).'",'."\n".'
    "sql_pass" => "'. mysql_real_escape_string($_POST["mysql_pass"]).'",'."\n".'
    "sql_db" => "'. mysql_real_escape_string($_POST["mysql_db"]).'"'."\n".'
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
'* @name    Nodes Studio    @version 2.0.3'."\n".
'* @author  Ripak Forzaken  <developing@nodes-tech.ru>'."\n".
'* @license http://www.apache.org/licenses/LICENSE-2.0 GNU Public License'."\n".
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
(5, 6, 'sed_augue_elit_interdum_et_risus_eu', 'en', 0, 'Sed augue elit, interdum et risus eu', '<p>Sed augue elit, interdum et risus eu, dapibus laoreet ligula. Sed interdum mi vitae tempor dignissim. Phasellus et accumsan elit, placerat finibus dolor. Sed consectetur leo ut augue sollicitudin, ut fermentum tortor aliquet. Donec at nibh sagittis, pretium ex a, malesuada ligula. Donec efficitur at felis nec volutpat. Praesent imperdiet enim sed magna accumsan semper quis eu nisi. Nunc sed leo porttitor, hendrerit est vel, dictum felis.</p>\r\n<p>Phasellus at erat vitae purus efficitur commodo eu eu orci. Quisque auctor lobortis justo ut venenatis. Nulla facilisi. Integer non scelerisque ex, sed tristique enim. Nunc lacus nunc, bibendum id semper in, porta consectetur est. Duis ultrices et ex at imperdiet. Nullam non scelerisque metus. Nullam fringilla ligula efficitur nibh venenatis, et dictum mi commodo. Aenean ultrices ut lectus sit amet venenatis. Vivamus finibus sit amet turpis quis sagittis. Pellentesque porttitor sapien et tortor lacinia, id malesuada metus dictum. Pellentesque ac volutpat urna.</p>', '6bae51404bbb794916fd5608afaab13f.jpg', ".date("U").", ".date("U")."),
(6, 7, 'maecenas_laoreet_ut_purus_nec_dictum', 'en', 0, 'Maecenas laoreet ut purus nec dictum', '<p>Maecenas laoreet ut purus nec dictum. Pellentesque condimentum nunc tellus, sed consectetur nulla efficitur nec. Nulla blandit a lorem at laoreet. Aliquam auctor aliquet viverra. Quisque hendrerit felis in quam hendrerit, vel porta nisi sodales. Aenean ac rhoncus libero. Morbi sit amet erat sed leo lacinia tempor nec eu velit. Pellentesque quis elementum ante. Quisque consectetur nisi ut ex aliquet varius. Nullam molestie mi sed odio hendrerit, ac semper nisl lobortis. Sed erat libero, dapibus sit amet posuere in, vestibulum hendrerit nisl. Fusce facilisis, arcu et convallis dignissim, dolor nibh fringilla massa, eget sodales turpis ligula quis lectus. Quisque dictum ex diam, vel sagittis ligula faucibus quis. Morbi pretium sapien ut fringilla aliquet. Nam sed fermentum ligula.</p>\r\n<p>Suspendisse vehicula nibh libero, et commodo enim imperdiet in. Phasellus ut laoreet arcu. Proin tincidunt odio vel arcu efficitur, eu ullamcorper magna venenatis. Pellentesque condimentum dictum tempus. Sed lacinia ante mi, vel convallis lectus vehicula id. Morbi ac interdum justo. Ut venenatis feugiat ligula, non rutrum lacus sollicitudin sed.</p>', '90a71fb7815be592731c9a7f904a105d.jpg', ".date("U").", ".date("U")."),
(7, 8, 'suspendisse_egestas', 'en', 0, 'Suspendisse Egestas', '<div>Suspendisse egestas auctor orci quis convallis. Curabitur commodo dapibus urna at tincidunt. Aliquam imperdiet condimentum gravida. Vivamus nisi nisl, pharetra quis accumsan a, fermentum eu augue. Morbi vel risus a neque lacinia luctus ut ac urna. Lorem ipsum dolor sit amet, consectetur adipiscing elit. Morbi tempus non ex eu aliquet.<br />Pellentesque leo quam, congue id imperdiet at, ullamcorper sit amet mi. Duis ligula yusto, feugiat in sem lobortis, cursus blandit neque. Vestibulum dignissim imperdiet elit, sed faucibus nulla tincidunt id. Sed sem dui, pellentesque eu ligula ac, dictum placerat ligula. Cum sociis natoque penatibus et magnis dis parturient montes, nascetur ridiculus mus. Nam accumsan quam nibh, ultricies fringilla odio egestas a. Mauris in rhoncus risus. Nulla blandit sem neque, ut hendrerit tellus luctus non. Nullam sed blandit lectus.</div>', '1594d26e8cac71099fc3e6079cc0ddf5.jpg', ".date("U").", ".date("U")."),
(8, 8, 'nunts_platserat_dolor', 'en', 0, 'Nunts platserat dolor', '<div>Nunc placerat, dolor quis tincidunt venenatis, sem lorem tincidunt felis, at vestibulum felis yusto quis velit. Fusce sit amet nunc nec nibh interdum sodales. Sed magna sapien, maximus sed bibendum sit amet, porttitor eu diam. Suspendisse tristique elit ante, sit amet efficitur nulla pellentesque at. Etiam pharetra odio et urna viverra, et luctus erat efficitur. Donec volutpat nulla ut lorem semper aliquet eget quis ipsum. Mauris eu dolor metus. Vivamus vekhicula metus dolor, ac posuere lorem aliquam a. Quisque tincidunt, erat eu bibendum mollis, ligula dolor interdum augue, et euismod yusto augue ac leo.<br />Curabitur et malesuada tortor, a volutpat yusto. Proin finibus leo eros, ut consequat metus euismod rutrum. Nulla vitae purus rhoncus, porttitor velit sed, consequat mauris. Vivamus aliquet pellentesque odio, sed scelerisque arcu iaculis nec. Vestibulum turpis ante, placerat ac ultricies sit amet, pharetra ullamcorper nibh. Vivamus ac nisi dui. Fusce felis nulla, fringilla sodales velit in, consectetur finibus sapien. Morbi molestie iaculis est, at lacinia arcu tristique vitae. In vulputate nulla eget iaculis pulvinar.</div>', 'af49139bf8468d73fdaf5d1353846cd9.jpg', ".date("U").", ".date("U")."),
(9, 8, 'mauris_non_letstus', 'en', 0, 'Mauris non letstus', '<div>Mauris non lectus elit. Fusce convallis eu quam at iaculis. Donec eu faucibus arcu. Nam non purus at neque imperdiet dignissim. Nullam fringilla augue eget nisi fringilla, eu consequat dolor ornare. Duis vekhicula tempus nunc vitae blandit. Aliquam ut aliquam ex. Donec et lobortis dolor. Fusce gravida eu ex eu fringilla. Aenean non quam molestie, elementum ipsum egestas, congue libero. Suspendisse vel nunc risus. Aenean eget mollis dolor.<br />Donec nec ipsum sollicitudin, facilisis mauris a, volutpat quam. Sed mollis mauris nulla, sit amet tempus est laoreet luctus. Suspendisse vitae dui tortor. Class aptent taciti sociosqu ad litora torquent per conubia nostra, per inceptos himenaeos. Morbi dignissim mauris sem, at dignissim leo accumsan id. Ut eget suscipit ligula. Cras nibh dolor, sodales non dapibus id, dictum nec nulla. Cras vitae odio imperdiet, sodales velit non, lacinia ante. Donec magna magna, imperdiet sit amet magna at, dictum faucibus risus. Quisque at suscipit est. Duis non ligula sed quam tincidunt fermentum et ac purus. Integer molestie lectus id nisi auctor suscipit in id quam.</div>', 'c19f3ddc36a060a42bb88ceb9498a86c.jpg', ".date("U").", ".date("U")."),
(10, 9, 'lorem_ipsum', 'ru', 1, 'Лорем ипсум', '<p>Лорем ипсум долор сит амет, цонсецтетур адиписцинг елит. Нуллам пулвинар цонгуе порта. Интегер лаореет анте еу теллус пхаретра виверра. Донец масса лорем, цонгуе вел орци егет, порттитор аццумсан юсто. Дуис сусципит цонсеqуат цонгуе. Лорем ипсум долор сит амет, цонсецтетур адиписцинг елит. Дуис вел ниси орци. Сед луцтус ин анте сед сусципит. Сед лациниа велит сцелерисqуе пурус еффицитур виверра. Сед лаореет бландит маурис. Вестибулум ат елит вел дуи моллис тристиqуе. Фусце рисус нунц, темпор сед бландит сед, улламцорпер а дуи. Фусце еу ауцтор неqуе, ат цоммодо ерос. Пхаселлус лигула еx, малесуада ин луцтус витае, лаореет ин еним. Етиам претиум нулла нец финибус семпер. Пеллентесqуе еффицитур ерос ин магна луцтус цоммодо.</p>', '5f8119c6c035102f15716fd6c7a09ad6.jpg', ".date("U").", ".date("U")."),
(11, 9, 'in_tempor_turpis_sit', 'ru', 2, 'Ин темпор турпис сит', '<p>Ин темпор турпис сит амет аугуе сцелерисqуе, нон порта еним вестибулум. Алиqуам сед нибх фрингилла лорем феугиат моллис ац нец нунц. Аенеан финибус метус нон лациниа лаореет. Нулла ат малесуада лигула. Донец нец орци вел анте ултрициес алиqуам. Црас вел ипсум ут либеро цондиментум бландит нец вел арцу. Проин ет лигула дигниссим, цонсеqуат сем ат, еффицитур ест. Проин ид виверра масса. Нунц темпус долор егет лигула цоммодо, ет иацулис qуам волутпат. Ин нон дицтум ипсум, вел ултрициес qуам.</p>', 'cc5155f90c9eec7ed1311540fb3865f0.jpg', ".date("U").", ".date("U")."),
(12, 9, 'nunc_aliquet_tellus_metus', 'ru', 3, 'Нунц алиqует теллус метус', '<p>Нунц алиqует теллус метус, егет вехицула турпис елементум еу. Сед еу вененатис орци. Фусце сит амет ерат ут ест цонваллис молестие. Алиqуам ут вехицула лорем, ат интердум урна. Цурабитур нулла ерос, цонсецтетур ид финибус сед, цондиментум ац нибх. Нулла ид долор лобортис, фрингилла ест ид, сцелерисqуе неqуе. Вивамус qуис либеро нон ниси рутрум тинцидунт. Цурабитур вариус ми витае маурис цурсус цондиментум. Проин ат плацерат еx. Сед хендрерит qуам ид порта маxимус. Дуис ут гравида лорем. Аенеан сит амет интердум сапиен.</p>', '73c46f4a48df76e2ea1b78e743cf73a6.jpg', ".date("U").", ".date("U")."),
(13, 10, 'nullam_a_velit_feugiat', 'ru', 0, 'Нуллам а велит феугиат', '<p>Нуллам а велит феугиат, елеифенд ест вел, егестас арцу. Нулла виверра бибендум ерос, ац еффицитур диам рутрум ин. Вестибулум вел магна вулпутате, порта фелис витае, егестас пурус. Вестибулум егестас турпис пурус, егет алиqует масса сцелерисqуе вестибулум. Фусце сед магна орци. Праесент цурсус либеро ут ултрициес цурсус. Морби ид сапиен сед диам лаореет порттитор порта qуис велит. Црас тортор ипсум, ауцтор сит амет сем ац, виверра феугиат велит. Дуис нец дапибус турпис. Qуисqуе лорем долор, темпор нон еним ет, цондиментум ултрицес долор. Донец qуис семпер диам, ут вулпутате магна. Црас хендрерит рисус сит амет масса вестибулум порта. Нам елементум елит ерат, витае феугиат теллус дапибус егет. Пхаселлус аццумсан улламцорпер qуам.</p>', '039f622d5dce82f34d18cde2dbb53a90.jpg', ".date("U").", ".date("U")."),
(14, 10, 'sed_augue_elit_interdum_et_risus_eu', 'ru', 0, 'Сед аугуе елит, интердум ет рисус', '<p>Сед аугуе елит, интердум ет рисус еу, дапибус лаореет лигула. Сед интердум ми витае темпор дигниссим. Пхаселлус ет аццумсан елит, плацерат финибус долор. Сед цонсецтетур лео ут аугуе соллицитудин, ут ферментум тортор алиqует. Донец ат нибх сагиттис, претиум еx а, малесуада лигула. Донец еффицитур ат фелис нец волутпат. Праесент импердиет еним сед магна аццумсан семпер qуис еу ниси. Нунц сед лео порттитор, хендрерит ест вел, дицтум фелис.<br />Пхаселлус ат ерат витае пурус еффицитур цоммодо еу еу орци. Qуисqуе ауцтор лобортис юсто ут вененатис. Нулла фацилиси. Интегер нон сцелерисqуе еx, сед тристиqуе еним. Нунц лацус нунц, бибендум ид семпер ин, порта цонсецтетур ест. Дуис ултрицес ет еx ат импердиет. Нуллам нон сцелерисqуе метус. Нуллам фрингилла лигула еффицитур нибх вененатис, ет дицтум ми цоммодо. Аенеан ултрицес ут лецтус сит амет вененатис. Вивамус финибус сит амет турпис qуис сагиттис. Пеллентесqуе порттитор сапиен ет тортор лациниа, ид малесуада метус дицтум. Пеллентесqуе ац волутпат урна.</p>', '6bae51404bbb794916fd5608afaab13f.jpg', ".date("U").", ".date("U")."),
(15, 11, 'maecenas_laoreet_ut_purus_nec_dictum', 'ru', 0, 'Маеценас лаореет ут пурус', '<p>Маеценас лаореет ут пурус нец дицтум. Пеллентесqуе цондиментум нунц теллус, сед цонсецтетур нулла еффицитур нец. Нулла бландит а лорем ат лаореет. Алиqуам ауцтор алиqует виверра. Qуисqуе хендрерит фелис ин qуам хендрерит, вел порта ниси содалес. Аенеан ац рхонцус либеро. Морби сит амет ерат сед лео лациниа темпор нец еу велит. Пеллентесqуе qуис елементум анте. Qуисqуе цонсецтетур ниси ут еx алиqует вариус. Нуллам молестие ми сед одио хендрерит, ац семпер нисл лобортис. Сед ерат либеро, дапибус сит амет посуере ин, вестибулум хендрерит нисл. Фусце фацилисис, арцу ет цонваллис дигниссим, долор нибх фрингилла масса, егет содалес турпис лигула qуис лецтус. Qуисqуе дицтум еx диам, вел сагиттис лигула фауцибус qуис. Морби претиум сапиен ут фрингилла алиqует. Нам сед ферментум лигула.<br />Суспендиссе вехицула нибх либеро, ет цоммодо еним импердиет ин. Пхаселлус ут лаореет арцу. Проин тинцидунт одио вел арцу еффицитур, еу улламцорпер магна вененатис. Пеллентесqуе цондиментум дицтум темпус. Сед лациниа анте ми, вел цонваллис лецтус вехицула ид. Морби ац интердум юсто. Ут вененатис феугиат лигула, нон рутрум лацус соллицитудин сед.</p>', '90a71fb7815be592731c9a7f904a105d.jpg', ".date("U").", ".date("U")."),
(16, 12, 'suspendisse_egestas', 'ru', 0, 'Суспендиссе егестас', '<div>Суспендиссе егестас ауцтор орци qуис цонваллис. Цурабитур цоммодо дапибус урна ат тинцидунт. Алиqуам импердиет цондиментум гравида. Вивамус ниси нисл, пхаретра qуис аццумсан а, ферментум еу аугуе. Морби вел рисус а неqуе лациниа луцтус ут ац урна. Лорем ипсум долор сит амет, цонсецтетур адиписцинг елит. Морби темпус нон еx еу алиqует.</div>\r\n<div>Пеллентесqуе лео qуам, цонгуе ид импердиет ат, улламцорпер сит амет ми. Дуис лигула юсто, феугиат ин сем лобортис, цурсус бландит неqуе. Вестибулум дигниссим импердиет елит, сед фауцибус нулла тинцидунт ид. Сед сем дуи, пеллентесqуе еу лигула ац, дицтум плацерат лигула. Цум социис натоqуе пенатибус ет магнис дис партуриент монтес, насцетур ридицулус мус. Нам аццумсан qуам нибх, ултрициес фрингилла одио егестас а. Маурис ин рхонцус рисус. Нулла бландит сем неqуе, ут хендрерит теллус луцтус нон. Нуллам сед бландит лецтус.</div>', '1594d26e8cac71099fc3e6079cc0ddf5.jpg', ".date("U").", ".date("U")."),
(17, 12, 'nunts_platserat_dolor', 'ru', 0, 'Нунц плацерат, долор', '<div>Нунц плацерат, долор qуис тинцидунт вененатис, сем лорем тинцидунт фелис, ат вестибулум фелис юсто qуис велит. Фусце сит амет нунц нец нибх интердум содалес. Сед магна сапиен, маxимус сед бибендум сит амет, порттитор еу диам. Суспендиссе тристиqуе елит анте, сит амет еффицитур нулла пеллентесqуе ат. Етиам пхаретра одио ет урна виверра, ет луцтус ерат еффицитур. Донец волутпат нулла ут лорем семпер алиqует егет qуис ипсум. Маурис еу долор метус. Вивамус вехицула метус долор, ац посуере лорем алиqуам а. Qуисqуе тинцидунт, ерат еу бибендум моллис, лигула долор интердум аугуе, ет еуисмод юсто аугуе ац лео.</div>\r\n<div>Цурабитур ет малесуада тортор, а волутпат юсто. Проин финибус лео ерос, ут цонсеqуат метус еуисмод рутрум. Нулла витае пурус рхонцус, порттитор велит сед, цонсеqуат маурис. Вивамус алиqует пеллентесqуе одио, сед сцелерисqуе арцу иацулис нец. Вестибулум турпис анте, плацерат ац ултрициес сит амет, пхаретра улламцорпер нибх. Вивамус ац ниси дуи. Фусце фелис нулла, фрингилла содалес велит ин, цонсецтетур финибус сапиен. Морби молестие иацулис ест, ат лациниа арцу тристиqуе витае. Ин вулпутате нулла егет иацулис пулвинар.</div>', 'af49139bf8468d73fdaf5d1353846cd9.jpg', ".date("U").", ".date("U")."),
(18, 12, 'mauris_non_letstus', 'ru', 0, 'Маурис нон лецтус', '<div>Маурис нон лецтус елит. Фусце цонваллис еу qуам ат иацулис. Донец еу фауцибус арцу. Нам нон пурус ат неqуе импердиет дигниссим. Нуллам фрингилла аугуе егет ниси фрингилла, еу цонсеqуат долор орнаре. Дуис вехицула темпус нунц витае бландит. Алиqуам ут алиqуам еx. Донец ет лобортис долор. Фусце гравида еу еx еу фрингилла. Аенеан нон qуам молестие, елементум ипсум егестас, цонгуе либеро. Суспендиссе вел нунц рисус. Аенеан егет моллис долор.</div>\r\n<div>Донец нец ипсум соллицитудин, фацилисис маурис а, волутпат qуам. Сед моллис маурис нулла, сит амет темпус ест лаореет луцтус. Суспендиссе витае дуи тортор. Цласс аптент тацити социосqу ад литора торqуент пер цонубиа ностра, пер инцептос хименаеос. Морби дигниссим маурис сем, ат дигниссим лео аццумсан ид. Ут егет сусципит лигула. Црас нибх долор, содалес нон дапибус ид, дицтум нец нулла. Црас витае одио импердиет, содалес велит нон, лациниа анте. Донец магна магна, импердиет сит амет магна ат, дицтум фауцибус рисус. Qуисqуе ат сусципит ест. Дуис нон лигула сед qуам тинцидунт ферментум ет ац пурус. Интегер молестие лецтус ид ниси ауцтор сусципит ин ид qуам.</div>', 'c19f3ddc36a060a42bb88ceb9498a86c.jpg', ".date("U").", ".date("U").");


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
";
            $arr = explode(";
", $query);
            $flag = 0;
            foreach($arr as $a){
                $a = trim($a);
                if(!empty($a)){
                    @mysql_query("SET NAMES utf8");
                    mysql_query($a) or die(mysql_error());
                    $flag++;
                }
            }
            require_once("engine/core/file.php");
            file::copy($_SERVER["DOCUMENT_ROOT"].$_SERVER["DIR"]."/temp/data", $_SERVER["DOCUMENT_ROOT"].$_SERVER["DIR"]."/img/data");
            unlink($_SERVER["DOCUMENT_ROOT"].$_SERVER["DIR"]."/temp/data");
            $output .= 'Ok.<br/>';
        }
        $query = 'SELECT * FROM `nodes_user` WHERE `id` = "1"';
        @mysql_query("SET NAMES utf8");
        $res = mysql_query($query) or die(mysql_error());
        $data = mysql_fetch_array($res);
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
    if(is_dir($_SERVER["DOCUMENT_ROOT"].$_SERVER["DIR"]."/temp/data")){
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
    <div style="width: 110px; float: left; margin-right: 10px;">Site name</div> <input class="input" required="required" type="text" name="name" value="Nodes Studio" ><br/>
    <div style="width: 110px; float: left; margin-right: 10px;">Site description</div> <input class="input" required="required" type="text" name="description" value="Web 2.0 Framework" ><br/>
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
ini_set('session.name', 'token');
ini_set('session.save_path', $_SERVER["DOCUMENT_ROOT"].$_SERVER["DIR"].'/session');
ini_set('session.gc_maxlifetime', 604800);
ini_set('session.entropy_file', '/dev/urandom');
ini_set('session.entropy_length', '512');
session_set_cookie_params(0, '/', '.'.$_SERVER["HTTP_HOST"]);
session_name('token');
session_start();
if(empty($_SESSION["Lang"])) $_SESSION["Lang"] = "en";
if(!empty($_POST["mysql_test"])){
    if(mysql_connect($_POST["server"], 
        $_POST["login"],
        $_POST["pass"])){
        if(mysql_select_db($_POST["db"])){ 
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
<meta name="description" content="Nodes Studio - Web 2.0 Framework" />
<meta property="og:description" content="Nodes Studio - Web 2.0 Framework" />
<link href="<?php echo $_SERVER["DIR"]; ?>/template/nodes.css" rel="stylesheet" type="text/css" />
<link href="<?php echo $_SERVER["DIR"]; ?>/template/default/template.css" rel="stylesheet" type="text/css" />
<?php
require_once('template/meta.php');
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