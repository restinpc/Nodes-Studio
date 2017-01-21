<?php
/**
* Framework Installer.
* @path /engine/code/install.php
*
* @name    Nodes Studio    @version 2.0.2
* @author  Alexandr Virtual    <developing@nodes-tech.ru>
* @license http://nodes-studio.com/license.txt GNU Public License
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
        `nodes_inbox`,
        `nodes_language`,
        `nodes_log`,
        `nodes_meta`,
        `nodes_order`,
        `nodes_outbox`,
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
  UNIQUE KEY `name` (`name`),
  PRIMARY KEY (`id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

INSERT INTO `nodes_config` (`name`, `value`, `text`, `type`) VALUES
('name', '".mysql_real_escape_string($_POST["name"])."', 'Site name', 'string'),
('description', '".mysql_real_escape_string($_POST["description"])."', 'Description', 'string'),
('email', '".mysql_real_escape_string($_POST["admin_email"])."', 'Site email', 'string'),
('language', '".mysql_real_escape_string($_POST["language"])."', 'Site language', 'string'),
('languages', '".mysql_real_escape_string(str_replace("'", "\'", $_POST["languages"]))."', 'Available languages', 'string'),
('image', '".$_SERVER["DIR"]."/img/cms/nodes_studio.png', 'Site image', 'string'),
('email_image', '".$_SERVER["DIR"]."/img/logo.png', 'Email header image', 'string');

INSERT INTO `nodes_user` (`name`, `photo`, `url`, `email`, `pass`, `balance`, `ip`, `ban`, `online`, `token`, `confirm`, `code`, `bulk_ignore`) VALUES
('".mysql_real_escape_string(str_replace("'", "\'", $_POST["admin_name"]))."', 'admin.jpg', '', '".htmlspecialchars($_POST["admin_email"])."', '".md5(strtolower($_POST["admin_pass"]))."', 0, '', -1, 0, '', 1, 0, 0);
        
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
        $output .= "Receiving MySQL data.. ";
        $sql = file_get_contents("http://nodes-studio.com/setup.php?host=".$_SERVER["HTTP_HOST"]);
        if(empty($sql)){
            $sql = file_get_contents ("res/db.sql");
        }
        $arr = explode(";"."\n", $sql);
        $flag = 0;
        foreach($arr as $a){
            $a = trim($a);
            if(!empty($a)){
                @mysql_query("SET NAMES utf8");
                mysql_query($a) or die(mysql_error());
                $flag++;
            }
        }
        if($flag) $output .= "Ok.<br/>Executed ".$flag." MySQL commands.</br>";
        else if(empty($arr)) $output .= "Ok. File is empty.<br/>";
        else return($output."Error.<br/>Installation aborted.".$error_output);
        $output .= 'Generation config.php.. ';
        $fname = "engine/nodes/config.php";
        $fname = fopen($fname, 'w') or die("Error. Can't open file engine/nodes/config.php");
        $source = '/**'."\n".'
* Framework config file'."\n".'
*/'."\n".'
global $config;'."\n".'
$config = array('."\n".'
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
        $fname = "cron.php";
        $fname = fopen($fname, 'w') or die("Error. Can't open file cron.php");
        $code = '#!/usr/bin/php
<?php 
/**
* Executable crontab file.
* Should be configured on autoexec every 1 minute.
*
* @name    Nodes Studio    @version 2.0.2
* @author  Alexandr Virtual    <developing@nodes-tech.ru>
* @license http://nodes-studio.com/license.txt GNU Public License
*/
$_SERVER["HTTP_HOST"] = "'.$_SERVER["HTTP_HOST"].'";
$_SERVER["DOCUMENT_ROOT"] = "'.$_SERVER["DOCUMENT_ROOT"].'";
$_SERVER["REQUEST_URI"] = "/cron.php";
ini_set(\'include_path\', $_SERVER["DOCUMENT_ROOT"]);
require_once("engine/nodes/engine.php");
require_once("engine/nodes/autoload.php");';
        fwrite($fname, $code);
        fclose($fname);
        chmod($file, 0705);
        $output .= 'Ok.<br/>';
        if(!empty($_POST["temp"])){
            $output .= 'Replacing temp data.. ';
            $query = "
INSERT INTO `nodes_catalog` (`id`, `caption`, `text`, `url`, `img`, `visible`, `lang`) VALUES
(5, 'Blog', '<p>Blog</p>', 'blog', '', 1, 'en'),
(6, 'News', '<p>News</p>', 'news', '', 1, 'en'),
(7, 'Events', '<p>Events</p>', 'events', '', 1, 'en'),
(8, 'Reviews', '<p>Reviews</p>', 'reviews', '', 1, 'en'),
(9, 'Блог', '<p>Блог</p>', 'blog', '', 1, 'ru'),
(10, 'Новости', '<p>Новости</p>', 'news', '', 1, 'ru'),
(11, 'События', '<p>События</p>', 'events', '', 1, 'ru'),
(12, 'Обзоры', '<p>Обзоры</p>', 'reviews', '', 1, 'ru');

INSERT INTO `nodes_content` (`cat_id`, `url`, `lang`, `order`, `caption`, `text`, `img`, `date`) VALUES
(5, 'lorem_ipsum', 'en', 0, 'Lorem ipsum', '<p>Lorem ipsum dolor sit amet, consectetur adipiscing elit. Nullam pulvinar congue porta. Integer laoreet ante eu tellus pharetra viverra. Donec massa lorem, congue vel orci eget, porttitor accumsan justo. Duis suscipit consequat congue. Lorem ipsum dolor sit amet, consectetur adipiscing elit. Duis vel nisi orci. Sed luctus in ante sed suscipit. Sed lacinia velit scelerisque purus efficitur viverra. Sed laoreet blandit mauris. Vestibulum at elit vel dui mollis tristique. Fusce risus nunc, tempor sed blandit sed, ullamcorper a dui. Fusce eu auctor neque, at commodo eros. Phasellus ligula ex, malesuada in luctus vitae, laoreet in enim. Etiam pretium nulla nec finibus semper. Pellentesque efficitur eros in magna luctus commodo.</span></p>', '5f8119c6c035102f15716fd6c7a09ad6.jpg', 1483004122),
(5, 'in_tempor_turpis_sit', 'en', 0, 'In tempor turpis sit', '<p>In tempor turpis sit amet augue scelerisque, non porta enim vestibulum. Aliquam sed nibh fringilla lorem feugiat mollis ac nec nunc. Aenean finibus metus non lacinia laoreet. Nulla at malesuada ligula. Donec nec orci vel ante ultricies aliquam. Cras vel ipsum ut libero condimentum blandit nec vel arcu. Proin et ligula dignissim, consequat sem at, efficitur est. Proin id viverra massa. Nunc tempus dolor eget ligula commodo, et iaculis quam volutpat. In non dictum ipsum, vel ultricies quam.</span></p>', 'cc5155f90c9eec7ed1311540fb3865f0.jpg', 1483004174),
(5, 'nunc_aliquet_tellus_metus', 'en', 0, 'Nunc aliquet tellus metus', '<p>Nunc aliquet tellus metus, eget vehicula turpis elementum eu. Sed eu venenatis orci. Fusce sit amet erat ut est convallis molestie. Aliquam ut vehicula lorem, at interdum urna. Curabitur nulla eros, consectetur id finibus sed, condimentum ac nibh. Nulla id dolor lobortis, fringilla est id, scelerisque neque. Vivamus quis libero non nisi rutrum tincidunt. Curabitur varius mi vitae mauris cursus condimentum. Proin at placerat ex. Sed hendrerit quam id porta maximus. Duis ut gravida lorem. Aenean sit amet interdum sapien.</span></p>', '73c46f4a48df76e2ea1b78e743cf73a6.jpg', 1483004286),
(6, 'nullam_a_velit_feugiat', 'en', 0, 'Nullam a velit feugiat', '<p>Nullam a velit feugiat, eleifend est vel, egestas arcu. Nulla viverra bibendum eros, ac efficitur diam rutrum in. Vestibulum vel magna vulputate, porta felis vitae, egestas purus. Vestibulum egestas turpis purus, eget aliquet massa scelerisque vestibulum. Fusce sed magna orci. Praesent cursus libero ut ultricies cursus. Morbi id sapien sed diam laoreet porttitor porta quis velit. Cras tortor ipsum, auctor sit amet sem ac, viverra feugiat velit. Duis nec dapibus turpis. Quisque lorem dolor, tempor non enim et, condimentum ultrices dolor. Donec quis semper diam, ut vulputate magna. Cras hendrerit risus sit amet massa vestibulum porta. Nam elementum elit erat, vitae feugiat tellus dapibus eget. Phasellus accumsan ullamcorper quam.</span></p>', '039f622d5dce82f34d18cde2dbb53a90.jpg', 1483004542),
(6, 'sed_augue_elit_interdum_et_risus_eu', 'en', 0, 'Sed augue elit, interdum et risus eu', '<p>Sed augue elit, interdum et risus eu, dapibus laoreet ligula. Sed interdum mi vitae tempor dignissim. Phasellus et accumsan elit, placerat finibus dolor. Sed consectetur leo ut augue sollicitudin, ut fermentum tortor aliquet. Donec at nibh sagittis, pretium ex a, malesuada ligula. Donec efficitur at felis nec volutpat. Praesent imperdiet enim sed magna accumsan semper quis eu nisi. Nunc sed leo porttitor, hendrerit est vel, dictum felis.</p>\r\n<p>Phasellus at erat vitae purus efficitur commodo eu eu orci. Quisque auctor lobortis justo ut venenatis. Nulla facilisi. Integer non scelerisque ex, sed tristique enim. Nunc lacus nunc, bibendum id semper in, porta consectetur est. Duis ultrices et ex at imperdiet. Nullam non scelerisque metus. Nullam fringilla ligula efficitur nibh venenatis, et dictum mi commodo. Aenean ultrices ut lectus sit amet venenatis. Vivamus finibus sit amet turpis quis sagittis. Pellentesque porttitor sapien et tortor lacinia, id malesuada metus dictum. Pellentesque ac volutpat urna.</p>', '6bae51404bbb794916fd5608afaab13f.jpg', 1483004615),
(7, 'maecenas_laoreet_ut_purus_nec_dictum', 'en', 0, 'Maecenas laoreet ut purus nec dictum', '<p>Maecenas laoreet ut purus nec dictum. Pellentesque condimentum nunc tellus, sed consectetur nulla efficitur nec. Nulla blandit a lorem at laoreet. Aliquam auctor aliquet viverra. Quisque hendrerit felis in quam hendrerit, vel porta nisi sodales. Aenean ac rhoncus libero. Morbi sit amet erat sed leo lacinia tempor nec eu velit. Pellentesque quis elementum ante. Quisque consectetur nisi ut ex aliquet varius. Nullam molestie mi sed odio hendrerit, ac semper nisl lobortis. Sed erat libero, dapibus sit amet posuere in, vestibulum hendrerit nisl. Fusce facilisis, arcu et convallis dignissim, dolor nibh fringilla massa, eget sodales turpis ligula quis lectus. Quisque dictum ex diam, vel sagittis ligula faucibus quis. Morbi pretium sapien ut fringilla aliquet. Nam sed fermentum ligula.</p>\r\n<p>Suspendisse vehicula nibh libero, et commodo enim imperdiet in. Phasellus ut laoreet arcu. Proin tincidunt odio vel arcu efficitur, eu ullamcorper magna venenatis. Pellentesque condimentum dictum tempus. Sed lacinia ante mi, vel convallis lectus vehicula id. Morbi ac interdum justo. Ut venenatis feugiat ligula, non rutrum lacus sollicitudin sed.</p>', '90a71fb7815be592731c9a7f904a105d.jpg', 1483004973),
(8, 'suspendisse_egestas', 'en', 0, 'Suspendisse Egestas', '<div>Suspendisse egestas auctor orci quis convallis. Curabitur commodo dapibus urna at tincidunt. Aliquam imperdiet condimentum gravida. Vivamus nisi nisl, pharetra quis accumsan a, fermentum eu augue. Morbi vel risus a neque lacinia luctus ut ac urna. Lorem ipsum dolor sit amet, consectetur adipiscing elit. Morbi tempus non ex eu aliquet.<br />Pellentesque leo quam, congue id imperdiet at, ullamcorper sit amet mi. Duis ligula yusto, feugiat in sem lobortis, cursus blandit neque. Vestibulum dignissim imperdiet elit, sed faucibus nulla tincidunt id. Sed sem dui, pellentesque eu ligula ac, dictum placerat ligula. Cum sociis natoque penatibus et magnis dis parturient montes, nascetur ridiculus mus. Nam accumsan quam nibh, ultricies fringilla odio egestas a. Mauris in rhoncus risus. Nulla blandit sem neque, ut hendrerit tellus luctus non. Nullam sed blandit lectus.</div>', '1594d26e8cac71099fc3e6079cc0ddf5.jpg', 1483094346),
(8, 'nunts_platserat_dolor', 'en', 0, 'Nunts platserat dolor', '<div>Nunc placerat, dolor quis tincidunt venenatis, sem lorem tincidunt felis, at vestibulum felis yusto quis velit. Fusce sit amet nunc nec nibh interdum sodales. Sed magna sapien, maximus sed bibendum sit amet, porttitor eu diam. Suspendisse tristique elit ante, sit amet efficitur nulla pellentesque at. Etiam pharetra odio et urna viverra, et luctus erat efficitur. Donec volutpat nulla ut lorem semper aliquet eget quis ipsum. Mauris eu dolor metus. Vivamus vekhicula metus dolor, ac posuere lorem aliquam a. Quisque tincidunt, erat eu bibendum mollis, ligula dolor interdum augue, et euismod yusto augue ac leo.<br />Curabitur et malesuada tortor, a volutpat yusto. Proin finibus leo eros, ut consequat metus euismod rutrum. Nulla vitae purus rhoncus, porttitor velit sed, consequat mauris. Vivamus aliquet pellentesque odio, sed scelerisque arcu iaculis nec. Vestibulum turpis ante, placerat ac ultricies sit amet, pharetra ullamcorper nibh. Vivamus ac nisi dui. Fusce felis nulla, fringilla sodales velit in, consectetur finibus sapien. Morbi molestie iaculis est, at lacinia arcu tristique vitae. In vulputate nulla eget iaculis pulvinar.</div>', 'af49139bf8468d73fdaf5d1353846cd9.jpg', 1483251590),
(8, 'mauris_non_letstus', 'en', 0, 'Mauris non letstus', '<div>Mauris non lectus elit. Fusce convallis eu quam at iaculis. Donec eu faucibus arcu. Nam non purus at neque imperdiet dignissim. Nullam fringilla augue eget nisi fringilla, eu consequat dolor ornare. Duis vekhicula tempus nunc vitae blandit. Aliquam ut aliquam ex. Donec et lobortis dolor. Fusce gravida eu ex eu fringilla. Aenean non quam molestie, elementum ipsum egestas, congue libero. Suspendisse vel nunc risus. Aenean eget mollis dolor.<br />Donec nec ipsum sollicitudin, facilisis mauris a, volutpat quam. Sed mollis mauris nulla, sit amet tempus est laoreet luctus. Suspendisse vitae dui tortor. Class aptent taciti sociosqu ad litora torquent per conubia nostra, per inceptos himenaeos. Morbi dignissim mauris sem, at dignissim leo accumsan id. Ut eget suscipit ligula. Cras nibh dolor, sodales non dapibus id, dictum nec nulla. Cras vitae odio imperdiet, sodales velit non, lacinia ante. Donec magna magna, imperdiet sit amet magna at, dictum faucibus risus. Quisque at suscipit est. Duis non ligula sed quam tincidunt fermentum et ac purus. Integer molestie lectus id nisi auctor suscipit in id quam.</div>', 'c19f3ddc36a060a42bb88ceb9498a86c.jpg', 1483251651),
(9, 'lorem_ipsum', 'ru', 1, 'Лорем ипсум', '<p>Лорем ипсум долор сит амет, цонсецтетур адиписцинг елит. Нуллам пулвинар цонгуе порта. Интегер лаореет анте еу теллус пхаретра виверра. Донец масса лорем, цонгуе вел орци егет, порттитор аццумсан юсто. Дуис сусципит цонсеqуат цонгуе. Лорем ипсум долор сит амет, цонсецтетур адиписцинг елит. Дуис вел ниси орци. Сед луцтус ин анте сед сусципит. Сед лациниа велит сцелерисqуе пурус еффицитур виверра. Сед лаореет бландит маурис. Вестибулум ат елит вел дуи моллис тристиqуе. Фусце рисус нунц, темпор сед бландит сед, улламцорпер а дуи. Фусце еу ауцтор неqуе, ат цоммодо ерос. Пхаселлус лигула еx, малесуада ин луцтус витае, лаореет ин еним. Етиам претиум нулла нец финибус семпер. Пеллентесqуе еффицитур ерос ин магна луцтус цоммодо.</p>', '5f8119c6c035102f15716fd6c7a09ad6.jpg', 1483252613),
(9, 'in_tempor_turpis_sit', 'ru', 2, 'Ин темпор турпис сит', '<p>Ин темпор турпис сит амет аугуе сцелерисqуе, нон порта еним вестибулум. Алиqуам сед нибх фрингилла лорем феугиат моллис ац нец нунц. Аенеан финибус метус нон лациниа лаореет. Нулла ат малесуада лигула. Донец нец орци вел анте ултрициес алиqуам. Црас вел ипсум ут либеро цондиментум бландит нец вел арцу. Проин ет лигула дигниссим, цонсеqуат сем ат, еффицитур ест. Проин ид виверра масса. Нунц темпус долор егет лигула цоммодо, ет иацулис qуам волутпат. Ин нон дицтум ипсум, вел ултрициес qуам.</p>', 'cc5155f90c9eec7ed1311540fb3865f0.jpg', 1483252574),
(9, 'nunc_aliquet_tellus_metus', 'ru', 3, 'Нунц алиqует теллус метус', '<p>Нунц алиqует теллус метус, егет вехицула турпис елементум еу. Сед еу вененатис орци. Фусце сит амет ерат ут ест цонваллис молестие. Алиqуам ут вехицула лорем, ат интердум урна. Цурабитур нулла ерос, цонсецтетур ид финибус сед, цондиментум ац нибх. Нулла ид долор лобортис, фрингилла ест ид, сцелерисqуе неqуе. Вивамус qуис либеро нон ниси рутрум тинцидунт. Цурабитур вариус ми витае маурис цурсус цондиментум. Проин ат плацерат еx. Сед хендрерит qуам ид порта маxимус. Дуис ут гравида лорем. Аенеан сит амет интердум сапиен.</p>', '73c46f4a48df76e2ea1b78e743cf73a6.jpg', 1483252395),
(10, 'nullam_a_velit_feugiat', 'ru', 0, 'Нуллам а велит феугиат', '<p>Нуллам а велит феугиат, елеифенд ест вел, егестас арцу. Нулла виверра бибендум ерос, ац еффицитур диам рутрум ин. Вестибулум вел магна вулпутате, порта фелис витае, егестас пурус. Вестибулум егестас турпис пурус, егет алиqует масса сцелерисqуе вестибулум. Фусце сед магна орци. Праесент цурсус либеро ут ултрициес цурсус. Морби ид сапиен сед диам лаореет порттитор порта qуис велит. Црас тортор ипсум, ауцтор сит амет сем ац, виверра феугиат велит. Дуис нец дапибус турпис. Qуисqуе лорем долор, темпор нон еним ет, цондиментум ултрицес долор. Донец qуис семпер диам, ут вулпутате магна. Црас хендрерит рисус сит амет масса вестибулум порта. Нам елементум елит ерат, витае феугиат теллус дапибус егет. Пхаселлус аццумсан улламцорпер qуам.</p>', '039f622d5dce82f34d18cde2dbb53a90.jpg', 1483251787),
(10, 'sed_augue_elit_interdum_et_risus_eu', 'ru', 0, 'Сед аугуе елит, интердум ет рисус', '<p>Сед аугуе елит, интердум ет рисус еу, дапибус лаореет лигула. Сед интердум ми витае темпор дигниссим. Пхаселлус ет аццумсан елит, плацерат финибус долор. Сед цонсецтетур лео ут аугуе соллицитудин, ут ферментум тортор алиqует. Донец ат нибх сагиттис, претиум еx а, малесуада лигула. Донец еффицитур ат фелис нец волутпат. Праесент импердиет еним сед магна аццумсан семпер qуис еу ниси. Нунц сед лео порттитор, хендрерит ест вел, дицтум фелис.<br />Пхаселлус ат ерат витае пурус еффицитур цоммодо еу еу орци. Qуисqуе ауцтор лобортис юсто ут вененатис. Нулла фацилиси. Интегер нон сцелерисqуе еx, сед тристиqуе еним. Нунц лацус нунц, бибендум ид семпер ин, порта цонсецтетур ест. Дуис ултрицес ет еx ат импердиет. Нуллам нон сцелерисqуе метус. Нуллам фрингилла лигула еффицитур нибх вененатис, ет дицтум ми цоммодо. Аенеан ултрицес ут лецтус сит амет вененатис. Вивамус финибус сит амет турпис qуис сагиттис. Пеллентесqуе порттитор сапиен ет тортор лациниа, ид малесуада метус дицтум. Пеллентесqуе ац волутпат урна.</p>', '6bae51404bbb794916fd5608afaab13f.jpg', 1483251888),
(11, 'maecenas_laoreet_ut_purus_nec_dictum', 'ru', 0, 'Маеценас лаореет ут пурус', '<p>Маеценас лаореет ут пурус нец дицтум. Пеллентесqуе цондиментум нунц теллус, сед цонсецтетур нулла еффицитур нец. Нулла бландит а лорем ат лаореет. Алиqуам ауцтор алиqует виверра. Qуисqуе хендрерит фелис ин qуам хендрерит, вел порта ниси содалес. Аенеан ац рхонцус либеро. Морби сит амет ерат сед лео лациниа темпор нец еу велит. Пеллентесqуе qуис елементум анте. Qуисqуе цонсецтетур ниси ут еx алиqует вариус. Нуллам молестие ми сед одио хендрерит, ац семпер нисл лобортис. Сед ерат либеро, дапибус сит амет посуере ин, вестибулум хендрерит нисл. Фусце фацилисис, арцу ет цонваллис дигниссим, долор нибх фрингилла масса, егет содалес турпис лигула qуис лецтус. Qуисqуе дицтум еx диам, вел сагиттис лигула фауцибус qуис. Морби претиум сапиен ут фрингилла алиqует. Нам сед ферментум лигула.<br />Суспендиссе вехицула нибх либеро, ет цоммодо еним импердиет ин. Пхаселлус ут лаореет арцу. Проин тинцидунт одио вел арцу еффицитур, еу улламцорпер магна вененатис. Пеллентесqуе цондиментум дицтум темпус. Сед лациниа анте ми, вел цонваллис лецтус вехицула ид. Морби ац интердум юсто. Ут вененатис феугиат лигула, нон рутрум лацус соллицитудин сед.</p>', '90a71fb7815be592731c9a7f904a105d.jpg', 1483252334),
(12, 'suspendisse_egestas', 'ru', 0, 'Суспендиссе егестас', '<div>Суспендиссе егестас ауцтор орци qуис цонваллис. Цурабитур цоммодо дапибус урна ат тинцидунт. Алиqуам импердиет цондиментум гравида. Вивамус ниси нисл, пхаретра qуис аццумсан а, ферментум еу аугуе. Морби вел рисус а неqуе лациниа луцтус ут ац урна. Лорем ипсум долор сит амет, цонсецтетур адиписцинг елит. Морби темпус нон еx еу алиqует.</div>\r\n<div>Пеллентесqуе лео qуам, цонгуе ид импердиет ат, улламцорпер сит амет ми. Дуис лигула юсто, феугиат ин сем лобортис, цурсус бландит неqуе. Вестибулум дигниссим импердиет елит, сед фауцибус нулла тинцидунт ид. Сед сем дуи, пеллентесqуе еу лигула ац, дицтум плацерат лигула. Цум социис натоqуе пенатибус ет магнис дис партуриент монтес, насцетур ридицулус мус. Нам аццумсан qуам нибх, ултрициес фрингилла одио егестас а. Маурис ин рхонцус рисус. Нулла бландит сем неqуе, ут хендрерит теллус луцтус нон. Нуллам сед бландит лецтус.</div>', '1594d26e8cac71099fc3e6079cc0ddf5.jpg', 1483093244),
(12, 'nunts_platserat_dolor', 'ru', 0, 'Нунц плацерат, долор', '<div>Нунц плацерат, долор qуис тинцидунт вененатис, сем лорем тинцидунт фелис, ат вестибулум фелис юсто qуис велит. Фусце сит амет нунц нец нибх интердум содалес. Сед магна сапиен, маxимус сед бибендум сит амет, порттитор еу диам. Суспендиссе тристиqуе елит анте, сит амет еффицитур нулла пеллентесqуе ат. Етиам пхаретра одио ет урна виверра, ет луцтус ерат еффицитур. Донец волутпат нулла ут лорем семпер алиqует егет qуис ипсум. Маурис еу долор метус. Вивамус вехицула метус долор, ац посуере лорем алиqуам а. Qуисqуе тинцидунт, ерат еу бибендум моллис, лигула долор интердум аугуе, ет еуисмод юсто аугуе ац лео.</div>\r\n<div>Цурабитур ет малесуада тортор, а волутпат юсто. Проин финибус лео ерос, ут цонсеqуат метус еуисмод рутрум. Нулла витае пурус рхонцус, порттитор велит сед, цонсеqуат маурис. Вивамус алиqует пеллентесqуе одио, сед сцелерисqуе арцу иацулис нец. Вестибулум турпис анте, плацерат ац ултрициес сит амет, пхаретра улламцорпер нибх. Вивамус ац ниси дуи. Фусце фелис нулла, фрингилла содалес велит ин, цонсецтетур финибус сапиен. Морби молестие иацулис ест, ат лациниа арцу тристиqуе витае. Ин вулпутате нулла егет иацулис пулвинар.</div>', 'af49139bf8468d73fdaf5d1353846cd9.jpg', 1483093396),
(12, 'mauris_non_letstus', 'ru', 0, 'Маурис нон лецтус', '<div>Маурис нон лецтус елит. Фусце цонваллис еу qуам ат иацулис. Донец еу фауцибус арцу. Нам нон пурус ат неqуе импердиет дигниссим. Нуллам фрингилла аугуе егет ниси фрингилла, еу цонсеqуат долор орнаре. Дуис вехицула темпус нунц витае бландит. Алиqуам ут алиqуам еx. Донец ет лобортис долор. Фусце гравида еу еx еу фрингилла. Аенеан нон qуам молестие, елементум ипсум егестас, цонгуе либеро. Суспендиссе вел нунц рисус. Аенеан егет моллис долор.</div>\r\n<div>Донец нец ипсум соллицитудин, фацилисис маурис а, волутпат qуам. Сед моллис маурис нулла, сит амет темпус ест лаореет луцтус. Суспендиссе витае дуи тортор. Цласс аптент тацити социосqу ад литора торqуент пер цонубиа ностра, пер инцептос хименаеос. Морби дигниссим маурис сем, ат дигниссим лео аццумсан ид. Ут егет сусципит лигула. Црас нибх долор, содалес нон дапибус ид, дицтум нец нулла. Црас витае одио импердиет, содалес велит нон, лациниа анте. Донец магна магна, импердиет сит амет магна ат, дицтум фауцибус рисус. Qуисqуе ат сусципит ест. Дуис нон лигула сед qуам тинцидунт ферментум ет ац пурус. Интегер молестие лецтус ид ниси ауцтор сусципит ин ид qуам.</div>', 'c19f3ddc36a060a42bb88ceb9498a86c.jpg', 1483093627);

INSERT INTO `nodes_shipping` (`id`, `user_id`, `fname`, `lname`, `country`, `state`, `city`, `zip`, `street1`, `street2`, `phone`) VALUES
(1, 1, '', '', 'United States', 'Columbia D.C', 'Washington', '20001', 'District of Columbia', '', '+1234567890');

INSERT INTO `nodes_product` (`id`, `user_id`, `title`, `text`, `img`, `shipping`, `price`, `date`, `status`, `views`, `rating`, `votes`) VALUES
(1, 1, 'Apple iPhone 6S Plus', 'Lorem ipsum dolor sit amet, consectetur adipiscing elit. Nullam pulvinar congue porta. Integer laoreet ante eu tellus pharetra viverra.', '24c06a8089c98ee219e73ef23fa11c11.jpg;54f3ccdc4a3367040cf55db8aafe7776.jpg;', 1, 1000, 1482929042, 1, 0, 0, 0),
(2, 1, 'Apple iPhone 7', 'In tempor turpis sit amet augue scelerisque, non porta enim vestibulum. Aliquam sed nibh fringilla lorem feugiat mollis ac nec nunc.', 'c84ed8754d4a11245a082c6e99d283a4.jpg;417a4d50fcc94a87e88a29eb9dfd3069.jpg;', 1, 1000, 1482992269, 1, 0, 0, 0),
(3, 1, 'Samsung Galaxy S7', 'Nunc aliquet tellus metus, eget vehicula turpis elementum eu. Sed eu venenatis orci. Fusce sit amet erat ut est convallis molestie.', '8a630515790453498d6987c5018b5add.jpg;', 1, 800, 1482993156, 1, 0, 0, 0),
(4, 1, 'Samsung Galaxy S7 Edge', 'Nullam a velit feugiat, eleifend est vel, egestas arcu. Nulla viverra bibendum eros, ac efficitur diam rutrum in.', '6bec9290a39a809bba33ba2e891129a6.jpg;', 1, 1000, 1482996486, 1, 0, 0, 0),
(5, 1, 'HTC One (M8)', 'Sed augue elit, interdum et risus eu, dapibus laoreet ligula. Sed interdum mi vitae tempor dignissim. Phasellus et accumsan elit, placerat finibus dolor.', '447a655794910fcbdbf48157a2638c3d.png;', 1, 400, 1482997293, 1, 0, 0, 0),
(6, 1, 'Nokia Lumia 1520', 'Maecenas laoreet ut purus nec dictum. Pellentesque condimentum nunc tellus, sed consectetur nulla efficitur nec. Nulla blandit a lorem at laoreet.', 'ab0a56a55b02391414084f8d77c85a0d.jpg;', 1, 400, 1482997684, 1, 0, 0, 0),
(7, 1, 'HTC 10', 'Nullam a velit feugiat, eleifend est vel, egestas arcu. Nulla viverra bibendum eros, ac efficitur diam rutrum in. Vestibulum vel magna vulputate, porta felis vitae, egestas purus.', '621c7486aef3fc9c3ebc3ef535c7f932.jpg;', 1, 700, 1484456413, 1, 0, 0, 0),
(8, 1, 'Google Pixel', 'Morbi id sapien sed diam laoreet porttitor porta quis velit. Cras tortor ipsum, auctor sit amet sem ac, viverra feugiat velit.', '3f61bef77de2589f1d8659b3ba559916.jpg;4c77d7f1a2d165b9b7c3046374ad58a0.jpg;', 1, 500, 1484457014, 1, 0, 0, 0),
(9, 1, 'Samsung Galaxy S6 Edge', 'Quisque lorem dolor, tempor non enim et, condimentum ultrices dolor. Sed interdum mi vitae tempor dignissim.', 'f138cb1d03ffa62618545ac621cc98be.jpg;', 1, 900, 1484457692, 1, 0, 0, 0);

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
            require_once("engine/core/manage_files.php");
            manage_files::copy("res/data", "img/data");
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
<script>
function new_update(){
    jQuery("#content").animate({opacity: 0}, 300);
    document.getElementById("post_form").submit();
}setTimeout(new_update, 5000);
</script>
<form method="POST" id="post_form" action="'.$_SERVER["DIR"].'/install.php">
    <input type="hidden" name="version" value="'.intval($new_version).'" />
    Updating engine to latest version after 5 seconds.<br/>
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
    if(is_dir($_SERVER["DOCUMENT_ROOT"].$_SERVER["DIR"]."/res/data")){
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
}else if(!file_exists("engine/nodes/config.php")||$_SESSION["user"]["id"]=="1"){
    $output = output();
}else die(engine::error(401));
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
    <script src="<?php echo $_SERVER["DIR"]; ?>/script/jquery-1.11.1.js" type="text/javascript"></script>
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
if(!window.jQuery) document.write(unescape('<script type="text/javascript" src="<?php echo $_SERVER["DIR"]; ?>/script/jquery-1.11.1.min.js">%3C/script%3E')); };</script>
</html>