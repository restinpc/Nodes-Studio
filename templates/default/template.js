/* Nodes Studio source file. Do not edit directly */

function search(search){
    result = prompt(search, ""); 
    if(result){ 
        window.location=root_dir+"/search/"+encodeURIComponent(result);  
    }
}

function show_developer(){
    document.getElementById("caption").style.display = "none";
    document.getElementById("freelance").style.display = "block"; 
}

function hide_developer(){
    document.getElementById("freelance").style.display = "none";
    document.getElementById("caption").style.display = "block";
}

function show_more(sender){
    document.getElementById("submenu").style.height = "auto"; 
    sender.style.display="none";
    document.getElementById("hidden_submenu").style.display = "block";
}

if(window.jQuery){

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
    jQuery('body,html').scrollTop(0);
}

function search(text){
    alertify.prompt('<h3>'+text+'</h3><br/>', function (e, str) {if (e) {
        if(str){
            window.location=root_dir+"/search/"+encodeURIComponent(str);  
        }
    }}, ""); 
}

function show_developer(){
    jQuery('#freelance').show();
    jQuery('#caption').hide(300);
}

function hide_developer(){
    jQuery('#freelance').hide(300);
    jQuery('#caption').show(300);
}

jQuery(function() {
    navScroll();
    jQuery(window).scroll(navScroll).resize(navScroll);
    jQuery('#menuIcon').click(function() { showHideMenu(); });
    jQuery('#floater').click(function() { scrolltoTop(); });
});
}