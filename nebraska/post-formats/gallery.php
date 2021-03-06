<?php

global $lambda_content_column, $theme_options, $lambda_meta_data;

$gallerytype = $lambda_meta_data->get_the_value('gallery_type');

if (get_post_type($post->ID) == UT_PORTFOLIO_SLUG)
    $gallerytype = $lambda_meta_data->get_the_value('portfolio_gallery_type');

#-----------------------------------------------------------------
# Define Image Width
#-----------------------------------------------------------------
if ($lambda_content_column == 'sixteen') {
    $lambda_image_width = '940';
} elseif ($lambda_content_column == 'eleven') {
    $lambda_image_width = '640';
} elseif ($lambda_content_column == 'eight') {
    $lambda_image_width = '460';
} else {
    $lambda_image_width = '460';
}


#-----------------------------------------------------------------
# Gallery Output
#-----------------------------------------------------------------
?>

<?php if (post_password_required()) : ?>

    <?php the_content(); ?>

<?php else : ?>

    <?php

    //extract wordpress gallery shortcode to retrieve image ID's
    $content = get_the_content();
    $pattern = get_shortcode_regex();
    preg_match("/$pattern/s", $content, $match);
    if (isset($match[2]) && ("gallery" == $match[2])) {
        $atts = $match[3];

        //$atts = shortcode_parse_atts( $match[3] );
        //$attachments = isset( $atts['ids'] ) ? explode( ',', $atts['ids'] ) : get_children( 'post_type=attachment&post_mime_type=image&post_parent=' . $post->ID .'&order=ASC&orderby=menu_order ID' );
    } else {
        $atts = '';
    }

    ?>

    <?php if ($gallerytype == "slider_gallery") { ?>

        <script type="text/javascript">
            (function ($) {

                $(document).ready(function () {

                    $(".galleryid-<?php the_ID(); ?>").flexslider({
                        animation: "fade",
                        slideshow: true
                    });

                });

            })(jQuery);
        </script>

    <?php } ?>

    <?php echo do_shortcode('[gallery ' . $atts . ']'); ?>

<?php endif; ?>