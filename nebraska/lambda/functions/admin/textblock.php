<?php if (!defined('OT_VERSION')) exit('No direct script access allowed');

/**
 * Text Block Option
 *
 * @access public
 * @param array $value
 * @param array $settings
 * @param int $int
 *
 * @return string
 * @since 1.0.0
 */
function option_tree_textblock($value, $settings, $int)
{
    ?>
    <div class="option option-textblock">
        <!-- <h3 class="text-title"><?php echo htmlspecialchars_decode($value->item_title); ?></h3> -->
        <div class="section">
            <div class="text_block">
                <?php echo htmlspecialchars_decode($value->item_desc); ?>
            </div>
        </div>
    </div>
    <?php
}