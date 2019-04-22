<?php
/**
* Print admin perfomance page.
* @path /engine/core/admin/print_admin_perfomance.php
* 
* @name    Nodes Studio    @version 2.0.1.9
* @author  Aleksandr Vorkunov  <developing@nodes-tech.ru>
* @license http://www.apache.org/licenses/LICENSE-2.0
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
* @usage <code> engine::print_admin_perfomance($cms); </code>
*/
function print_admin_perfomance($cms){
    $query = 'SELECT `access`.`access` FROM `nodes_access` AS `access` '
            . 'LEFT JOIN `nodes_admin` AS `admin` ON `admin`.`url` = "perfomance" '
            . 'WHERE `access`.`user_id` = "'.$_SESSION["user"]["id"].'" '
            . 'AND `access`.`admin_id` = `admin`.`id`';
    $admin_res = engine::mysql($query);
    $admin_data = mysqli_fetch_array($admin_res);
    $admin_access = intval($admin_data["access"]);
    if(!$admin_access){
        engine::error(401);
        return;
    }
    if($_GET["action"]=="stat" || empty($_GET["action"])){
        $stat = '<b>'.lang("Statistic").'</b>';
        $pages = '<a vr-control id="perfomance-pages" href="'.$_SERVER["DIR"].'/admin?mode=perfomance&action=pages&interval='.$_GET["interval"].'&date='.$_GET["date"].'">'.lang("Pages").'</a>';
    }else if($_GET["action"]=="pages"){
        $stat = '<a vr-control id="perfomance-stat" href="'.$_SERVER["DIR"].'/admin?mode=perfomance&action=stat&interval='.$_GET["interval"].'&date='.$_GET["date"].'">'.lang("Statistic").'</a>';
        $pages = '<b>'.lang("Pages").'</b>';
    }
    $from = '';
    $to = '';
    if($_GET["interval"]=="hour" || empty($_GET["interval"])){
        $by_hour = '<b>'.lang("By hours").'</b>';
        $by_day = '<a vr-control id="by-days" href="'.$_SERVER["DIR"].'/admin?mode=perfomance&action='.$_GET["action"].'&interval=day&date='.$_GET["date"].'">'.lang("By days").'</a>';
        $by_week = '<a vr-control id="by-weeks" href="'.$_SERVER["DIR"].'/admin?mode=perfomance&action='.$_GET["action"].'&interval=week&date='.$_GET["date"].'">'.lang("By weeks").'</a>';
        $by_month = '<a vr-control id="by-months" href="'.$_SERVER["DIR"].'/admin?mode=perfomance&action='.$_GET["action"].'&interval=month&date='.$_GET["date"].'">'.lang("By months").'</a>';
        if(empty($_GET["date"])){
            $from = strtotime(date('Y-m-d')." 00:00:00");
            $to = date("U");
            $timeStamp = strtotime(date('Y-m-d')." 00:00:00 - 1 days");
            $date1 = date('d/m/Y', $timeStamp);
            $url_date1 = date("Y-m-d", $timeStamp);
            $prev = '<a vr-control id="date-'.$url_date1.'" href="'.$_SERVER["DIR"].'/admin?mode=perfomance&action='.$_GET["action"].'&interval=hour&date='.$url_date1.'">&laquo; '.$date1.'</a>';
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
            $prev = '<a vr-control id="date-'.$url_date1.'" href="'.$_SERVER["DIR"].'/admin?mode=perfomance&action='.$_GET["action"].'&interval=hour&date='.$url_date1.'">&laquo; '.$date1.'</a>';
            $now = '<b>'.date("d/m/Y", strtotime($_GET["date"])).'</b>';
            if(strtotime($url_date2)<=strtotime(date("Y-m-d"))){
                $next = '<a vr-control id="date-'.$url_date2.'" href="'.$_SERVER["DIR"].'/admin?mode=perfomance&action='.$_GET["action"].'&interval=hour&date='.$url_date2.'">'.$date2.' &raquo;</a>'; 
            }else{
                $next = '&nbsp;'; 
            } 
        }
    }
    if($_GET["interval"]=="day"){
        $by_hour = '<a vr-control id="by-hours" href="'.$_SERVER["DIR"].'/admin?mode=perfomance&action='.$_GET["action"].'&interval=hour&date='.$_GET["date"].'">'.lang("By hours").'</a>';
        $by_day = '<b>'.lang("By days").'</b>';
        $by_week = '<a vr-control id="by-weeks" href="'.$_SERVER["DIR"].'/admin?mode=perfomance&action='.$_GET["action"].'&interval=week&date='.$_GET["date"].'">'.lang("By weeks").'</a>';
        $by_month = '<a vr-control id="by-months" href="'.$_SERVER["DIR"].'/admin?mode=perfomance&action='.$_GET["action"].'&interval=month&date='.$_GET["date"].'">'.lang("By months").'</a>';
        if(empty($_GET["date"])){
            $from = strtotime(date('Y-m-d')." 00:00:00");
            $to = date("U");
            $timeStamp = strtotime(date('Y-m-d')." 00:00:00 - 1 days");
            $date1 = date('d/m/Y', $timeStamp);
            $url_date1 = date("Y-m-d", $timeStamp);
            $prev = '<a vr-control id="date-'.$url_date1.'" href="'.$_SERVER["DIR"].'/admin?mode=perfomance&action='.$_GET["action"].'&interval=day&date='.$url_date1.'">&laquo; '.$date1.'</a>';
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
            $prev = '<a vr-control id="date-'.$url_date1.'" href="'.$_SERVER["DIR"].'/admin?mode=perfomance&action='.$_GET["action"].'&interval=day&date='.$url_date1.'">&laquo; '.$date1.'</a>';
            $now = '<b>'.date("d/m/Y", strtotime($_GET["date"])).'</b>';
            if(strtotime($url_date2)<=strtotime(date("Y-m-d"))){
                $next = '<a vr-control id="date-'.$url_date2.'" href="'.$_SERVER["DIR"].'/admin?mode=perfomance&action='.$_GET["action"].'&interval=day&date='.$url_date2.'">'.$date2.' &raquo;</a>'; 
            }else{
                $next = '&nbsp;'; 
            } 
        }
    }else if($_GET["interval"]=="week"){
        $by_hour = '<a vr-control id="by-hours" href="'.$_SERVER["DIR"].'/admin?mode=perfomance&action='.$_GET["action"].'&interval=hour&date='.$_GET["date"].'">'.lang("By hours").'</a>';
        $by_day = '<a vr-control id="by-days" href="'.$_SERVER["DIR"].'/admin?mode=perfomance&action='.$_GET["action"].'&interval=day&date='.$_GET["date"].'">'.lang("By days").'</a>';
        $by_week = '<b>'.lang("By weeks").'</b>';
        $by_month = '<a vr-control id="by-months" href="'.$_SERVER["DIR"].'/admin?mode=perfomance&action='.$_GET["action"].'&interval=month&date='.$_GET["date"].'">'.lang("By months").'</a>';
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
            $prev = '<a vr-control id="date-'.$link_date1.'" href="'.$_SERVER["DIR"].'/admin?mode=perfomance&action='.$_GET["action"].'&interval=week&date='.$link_date1.'">&laquo; '.$date11.' - '.$date1.'</a>';
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
            $prev = '<a vr-control id="date-'.$link_date1.'" href="'.$_SERVER["DIR"].'/admin?mode=perfomance&action='.$_GET["action"].'&interval=week&date='.$link_date1.'">&laquo; '.$date11.' - '.$date1.'</a>';
            $now = '<b>'.$date1.' - '.$date.'</b>';
            if(strtotime($_GET["date"]."00:00:00".$next2)<=strtotime(date("Y-m-d"))){
                $next = '<a vr-control id="date-'.$link_date2.'" href="'.$_SERVER["DIR"].'/admin?mode=perfomance&action='.$_GET["action"].'&interval=week&date='.$link_date2.'">'.$date2.' - '.$date22.' &raquo;</a>'; 
            }else{
                $next = '&nbsp;'; 
            }
        }  
    }else if($_GET["interval"]=="month"){
        $by_hour = '<a vr-control id="by-hours" href="'.$_SERVER["DIR"].'/admin?mode=perfomance&action='.$_GET["action"].'&interval=hour&date='.$_GET["date"].'">'.lang("By hours").'</a>';
        $by_day = '<a vr-control id="by-days" href="'.$_SERVER["DIR"].'/admin?mode=perfomance&action='.$_GET["action"].'&interval=day&date='.$_GET["date"].'">'.lang("By days").'</a>';
        $by_week = '<a vr-control id="by-weeks" href="'.$_SERVER["DIR"].'/admin?mode=perfomance&action='.$_GET["action"].'&interval=week&date='.$_GET["date"].'">'.lang("By weeks").'</a>';
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
            $prev = '<a vr-control id="date-'.$link_date1.'" href="'.$_SERVER["DIR"].'/admin?mode=perfomance&action='.$_GET["action"].'&interval=month&date='.$link_date1.'">&laquo; '.$date11.' - '.$date1.'</a>';
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
            $prev = '<a vr-control id="date-'.$link_date1.'" href="'.$_SERVER["DIR"].'/admin?mode=perfomance&action='.$_GET["action"].'&interval=month&date='.$link_date1.'">&laquo; '.$date11.' - '.$date1.'</a>';
            $now = '<b>'.$date1.' - '.$date.'</b>';
            if(strtotime($_GET["date"]."00:00:00".$next2)<=strtotime(date("Y-m-d"))){
                $next = '<a id="date-'.$link_date2.'" href="'.$_SERVER["DIR"].'/admin?mode=perfomance&action='.$_GET["action"].'&interval=month&date='.$link_date2.'">'.$date2.' - '.$date22.' &raquo;</a>'; 
            }else{
                $next = '&nbsp;'; 
            }
        }
    }
    $query = 'SELECT AVG(`script_time`) FROM `nodes_perfomance` WHERE `script_time` > 0';
    $res = engine::mysql($query);
    $mid_script = mysqli_fetch_array($res);
    $query = 'SELECT AVG(`server_time`) FROM `nodes_perfomance` WHERE `server_time` > 0';
    $res = engine::mysql($query);
    $mid_server = mysqli_fetch_array($res);
    $fout = '<div class="statistic">
        <div class="statistic_head w200">
            <table>
            <tr>
                <td align=center>'.$stat.'</td> 
                <td align=center>'.$pages.'</td>
            </tr>
            </table>
        </div>
        <div class="statistic_date w400">
            <table>
            <tr>
                <td align=center>'.$by_hour.'</td>
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
        </table>';
    if($_GET["action"]=="stat" || empty($_GET["action"])){
        $fout .= '<br/><center class="lh2"><span class="statistic_span" style="color: rgb(68,115,186);">'.lang("Average Server Response").': '.round($mid_server[0],2).'</span> ';
        $fout .= '<span class="statistic_span" style="color: rgb(20,180,180);">'.lang("Average Site Response").': '.round($mid_script[0],2).'</span><br/><br/>';
        $fout .= '<img width=100% title="'.lang("Page generation time").'" class="w600" src="'.$_SERVER["DIR"].'/perfomance.php?interval='.((!empty($_GET["interval"]))?$_GET["interval"]:"hour").'&date='.$_GET["date"].'&rand='.rand(0,100).'" /></center><br/>';
    }else if($_GET["action"]=="pages"){
        $query = 'SELECT * FROM `nodes_perfomance` WHERE `date` >= "'.$from.'" AND `date` <= "'.$to.'"';
        $res = engine::mysql($query);
        $pages = array();
        $perfomance = array();
        while($data = mysqli_fetch_array($res)){
            $pages[$data["cache_id"]]++;
            $perfomance[$data["cache_id"]]+=$data["script_time"];
        }
        for($i = 0; $i < count($pages); $i++){
            $pages[$i] = round($perfomance[$i]/$pages[$i],2);
        }
        unset($perfomance);
        array_multisort($pages);
        $top = '<div class="table">
        <table width=100% id="table">
        <thead>
        <tr>
            <th>URL</th>
            <th>'.lang("Time").'</th>
        </tr>';
        $max_val = 0;
        foreach($pages as $page=>$count){
            if(!$count) continue;
            $query = 'SELECT `url` FROM `nodes_cache` WHERE `id` = "'.$page.'"';
            $res = engine::mysql($query);
            $data = mysqli_fetch_array($res);
            if($count>$mid_script[0]) $color = 'red';
            else $color = 'blue';
            if($count > $max_val){
                $table = '<tr><td align=left><a vr-control id="link-'.$page.'" href="'.$data["url"].'" class="'.$color.'" target="_blank">'.$data["url"].'</a></td>'
                        . '<td>'.$count.' '.lang("seconds").'</td></tr>'.$table;
                $max_val=$count;
            }else{
                $table .= '<tr><td align=left><a vr-control id="link-'.$page.'" href="'.$data["url"].'" class="'.$color.'" target="_blank">'.$data["url"].'</a></td>'
                        . '<td>'.$count.' '.lang("seconds").'</td></tr>';  
                $min_val = $count;
            }
        }$table = $top.$table.'</table></div>';
        if($max_val>0){
            $fout .= $table;
        }else{
            $fout .= '<div class="clear_block">'.lang("Data not found").'</div>';
        }
    }
    $fout .= '</div>';    
    return $fout;
}
