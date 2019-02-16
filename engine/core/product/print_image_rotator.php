<?php
/**
* Prints an image rotator block.
* @path /engine/core/product/print_image_rotator.php
* 
* @name    Nodes Studio    @version 3.0.0.1
* @author  Aleksandr Vorkunov  <developing@nodes-tech.ru>
* @license http://www.apache.org/licenses/LICENSE-2.0
*
* @var $site->title - Page title.
* @var $site->content - Page HTML data.
* @var $site->keywords - Array meta keywords.
* @var $site->description - Page meta description.
* @var $site->img - Page meta image.
* @var $site->onload - Page executable JavaScript code.
* @var $site->configs - Array MySQL configs.
* 
* @param object $site Site class object.
* @param array $images Array with images for rotation.
* @return string Returns content of block on success, or die with error.
* @usage <code> 
*   $images = array("/img/1.jpg", "/img/2.jpg"); 
*   $caption = "...";
*   engine::print_image_rotator($site, $caption, $images); 
* </code>
*/
function print_image_rotator($site, $caption, $images){
    $images = array_filter($images, function($element) { return !empty($element); });
    $fout = '
    <div id="jssor_1" style="position: relative; margin: 0 auto; left: 0px; width: 600px; height: 500px; overflow: hidden; visibility: hidden;">
        <div data-u="slides" id="slider_block" style="cursor: default; position: relative; width: 600px; top: 0px; left: 0px; height: 500px; overflow: hidden;">
            <div><a vr-control id="link-g0" onClick=\'onpop_state = 1; document.getElementById("g0").click();\'><img data-u="image" src="'.$_SERVER["DIR"].'/img/data/big/'.$images[0].'"  /></a> </div>';
    $size = getimagesize($_SERVER["DIR"].'/img/data/big/'.$images[0]);
    if(!$size[0]){
        $size = getimagesize($images[0]);
        if(!$size[0]){
            $size = getimagesize($_SERVER["PUBLIC_URL"].'/img/data/big/'.$images[0]);
        }
    }
    if(count($images)==1){
        $class = 'class="hidden"';
    }
    $galery = '
    <figure itemprop="associatedMedia" itemscope itemtype="http://schema.org/ImageObject" '.$class.'>
        <a vr-control id="link-g00" target="_blank" href="'.$_SERVER["DIR"].'/img/data/big/'.$images[0].'" itemprop="contentUrl" data-size="'.$size[0].'x'.$size[1].'">
            <img id="g0" src="'.$_SERVER["DIR"].'/img/data/big/'.$images[0].'" itemprop="thumbnail" alt="'.$caption.' 1" />
        </a>
        <figcaption itemprop="caption description">'.$caption.'</figcaption>                                 
    </figure>';
    
    for($i = 1; $i<count($images); $i++){
        if(!empty($images[$i])){
            if($i==count($images)-1){
                $size = getimagesize($_SERVER["DIR"].'/img/data/big/'.$images[$i]);
                if(!$size[0]){
                    $size = getimagesize($images[$i]);
                    if(!$size[0]){
                        $size = getimagesize($_SERVER["PUBLIC_URL"].'/img/data/big/'.$images[$i]);
                    }
                }     
                $fout .= '<div style="display:none;"> '
                    . '<a vr-control id="link-'.$i.'" onClick=\'onpop_state = 1; document.getElementById("g'.$i.'").click();\'>'
                    . '<img data-u="image" src="'.$_SERVER["DIR"].'/img/data/big/'.$images[$i].'"  />'
                    . '</a></div>';
                $galery .= '
                    <figure itemprop="associatedMedia" itemscope itemtype="http://schema.org/ImageObject" >
                        <a vr-control id="link-g'.($i).'" target="_blank" href="'.$_SERVER["DIR"].'/img/data/big/'.$images[$i].'" itemprop="contentUrl" data-size="'.$size[0].'x'.$size[1].'">
                            <img id="g'.($i).'" src="'.$_SERVER["DIR"].'/img/data/big/'.$images[$i].'" itemprop="thumbnail" alt="'.$caption.' '.($i+1).'" />
                        </a>
                        <figcaption itemprop="caption description">'.$caption.'</figcaption>                                 
                    </figure>';
            }else{
                $fout .= '<div class="hidden"> <img data-u="image" src="'.$_SERVER["DIR"].'/img/data/big/'.$images[$i].'" /> </div>';
            }
        }
    }
    $fout .= '                  
        </div>
        <div data-u="navigator" class="jssorb13" style="bottom:16px;right:16px;" data-autocenter="1">
            <div data-u="prototype" style="width:21px;height:21px;"></div>
        </div>
    </div>
    <div class="nodes_galery" itemscope itemtype="http://schema.org/ImageGallery">'.$galery.'</div>
    <div class="pswp" tabindex="-1" role="dialog" aria-hidden="true">
        <div class="pswp__bg"></div>
        <div class="pswp__scroll-wrap">
            <div class="pswp__container">
                <div class="pswp__item"></div>
                <div class="pswp__item"></div>
                <div class="pswp__item"></div>
            </div>
            <div class="pswp__ui pswp__ui--hidden">
                <div class="pswp__top-bar">
                    <div class="pswp__counter"></div>
                    <button vr-control id="pswp-button-close" class="pswp__button pswp__button--close" title="'.lang("Close").'"></button>
                    <button vr-control id="pswp-button-fs" class="pswp__button pswp__button--fs" title="'.lang("Toggle fullscreen").'"></button>
                    <button vr-control id="pswp-button-zoom" class="pswp__button pswp__button--zoom" title="'.lang("Zoom in/out").'"></button>
                    <div class="pswp__preloader">
                        <div class="pswp__preloader__icn">
                          <div class="pswp__preloader__cut">
                            <div class="pswp__preloader__donut"></div>
                          </div>
                        </div>
                    </div>
                </div>
                <div class="pswp__share-modal pswp__share-modal--hidden pswp__single-tap">
                    <div class="pswp__share-tooltip"></div> 
                </div>
                <button vr-control id="pswp-button-prev" class="pswp__button pswp__button--arrow--left" title="'.lang("Previous (arrow left)").'">
                </button>
                <button vr-control id="pswp-button-next" class="pswp__button pswp__button--arrow--right" title="'.lang("Next (arrow right)").'">
                </button>
                <div class="pswp__caption">
                    <div class="pswp__caption__center"></div>
                </div>
            </div>
        </div>
    </div>';
    
    $site->onload .= '; show_rotator(\'.nodes_galery\');';
    return $fout;
}