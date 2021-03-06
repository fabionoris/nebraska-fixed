<?php

#-----------------------------------------------------------------
# Before Content
#-----------------------------------------------------------------

if (!function_exists('lambda_before_content')) {
    function lambda_before_content($columns)
    {
        global $lambda_meta_data;
        if (is_home()) {
            //get the meta data from the blog page
            $homeid = get_option('page_for_posts');
            $sidebar_align = get_post_meta($homeid, $lambda_meta_data->get_the_id(), TRUE);
        } else {
            $sidebar_align = $lambda_meta_data->the_meta();
        }
        $sidebar_align = (isset($sidebar_align['sidebar_align'])) ? $sidebar_align['sidebar_align'] : get_option_tree('sidebar_alignement');

        #-----------------------------------------------------------------
        # Standard Column Set
        #-----------------------------------------------------------------
        if (empty($columns) && $sidebar_align != 'both') {
            //one sidebar
            $columns = 'eleven';
            $GLOBALS['lambda_content_column'] = $columns;
        } elseif (empty($columns) && $sidebar_align == 'both') {
            //two sidebars
            $columns = 'eight';
            $GLOBALS['lambda_content_column'] = $columns;
        } else {
            // Check the function for a returned variable
            $columns = $columns;
            $GLOBALS['lambda_content_column'] = $columns;
        }

        #----------------------------------------------------------------
        # Markup
        #----------------------------------------------------------------
        #start content wrap and content
        echo '<div id="content-wrap" class="fluid clearfix" data-content="content"><!-- /#start content-wrap -->

				<div class="container">';
        //Call Second Sidebar
        if ($columns == 'eight' && $sidebar_align == 'both') {
            get_sidebar('second');
        }
        echo '<div id="content" class="' . $columns . ' columns">';
    }
}

#-----------------------------------------------------------------
# After Content
#-----------------------------------------------------------------
if (!function_exists('lambda_after_content')) {
    function lambda_after_content()
    {
        #close content wrap
        echo '</div><!-- /#content-wrap -->';
    }
}

#-----------------------------------------------------------------
# Before Sidebar - do_action('st_before_sidebar')
#-----------------------------------------------------------------
if (!function_exists('before_sidebar')) {
    function before_sidebar($columns)
    {
        global $lambda_meta_data;
        if (is_home()) {
            //get the meta data from the blog page
            $homeid = get_option('page_for_posts');
            $sidebar_align = get_post_meta($homeid, $lambda_meta_data->get_the_id(), TRUE);
        } else {
            $sidebar_align = $lambda_meta_data->the_meta();
        }
        $sidebar_align = (isset($sidebar_align['sidebar_align'])) ? $sidebar_align['sidebar_align'] : get_option_tree('sidebar_alignement');
        if (empty($columns) && $sidebar_align != 'both') {
            //one sidebar
            $columns = 'five columns';
        } elseif (empty($columns) && $sidebar_align == 'both') {
            //two sidebars
            $columns = 'four columns';
        } else {
            // Check the function for a returned variable
            $columns = $columns;
        }
        $sID = ($columns == 'widget-sidebar') ? '_' . rand(1, 100) : '';
        echo '<aside id="sidebar' . $sID . '" class="' . $columns . '" role="complementary">';
    }
}

add_action('st_before_sidebar', 'before_sidebar');

#-----------------------------------------------------------------
# After Sidebar
#-----------------------------------------------------------------
if (!function_exists('after_sidebar')) {
    function after_sidebar()
    {
        // Additional Content could be added here
        echo '</aside><!-- #sidebar -->';
    }
}

add_action('st_after_sidebar', 'after_sidebar');

#-----------------------------------------------------------------
# Before Second Sidebar - do_action('st_before_sidebar_second')
#-----------------------------------------------------------------
if (!function_exists('before_sidebar_second')) {
    function before_sidebar_second($columns)
    {
        if (empty($columns)) {
            // Set the default
            $columns = 'four';
        } else {
            // Check the function for a returned variable
            $columns = $columns;
        }
        echo '<aside id="sidebar_second" class="' . $columns . ' columns" role="complementary">';
    }
}

add_action('st_before_sidebar_second', 'before_sidebar_second');

#-----------------------------------------------------------------
# After Second Sidebar
#-----------------------------------------------------------------
if (!function_exists('after_sidebar_second')) {
    function after_sidebar_second()
    {
        // Additional Content could be added here
        echo '</aside><!-- #sidebar -->';
    }
}

add_action('st_after_sidebar_second', 'after_sidebar_second');

#-----------------------------------------------------------------
# Comment Styles
#-----------------------------------------------------------------
if (!function_exists('st_comments')) :
    function st_comments($comment, $args, $depth)
    {
        $GLOBALS['comment'] = $comment;
        $admincomment = (1 == $comment->user_id) ? 'admin-comment' : '';
        ?>

    <li <?php comment_class(); ?> id="li-comment-<?php comment_ID() ?>">

        <article id="comment-<?php comment_ID(); ?>" class="single-comment clearfix">
            <figure class="comment-avatar <?php echo $admincomment; ?>">
                <?php echo get_avatar($comment, 40); ?>
            </figure>
            <div class="comment-content">
                <div class="comment-meta clearfix">
                    <span class="comment-author"><?php echo get_comment_author_link(); ?></span>
                    <span class="comment-time"><?php echo get_comment_date() . '  -  ' . get_comment_time(); ?></span>
                    <span class="comment-repy"><?php comment_reply_link(array_merge($args, array('reply_text' => __('Reply', UT_THEME_NAME), 'depth' => $depth, 'max_depth' => $args['max_depth']))); ?></span>
                    <span class="comment-edit"><?php edit_comment_link(__('Edit Comment', UT_THEME_NAME), '  ', ''); ?></span>
                </div>
                <div class="comment-text">
                    <?php comment_text(); ?>

                    <?php if ($comment->comment_approved == '0') : ?>
                        <em><?php _e('Comment is awaiting moderation', UT_THEME_NAME); ?></em>
                    <?php endif; ?>
                </div>
            </div>
        </article>
    <!-- </li> -->
    <?php }
endif;
?>