<?php

/**
 * The Sidebar containing the primary blog sidebar
 *
 * lambda framework v 2.1
 * by www.unitedthemes.com
 * since lambda framework v 1.0
 * based on skeleton
 */

global $lambda_meta_data, $wp_query;

$page_id = (isset($wp_query->post)) ? $wp_query->post->ID : null;

if (is_home()) {

    $homeid = get_option('page_for_posts');
    $sidebar = get_post_meta($homeid, $lambda_meta_data->get_the_id(), TRUE);

} else {

    $sidebar = get_post_meta($page_id, $lambda_meta_data->get_the_id(), TRUE);

}

do_action('st_before_sidebar');

echo '<ul class="sidebar">';

if (!isset($sidebar['sidebar']) || (isset($sidebar['sidebar']) && $sidebar['sidebar'] == UT_THEME_INITIAL . "sidebar_default")) {

    dynamic_sidebar(get_option_tree('select_sidebar'));

} elseif (isset($sidebar['sidebar'])) {

    dynamic_sidebar($sidebar['sidebar']);

}

echo '</ul>';

do_action('st_after_sidebar');