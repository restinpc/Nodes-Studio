<?php
/**
* Print admin attendance page.
* @path /engine/core/admin/print_admin_attendance.php
* 
* @name    Nodes Studio    @version 2.0.7
* @author  Aleksandr Vorkunov  <developing@nodes-tech.ru>
* @license http://www.apache.org/licenses/LICENSE-2.0 GNU Public License
*
* @var $cms->site - Site object.
* @var $cms->title - Page title.
* @var $cms->content - Page HTML data.
* @var $cms->menu - Page HTML navigaton menu.
* @var $cms->onload - Page executable JavaScript code.
* @var $cms->statistic - Array with statistics.
* 
* @param object $cms Admin class object.
* @return string Returns content of page on success, or die with error.
* @usage <code> engine::print_admin_attendance($cms); </code>
*/
function print_admin_attendance($cms){
    if($_GET["action"]=="stat" || empty($_GET["action"])){
        $stat = '<b>'.lang("Statistic").'</b>';
        $pages = '<a href="'.$_SERVER["DIR"].'/admin?mode=attendance&action=pages&interval='.$_GET["interval"].'&date='.$_GET["date"].'">'.lang("Pages").'</a>';
        $users = '<a href="'.$_SERVER["DIR"].'/admin?mode=attendance&action=users&interval='.$_GET["interval"].'&date='.$_GET["date"].'">'.lang("Users").'</a>';
    }else if($_GET["action"]=="pages"){
        $stat = '<a href="'.$_SERVER["DIR"].'/admin?mode=attendance&action=stat&interval='.$_GET["interval"].'&date='.$_GET["date"].'">'.lang("Statistic").'</a>';
        $pages = '<b>'.lang("Pages").'</b>';
        $users = '<a href="'.$_SERVER["DIR"].'/admin?mode=attendance&action=users&interval='.$_GET["interval"].'&date='.$_GET["date"].'">'.lang("Users").'</a>';
    }else if($_GET["action"]=="users"){
        $stat = '<a href="'.$_SERVER["DIR"].'/admin?mode=attendance&action=stat&interval='.$_GET["interval"].'&date='.$_GET["date"].'">'.lang("Statistic").'</a>';
        $pages = '<a href="'.$_SERVER["DIR"].'/admin?mode=attendance&action=pages&interval='.$_GET["interval"].'&date='.$_GET["date"].'">'.lang("Pages").'</a>';
        $referrers = '<a href="'.$_SERVER["DIR"].'/admin?mode=attendance&action=ref&interval='.$_GET["interval"].'&date='.$_GET["date"].'">'.lang("Referrers").'</a>';
        $users = '<b>'.lang("Users").'</b>';
    }
    $from = '';
    $to = '';
    if($_GET["interval"]=="day" || empty($_GET["interval"])){
        $by_day = '<b>'.lang("By days").'</b>';
        $by_week = '<a href="'.$_SERVER["DIR"].'/admin?mode=attendance&action='.$_GET["action"].'&interval=week&date='.$_GET["date"].'">'.lang("By weeks").'</a>';
        $by_month = '<a href="'.$_SERVER["DIR"].'/admin?mode=attendance&action='.$_GET["action"].'&interval=month&date='.$_GET["date"].'">'.lang("By months").'</a>';
        if(empty($_GET["date"])){
            $from = strtotime(date('Y-m-d')." 00:00:00");
            $to = date("U");
            $timeStamp = strtotime(date('Y-m-d')." 00:00:00 - 1 days");
            $date1 = date('d/m/Y', $timeStamp);
            $url_date1 = date("Y-m-d", $timeStamp);
            $prev = '<a href="'.$_SERVER["DIR"].'/admin?mode=attendance&action='.$_GET["action"].'&interval=day&date='.$url_date1.'">&laquo; '.$date1.'</a>';
            $now = '<b>'.date("d/m/Y").'</b>';
            $next = '&nbsp;';
        }else{
            $from = strtotime($_GET["date"]." 00:00:00");
            $to = strtotime($_GET["date"]." 23:59:59");
            $timeStamp = strtotime($_GET["date"]." 00:00:00 - 1 days");
            $date1 = date('d/m/Y', $timeStamp);
            $url_date1 = date("Y-m-d", $timeStamp);
            $timeStamp = strtotime($_GET["date"]." 00:00:00 + 1 days");
            $date2 = date('d/m/Y', $timeStamp);
            $url_date2 = date("Y-m-d", $timeStamp);
            $prev = '<a href="'.$_SERVER["DIR"].'/admin?mode=attendance&action='.$_GET["action"].'&interval=day&date='.$url_date1.'">&laquo; '.$date1.'</a>';
            $now = '<b>'.date("d/m/Y", strtotime($_GET["date"])).'</b>';
            if(strtotime($url_date2)<=strtotime(date("Y-m-d"))){
                $next = '<a href="'.$_SERVER["DIR"].'/admin?mode=attendance&action='.$_GET["action"].'&interval=day&date='.$url_date2.'">'.$date2.' &raquo;</a>'; 
            }else{
                $next = '&nbsp;'; 
            } 
        }
    }else if($_GET["interval"]=="week"){
        $by_day = '<a href="'.$_SERVER["DIR"].'/admin?mode=attendance&action='.$_GET["action"].'&interval=day&date='.$_GET["date"].'">'.lang("By days").'</a>';
        $by_week = '<b>'.lang("By weeks").'</b>';
        $by_month = '<a href="'.$_SERVER["DIR"].'/admin?mode=attendance&action='.$_GET["action"].'&interval=month&date='.$_GET["date"].'">'.lang("By months").'</a>';
        $prev = ' - 7 days';
        $prev2 = ' - 14 days';
        $next = ' + 0 days';
        $next2 = ' + 7 days';
        if(empty($_GET["date"])){
            $from = strtotime(date('Y-m-d')." 23:59:59  - 7 days");
            $to = date("U");
            $timeStamp = strtotime(date('Y-m-d')." 00:00:00".$prev);
            $date1 = date('d.m', $timeStamp);
            $link_date1 = date('Y-m-d', $timeStamp); 
            $timeStamp = strtotime(date('Y-m-d')." 00:00:00".$prev2);
            $date11 = date('d.m', $timeStamp);
            $prev = '<a href="'.$_SERVER["DIR"].'/admin?mode=attendance&action='.$_GET["action"].'&interval=week&date='.$link_date1.'">&laquo; '.$date11.' - '.$date1.'</a>';
            $now = '<b>'.$date1.' - '.date("d.m").'</b>';
            $next = '&nbsp;';
        }else{
            $from = strtotime($_GET["date"]." 23:59:59 - 7 days");
            $to = strtotime($_GET["date"]." 23:59:59");
            $date = date('d.m', strtotime($_GET["date"]));
            $timeStamp = strtotime($_GET["date"]."00:00:00".$prev);
            $date1 = date('d.m', $timeStamp);
            $link_date1 = date('Y-m-d', $timeStamp);
            $timeStamp = strtotime($_GET["date"]."00:00:00".$prev2);
            $date11 = date('d.m', $timeStamp);
            $timeStamp = strtotime($_GET["date"]."00:00:00".$next);
            $date2 = date('d.m', $timeStamp);
            $timeStamp = strtotime($_GET["date"]."00:00:00".$next2);
            $date22 = date('d.m', $timeStamp);
            $link_date2 = date('Y-m-d', $timeStamp);
            $prev = '<a href="'.$_SERVER["DIR"].'/admin?mode=attendance&action='.$_GET["action"].'&interval=week&date='.$link_date1.'">&laquo; '.$date11.' - '.$date1.'</a>';
            $now = '<b>'.$date1.' - '.$date.'</b>';
            if(strtotime($_GET["date"]."00:00:00".$next2)<=strtotime(date("Y-m-d"))){
                $next = '<a href="'.$_SERVER["DIR"].'/admin?mode=attendance&action='.$_GET["action"].'&interval=week&date='.$link_date2.'">'.$date2.' - '.$date22.' &raquo;</a>'; 
            }else{
                $next = '&nbsp;'; 
            }
        }   
    }else if($_GET["interval"]=="month"){
        $by_day = '<a href="'.$_SERVER["DIR"].'/admin?mode=attendance&action='.$_GET["action"].'&interval=day&date='.$_GET["date"].'">'.lang("By days").'</a>';
        $by_week = '<a href="'.$_SERVER["DIR"].'/admin?mode=attendance&action='.$_GET["action"].'&interval=week&date='.$_GET["date"].'">'.lang("By weeks").'</a>';
        $by_month = '<b>'.lang("By months").'</b>';
        $prev = ' - 1 month';
        $prev2 = ' - 2 month';
        $next = ' + 0 month';
        $next2 = ' + 1 month';
        if(empty($_GET["date"])){
            $from = strtotime(date('Y-m-d')." 23:59:59  - 1 month");
            $to = date("U");
            $timeStamp = strtotime(date('Y-m-d')."00:00:00".$prev);
            $date1 = date('m.Y', $timeStamp);
            $link_date1 = date('Y-m-d', $timeStamp); 
            $timeStamp = strtotime(date('Y-m-d')."00:00:00".$prev2);
            $date11 = date('m.Y', $timeStamp);
            $prev = '<a href="'.$_SERVER["DIR"].'/admin?mode=attendance&action='.$_GET["action"].'&interval=month&date='.$link_date1.'">&laquo; '.$date11.' - '.$date1.'</a>';
            $now = '<b>'.$date1.' - '.date("m.Y").'</b>';
            $next = '&nbsp;';
        }else{
            $from = strtotime($_GET["date"]." 23:59:59 - 1 month");
            $to = strtotime($_GET["date"]." 23:59:59");
            $date = date('m.Y', strtotime($_GET["date"]));
            $timeStamp = strtotime($_GET["date"]."00:00:00".$prev);
            $date1 = date('m.Y', $timeStamp);
            $link_date1 = date('Y-m-d', $timeStamp);
            $timeStamp = strtotime($_GET["date"]."00:00:00".$prev2);
            $date11 = date('m.Y', $timeStamp);
            $timeStamp = strtotime($_GET["date"]."00:00:00".$next);
            $date2 = date('m.Y', $timeStamp);
            $timeStamp = strtotime($_GET["date"]."00:00:00".$next2);
            $date22 = date('m.Y', $timeStamp);
            $link_date2 = date('Y-m-d', $timeStamp);
            $prev = '<a href="'.$_SERVER["DIR"].'/admin?mode=attendance&action='.$_GET["action"].'&interval=month&date='.$link_date1.'">&laquo; '.$date11.' - '.$date1.'</a>';
            $now = '<b>'.$date1.' - '.$date.'</b>';
            if(strtotime($_GET["date"]."00:00:00".$next2)<=strtotime(date("Y-m-d"))){
                $next = '<a href="'.$_SERVER["DIR"].'/admin?mode=attendance&action='.$_GET["action"].'&interval=month&date='.$link_date2.'">'.$date2.' - '.$date22.' &raquo;</a>'; 
            }else{
                $next = '&nbsp;'; 
            }
        }
    }
    $fout = '<div class="statistic">
        <div class="statistic_head">
            <table>
            <tr>
                <td align=center>'.$stat.'</td> 
                <td align=center>'.$pages.'</td>
                <td align=center>'.$users.'</td>  
            </tr>
            </table>
        </div>
        <div class="statistic_date">
            <table>
            <tr>
                <td align=center>'.$by_day.'</td>
                <td align=center>'.$by_week.'</td>
                <td align=center>'.$by_month.'</td>
            </tr>
            </table>
        </div>
        <div class="clear"></div>
        <table class="statistic_nav">
        <tr>
            <td align=center>'.$prev.'</td>
            <td align=center>'.$now.'</td>
            <td align=center>'.$next.'</td>
        </tr>
        </table><br/>';
    if($_GET["action"]=="stat" || empty($_GET["action"])){
        $query = 'SELECT COUNT(DISTINCT `token`, `ip`) as `a`, COUNT(`id`) as `b` FROM `nodes_attendance` WHERE `date` >= "'.$from.'" AND `date` <= "'.$to.'" AND `display` = "1"';
        $res = engine::mysql($query);
        $data = mysql_fetch_array($res);
        $views = $data['b'];
        $visit = $data['a'];
        $query = 'SELECT COUNT(DISTINCT `att`.`token`, `att`.`ip`) as `a` FROM `nodes_attendance` AS `att` '
                . 'LEFT JOIN `nodes_agent` AS `agent` ON `agent`.`id` = `att`.`agent_id` '
                . 'WHERE `date` >= "'.$from.'" AND `date` <= "'.$to.'" AND `agent`.`bot` = 1';
        $res = engine::mysql($query);
        $data = mysql_fetch_array($res);
        $bots_visit = $data['a'];
        $fout .= '<center class="lh2"><span class="statistic_span">'.lang("Visitors").": ".$visit.'</span> ';
        $fout .= '<span class="statistic_span"  style="color: rgb(20,180,180);">'.lang("Views").": ".$views.'</span> ';
        $fout .= '<span class="statistic_span blue">'.lang("Bots").": ".$bots_visit.'</span> ';
        $fout .= '<img width=100% class="w600" src="'.$_SERVER["DIR"].'/attandance.php?interval='.((!empty($_GET["interval"]))?$_GET["interval"]:"day").'&date='.$_GET["date"].'&rand='.rand(0,100).'" /></center>';
    }else if($_GET["action"]=="pages"){
        $query = 'SELECT `cache`.`url`, `att`.`id` AS `act_id` FROM `nodes_attendance` AS `att` '
                . 'LEFT JOIN `nodes_cache` AS `cache` ON `cache`.`id` = `att`.`cache_id` '
                . 'WHERE `att`.`date` >= "'.$from.'" AND `att`.`date` <= "'.$to.'" AND `att`.`display` = "1"';
        $res = engine::mysql($query);
        $pages = array();
        $visitors = array();
        $actions = array();
        $sessions = array();
        while($data = mysql_fetch_array($res)){
            $pages[$data["url"]]++;
            $query = 'SELECT COUNT(*) FROM `nodes_pattern` WHERE `attendance_id` = "'.$data["act_id"].'"';
            $r = engine::mysql($query);
            $d = mysql_fetch_array($r);
            $actions[$data["url"]]+=$d[0];
        }
        array_multisort($pages);
        $fout .= '<div class="table">
        <table width=100% id="table">
        <thead>
        <tr>
            <th>URL</th>
            <th>'.lang("Views").'</th>
            <th>'.lang("Actions").'</th>
        </tr>';
        $max_val = 0;
        $table = '';
        foreach($pages as $page=>$count){
            if($count > $max_val){
                $table = '<tr><td align=left><a href="'.$page.'" target="_blank">'.$page.'</a></td>'
                        . '<td>'.$count.'</td>
                            <td>'.$actions[$page].'</td>
                            </tr>'.$table;
                $max_val=$count;
            }else if($count < $min_val){
                $table .= '<tr><td align=left><a href="'.$page.'" target="_blank">'.$page.'</a></td>'
                        . '<td>'.$count.'</td>
                           <td>'.$actions[$page].'</td></tr>';  
                $min_val = $count;
            }
        }$fout .= $table.'</table></div>';
    }else if($_GET["action"]=="users"){
        $tokens = array();
        $query = 'SELECT * FROM `nodes_attendance` WHERE `date` >= "'.$from.'" AND `date` <= "'.$to.'" AND `display` = "1" ORDER BY `date` ASC';
        $res = engine::mysql($query);
        $fout .= '<div class="table">
        <table width=100% id="table" style="max-width: 640px;">
        <thead>
        <tr>
            <th>'.lang("Date").'</th>
            <th>'.lang("Referrer").'</th>
            <th>'.lang("User").'</th>
            <th>'.lang("Pages").'</th>
            <th>'.lang("Actions").'</th>
            <th width=80>&nbsp;</th>
        </tr>';
        while($data = mysql_fetch_array($res)){
            if(!in_array($data["token"], $tokens)){
                $query = 'SELECT COUNT(*) FROM `nodes_pattern` WHERE `attendance_id` = "'.$data["id"].'" ORDER BY `id` ASC';
                $r = engine::mysql($query);
                $d = mysql_fetch_array($r);
                $query = 'SELECT COUNT(*) FROM `nodes_attendance` WHERE `token` = "'.$data["token"].'"';
                $rr = engine::mysql($query);
                $dd = mysql_fetch_array($rr);
                array_push($tokens, $data["token"]);
                $query = 'SELECT * FROM `nodes_attendance` WHERE `token` = "'.$data["token"].'" AND `user_id` <> 0';
                $kr = engine::mysql($query);
                $kd = mysql_fetch_array($kr);
                if(empty($kd)){ 
                    $user_name = "Anonim";
                }else{
                    $query = 'SELECT * FROM `nodes_user` WHERE `id` = "'.$kd["user_id"].'"';
                    $dr = engine::mysql($query);
                    $user = mysql_fetch_array($dr);
                    $user_name = $user["name"];
                }
                $query = 'SELECT `width`, `height` FROM `nodes_pattern` WHERE `attendance_id` = "'.$data["id"].'" ORDER BY `id` ASC';
                $wr = engine::mysql($query);
                $window = mysql_fetch_array($wr);
                $query = 'SELECT `ref_id` FROM `nodes_attendance` WHERE `token` = "'.$data["token"].'" AND `ref_id` != 0';
                $ref = engine::mysql($query);
                $dref = mysql_fetch_array($ref);
                if($dref){
                    $query = 'SELECT * FROM `nodes_referrer` WHERE `id` = "'.$dref["ref_id"].'"';
                    $ddref = engine::mysql($query);
                    $ref_data = mysql_fetch_array($ddref);
                    $url = parse_url($ref_data["name"]);
                    $link = '<a href="'.$ref_data["name"].'" target="_blank">'.$url["host"].'</a>';
                }else{
                    $link = "Blank";
                }
                if($d[0]>0){
                    $button = '<a onClick=\'window.open("/pattern.php?token='.$data["token"].'", "'.lang("View session").'", "width='.($window["width"]).',height='.($window["height"]+25).'");\' class="btn small">'.lang("View session").'</a>';
                }else{
                    $button = '&nbsp;';
                }
                $fout .= '<tr style="text-align:left;">
                    <td>'.date("Y-m-d H:i:s", $data["date"]).'</td>
                    <td>'.$link.'</td>
                    <td>'.$user_name.'</td>
                    <td>'.$dd[0].'</td>
                    <td>'.$d[0].'</td>
                    <td>'.$button.'</td>
                </tr>';
            }
        }
        $fout .= '</table></div>';
    }
    $fout .= '</div><br/>';
    return $fout;
}