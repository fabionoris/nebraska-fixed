</div>
<div class="clear"></div>
</div><!-- /.columns (#content) -->
<?php

/**
 * The Footer
 *
 * lambda framework v 2.1
 * by www.unitedthemes.com
 * since lambda framework v 2.0
 */

global $lambda_meta_data, $theme_options;
$metadata = $lambda_meta_data->the_meta();

$footerwidgets = is_active_sidebar('first-footer-widget-area') + is_active_sidebar('second-footer-widget-area') + is_active_sidebar('third-footer-widget-area') + is_active_sidebar('fourth-footer-widget-area');
$class = ($footerwidgets == '0' ? 'noborder' : 'normal'); ?>

<div id="footer-wrap" class="fluid clearfix">
    <div class="container">
        <footer id="footer" class="<?php echo $class; ?> sixteen columns">

            <?php //loads sidebar-footer.php
            get_sidebar('footer');
            ?>

        </footer><!--/#footer-->

    </div><!--/.container-->
</div><!--/#footer-wrap-->

<div id="sub-footer-wrap" class="clearfix">
    <div class="container">
        <div class="sixteen columns">
            <div class="scissors"></div>
            <div class="copyright eight columns alpha">

                <?php if (!get_option_tree('site_copyright')) { ?>

                    &copy; <?php echo date('Y'); ?> <a
                            href="<?php echo home_url(); ?>"><?php echo get_bloginfo('name'); ?></a>

                <?php } else { ?>

                    <?php echo get_option_tree('site_copyright'); ?>

                <?php } ?>

            </div>

            <?php

            $copyright = (get_option('lambdacopyright')) ? get_option('lambdacopyright') : '';
            $copyrightlink = (get_option('lambdacopyrightlink')) ? get_option('lambdacopyrightlink') : '';

            ?>

            <div class="unitedthemes eight columns omega">

                <?php if (!empty($copyright)) : ?>

                    Powered by <a href="<?php echo $copyrightlink; ?>"><?php echo $copyright; ?></a>

                <?php endif; ?>

            </div>

        </div>
    </div>
</div><!--/#sub-footer-wrap-->

</div><!--/#wrap -->

<?php
#-----------------------------------------------------------------
# Special JavaScripts
# Do not edit anything below to keep theme functions
#-----------------------------------------------------------------

// Google Analytics
if (get_option_tree('google_analytics')) {
    echo stripslashes(get_option_tree('google_analytics'));
} ?>

<?php wp_footer(); ?>

</body>
</html>