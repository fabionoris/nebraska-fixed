<?php

/**
 * Portfolio Video Widget
 * lambda framework v 1.0
 * by www.unitedthemes.com
 * since framework v 1.0
 */

class WP_Widget_Video extends WP_Widget {

    function __construct() {
        $widget_ops = array('classname' => 'lambda_widget_video', 'description' => __( 'Insert your embedded code in here!', UT_THEME_NAME) );
        parent::__construct('lw_video', __('Lambda - Embedded Video', UT_THEME_NAME), $widget_ops);
        $this->alt_option_name = 'lambda_widget_video';
    }
    function form($instance) {
        $title = ( isset($instance['title']) && !empty($instance['title']) ) ? esc_attr($instance['title']) : '';
        $text = ( isset($instance['text']) && !empty($instance['text']) ) ? esc_attr($instance['text']) : '';

        ?>

        <label><?php _e('Title', UT_THEME_NAME); ?>: <input type="text" style="width:100%;" id="<?php echo $this->get_field_id('title'); ?>" name="<?php echo $this->get_field_name('title'); ?>" value="<?php echo $title; ?>" /></label>
        <label><?php _e('Text', UT_THEME_NAME); ?>: <textarea class="widefat" rows="16" cols="20" id="<?php echo $this->get_field_id('text'); ?>" name="<?php echo $this->get_field_name('text'); ?>"><?php echo $text; ?></textarea></label>

        <?php
    }

    function update($new_instance, $old_instance) {
        return $new_instance;
    }

    function widget( $args, $instance ){
        extract( $args );
        extract( $instance );

        if( $title )
            $title = $before_title.do_shortcode($title).$after_title;

        $text = do_shortcode( $text );
        $text = $title.'<div class="lambda-video">'.$text.'</div>';

        echo "$before_widget
	    	$text
		  $after_widget";
    }
}

add_action('widgets_init', function() {return register_widget("wp_widget_video");});

?>
