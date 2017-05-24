<?php
/**
* Prints an image viewer and updates pictures inside article
* @path /engine/core/function/print_image_viewer.php
* 
* @name    Nodes Studio    @version 2.0.3
* @author  Ripak Forzaken  <developing@nodes-tech.ru>
* @license http://www.apache.org/licenses/LICENSE-2.0 GNU Public License
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
* @param string $text Text of article.
* @param string $caption Article name.
* @param array $images Array with images for rotation.
* @return string Returns content of article on success.
* @usage <code> 
*   $text = 'Text of article. <img src="/img/1.jpg" /> <br/> <img src="/img/2.jpg" /> ';
*   $caption = 'Article name';
*   $images = array("/img/1.jpg", "/img/2.jpg"); 
*   engine::print_image_viewer($site, $text, $caption, $images); 
* </code>
*/
function print_image_viewer($site, $text, $caption, $images){
    if(!empty($images)){
        for($i = 0; $i<count($images[1]); $i++){
            $image = $images[1][$i];
            $image = str_replace('../img', '/img', $image);
            $size = getimagesize($image);
            $text = str_replace($images[1][$i].'"', $image.'" alt="'.$image.'" onClick=\'nodes_galery("'.$image.'");\' class="img pointer"', $text);
            if(!$size[0]){
                $size = getimagesize($_SERVER["PUBLIC_URL"].$image);
            }  
            if($size[0]){
                $galery .= '<figure itemprop="associatedMedia" itemscope itemtype="http://schema.org/ImageObject">
                    <a target="_blank" href="'.$image.'" itemprop="contentUrl" data-size="'.$size[0].'x'.$size[1].'">
                        <img id="nodes_galery_'.$i.'" src="'.$image.'" alt="'.$image.'" itemprop="thumbnail" alt="Image'.$i.'" />
                    </a>
                    <figcaption itemprop="caption description">'.$caption.'</figcaption>                                 
                </figure>';
            }
        }  
    }
    if(!empty($galery)){
        $fout = $text.'
        <div class="nodes_galery hidden" itemscope itemtype="http://schema.org/ImageGallery">
            '.$galery.'
        </div>
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
                        <button class="pswp__button pswp__button--close" title="Close (Esc)"></button>
                        <button class="pswp__button pswp__button--fs" title="Toggle fullscreen"></button>
                        <button class="pswp__button pswp__button--zoom" title="Zoom in/out"></button>
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
                    <button class="pswp__button pswp__button--arrow--left" title="Previous (arrow left)">
                    </button>
                    <button class="pswp__button pswp__button--arrow--right" title="Next (arrow right)">
                    </button>
                    <div class="pswp__caption">
                        <div class="pswp__caption__center"></div>
                    </div>
                </div>
            </div>
        </div>';
        $site->onload .= '; show_rotator(\'.nodes_galery\'); ';
        return $fout;
    }else return $text;
}