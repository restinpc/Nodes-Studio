/* TODO - Your code here */ if(window.jQuery){var scrolltoTop=function(){jQuery("html, body").animate({scrollTop:0},200,"swing")},navScroll=function(){0<jQuery(window).scrollTop()?jQuery("#floater").show():jQuery("#floater").hide();0<jQuery(window).scrollTop()||jQuery("#menuIcon").hasClass("x")?jQuery("#mainHead").addClass("scrollDown"):jQuery("#mainHead").removeClass("scrollDown")},hideMenu=function(){jQuery("#bigNav").fadeOut(150);jQuery("#menuIcon").removeClass("x");navScroll()},showHideMenu=function(){jQuery("#bigNav").fadeToggle(150);
jQuery("#menuIcon").toggleClass("x");navScroll()};jQuery(function(){navScroll();jQuery(window).scroll(navScroll).resize(navScroll);jQuery("#menuIcon").click(function(){showHideMenu()});jQuery("#floater").click(function(){scrolltoTop()})})}function show_more(a){document.getElementById("submenu").style.height="auto";a.style.display="none";document.getElementById("hidden_submenu").style.display="block"}
function search(a){if(result=prompt(a,""))window.location=root_dir+"/search/"+encodeURIComponent(result)}function show_developer(){window.jQuery?(jQuery("#freelance").show(),jQuery("#caption").hide(300)):(document.getElementById("caption").style.display="none",document.getElementById("freelance").style.display="block")}
function hide_developer(){window.jQuery?(jQuery("#freelance").hide(300),jQuery("#caption").show(300)):(document.getElementById("freelance").style.display="none",document.getElementById("caption").style.display="block")};