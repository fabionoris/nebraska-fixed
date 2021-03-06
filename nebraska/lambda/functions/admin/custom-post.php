<?php if (!defined('OT_VERSION')) exit('No direct script access allowed');

/**
 * Custom Post Option
 *
 * @access public
 * @param array $value
 * @param array $settings
 * @param int $int
 *
 * @return string
 * @since 1.0.0
 *
 */
function option_tree_custom_post($value, $settings, $int)
{
    ?>
    <div class="option option-select">
        <div class="lambda-opttitle">
            <div class="lambda-opttitle-pad">
                <?php echo htmlspecialchars_decode($value->item_title); ?>
                <span class="infoButton right">
				<img class="infoImage" src="<?php echo OT_PLUGIN_URL; ?>/assets/images/info.png" width="40px"
                     height="20px" alt="Info" style="left: 0px;">
		</span>
            </div>
        </div>
        <div class="section">
            <div class="element">
                <div class="select_wrapper">
                    <select name="<?php echo $value->item_id; ?>" id="<?php echo $value->item_id; ?>" class="select">
                        <?php
                        $posts = &get_posts(array('post_type' => trim($value->item_options), 'numberposts' => -1, 'orderby' => 'title', 'order' => 'ASC'));
                        if ($posts) {
                            echo '<option value="">-- Choose One --</option>';
                            foreach ($posts as $post) {
                                $selected = '';
                                if (isset($settings[$value->item_id]) && $settings[$value->item_id] == $post->ID) {
                                    $selected = ' selected="selected"';
                                }
                                echo '<option value="' . $post->ID . '"' . $selected . '>' . $post->post_title . '</option>';
                            }
                        } else {
                            echo '<option value="0">No Custom Posts Available</option>';
                        }
                        ?>
                    </select>
                </div>
            </div> <?php if ($value->item_desc) { ?>
                <div class="desc alert alert-neutral"><?php echo htmlspecialchars_decode($value->item_desc); ?></div>
                <div class="clear"></div>
            <?php } ?>
        </div>
    </div>
    <?php
}

/**
 * Custom Posts Option
 *
 * @access public
 * @param array $value
 * @param array $settings
 * @param int $int
 *
 * @return string
 * @since 1.0.0
 *
 */
function option_tree_custom_posts($value, $settings, $int)
{
    ?>
    <div class="option option-checbox">
        <div class="lambda-opttitle">
            <div class="lambda-opttitle-pad">
                <?php echo htmlspecialchars_decode($value->item_title); ?>
            </div>
        </div>
        <div class="section">
            <div class="element">
                <?php
                // Check for settings item value
                if (isset($settings[$value->item_id])) {
                    $ch_values = (array)$settings[$value->item_id];
                } else {
                    $ch_values = array();
                }
                // Loop through tags
                $posts = &get_posts(array('post_type' => $value->item_options, 'numberposts' => -1, 'orderby' => 'title', 'order' => 'ASC'));
                if ($posts) {
                    $count = 0;
                    foreach ($posts as $post) {
                        $checked = '';
                        if (in_array($post->ID, $ch_values)) {
                            $checked = ' checked="checked"';
                        }
                        echo '<div class="input_wrap"><input name="' . $value->item_id . '[' . $count . ']" id="' . $value->item_id . '_' . $count . '" type="checkbox" value="' . $post->ID . '"' . $checked . ' /><label for="' . $value->item_id . '_' . $count . '">' . $post->post_title . '</label></div>';
                        $count++;
                    }
                } else {
                    echo '<p>No Custom Posts Available</p>';
                }
                ?>
            </div> <?php if ($value->item_desc) { ?>
                <div class="desc alert alert-neutral">            <?php echo htmlspecialchars_decode($value->item_desc); ?>
                </div>
            <?php } ?>
        </div>
    </div>
    <?php
}