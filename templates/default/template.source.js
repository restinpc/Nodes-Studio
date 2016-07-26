//  TODO - Your code here

if(window.jQuery){
    
jQuery(function() {
    navScroll();
    jQuery(window).scroll(navScroll).resize(navScroll);
    jQuery('#menuIcon').click(function() { showHideMenu(); });
    jQuery('#floater').click(function() { scrolltoTop(); });
});

function showHideMenu() {
    jQuery('#bigNav').fadeToggle(150);
    jQuery('#menuIcon').toggleClass('x');
    navScroll();
}

function hideMenu() {
    jQuery('#bigNav').fadeOut(150);
    jQuery('#menuIcon').removeClass('x');
    navScroll();
}

function navScroll() {
    if(jQuery(window).scrollTop() > 0){
        jQuery('#floater').show();
    }else{
        jQuery('#floater').hide();
    }
    if(jQuery(window).scrollTop() > 0 || jQuery("#menuIcon").hasClass("x")){
        jQuery('#mainHead').addClass('scrollDown');
    }else{
        jQuery('#mainHead').removeClass('scrollDown');
    }
}

function scrolltoTop(){
    var speed = 1000;
    jQuery('html, body').animate({scrollTop:0}, speed,'swing');
}
}


function show_more(sender){
    document.getElementById("submenu").style.height = "auto"; 
    sender.style.display="none";
    document.getElementById("hidden_submenu").style.display = "block";
}

function search(search){
    result = prompt(search, ""); 
    if(result){ 
        window.location=root_dir+"/search/"+encodeURIComponent(result);  
    }
}

function show_developer(){
    if(window.jQuery){
        jQuery('#freelance').show();
        jQuery('#caption').hide(300);
    }else{
        document.getElementById("caption").style.display = "none";
        document.getElementById("freelance").style.display="block"; 
    }
}

function hide_developer(){
    if(window.jQuery){
        jQuery('#freelance').hide(300);
        jQuery('#caption').show(300);
    }else{
        document.getElementById("freelance").style.display="none";
        document.getElementById("caption").style.display = "block";
    }
}
