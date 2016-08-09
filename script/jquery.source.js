/* Nodes Studio source file. Do not edit directly */

if(window.jQuery){
    
jQuery(function() {
    ajaxing();
});

var window_state = 0;
function searchText( string, needle ) {
   return !!(string.search( needle ) + 1);
}

window.stateChangeIsLocal = true;
History.enabled = true;          
window.onpopstate = function() {              
    goto(window.location.href); 
}

function ajaxing(){
window_state = 0;
jQuery('a').on('click', function(e) {
    if(jQuery(this).attr('href')){
        if(jQuery(this).attr('target') != "_blank" && jQuery(this).attr('target') != "_parent"){
            try{
                if(jQuery('.mdl-layout__drawer').attr('aria-hidden')=="false"){
                    jQuery('.mdl-layout__obfuscator').click();
                }jQuery('.android-content').scrollTop(0);
                jQuery('.android-header').removeClass("is-casting-shadow");
            }catch(err){};
            try{
                hideMenu();
                jQuery('body,html').scrollTop(0);
            }catch(err){};
            if(searchText(jQuery(this).attr('href'), location.hostname)){
                e.preventDefault();
                history.pushState('', '', jQuery(this).attr('href'));
                goto(jQuery(this).attr('href'));
            }else if(!searchText(jQuery(this).attr('href'), "http")){
                e.preventDefault();
                history.pushState('', '', jQuery(this).attr('href'));
                goto(jQuery(this).attr('href'));
                
            }
        }
    }
});
}

function refresh_page(){
    jQuery("#content").animate({opacity: 0}, 300);
    document.getElementById("query_form").submit();
}

function goto(href) {
    var hash = href.split("#");
    if(!window_state){
        if( href[0] != "#"){
            window_state = 1;
            jQuery("#content").animate({opacity: 0}, 300);
            console.log("Downloading "+href);
            var to = setTimeout(function(){ jQuery("#content").animate({opacity: 1}, 500); }, 10000);
            jQuery.ajax({
              url: href,
              async: true,
              type: "POST",
              data: {'jQuery': 'true'},
                success: function (data) {
                    console.log("Receiving "+href);
                    if(data[data.length-1]=="="){
                        data = base64_decode(data);
                    }setTimeout(ajaxing, 1);
                    setTimeout(checkAnshors, 1);
                    var title = jQuery(data).filter('title').text();
                    document.title = title;
                    jQuery('.site_title').text(title);
                    setTimeout(function(){ console.log("Showing "+href); jQuery("#content").html(data); jQuery("#content").animate({opacity: 1}, 500); clearTimeout(to); }, 300);
                }
            });
        }else{
            showAnchor(hash[1]);
        }
    }
} 

function showAnchor(anchor){
    var element = 'a[name="'+anchor+'"]';
    var pos = jQuery(element);
    jQuery('body,html').animate({scrollTop:(pos.offsetTop-80)}, 200,'swing');  
}

function checkAnshors(){
    var hash = window.location.href.split("#");
    if(hash[1]!=""){
        showAnchor(hash[1]);
    }
}

function goto_page(page){
    document.getElementById("page_field").value=page;
    refresh_page();
}

function submit_search_form(){
    document.getElementById("page_field").value=1;
    refresh_page();
}

function addSiteFade() {
    if (jQuery('#siteFade').length == 0) {
        return jQuery("<div id='siteFade'></div>").appendTo('body').fadeIn();
    }
}

function removeSiteFade() {
    jQuery('#siteFade').fadeOut(function() {
        jQuery(this).remove()
    });
}

}