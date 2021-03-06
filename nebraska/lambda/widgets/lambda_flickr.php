<?php

/**
 * Flickr Widget
 * lambda framework v 2.1
 * by www.unitedthemes.com
 * since framework v 1.0
 */

class WP_Widget_Flickr extends WP_Widget
{
    protected $slug = 'lambda_flickr';

    function __construct()
    {
        $widget_ops = array('classname' => 'lambda_widget_flickr', 'description' => __('Displays Flickr images by user or tags.', UT_THEME_NAME));
        parent::__construct('lw_flickr', __('Lambda - Flickrstream', UT_THEME_NAME), $widget_ops);
        $this->alt_option_name = 'lambda_widget_flickr';
    }

    function form($instance)
    {
        if ($instance) {
            $title = esc_attr($instance['title']);
            $flickr_public_values = esc_attr($instance['lambda_flickr_public_values']);
            $flickr_limit = esc_attr($instance['lambda_flickr_limit']);
            if (!$flickr_limit) $flickr_limit = 8;
        } ?>

        <p>
            <label for="<?php echo $this->get_field_id('title'); ?>"><?php _e('Title:', UT_THEME_NAME); ?>
                <input class="widefat" id="<?php echo $this->get_field_id('title'); ?>"
                       name="<?php echo $this->get_field_name('title'); ?>" type="text" value="<?php echo $title; ?>"/>
            </label>
        </p>
        <p>
            <label for="<?php echo $this->get_field_id('lambda_flickr_public_values'); ?>"><?php _e('Flickr ID:', UT_THEME_NAME); ?></label>
            <input id="<?php echo $this->get_field_id('lambda_flickr_public_values'); ?>"
                   name="<?php echo $this->get_field_name('lambda_flickr_public_values'); ?>" type="text"
                   value="<?php echo $flickr_public_values; ?>"/>
        </p>
        <p>
            <label for="<?php echo $this->get_field_id('lambda_flickr_limit'); ?>"><?php _e('Limit:', UT_THEME_NAME); ?></label>
            <input id="<?php echo $this->get_field_id('lambda_flickr_limit'); ?>"
                   name="<?php echo $this->get_field_name('lambda_flickr_limit'); ?>" size="1" type="text"
                   value="<?php echo $flickr_limit; ?>"/>
        </p>

        <?php
    }

    function update($new_instance, $old_instance)
    {
        return $new_instance;
    }

    function widget($args, $instance)
    {
        extract($args);
        extract($instance);
        $title = apply_filters($this->slug, $title);

        if ($title)
            $title = $before_title . do_shortcode($title) . $after_title;

        $var = rand();

        echo $before_widget . $title . '
	
		<script type="text/javascript">
		(function($){
				
				$(document).ready(function($) {
					   $.getJSON("http://api.flickr.com/services/feeds/photos_public.gne?id=' . $lambda_flickr_public_values . '&format=json&jsoncallback=?", function(data) {
							  
							  for (i=1; i<=' . $lambda_flickr_limit . '; i++) {
									var pic = data.items[i];
								    var smallpic = pic.media.m.replace(\'_m.jpg\', \'_s.jpg\');									
									$(".flickr_' . $var . '").append("<div class=\"overflow-hidden imagepost\"><a title=\'" + pic.title + "\' href=\'" + pic.link + "\' target=\'_blank\'><img src=\'" + smallpic + "\' /><div class=\"hover-overlay\" style=\"display:none;\"><span class=\"flicker-hover\"><img alt=\"' . __("Flickr Image", UT_THEME_INITIAL) . '\" src=\"' . get_template_directory_uri() . '/images/flickr-hover-icon.png\"></span></div></a></div>");
						      }
					   });
				});
				
		})(jQuery);	
		</script>
	    <div class="flickr_items flickr_' . $var . ' clearfix">
	    </div>		
	
	' . $after_widget;

    }

}

add_action('widgets_init', function(){return register_widget("WP_Widget_Flickr");});

?>