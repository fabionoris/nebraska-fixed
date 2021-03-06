<?php

/**
 * The Template for displaying all single Portfolios Items
 *
 * @package WordPress
 * @subpackage skeleton lambda framework v 2.0
 * by www.unitedthemes.com
 * since lambda framework v 1.0
 * based on skeleton
 */

//Includes the header.php
get_header();

//Includes the template-part-slider.php
get_template_part('template-part', 'slider');

//Includes the template-part-teaser.php
get_template_part('template-part', 'teaser');

//Content opener - this function can be found in functions/theme-layout-functions.php line 5-50
lambda_before_content($columns = 'sixteen');

//The content loop
get_template_part('loop', 'portfolio');

//Content closer - this function can be found in functions/theme-layout-functions.php line 56-61
lambda_after_content();

//Includes the footer.php
get_footer();