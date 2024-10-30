=== Cf Image Gallery ===
Contributors: AI.Takeuchi
Tags: image, gallery, plugin, shortcode, attachment image, Custom Field Template plugin, Additional Image Sizes (zui) plugin
Requires at least: 2.6
Tested up to: 3.2.1
Stable tag: 0.1.3

Cf Image Gallery create image gallery from attachment images.
write shortcode to content and upload images, only.
I recomend use some plugins together, these plugins is "Custom Field Template", "Additional Image Sizes (zui)" and "WPtouch".


== Description ==

Cf Image Gallery create image gallery from attachment images.
write shortcode to content and upload images, only.
I recomend use some plugins together, these plugins is "Custom Field Template" and "Additional Image Sizes (zui)".


== Installation ==

1. Install plugins and activate.
2. Write shortcode to content.
3. Upload images.

[For basic Installation, you can also have a look at the plugin homepage.](http://cfshoppingcart.silverpigeon.jp/?p=1017)

shortcode usage: [cf_image_gallery options]

example shortcode: [cf_image_gallery thumbnail_image_size=thumbnail link_image_size=medium first_image_size=large max_image=4 random=1 way_to_display=swap fix_size_type=width debug=off]

options:

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

* sp_value is smartphone option. example max_image will be overwrite sp_max_image option, if activated WPtouch plugin and visiter use smartphone.


css block structure:

<div class="cf_image_gallery css_selector_prefix  css_selector_prefix_pc/css_selector_prefix_applemobile">
  -- first image --
  <div class="cfimg-caption-first-div cfimg-caption/cfimg-no-caption">
    <span/a class="first_image_thumb">
      <img src="..." />
    </span>
    <p class="cfimg-caption-text">caption</p>
  </div>
  -----------------
  <div class="thumb_block">
    -- repeat this block --
    <div class="cfimg-caption-div cfimg-caption/cfimg-no-caption">
      <span/a class="thumb">
        <img src="..." />
      </span>
      <p class="cfimg-caption-text">caption</p>
    </div>
    -----------------------
  </div>
</div>


example Custom Field Template plugin setting:
[File Upload]
type = file
[File Upload *alt]
type = textarea
cols = 12
rows = 3
[File Upload *title]
type = textarea
cols = 12
rows = 3
[File Upload *caption]
type = textarea
cols = 12
rows = 3

[File Upload2]
type = file
[File Upload2 *alt]
type = textarea
cols = 12
rows = 3
[File Upload2 *title]
type = textarea
cols = 12
rows = 3
[File Upload2 *caption]
type = textarea
cols = 12
rows = 3

[File Upload3]
type = file
[File Upload3 *alt]
type = textarea
cols = 12
rows = 3
[File Upload3 *title]
type = textarea
cols = 12
rows = 3
[File Upload3 *caption]
type = textarea
cols = 12
rows = 3

[File Upload4]
type = file
[File Upload4 *alt]
type = textarea
cols = 12
rows = 3
[File Upload4 *title]
type = textarea
cols = 12
rows = 3
[File Upload4 *caption]
type = textarea
cols = 12
rows = 3


== Changelog ==

= 0.1.3 =
* Added option: thumbnail_image_caption and first_image_caption.
* Supported alt and title attribute of img tag.

= 0.1.2 =
* Bug fix, remove print_r.

= 0.1.1 =
* Add load jQuery function.
* Bug fix.


== Screenshots ==

1. screenshot-1.png
2. screenshot-2.png


== More plugins. Thank you! ==

Name: Custom Field Template
URL: http://wordpress.org/extend/plugins/custom-field-template/

Name: Additional Image Sizes (zui)
URL: http://wordpress.org/extend/plugins/additional-image-sizes-zui/

Name: WPtouch
URL: http://wordpress.org/extend/plugins/wptouch/


== Others ==

#I can not speak english very well.
#I would like you to tell me mistake my English, code and others.
#thanks.
Cf Image Gallery Website: http://cfshoppingcart.silverpigeon.jp/
Blog: http://takeai.silverpigeon.jp/
AI.Takeuchi <takeai@silverpigeon.jp>


