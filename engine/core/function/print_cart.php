<?php
/**
* Prints cart block.
* @path /engine/core/function/print_cart.php
* 
* @name    Nodes Studio    @version 2.0.3
* @author  Alexandr Vorkunov  <developing@nodes-tech.ru>
* @license http://www.apache.org/licenses/LICENSE-2.0 GNU Public License
* 
* @param int $count Number of items in cart.
* @return string Returns content of block on success, or die with error.
* @usage <code> engine::print_cart(1); </code>
*/
require_once("engine/nodes/language.php");
function print_cart($count){
    $fout = '<div class="buy_cart">
        <div id="nodes_cart" class="'.($count>0?'':'hidden').'" onClick=\'show_order();\'>
        <div class="cart_labels">
            <div class="label_1"><a id="cart_link">'.lang("Your Shopping Cart").'</a></div> 
            <div class="label_2 cart_img">&nbsp;</div> 
            <div class="label_3"> <span class="purcases_count">'.$count.'</span> '.lang("item(s)").'</div>
        </div>
        </div>
        <div id="nodes_cart_wrapper" class="'.($count>0?'':'hidden').'"> </div>
    </div>';
    return $fout;
}