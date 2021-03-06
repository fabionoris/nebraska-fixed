<?php if (!defined('OT_VERSION')) exit('No direct script access allowed');

/**
 * Displays or returns a value from the 'option_tree' array.
 *
 * @param string $item_id
 * @param array $options
 * @param bool $echo
 * @param bool $is_array
 * @param int $offset
 *
 * @return mixed array or comma seperated lists of values
 * @uses get_option()
 *
 * @access public
 * @since 1.0.0
 */
function get_option_tree($item_id = '', $options = '', $echo = false, $is_array = false, $offset = -1)
{
    // Load saved options
    if (!$options)
        $options = get_option('option_tree');

    // No value return
    if (!isset($options[$item_id]) || empty($options[$item_id]))
        return;

    // Set content value & strip slashes
    $content = option_tree_stripslashes($options[$item_id]);

    // Is an array
    if ($is_array == true) {
        // Saved as a comma seperated lists of values, explode into an array
        if (!is_array($content))
            $content = explode(',', $content);

        // Get an array value using an offset
        if (is_numeric($offset) && $offset >= 0)
            $content = $content[$offset];

        // Not an array
    }

    // Not an array
    else if ($is_array == false) {
        // Saved as array, implode and return a comma seperated lists of values
        if (is_array($content))
            $content = implode(',', $content);
    }

    // Echo content
    if ($echo)
        echo $content;

    return $content;
}

/**
 * Custom stripslashes from single value or array.
 *
 * @param mixed $input
 *
 * @return mixed
 * @uses stripslashes()
 *
 * @access public
 * @since 1.1.3
 */
function option_tree_stripslashes($input)
{
    if (is_array($input)) {
        foreach ($input as &$val) {
            if (is_array($val)) {
                $val = option_tree_stripslashes($val);
            } else {
                $val = stripslashes($val);
            }
        }
    } else {
        $input = stripslashes($input);
    }
    return $input;
}