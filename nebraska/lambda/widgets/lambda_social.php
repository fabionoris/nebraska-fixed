<?php

/**
 * Social Icon Widget
 * lambda framework v 1.0
 * by www.unitedthemes.com
 */

class WP_Widget_Social extends WP_Widget
{
    protected $slug = 'lambda_social';

    function __construct()
    {
        $widget_ops = array('classname' => 'lambda_widget_social clearfix', 'description' => __('Displays Social Icons!', UT_THEME_NAME));
        parent::__construct('lw_social', __('Lambda Social Media Widget', UT_THEME_NAME), $widget_ops);
        $this->alt_option_name = 'lambda_widget_social';

    }


    function widget($args, $instance)
    {
        extract($args);

        $title = (isset($instance['title']) && !empty($instance['title'])) ? $instance['title'] : ''; ?>


        <?php echo $before_widget; ?>

        <?php if (isset($title) && !empty($title)) echo '<h3 class="widget-title"><span>' . $title . '</span></h3>'; ?>

        <?php global $theme_options; ?>

        <ul class="lambda-sociallinks clearfix">

            <?php $target = (isset($theme_options['new_browser_tab']) && $theme_options['new_browser_tab'] == 'yes') ? 'target="_blank"' : '';

            foreach ($theme_options['social_links'] as $social => $link) {

                if (isset($link) && !empty($link))
                    echo '<li><a href="' . $link . '" class="' . $social . '" title="' . ucfirst($social) . '" ' . $target . '>' . ucfirst($social) . '</a></li>';

            } ?>

        </ul><!-- end .sociallinks -->

        <?php

        echo $after_widget;
    }

    function update($new_instance, $old_instance)
    {
        return $new_instance;
    }

    function form($instance)
    {
        $title = esc_attr($instance['title']); ?>

        <p class="description">
            <?php _e('You can manage your social media links under Theme Options -> Settings -> Social Media Links', UT_THEME_INITIAL); ?>
        <p>

        <p>
            <label for="<?php echo $this->get_field_id('title'); ?>"><?php _e('Title:', UT_THEME_INITIAL); ?></label>
            <input type="text" name="<?php echo $this->get_field_name('title'); ?>" value="<?php echo $title; ?>"
                   class="widefat" id="<?php echo $this->get_field_id('title'); ?>"/>
        </p>

    <?php }
}

add_action('widgets_init', function() {return register_widget("WP_Widget_Social");});

?>