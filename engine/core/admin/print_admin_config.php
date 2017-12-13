<?php
/**
* Print admin config page.
* @path /engine/core/admin/print_admin_config.php
* 
* @name    Nodes Studio    @version 2.0.3
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
* @usage <code> engine::print_admin_config($cms); </code>
*/
function print_admin_config($cms){
    if(!empty($_POST)){
        foreach($_POST as $arr=>$value){
            $query = 'UPDATE `nodes_config` SET `value` = "'.mysql_real_escape_string($value).'" WHERE `name` = "'.$arr.'"';
            engine::mysql($query);
        }
    }
    $query = 'SELECT * FROM `nodes_config` WHERE `text` <> "System" ORDER BY `id`';
    $res = engine::mysql($query);
    $fout = '<div class="document640">
            <form method="POST"><div class="table">
        <table width=100% id="table">';
    while($data = mysql_fetch_array($res)){
        if($data["type"]=="bool"){
            $fout .= '
                <tr>
                    <td width=100 align=left class="p5">'.$data["text"].'</td>
                    <td class="p5" align=left >
                    <select class="input w100p" name="'.$data["name"].'">';
            if($data["value"]){
                $fout .= '<option value="0">'.lang("No").'</option>'
                        . '<option value="1" selected>'.lang("Yes").'</option>';
            }else{
                $fout .= '<option value="0" selected>'.lang("No").'</option>'
                        . '<option value="1">'.lang("Yes").'</option>';
            }
            $fout .= '        
                    </select>
                    </td>
                </tr>';
        }else{
            $fout .= '
                <tr>
                    <td width=100 align=left class="p5">'.$data["text"].'</td>
                    <td class="p5" align=left >
                    <input class="input w100p" type="text" name="'.$data["name"].'" value="'.$data["value"].'" />
                    </td>
                </tr>';
        }
    }
    $fout .= '</table>'
            . '</div><br/>'
            . '<input type="submit" class="btn w280" value="'.lang("Save settings").'" />'
            . '</form>'
            . '</div>';
    return $fout;
}

