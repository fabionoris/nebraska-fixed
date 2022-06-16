<?php

/**
 * Template Name: Page Creator
 *
 * lambda framework v 2.1
 * by www.unitedthemes.com
 * since lambda framework v 1.0
 * based on skeleton
 */

global $lambda_meta_data;

$meta_sidebar = $lambda_meta_data->get_the_value('sidebar');
$meta_sidebar = (!empty($meta_sidebar)) ? $meta_sidebar : get_option_tree('select_sidebar');

//Includes the header.php
get_header(); ?>

<?php
//Includes the template-part-slider.php
get_template_part('template-part', 'slider');

//Includes the template-part-teaser.php
get_template_part('template-part', 'teaser');

//Set column layout depending if user wants to display a sidebar
if ($meta_sidebar != UT_THEME_INITIAL . 'sidebar_none') {

    lambda_before_content($columns = '');

} elseif ($meta_sidebar == UT_THEME_INITIAL . 'sidebar_none') {

    lambda_before_content($columns = 'sixteen');

}

get_template_part('loop', 'pagecreator');

//Content closer - this function can be found in functions/theme-layout-functions.php line 56-61
lambda_after_content();

//Include the sidebar-page.php
if (empty($columns))
    get_sidebar();

//Includes the footer.php
get_footer();

?>