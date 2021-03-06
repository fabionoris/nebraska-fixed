<?php

#-----------------------------------------------------------------
# Default FlexSlider
#-----------------------------------------------------------------
if (!function_exists('flexslider')) {
    function flexslider()
    {
        global $theme_options, $lambda_meta_data;

        //Overwrite option tree settings with meta panel settings on chosen page template
        if (is_page_template('template-blog.php') || is_page_template('template-pagecreator.php')) {
            $theme_options = $lambda_meta_data->the_meta();
        }

        //Slider settings -
        $animation = (isset($theme_options['static_slide_effects'])) ? $theme_options['static_slide_effects'] : false;
        $slideDirection = (isset($theme_options['slidedirection'])) ? $theme_options['slidedirection'] : false;
        $slideshowSpeed = (isset($theme_options['animationduration'])) ? $theme_options['animationduration'] : false;
        $animationDuration = (isset($theme_options['slideshowspeed'])) ? $theme_options['slideshowspeed'] : false;
        $pauseOnHover = (isset($theme_options['slider_pause_on_hover'])) ? $theme_options['slider_pause_on_hover'] : false;

        //Option Tree only delivers Yes or No
        if ($pauseOnHover == 'Yes') {
            $pauseOnHover = 'true';
        }

        if ($pauseOnHover == 'No') {
            $pauseOnHover = 'false';
        }
        ?>

        <script type="text/javascript">
            jQuery(document).ready(function ($) {
                $('.flexslider').flexslider({
                    <?php echo ($animation) ? 'animation:"' . $animation . '",' : ''; ?>
                    <?php echo ($slideDirection) ? 'slideDirection:"' . $slideDirection . '",' : ''; ?>
                    <?php echo ($slideshowSpeed) ? 'slideshowSpeed:"' . $slideshowSpeed . '",' : ''; ?>
                    <?php echo ($animationDuration) ? 'animationDuration:"' . $animationDuration . '",' : ''; ?>
                    <?php echo ($pauseOnHover) ? 'pauseOnHover:' . $pauseOnHover . '' : ''; ?>
                });
            });
        </script>
        <?php
    }
}

#-----------------------------------------------------------------
# Widget Flexslider
#-----------------------------------------------------------------
if (!function_exists('widget_flexslider')) {
    function widget_flexslider()
    {
        global $lambda_meta_data; ?>
        <script type="text/javascript">
            jQuery(document).ready(function ($) {
                $('.widget_flexslider').flexslider({
                    animation: "fade",
                    controlNav: true
                });
            });
        </script>
        <?php
    }
}

#-----------------------------------------------------------------
# Lambda Audio Player
#-----------------------------------------------------------------
if (!function_exists('lambda_audioplayer_java')) {
    function lambda_audioplayer_java($audiometa, $postID)
    {
        //Audio Files
        $mp3 = $audiometa['mp3_url'];
        $ogg = $audiometa['ogg_url'];
        if (isset($audiometa['portfolio_mp3_url'])) {
            $mp3 = $audiometa['portfolio_mp3_url'];
        }
        if (isset($audiometa['single_mp3_url'])) {
            $mp3 = $audiometa['single_mp3_url'];
        }
        if (isset($audiometa['portfolio_ogg_url'])) {
            $ogg = $audiometa['portfolio_ogg_url'];
        }
        if (isset($audiometa['single_ogg_url'])) {
            $ogg = $audiometa['single_ogg_url'];
        }
        ?>
        <div class="thumb">
            <div class="audiopost">
                <audio controls="controls" preload>
                    <?php if ($mp3) { ?>
                        <source src="<?php echo $mp3; ?>" type='audio/mpeg'/>
                    <?php } ?>

                    <?php if ($ogg) { ?>
                        <source src="<?php echo $ogg; ?>" type='audio/ogg'/>
                    <?php } ?>
                    Your browser does not support the audio element.
                </audio>
            </div>
        </div>
    <?php }
}

