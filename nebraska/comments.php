<?php

/**
 * @package WordPress
 * @subpackage Default_Theme
 */

// Do not delete these lines
if (!empty($_SERVER['SCRIPT_FILENAME']) && 'comments.php' == basename($_SERVER['SCRIPT_FILENAME']))
    die ('Please do not load this page directly. Thanks!');

if (post_password_required()) { ?>
    <p class="nocomments"><?php _e('This post is password protected. Enter the password to view comments.', UT_THEME_NAME); ?></p>
    <?php
    return;
}
?>

<!-- You can start editing here -->
<div id="comments">
    <?php if (have_comments()) : ?>

        <h3 class="comments-title"><span>
		<?php printf(_n('Comment (1)', 'Comments (%1$s)', get_comments_number(), UT_THEME_NAME),
            number_format_i18n(get_comments_number()), get_the_title()); ?>
	</span></h3>

        <ul class="commentlist">
            <?php wp_list_comments("callback=st_comments"); ?>
        </ul>

    <!-- Comments navigation disabled
	<div class="navigation">
		<div class="alignleft"><?php previous_comments_link() ?></div>
		<div class="alignright"><?php next_comments_link() ?></div>
	</div>
	-->

    <?php else : // This is displayed if there are no comments so far ?>

    <?php endif; ?>

</div>
<?php if (comments_open()) : ?>

    <?php comment_form(array('title_reply' => __('<span>Leave a Reply</span>'))); ?>

<?php endif; // If you delete this the sky will fall on your head ?>