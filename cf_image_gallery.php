<?php
/*
Plugin Name: Cf Image Gallery
Plugin URI: http://takeai.silverpigeon.jp/
Description: Placement simply shopping cart to content.
Author: AI.Takeuchi
Version: 0.1.3
Author URI: http://takeai.silverpigeon.jp/
*/
/*
 * cf_image_gallery.php
 * -*- Encoding: utf8n -*-
 */

/*
 * shortcode usage: [cf_image_gallery thumbnail_image_size=thumbnail link_image_size=medium first_image_size=large max_image=4 random=1 way_to_display=swap fix_size_type=width debug=off]
 *
 * css_selector_prefix     : default: 'cf_image_gallery'
 * thumbnail_image_size    : thumbnail/medium/large thumbnail image size
 * thumbnail_image_caption : off/on display caption below thumbnail image
 * link_image_size         : thumbnail/medium/large link image size
 * first_image_size        : thumbnail/medium/large first image size
 * first_image_caption     : off/on display caption below first image
 * first_image_link        : on/off
 * display_first_image     : off/on
 * display_thumbnail_image : on/off
 * max_image               : number of thumbnail images
 * random                  : off/on display random image
 * way_to_display          : link/swap
 * fix_size_type           : none/width/height fix image size by css
 * debug                   : off/on
 * --
 * sp_value: smart phone settings
 *
 * css block structure:
 *
 * <div class="cf_image_gallery css_selector_prefix  css_selector_prefix_pc/css_selector_prefix_applemobile">
 *   -- first image --
 *   <div class="cfimg-caption-first-div cfimg-caption/cfimg-no-caption">
 *     <span/a class="first_image_thumb">
 *       <img src="..." />
 *     </span>
 *     <p class="cfimg-caption-text">caption</p>
 *   </div>
 *   -----------------
 *   <div class="thumb_block">
 *     -- repeat below block --
 *     <div class="cfimg-caption-div cfimg-caption/cfimg-no-caption">
 *       <span/a class="thumb">
 *         <img src="..." />
 *       </span>
 *       <p class="cfimg-caption-text">caption</p>
 *     </div>
 *     -----------------------
 *   </div>
 * </div>
 */
function cf_image_gallery($args) {
    //print_r($args);
    if ($args['way_to_display'] === 'swap') {
        add_action('wp_footer', 'cf_image_gallery_javascript');
    }
    $cf_image = new class_cf_image_gallery($args);
    return $cf_image->get_cf_image();
}
add_shortcode('cf_image_gallery', 'cf_image_gallery');
wp_enqueue_script('jquery');

class class_cf_image_gallery {
    var $args_key_list;
    var $applemobile;
    var $args;
    var $cfs;
    
    function class_cf_image_gallery($args) {
        global $post;
        
        // args key list
        $this->args_key_list = array('css_selector_prefix',
                                     'thumbnail_image_size',
                                     'thumbnail_image_caption',
                                     'link_image_size',
                                     'first_image_size',
                                     'first_image_caption',
                                     'first_image_link',
                                     'display_first_image',
                                     'display_thumbnail_image',
                                     'max_image',
                                     'random',
                                     'way_to_display',
                                     'fix_size_type',
                                     'debug',
                                     );
        
        if (!isset($args['css_selector_prefix'])) $args['css_selector_prefix'] = 'cf_image_gallery';
        if (!isset($args['way_to_display'])) $args['way_to_display'] = 'link';

        $this->args = $args;
        
        // check smart phone
        $this->applemobile();
        $this->set_smartphone_value();

        $this->cfs = get_post_custom($post->ID);
        //print_r($args);
        //print_r($cfs);
    }

    function alt_title($img, $alt, $title) {
        if ($alt) {
            $img = preg_replace('/alt="(.*?)"/', 'alt="'.$alt.'"', $img);
        }
        if ($title) {
            $img = preg_replace('/title="(.*?)"/', 'title="'.$title.'"', $img);
        }
        //echo htmlspecialchars($img);
        return $img;
    }