#-----------------------------------------------------------------
# Nonverbla Player
#-----------------------------------------------------------------
if (!function_exists('nonverbla_video_player')) {
    function nonverbla_video_player($videometa, $postID)
    { ?>
        <?php
        global $columns;
        //Player Colors
        $playerbgColor = get_option_tree('video_main_color');
        $controlColor = get_option_tree('video_control_color');
        $controlBackColor = get_option_tree('video_control_bg_color');
        $controlColor = ereg_replace('#', '0x', $controlColor);
        $controlBackColor = ereg_replace('#', '0x', $controlBackColor);
        // Standard Video URL
        $videoURL = $videometa['nonverbla_url'];
        $videoHDURL = $videometa['nonverbla_hd_url'];
        if (isset($videometa['mp4_url'])) {
            $mp4 = $videometa['mp4_url'];
        }
        //Video Width Settings
        $videowidth = ($columns == 'sixteen') ? '934' : '632';
        if (is_home() || is_front_page() || is_archive() || is_search()) {
            $videowidth = '328';
        }
        //Poster Image
        if (isset($videometa['single_poster_image'])) {
            $posterurl = $videometa['single_poster_image'];
        } elseif (isset($videometa['poster_image'])) {
            $posterurl = $videometa['poster_image'];
        } else {
            $posterurl = wp_get_attachment_url(get_post_thumbnail_id($postID));
        }
        $posterimage = ($posterurl[0]) ? aq_resize($posterurl, $videowidth, '', true) : $videometa['poster_image'];
        // Featured Slider
        if (isset($videometa['single_mp4_url'])) {
            $mp4 = $videometa['single_mp4_url'];
        }
        if (isset($videometa['single_nonverbla_url'])) {
            $videoURL = $videometa['single_nonverbla_url'];
        }
        if (isset($videometa['single_nonverbla_hd_url'])) {
            $videoHDURL = $videometa['single_nonverbla_hd_url'];
        }
        ?>
        <div class="elastic-video-wrapper">
            <div class="elastic-video">
                <div id="videoPlayer_<?php echo $postID; ?>">
                    <?php // Fallback for Ipad
                    ?>
                    <video controls="controls" width="<?php echo $width; ?>px">
                        <?php if ($mp4) { ?>
                            <source src="<?php echo $mp4; ?>" type='video/mp4'/>
                        <?php } ?>
                    </video>
                </div>
                <script type="text/javascript">

                    var flashvars = {};
                    flashvars.mediaURL = "<?php echo $videoURL; ?>";
                    <?php if(!empty($posterimage)) { ?> flashvars.teaserURL = "<?php echo $posterimage; ?>"; <?php } ?>
                    <?php if(!empty($videoHDURL)) { ?>
                    flashvars.hdURL = "<?php echo $videoHDURL; ?>";
                    flashvars.defaultHD = "true";
                    <?php } ?>
                    flashvars.allowSmoothing = "true";
                    flashvars.autoPlay = "false";
                    flashvars.buffer = "6";
                    flashvars.showTimecode = "true";
                    flashvars.loop = "false";
                    flashvars.controlColor = "<?php echo $controlColor; ?>";
                    flashvars.controlBackColor = "<?php echo $controlBackColor; ?>";
                    flashvars.scaleIfFullScreen = "true";
                    flashvars.showScalingButton = "true";
                    flashvars.defaultVolume = "100";
                    flashvars.crop = "false";
                    //flashvars.onClick = "toggleFullScreen";

                    var params = {};
                    params.menu = "false";
                    params.allowFullScreen = "true";
                    params.allowScriptAccess = "always";
                    params.wmode = "transparent"

                    var attributes = {};
                    attributes.id = "nonverblaster";
                    attributes.bgcolor = "<?php echo $playerbgColor; ?>"

                    /* Shockwave Flash Disabled */
                    //The original Nebraska code was here

                </script>
            </div>
        </div>
    <?php }
}

#-----------------------------------------------------------------
# Infinite Scroll
#-----------------------------------------------------------------
function infinite_scroll_js()
{
    wp_register_script('infinite_scroll', get_template_directory_uri() . '/javascripts/jquery.infinitescroll.min.js', array('jquery'), null, true);
    if (is_page_template('template-home-infinite.php')) {
        wp_enqueue_script('infinite_scroll');
    }
}

add_action('wp_enqueue_scripts', 'infinite_scroll_js');

function lambda_needed_js()
{
    global $theme_options;
    $columnset = 3; ?>
    <script type="text/javascript">
        (function ($) {

            <?php if( is_page_template('template-home-infinite.php') ) { ?>

            var $container = $('#infinity-blog');
            $(window).load(function () {
                $container.isotope({
                    itemSelector: '.infinity-post',
                    animationEngine: 'best-available',
                    transformsEnabled: true
                });
            });

            $(window).smartresize(function () {
                $container.isotope({
                    itemSelector: '.infinity-post',
                    animationEngine: 'best-available',
                    transformsEnabled: true
                });
            });

            var infinite_scroll = {
                loading: {
                    img: "<?php echo get_template_directory_uri(); ?>/images/ajax-loader.gif",
                    msgText: "<?php _e('Loading the next set of posts...', UT_THEME_INITIAL); ?>",
                    finishedMsg: "<?php _e('All posts loaded.', UT_THEME_INITIAL); ?>"
                },
                "nextSelector": "#nav-below .nav-previous a",
                "navSelector": "#nav-below",
                "itemSelector": ".infinity-post",
                "contentSelector": "#infinity-blog"
            };

            jQuery(infinite_scroll.contentSelector).infinitescroll(infinite_scroll,
                function (arrayOfNewElems) {
                    $container.isotope('appended', $(arrayOfNewElems));
                    jQuery('.post-slider').each(function () {
                        jQuery("#" + this.id).flexslider({
                            animation: "fade",
                            slideshow: true,
                            slideshowSpeed: 2500
                        });
                    });
                    jQuery(".lambda-video").fitVids();
                    $container.isotope('reLayout');
                });

            <?php } ?>

            <?php if(isset($theme_options['activate_prettyphoto']) && $theme_options['activate_prettyphoto'] == 'on')    { ?>

            /* Prettyphoto
            ================================================== */
            $("a[data-rel^='prettyPhoto']").prettyPhoto({
                show_title: false
            });

            <?php } ?>

        })(jQuery);
    </script>
    <?php
}

add_action('wp_footer', 'lambda_needed_js', 100);

?>