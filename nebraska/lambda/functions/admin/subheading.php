<?php if (!defined('OT_VERSION')) exit('No direct script access allowed');

/**
 * Heading Option
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
function option_tree_subheading($value, $settings, $int, $active)
{
    $active_item = '';

    if ($active == '0')
        $active_item = 'in active';

    echo ($int > 1) ? '</div>' : false;
    echo '<div id="option_' . $value->item_id . '" class="tab-pane fade ' . $active_item . '">';
    echo '<div class="lambda-options-tab-title"><h2>' . htmlspecialchars_decode($value->item_title) . '</h2></div>';
    echo '<input type="hidden" name="' . $value->item_id . '" value="' . htmlspecialchars_decode($value->item_title) . '" />';
}