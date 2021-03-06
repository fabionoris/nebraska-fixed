<?php

/**
 * Camera Slider Java & HTML Markup
 * lambda framework v 2.1
 * by www.unitedthemes.com
 * since framework v 2.0
 */

#-----------------------------------------------------------------
# Camera Slider HTML Output
#-----------------------------------------------------------------
if (!function_exists('getCameraHTML')) {

    function getCameraHTML($slider_result)
    {
        $options = get_option($slider_result->option_name);

        $html = '<div class="clearfix ut-slider-wrap"><div class="camera_white_skin cameraslider_' . $slider_result->id . ' camera_wrap">';

        $z = 0;
        if (is_array($options['slides'])):
            foreach ($options['slides'] as $slide) {

                if ($slide['imgurl'])
                    $html .= '<div data-thumb="' . aq_resize($slide['imgurl'], 50, 50, true) . '" data-src="' . $slide['imgurl'] . '">';

                if (!$slide['imgurl'])
                    $html .= '<div data-src="' . get_template_directory_uri() . '/images/blank.gif">';

                if ($slide['video'])
                    $html .= '<div class="caption_play"><a data-rel="prettySlider" href="' . extractURL($slide['video']) . '">PLAY</a></div>';

                if (($slide['caption_desc'] || $slide['caption_text']))
                    $html .= '<div class="camera_caption fadeIn"><div class="captionwrap"><div class="nevada-caption ' . $slide['caption_color'] . ' ' . $slide['caption_align'] . '"><h2>' . $slide['caption_text'] . '</h2>';

                if ($slide['caption_desc'])
                    $html .= '<p>' . $slide['caption_desc'] . '</p>';

                if ($slide['buttontext'])
                    $html .= '<a href="' . $slide['caption_link'] . '" class="excerpt">' . $slide['buttontext'] . '</a>';

                if (($slide['caption_desc'] || $slide['caption_text']))
                    $html .= '</div></div></div>';

                $html .= '</div>';

                $z++;
            }

        endif;

        $html .= '</div></div>';

        return $html;
    }
}

function camera_form_array()
{
    $default = array(
        'fx' => array('default' => 'random',
            'keyvalues' => 'random;simpleFade;curtainTopLeft;curtainTopRight;curtainBottomLeft;curtainBottomRight;curtainSliceLeft;curtainSliceRight;blindCurtainTopLeft;blindCurtainTopRight;blindCurtainBottomLeft;blindCurtainBottomRight;blindCurtainSliceBottom;blindCurtainSliceTop;stampede;mosaic;mosaicReverse;mosaicRandom;mosaicSpiral;mosaicSpiralReverse;topLeftBottomRight;bottomRightTopLeft;bottomLeftTopRight;bottomLeftTopRight;scrollLeft;scrollRight;scrollHorz;scrollBottom;scrollTop',
            'keytype' => 'select',
            'js' => 'char',
            'fullname' => __('Transition Effect', UT_THEME_NAME),
            'description' => __('Select your transition effect type', UT_THEME_NAME)),

        'easing' => array('default' => 'easeInQuad;',
            'keyvalues' => 'linear;swing;easeInQuad;easeOutQuad;easeInOutQuad;easeInCubic;easeOutCubic;easeInOutCubic;easeOutQuart;easeInOutQuart;easeInQuint;easeOutQuint;easeInOutQuint;easeInSine;easeOutSine;easeInOutSine;easeInExpo;easeOutExpo;easeInOutExpo;easeInCirc;easeOutCirc;easeInOutCirc;easeInElastic;easeOutElastic;easeInOutElastic;easeInBounce;easeOutBounce;easeInOutBounce;easeInBack;easeOutBack;easeInOutBack',
            'keytype' => 'select',
            'js' => 'char',
            'fullname' => __('Easing Effect', UT_THEME_NAME),
            'description' => __('Select your easing effect type', UT_THEME_NAME)),

        'height' => array('default' => '30%',
            'keytype' => 'input',
            'js' => 'char',
            'fullname' => __('Slide Show Height', UT_THEME_NAME),
            'description' => __('here you can type pixels (for instance 300px), a percentage (relative to the width of the slideshow, for instance 50%)', UT_THEME_NAME)),

        'time' => array('default' => '2000',
            'keytype' => 'input',
            'fullname' => __('Slide Show Speed', UT_THEME_NAME),
            'description' => __('milliseconds between the end of the sliding effect and the start of the nex one', UT_THEME_NAME)),

        'transPeriod' => array('default' => '800',
            'keytype' => 'input',
            'fullname' => __('Animation Speed', UT_THEME_NAME),
            'description' => __('length of the sliding effect in milliseconds', UT_THEME_NAME)),

        'loader' => array('default' => 'bar',
            'keyvalues' => 'pie;bar;none',
            'keytype' => 'select',
            'js' => 'char',
            'fullname' => __('Loader Style', UT_THEME_NAME),
            'description' => __('even if you choose "pie", old browsers like IE8- can\'t display it... they will display always a loading bar', UT_THEME_NAME)),

        'piePosition' => array('default' => 'rightTop',
            'keyvalues' => 'rightTop;leftTop;leftBottom;rightBottom',
            'keytype' => 'select',
            'js' => 'char',
            'fullname' => __('Loader Position', UT_THEME_NAME),
            'description' => __('choose one of the 4 Positions', UT_THEME_NAME)),

        'loaderOpacity' => array('default' => '1',
            'keyvalues' => '1;2;3;4;5;6;7;8;9;10',
            'keytype' => 'select',
            'js' => 'char',
            'fullname' => __('Loader Opacity', UT_THEME_NAME),
            'description' => __('Change the loader opacity', UT_THEME_NAME)),

        'navigationHover' => array('default' => 'true',
            'keyvalues' => 'true;false',
            'js' => 'bolean',
            'keytype' => 'radio',
            'fullname' => __('Display Controls on hover?', UT_THEME_NAME),
            'description' => __('if true the navigation button (prev, next and play/stop buttons) will be visible on hover state only, if false they will be visible always', UT_THEME_NAME)),

        'pagination' => array('default' => 'true',
            'keyvalues' => 'true;false',
            'js' => 'bolean',
            'keytype' => 'radio',
            'fullname' => __('Display Bullets?', UT_THEME_NAME),
            'description' => __('If true each slide will create a bullet instead of a thumbnail', UT_THEME_NAME)),

        'thumbnails' => array('default' => 'true',
            'keyvalues' => 'true;false',
            'js' => 'bolean',
            'keytype' => 'radio',
            'fullname' => __('Display Thumbnails?', UT_THEME_NAME),
            'description' => __('If true the user will see thumbnails as an additional menu when hovering the bullets', UT_THEME_NAME))
    );

    return $default;
}