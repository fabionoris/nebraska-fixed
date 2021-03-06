<?php

/**
 * SoundCloud
 * Version: 2.0
 * Author: Johannes Wagener <johannes@soundcloud.com>
 * Author URI: http://johannes.wagener.cc
 * add_shortcode( "soundcloud", "soundcloud_shortcode" );
 * Enhanced by UnitedThemes
*/

add_shortcode("soundcloud", "lambda_soundcloud_shortcode");

function lambda_soundcloud_shortcode($attributes, $content = null)
{
    return (new LambdaSoundcloudShortcode)->parse($attributes, $content);
}

class LambdaSoundcloudShortcode
{
    const IFRAME_HEIGHT = '166';
    const IFRAME_TRACKLIST_HEIGHT = '450';
    const IFRAME_WIDTH = '100%';

    public function parse($attributes, $content = null)
    {
        extract(shortcode_atts(array(
            'url' => $content,
            'iframe' => self::getDefaultIframePreference(),
            'params' => self::getDefaultQuery(),
            'height' => '',
            'width' => ''
        ), $attributes));
        $iframe = true;
        $type = self::getType($url);
        $width = self::getWidth($width, $iframe, $type);
        $height = self::getHeight($height, $iframe, $type);
        return self::getHTML($url, $iframe, $params, $width, $height);
    }

    public function getDefaultQuery()
    {
        $options = array(
            'auto_play',
            'show_comments',
            'theme_color',
            'show_artwork'
        );
        $params = array();
        foreach ($options as &$option) {
            $value = get_option_tree('soundcloud_' . $option, '');
            if (!empty($value)) {
                if ($value == 'Yes') {
                    $value = 'true';
                } else {
                    $value = 'false';
                }
                $params[$option] = $value;
            }
        }
        $params['color'] = str_replace('#', '', get_option_tree('color_scheme'));
        return http_build_query($params);
    }

    public function getDefaultIframePreference()
    {
        $pref = get_option_tree('soundcloud_player_iframe', '', false, false);
        ($pref == 'HTML5') ? $pref = 'true' : $pref = 'false';
        return self::booleanize($pref);
    }

    private function booleanize($value)
    {
        if ($value && strtolower($value) !== "false") {
            return true;
        } else {
            return false;
        }
    }

    private function isLegacyURL($url)
    {
        return !preg_match("/api.soundcloud.com/i", $url);
    }

    private function getWidth($width, $iframe, $type)
    {
        if (empty($width)) {
            $width = get_option_tree('soundcloud_player_width');
            $width = $width === '' ? self::IFRAME_WIDTH : $width;
        }
        return $width;
    }

    private function getHeight($height, $iframe, $type)
    {
        switch ($type) {
            case 'groups':
            case 'sets':
            case 'playlists':
                $height = (empty($height)) ? get_option_tree('soundcloud_player_height_multi') : $height;
                $height = (empty($height)) ? $default : $height;
                $height = self::fixHeight($height, self::IFRAME_TRACKLIST_HEIGHT);
                break;
            default:
                $height = (empty($height)) ? get_option_tree('soundcloud_player_height') : $height;
                $height = (empty($height)) ? $default : $height;
                $height = self::fixHeight($height, self::IFRAME_HEIGHT);
                break;
        }
        return $height;
    }

    private function fixHeight($height, $min_height)
    {
        if (!preg_match("/[0-9]+%/", $height) && intval($height) < $min_height) {
            $height = $min_height;
        }
        return $height;
    }

    private function getType($url)
    {
        if (empty($url)) {
            return false;
        }
        if ($url = parse_url($url)) {
            $splitted_url = split("/", $url['path']);
            $media_type = $splitted_url[count($splitted_url) - 2];
            return $media_type;
        }
    }

    private function getHTML($url, $iframe, $params, $width, $height)
    {
        $encoded_url = urlencode($url);
        $parsed_url = parse_url($url);
        $player_host = 'w.soundcloud.com';
        $player_params = 'url=' . $encoded_url . '&' . $params;
        $player_src = 'http://' . $player_host . '/player/?' . $player_params;
        $width = esc_attr($width);
        $height = esc_attr($height);
        $player_src = esc_attr($player_src);
        $html = '<iframe width="' . $width . '" height="' . $height . '" scrolling="no" frameborder="no" src="' . $player_src . '"></iframe>';
        return $html;
    }
}