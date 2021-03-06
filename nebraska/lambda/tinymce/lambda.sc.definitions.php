<?php

#-----------------------------------------------------------------
# Column Layouts
#-----------------------------------------------------------------

//Thirds
$lambda_shortcodes['headline_1'] = array('type' => 's', 'title' => __('One Third Column Shortcodes', UT_THEME_NAME));
$lambda_shortcodes['one_third'] = array('type' => 'c', 'title' => __('One Third Column', UT_THEME_NAME), 'attr' => array('last' => array('type' => 'custom', 'title' => 'Last Column')));
$lambda_shortcodes['two_thirds'] = array('type' => 'c', 'title' => __('Two Thirds Column', UT_THEME_NAME), 'attr' => array('last' => array('type' => 'custom', 'title' => 'Last Column')));

//Half
$lambda_shortcodes['headline_2'] = array('type' => 's', 'title' => __('One Half Column Shortcodes', UT_THEME_NAME));
$lambda_shortcodes['one_half'] = array('type' => 'c', 'title' => __('One Half Column', UT_THEME_NAME), 'attr' => array('last' => array('type' => 'custom', 'title' => 'Last Column')));

//Fourth
$lambda_shortcodes['headline_3'] = array('type' => 's', 'title' => __('One Fourth Column Shortcodes', UT_THEME_NAME));
$lambda_shortcodes['one_fourth'] = array('type' => 'c', 'title' => __('One Fourth Column', UT_THEME_NAME), 'attr' => array('last' => array('type' => 'custom', 'title' => 'Last Column')));
$lambda_shortcodes['three_fourths'] = array('type' => 'c', 'title' => __('Three Fourths Column', UT_THEME_NAME), 'attr' => array('last' => array('type' => 'custom', 'title' => 'Last Column')));

//Fifth
$lambda_shortcodes['headline_4'] = array('type' => 's', 'title' => __('One Fifth Column Shortcodes', UT_THEME_NAME));
$lambda_shortcodes['one_fifth'] = array('type' => 'c', 'title' => __('One Fifth Column', UT_THEME_NAME), 'attr' => array('last' => array('type' => 'custom', 'title' => 'Last Column')));
$lambda_shortcodes['two_fifth'] = array('type' => 'c', 'title' => __('Two Fifth Column', UT_THEME_NAME), 'attr' => array('last' => array('type' => 'custom', 'title' => 'Last Column')));
$lambda_shortcodes['three_fifth'] = array('type' => 'c', 'title' => __('Three Fifth Column', UT_THEME_NAME), 'attr' => array('last' => array('type' => 'custom', 'title' => 'Last Column')));
$lambda_shortcodes['four_fifth'] = array('type' => 'c', 'title' => __('Four Fifth Column', UT_THEME_NAME), 'attr' => array('last' => array('type' => 'custom', 'title' => 'Last Column')));

//Sixth
$lambda_shortcodes['headline_5'] = array('type' => 's', 'title' => __('One Sixth Column Shortcodes', UT_THEME_NAME));
$lambda_shortcodes['one_sixth'] = array('type' => 'c', 'title' => __('One Sixth Column', UT_THEME_NAME), 'attr' => array('last' => array('type' => 'custom', 'title' => 'Last Column')));
$lambda_shortcodes['five_sixth'] = array('type' => 'c', 'title' => __('Five Sixth Column', UT_THEME_NAME), 'attr' => array('last' => array('type' => 'custom', 'title' => 'Last Column')));

#-----------------------------------------------------------------
# Elements like Tabs & Toggle or Callout
#-----------------------------------------------------------------
$lambda_shortcodes['headline_6'] = array('type' => 's', 'title' => __('Elements', UT_THEME_NAME));

//Toggle
$lambda_shortcodes['toggle'] = array('type' => 'c', 'title' => __('Toggle Panel', UT_THEME_NAME), 'attr' => array('title' => array('type' => 'text', 'title' => 'Title')));

//Tabs
$lambda_shortcodes['tabgroup'] = array('type' => 'm', 'title' => __('Tabs', UT_THEME_NAME), 'attr' => array('item' => array('type' => 'custom')));

//Blockquote
$lambda_shortcodes['blockquote_left'] = array('type' => 'c', 'title' => __('Blockquote (left)', UT_THEME_NAME));

//Blockquote
$lambda_shortcodes['blockquote_right'] = array('type' => 'c', 'title' => __('Blockquote (right)', UT_THEME_NAME));

//Highlight
$lambda_shortcodes['highlight_one'] = array('type' => 'c', 'title' => __('Highlight (style one)', UT_THEME_NAME));
$lambda_shortcodes['highlight_two'] = array('type' => 'c', 'title' => __('Highlight (style two)', UT_THEME_NAME));
$lambda_shortcodes['highlight_three'] = array('type' => 'c', 'title' => __('Highlight (style three)', UT_THEME_NAME));
$lambda_shortcodes['highlight_four'] = array('type' => 'c', 'title' => __('Highlight (style four)', UT_THEME_NAME));

//Dropcap
$lambda_shortcodes['dropcap_one'] = array('type' => 'c', 'title' => __('Dropcap One', UT_THEME_NAME));
$lambda_shortcodes['dropcap_two'] = array('type' => 'c', 'title' => __('Dropcap Two', UT_THEME_NAME));

//Alerts
$lambda_shortcodes['alert'] = array('type' => 'c', 'title' => __('Notification Box', UT_THEME_NAME), 'attr' => array('color' => array('type' => 'radio', 'title' => 'Color', 'def' => 'info', 'opt' => array('white' => 'White', 'red' => 'Red', 'green' => 'Green', 'blue' => 'Blue', 'yellow' => 'Yellow'))));