/* Nodes Studio 2.0.4 default template script. 04/08/2017 */
function search(e){result=prompt(e,""),result&&(window.location=root_dir+"/search/"+encodeURIComponent(result))}function show_bin(){jQuery.ajax({type:"POST",data:{show_bin:"1"},url:root_dir+"/bin.php",success:function(e){jQuery(".buy_cart").length?jQuery(".buy_cart").replaceWith(e):jQuery("body").append(e)}})}function showHideMenu(){jQuery("#bigNav").fadeToggle(150),jQuery("#menuIcon").toggleClass("x"),jQuery("#mainHead").toggleClass("popupMenu"),jQuery("#menuIcon .nav_button").toggleClass("hidden")}function hideMenu(){jQuery("#bigNav").fadeOut(150),jQuery("#menuIcon").removeClass("x"),jQuery("#mainHead").removeClass("popupMenu"),jQuery("#menuIcon .nav_button").removeClass("hidden")}function navScroll(){if(jQuery(window).scrollTop()>0?jQuery("#floater").show():jQuery("#floater").hide(),jQuery(window).scrollTop()>0||jQuery("#menuIcon").hasClass("x"))if(lastPos>jQuery(window).scrollTop())for(jQuery("#mainHead").addClass("scrollDown"),$i=0;$i<=100;)setTimeout(function(){jQuery("#mainHead").css("opacity",$i/100)},$i),$i++;else{for($i=100;$i>=0;)setTimeout(function(){jQuery("#mainHead").css("opacity",$i/100)},$i),$i--;jQuery("#mainHead").removeClass("scrollDown"),hideMenu()}else jQuery("#mainHead").removeClass("scrollDown"),jQuery("#mainHead").css("opacity","1");lastPos=jQuery(window).scrollTop()}function search(e){alertify.prompt("<h3>"+e+"</h3><br/>",function(e,o){e&&o&&(window.location=root_dir+"/search/"+encodeURIComponent(o))},"")}if(window.jQuery){var lastPos=0;jQuery(function(){navScroll(),load_events&&(jQuery(window).scroll(navScroll).resize(navScroll),jQuery("#menuIcon").click(function(){showHideMenu()}),jQuery("#floater").click(function(){scrolltoTop()}))})}