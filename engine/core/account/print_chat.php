<?php
/**
* Print account chat page.
* @path /engine/core/account/print_chat.php
* 
* @name    Nodes Studio    @version 2.0.2
* @author  Alexandr Virtual    <developing@nodes-tech.ru>
* @license http://nodes-studio.com/license.txt GNU Public License
* 
* @param int $user_id @mysql[nodes_user]->id.
* @return string Returns content of page on success, or die with error.
* @usage <code> engine::print_chat(1); </code>
*/
function print_chat($user_id){
    $query = 'SELECT * FROM `nodes_inbox` WHERE (`from` = '.$_SESSION["user"]["id"].' AND `to` = '.$user_id.') OR '
        . '(`from` = '.$user_id.' AND `to` = '.$_SESSION["user"]["id"].') ORDER BY `date` ASC';
    $res = engine::mysql($query);
    $fout = '<table class="chat_table" border=0 >';
    while( $data = mysql_fetch_array($res)){
        if($data["from"] == $_SESSION["user"]["id"]){
            if($data["readed"]=="0"){
                $fout .= '<tr><td class="chat_unreaded">';
            }else{
                $fout .= '<tr><td>';
            }
            $fout .= '<div class="chat_left">'
                    . '<table class="list">'
                    . '<td align=left width=100% valign=top>';
            if(!$data["system"]){
                $fout .= '<span class="chat_left_text">'.lang("Sended").' '.date("d.m", $data["date"]).' '.lang("at").' '.date("H:i", $data["date"]).'</span><br/>'.$data["text"];
            }else{
                $fout .= '<span class="chat_left_text">'.lang("System message").' '.date("d.m", $data["date"]).' '.lang("at").' '.date("H:i", $data["date"]).'</span><br/>'.'<i>'.lang($data["text"]).'</i>';
            }     
            $fout .= '</td>'
                    . '</tr>'
                    . '</table>'
                    . '<div class="chat_left_bubble">&nbsp;</div>'
                    . '</div>';
        }else{
            $fout .= '<tr><td><div class="chat_right">'
                    . '<table cellpadding=0 cellspacing=0 height=100% class="list received" >'
                    . '<td align=left width=100% valign=top>';
            if(!$data["system"]){
                $fout .= '<div class="chat_right_text">'
                        . lang("Received").' '.date("d.m", $data["date"]).' '.lang("at").' '.date("H:i", $data["date"]).'</div>'
                    . '<div class="clear"></div>'.$data["text"];
            }else{
                $fout .= '<div class="chat_right_text">'
                        . lang("System message").' '.date("d.m", $data["date"]).' '.lang("at").' '.date("H:i", $data["date"]).'</div>'
                    . '<div class="clear"></div>'.'<i>'.lang($data["text"]).'</i>';
            }     
            $fout .= '</td>'
                    . '</tr>'
                    . '</table>'
                    . '<div class="chat_right_bubble">&nbsp;</div>'
                    . '</div>'
                    . '</td></tr>';
        }
    }$fout .= '<tr><td> </td></tr></table>';
    return $fout;
}