    function get_cf_value($cf_key) {
        global $post;
        $cfs = $this->cfs;
        $args = $this->args;
        
        $meta = get_post_meta($post->ID, $cf_key, true);
        if (!$meta) return NULL;

        //$img['key'] = $cf_key;
        //$img['value'] = $cfs[$cf_key][0];
        $img['alt'] = nl2br($cfs[$cf_key . ' *alt'][0]);
        $img['title'] = nl2br($cfs[$cf_key . ' *title'][0]);
        $img['caption'] = nl2br($cfs[$cf_key . ' *caption'][0]);
        $img['caption_html'] = '<p class="cfimg-caption-text">'.$img['caption'].'</p>';
        
        // get img tag
        $img['thumbnail']['html'] = wp_get_attachment_image($meta, $args['thumbnail_image_size']);
        if (!$img['thumbnail']['html']) return NULL;
        
        // alt and title
        if ($img['alt'] && !$img['title']) {
            $img['title'] = $img['alt'];
        }
        if (!$img['alt'] && $img['title']) {
            $img['alt'] = $img['title'];
        }
        if (!$img['alt']) {
            preg_match('/alt="(.*?)"/', $img['thumbnail']['html'], $match);
            $img['alt'] = $match[1];
        }
        if (!$img['title']) {
            preg_match('/title="(.*?)"/', $img['thumbnail']['html'], $match);
            $img['title'] = $match[1];
        }
        
        $img['thumbnail']['html'] = $this->alt_title($img['thumbnail']['html'], $img['alt'], $img['title']);
        //$src = wp_get_attachment_image_src($meta, $args['thumbnail_image_size']);
        //$img['thumbnail']['src'] = $src[0];
        //$img['thumbnail']['width'] = $src[1];
        //$img['thumbnail']['height'] = $src[2];
        
        $img['first']['html'] = wp_get_attachment_image($meta, $args['first_image_size']);
        $img['first']['html'] = $this->alt_title($img['first']['html'], $img['alt'], $img['title']);
        $src = wp_get_attachment_image_src($meta, $args['first_image_size']);
        $img['first']['src'] = $src[0];
        $img['first']['width'] = $src[1];
        $img['first']['height'] = $src[2];

        //$img['link']['html'] = wp_get_attachment_image($meta, $args['link_image_size']);
        //$img['link']['html'] = $this->alt_title($img['link']['html'], $img['alt'], $img['title']);
        $src = wp_get_attachment_image_src($meta, $args['link_image_size']);
        //echo htmlspecialchars($src[0]);
        $img['link']['src'] = $src[0];
        $img['link']['width'] = $src[1];
        $img['link']['height'] = $src[2];
        
        
        // caption
        if ($args['thumbnail_image_caption'] === 'on') {
            $caption_thumbnail_class = 'cfimg-caption-div cfimg-caption';
            $caption_html = $img['caption_html'];
            if (!$img['caption']) {
                $img['caption'] = '&nbsp;';
            }
        } else {
            $caption_thumbnail_class = 'cfimg-caption-div cfimg-no-caption';
            $caption_html = '';
        }
        
        $img['tag']['swap'] = '<div class="'.$caption_thumbnail_class.'"><span class="thumb" first_img_width="' . $img['first']['width'] . '" first_img_height="' . $img['first']['height'] . '" first_img_src="' . $img['first']['src'] . '" first_img_alt="' . $img['alt'] . '" first_img_title="' . $img['title'] . '" first_img_caption="' . $img['caption'] . '" link_img_width="' . $img['link']['width'] . '" link_img_height="' . $img['link']['height'] . '" link_img_src="' . $img['link']['src'] . '">' . $img['thumbnail']['html'] . '</span>'.$caption_html.'</div>';
        
        $img['tag']['link'] = '<div class="'.$caption_thumbnail_class.'"><a class="thumb" width="' . $img['link']['width'] . '" height="' . $img['link']['height'] . '" href="' . $img['link']['src'] . '">' . $img['thumbnail']['html'] . '</a>'.$caption_html.'</div>';
        
        //$img = $this->alt_title($img);
        //print_r($img);
        return $img;
    }


    function get_cf_image() {
        global $post;
        $args = $this->args;
        $cfs = $this->cfs;
        
        $first = '';
        $img_tags_link = array();
        $img_tags_swap = array();
        foreach ($cfs as $name => $value) {
            $img = $this->get_cf_value($name);
            if (!$img) continue;
            $imgs[$name] = $img;
            
            // check max image
            if ($args['random'] !== 'on' && isset($args['max_image']) && count($imgs) >= $args['max_image']) {
                break;
            }
        }
        
        // Non custom field attachment image data
        if (!$imgs) return;
        
        if (count($imgs) == 1) {
            $args['way_to_display'] = 'link';
            $args['display_first_image'] = 'on';
            $args['display_thumbnail_image'] = 'off';
            $img_tags = $img_tags_link;
        }
        
        // random
        if ($args['random'] === 'on') {
            $imgs = $this->random($imgs);
        }
        // get first image
        $first = $this->get_first_image($imgs);
        
        // make return result html
        $html = '';
        if ($args['way_to_display'] == 'swap' || $args['display_first_image'] == 'on') {
            $html .= $first;
        }

        // make thumbnail images
        $img_tags = $this->get_imgs_array($imgs, 'tag', $args['way_to_display']);
        $html .= '<div class="thumb_block">' . join(' ', $img_tags) . '</div>';

        $fix_size_type = 'fix_size_type="'. $args['fix_size_type'] . '"';
        if ($this->applemobile) {
            $terminaltype = $args['css_selector_prefix'] . '_applemobile';
        } else {
            $terminaltype = $args['css_selector_prefix'] . '_pc';
        }
        
        return '<div class="cf_image_gallery ' . $args['css_selector_prefix'] . ' ' . $terminaltype . '" ' . $fix_size_type . ' >' . $html . '</div>';
    }

    function get_imgs_array($imgs, $key1, $key2 = '') {
        $values = array();
        if ($key2) {
            foreach ($imgs as $key => $value) {
                $values[] = $value[$key1][$key2];
            }
        } else {
            foreach ($imgs as $key => $value) {
                $values[] = $value[$key1];
            }
        }
        return $values;
    }
    
    function get_first_image($imgs) {
        $args = $this->args;
        
        $first = array_shift($imgs);
        //print_r($first);

        if ($args['first_image_caption'] === 'on') {
            $caption_first_class = 'cfimg-caption-first-div cfimg-caption';
            $caption_html = $first['caption_html'];
            if (!$first['caption']) {
                $first['caption'] = '&nbsp;';
            }
        } else {
            $caption_first_class = 'cfimg-caption-first-div cfimg-no-caption';
            $caption_html = '';
        }

        if ($first['alt']) {
            $alt = 'alt="'.$first['alt'].'"';
        }
        if ($first['title']) {
            $title = 'title="'.$first['title'].'"';
        }

        //echo htmlspecialchars($img['link']['src']);
        if ($args['first_image_link'] == 'on') {
            $img_tag = '<div class="'.$caption_first_class.'"><a class="first_image_thumb" href="' . $first['link']['src'] . '">' . $first['first']['html'] . '</a>'.$caption_html.'</div>';
        } else {
            $img_tag = '<div class="'.$caption_first_class.'"><span class="first_image_thumb" href="' . $img['link']['src'] . '">' . $first['first']['html'] . '</span>'.$caption_html.'</div>';
        }
        return '<div class="first_img">' . $img_tag . '</div>';
    }
    
    function random($img_tags) {
        global $post;
        $args = $this->args;

        // why necessary?
        if (count($img_tags) == 1) return $img_tags;
        
        if ($args['random'] === 'on') {
            $max = count($img_tags);
            if (isset($args['max_image'])) {
                if ($max > $args['max_image']) {
                    $max = $args['max_image'];
                }
            }
            $keys = array_keys($img_tags);
            shuffle($keys);
            //print_r($keys);

            $ret = array();
            for ($i = 0; $i < $max; $i++) {
                $k = array_shift($keys);
                $ret[$k] = $img_tags[$k];
            }
        }
        if (!is_array($ret)) {
            $ret = array_merge(array(), $ret);
        }
        return $ret;
    }
    
    function applemobile() {
        // check smart phone
        $this->applemobile = NULL; // NULL or true or false
        
        if (class_exists('WPtouchPlugin')) {
            $wptouch = new WPtouchPlugin();
            $this->applemobile = $wptouch->applemobile; // boolean
            if ($this->args['debug'] == 'on' && $this->applemobile) {
                echo '<p>cf_image_gallery: applemobile = '.$this->applemobile . '</p>';
            }
        } else if ($this->args['debug'] == 'on') {
            echo '<p>cf_image_gallery: WPtouchPlugin not found.</p>';
        }
    }

    function set_smartphone_value() {
        // overridde value on smart phone value
        if ($this->applemobile) {
            foreach ($this->args_key_list as $k => $v) {
                $sp = 'sp_' . $v;
                if (!isset($this->args[$sp])) {
                    if ($this->args['debug'] == 'on') {
                        echo '<p>cf_image_gallery: args key "' . $sp . '" was not define.</p>';
                    }
                    continue;
                }
                $this->args[$v] = $this->args[$sp];
            }
        }
    }

    
    
}// class


function cf_image_gallery_javascript() {
    //print_r($args);
    $js = <<< EOJS
<script type="text/javascript">
//<![CDATA[
var cf_image_gallery_javascript = { version: '1' }
cf_image_gallery_javascript.check_click = false;
jQuery(document).ready(function() {
    cf_image_gallery_javascript.init();
    // Reset Ready.functions
    jQuery('*').click(function(){
        if (cf_image_gallery_javascript.check_click) return false;
        
        var body_height = jQuery("body").height();
        if (cfshoppingcart_js.body_height != body_height) {
            cfshoppingcart_js.body_height = body_height;
            //document.title = document.title + '.';
            // Reset this javascript ready function
            cf_image_gallery_javascript.init();
        }
    });
});
cf_image_gallery_javascript.init = function() {
    if (cf_image_gallery_javascript.check_click) return false;
    //alert('a');
    
    //var cthumb = 'div.cf_image_gallery span.thumb';
    var cthumb = 'div.cf_image_gallery div.cfimg-caption-div';
    jQuery(cthumb).click(function () {
        cf_image_gallery_javascript.check_click = true;
        
        //alert('click');
        var lw = jQuery(this).parent().parent().children('div.first_img').children().children().children().attr("width");
        var lh = jQuery(this).parent().parent().children('div.first_img').children().children().children().attr("height");
        //alert(lw + ', ' + lh);
        //  lw = 300;
        //  lx = 300;
        
        //var href = jQuery(this).attr("href");
        var w = jQuery(this).children().attr("first_img_width");
        var h = jQuery(this).children().attr("first_img_height");
        //alert(w + ', ' + h + ', ' + href);
        
        var fix_size_type = jQuery(this).parent().parent().attr('fix_size_type');
        //alert(fix_size_type);
        if (fix_size_type == 'height') {
            // keep uniform height
            if (lh == h) {
            } else if (lh > h) {
                ah = lh / h;
                w = w * ah;
                h = lh;
            } else if (lh < h) {
                ah = lh / h;
                w = w * ah;
                h = lh;
            }
        } else if (fix_size_type == 'width') {
            // keep uniform width
            if (lw == w) {
            } else if (lw > w) {
                aw = lw / w;
                h = h * aw;
                w = lw;
            } else if (lw < w) {
                aw = lw / w;
                h = h * aw;
                w = lw;
            }
        }
        h = parseInt(h);
        w = parseInt(w);
        //alert(w + ', ' + h + ', ' + href);
        //
        var alt = jQuery(this).children().attr('first_img_alt');
        var title = jQuery(this).children().attr('first_img_title');
        var src = jQuery(this).children().attr('first_img_src');
        var href = jQuery(this).children().attr('link_img_src');
        var caption = jQuery(this).children().attr('first_img_caption');
        //alert(caption);
        jQuery(this).parent().parent('div').children('div.first_img').children().children('p').html(caption);
        jQuery(this).parent().parent('div').children('div.first_img').children().children().children('img').attr('src',src);
        jQuery(this).parent().parent('div').children('div.first_img').children().children('a').attr('href',href);
        //
        jQuery(this).parent().parent('div').children('div.first_img').children().children().children('img').css('width',w);
        jQuery(this).parent().parent('div').children('div.first_img').children().children().children('img').css('height',h);
        jQuery(this).parent().parent('div').children('div.first_img').children().children().children('img').attr('alt',alt);
        jQuery(this).parent().parent('div').children('div.first_img').children().children().children('img').attr('title',title);
        
        cf_image_gallery_javascript.check_click = false;
        return false;
    });
}
//]]>
</script>
EOJS;
    echo $js;
}

?>
