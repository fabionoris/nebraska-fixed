<?php if (!defined('OT_VERSION')) exit('No direct script access allowed');

/**
 * OptionTree Admin
 *
 * @package     WordPress
 * @subpackage  OptionTree
 * @since       1.0.0
 * @author      Derek Herman
 */
class OT_Admin
{
    private $table_name;
    private $version;
    private $option_array;
    private $ot_file;
    private $ot_data;
    private $ot_layout;
    private $theme_options_xml;
    private $theme_options_data;
    private $theme_options_layout;
    private $has_xml;
    private $has_data;
    private $has_layout;
    private $show_docs;

    /**
     * PHP4 contructor
     *
     * @since 1.1.6
     */
    function OT_Admin()
    {
        $this->__construct();
    }

    /**
     * PHP5 contructor
     *
     * @since 1.0.0
     */
    function __construct()
    {
        global $table_prefix;

        $this->version = OT_VERSION;
        $this->table_name = $table_prefix . UT_THEME_INITIAL . 'option_tree';
        define('OT_TABLE_NAME', $this->table_name);
        $this->option_array = $this->option_tree_data();

        // File path & name without extension
        $this->ot_file = '/lambda/assets/optionsdata/theme-options.xml';
        $this->ot_data = '/lambda/assets/optionsdata/theme-options.txt';
        $this->ot_layout = '/lambda/assets/optionsdata/layouts.txt';

        // XML file path
        $this->theme_options_xml = get_stylesheet_directory() . $this->ot_file;
        if (!is_readable($this->theme_options_xml)) // no file try parent theme
            $this->theme_options_xml = get_template_directory() . $this->ot_file;

        // Data file path
        $this->theme_options_data = get_stylesheet_directory() . $this->ot_data;
        if (!is_readable($this->theme_options_data)) // no file try parent theme
            $this->theme_options_data = get_template_directory() . $this->ot_data;

        // Layout file path
        $this->theme_options_layout = get_stylesheet_directory() . $this->ot_layout;
        if (!is_readable($this->theme_options_layout)) // no file try parent theme
            $this->theme_options_layout = get_template_directory() . $this->ot_layout;

        // Check for files
        $this->has_xml = (is_readable($this->theme_options_xml)) ? true : false;
        $this->has_data = (is_readable($this->theme_options_data)) ? true : false;
        $this->has_layout = (is_readable($this->theme_options_layout)) ? true : false;

        //delete_option('option_tree');
    }

    /**
     * Initiate Plugin & setup main options
     *
     * @return bool
     * @uses add_option()
     * @uses option_tree_activate()
     * @uses wp_redirect()
     * @uses admin_url()
     *
     * @access public
     * @since 1.0.0
     *
     * @uses get_option()
     */
    function option_tree_init()
    {
        // Check for activation
        $check = get_option('option_tree_activation');

        if ($check != "set") {
            add_option('option_tree_activation', 'set');

            // Load DB activation function if updating plugin
            $this->option_tree_activate();

            if ($this->has_xml == true && $this->show_docs == false) {
                // Redirect
                wp_redirect(admin_url() . 'themes.php?page=option_tree');
            } else {
                // Redirect
                wp_redirect(admin_url() . 'admin.php?page=option_tree_settings#import_options');
            }
        }
        return false;
    }

    /**
     * Plugin Table Structure
     *
     * @access public
     * @param string $type
     *
     * @return string
     * @since 1.0.0
     */
    function option_tree_table($type = '')
    {
        if ($type == 'create') {
            $sql = "CREATE TABLE {$this->table_name} (
        id mediumint(9) NOT NULL AUTO_INCREMENT PRIMARY KEY,
        item_id VARCHAR(50) NOT NULL,
        item_title VARCHAR(100) NOT NULL,
        item_desc LONGTEXT,
        item_type VARCHAR(30) NOT NULL,
        item_options VARCHAR(250) DEFAULT NULL,
        item_sort mediumint(9) DEFAULT '0' NOT NULL,
        UNIQUE KEY (item_id)
      ) ENGINE=MyISAM DEFAULT CHARSET=utf8 COLLATE=utf8_general_ci;";
        }
        return $sql;
    }

    /**
     * Plugin Activation
     *
     * @return void
     * @uses get_option()
     * @uses dbDelta()
     * @uses option_tree_table()
     * @uses option_tree_default_data()
     * @uses update_option()
     * @uses add_option()
     *
     * @access public
     * @since 1.0.0
     *
     * @uses get_var()
     */
    function option_tree_activate()
    {
        global $wpdb;

        // Check for table
        $new_installation = $wpdb->get_var("show tables like '$this->table_name'") != $this->table_name;

        // Check for installed version
        $installed_ver = get_option('option_tree_version');

        // Add/update table
        if ($installed_ver != $this->version) {
            // run query
            require_once(ABSPATH . 'wp-admin/includes/upgrade.php');
            dbDelta($this->option_tree_table('create'));

            // Has xml file load defaults
            if ($this->has_xml == true)
                $this->option_tree_load_theme_files();
        }

        // New install
        if ($new_installation)
            $this->option_tree_default_data();

        // New Version Update
        if ($installed_ver != $this->version) {
            update_option('option_tree_version', $this->version);
        } else if (!$installed_ver) {
            add_option('option_tree_version', $this->version);
        }
    }

    /**
     * Plugin Deactivation delete options
     *
     * @return void
     * @since 1.0.0
     *
     * @uses delete_option()
     *
     * @access public
     */
    function option_tree_deactivate()
    {
        // Remove activation check & version
        delete_option('option_tree_activation');
        delete_option('option_tree_version');
    }

    /**
     * Load Default Data from theme included files
     *
     * @access public
     * @return void
     * @since 1.1.7
     */
    function option_tree_load_theme_files()
    {
        global $wpdb;

        $rawdata = file_get_contents($this->theme_options_xml);

        if ($rawdata) {
            $new_options = new SimpleXMLElement($rawdata);

            // Drop table
            if ($wpdb->get_var("show tables like '$this->table_name'") == $this->table_name) {
                $wpdb->query("DROP TABLE $this->table_name");
            }

            // Create table
            $wpdb->query($this->option_tree_table('create'));

            foreach ($new_options->row as $value) {
                $wpdb->insert($this->table_name,
                    array(
                        'item_id' => $value->item_id,
                        'item_title' => $value->item_title,
                        'item_desc' => $value->item_desc,
                        'item_type' => $value->item_type,
                        'item_options' => $value->item_options
                    )
                );
            }
        }

        // Check for Data file and data not saved
        if ($this->has_data == true && !get_option('option_tree')) {
            $rawdata = file_get_contents($this->theme_options_data);
            $new_options = unserialize(base64_decode($rawdata));

            // Check if array()
            if (is_array($new_options)) {
                // create new options
                add_option('option_tree', $new_options);
            }
        }

        // Check for Layout file and layouts not saved
        if ($this->has_layout == true && !get_option('option_tree_layouts')) {
            $rawdata = file_get_contents($this->theme_options_layout);
            $new_layouts = unserialize(base64_decode($rawdata));

            // Check if array()
            if (is_array($new_layouts)) {
                // create new layouts
                add_option('option_tree_layouts', $new_layouts);
            }
        }
    }

    /**
     * Plugin Activation Default Data
     *
     * @return void
     * @uses prepare()
     *
     * @access public
     * @since 1.0.0
     *
     * @uses query()
     */
    function option_tree_default_data()
    {
        // Load from files if they exist
        if ($this->has_xml == true) {
            $this->option_tree_load_theme_files();
        } else {
            global $wpdb;

            // Only run these queries if no xml file exist
            $wpdb->query($wpdb->prepare("
        INSERT INTO {$this->table_name}
        ( item_id, item_title, item_type )
        VALUES ( %s, %s, %s ) ",
                array('general_default', 'General', 'heading')));

            $wpdb->query($wpdb->prepare("
        INSERT INTO {$this->table_name}
        ( item_id, item_title, item_type )
        VALUES ( %s, %s, %s ) ",
                array('test_input', 'Test Input', 'input')));
        }
    }

    /**
     * Restore Table Data if empty
     *
     * @return void
     * @uses option_tree_activate()
     * @uses wp_redirect()
     * @uses admin_url()
     *
     * @access public
     * @since 1.0.0
     *
     * @uses delete_option()
     */
    function option_tree_restore_default_data()
    {
        global $wpdb;

        // Drop table
        if ($wpdb->get_var("show tables like '$this->table_name'") == $this->table_name) {
            $wpdb->query("DROP TABLE $this->table_name");
        }
        // Remove activation check
        delete_option('option_tree_version');

        // Load DB activation function
        $this->option_tree_activate();

        // Redirect
        if ($this->has_xml == true && $this->show_docs == false) {
            wp_redirect(admin_url() . 'themes.php?page=option_tree');
        } else {
            wp_redirect(admin_url() . 'admin.php?page=option_tree_settings');
        }
    }

    /**
     * Add Admin Menu Items & Test Actions
     *
     * @param int $param
     *
     * @return void
     * @uses option_tree_export_xml()
     * @uses option_tree_data()
     * @uses get_results()
     * @uses option_tree_restore_default_data()
     * @uses option_tree_activate()
     * @uses get_option()
     * @uses option_tree_import_xml()
     * @uses get_user_option()
     * @uses add_object_page()
     * @uses add_submenu_page()
     * @uses add_action()
     *
     * @access public
     * @since 1.0.0
     */
    function option_tree_admin()
    {
        global $wpdb;

        // Export XML - run before anything else
        if (isset($_GET['action']) && $_GET['action'] == 'ot-export-xml')
            option_tree_export_xml($this->option_tree_data(), $this->table_name);

        // Grab saved table option

        if ($wpdb->get_var("show tables like '$this->table_name'") == $this->table_name) {
            $test_options = $wpdb->get_results("SELECT * FROM {$this->table_name}");
        }

        // Restore table if needed
        if (empty($test_options))
            $this->option_tree_restore_default_data();

        // Upgrade DB automatically
        $this->option_tree_activate();

        // Load options array
        $settings = get_option('option_tree');

        // Upload xml data
        $this->option_tree_import_xml();

        // If XML file came with the theme don't build the whole UI
        if ($this->has_xml == true && $this->show_docs == false) {

            // Set admin color for icon
            $icon = (get_user_option('admin_color') == 'classic') ? OT_PLUGIN_URL . '/assets/images/icons/bricks.png' : OT_PLUGIN_URL . '/assets/images/icons/bricks.png';

            // Create menu items
            add_object_page('Theme Options', 'Theme Options', 'edit_theme_options', 'option_tree', array($this, 'option_tree_options_page'), $icon);
            $option_tree_options = add_submenu_page('option_tree', 'Theme Options', 'Theme Options', 'edit_theme_options', 'option_tree', array($this, 'option_tree_options_page'));

            // Add menu item
            add_action("admin_print_styles-$option_tree_options", array($this, 'option_tree_load'));

        } else {
            // Set admin color for icon
            $icon = (get_user_option('admin_color') == 'classic') ? OT_PLUGIN_URL . '/assets/images/icons/bricks.png' : OT_PLUGIN_URL . '/assets/images/icons/bricks.png';

            // Create menu items
            add_object_page('Theme Options', 'Theme Options', 'edit_theme_options', 'option_tree', array($this, 'option_tree_options_page'), $icon);
            $option_tree_options = add_submenu_page('option_tree', 'Theme Options', 'Theme Options', 'edit_theme_options', 'option_tree', array($this, 'option_tree_options_page'));
            $option_tree_settings = add_submenu_page('option_tree', 'Theme Options', 'Settings', 'edit_theme_options', 'option_tree_settings', array($this, 'option_tree_settings_page'));

            // Add menu items
            add_action("admin_print_styles-$option_tree_options", array($this, 'option_tree_load'));
            add_action("admin_print_styles-$option_tree_settings", array($this, 'option_tree_load'));
        }
    }

    /**
     * Load Scripts & Styles
     *
     * @return void
     * @uses get_user_option()
     * @uses add_thickbox()
     * @uses wp_enqueue_script()
     * @uses wp_deregister_style()
     *
     * @access public
     * @since 1.0.0
     *
     * @uses wp_enqueue_style()
     */
    function option_tree_load()
    {
        // Enqueue styles
        wp_enqueue_style('option-tree-style', OT_PLUGIN_URL . '/assets/css/lambda.ui.css', false, $this->version, 'screen');

        // Enqueue scripts
        add_thickbox();
        wp_enqueue_script('jquery-table-dnd', OT_PLUGIN_URL . '/assets/js/jquery.table.dnd.js', array('jquery'), $this->version, true);
        wp_enqueue_script('jquery-color-picker', OT_PLUGIN_URL . '/assets/js/jquery.color.picker.js', array('jquery'), $this->version, true);
        wp_enqueue_script('bootstrap', OT_PLUGIN_URL . '/assets/js/bootstrap.js', array('jquery'), '2.0.3', true);
        wp_enqueue_script('jquery-option-tree', OT_PLUGIN_URL . '/assets/js/jquery.option.tree.js', array('jquery', 'media-upload', 'thickbox', 'jquery-ui-core', 'jquery-ui-tabs', 'jquery-table-dnd', 'jquery-color-picker', 'jquery-ui-sortable'), $this->version, true);

        // Remove GD star rating conflicts
        wp_deregister_style('gdsr-jquery-ui-core');
        wp_deregister_style('gdsr-jquery-ui-theme');
        wp_deregister_style('colors-css');

        // Remove Cispm Mail Contact jQuery UI
        wp_deregister_script('jquery-ui-1.7.2.custom.min');
    }

    /**
     * Grab the wp_option_tree table options array
     *
     * @return array
     * @since 1.0.0
     *
     * @uses get_results()
     *
     * @access public
     */
    function option_tree_data()
    {
        global $wpdb;

        // Create an array of options
        if ($wpdb->get_var("show tables like '$this->table_name'") == $this->table_name) {
            $db_options = $wpdb->get_results("SELECT * FROM {$this->table_name} ORDER BY item_sort ASC");
            return $db_options;
        }
    }

    /**
     * Theme Options Page
     *
     * @return string
     * @uses get_option_page_ID()
     * @uses option_tree_check_post_lock()
     * @uses option_tree_check_post_lock()
     * @uses option_tree_notice_post_locked()
     *
     * @access public
     * @since 1.0.0
     *
     * @uses get_option()
     */
    function option_tree_options_page()
    {
        // Hook before page loads
        do_action('option_tree_admin_header');

        // Set
        $ot_array = $this->option_array;

        // Load saved option_tree
        $settings = get_option('option_tree');

        // Load Saved Layouts
        $layouts = get_option('option_tree_layouts');

        // Private page ID
        $post_id = $this->get_option_page_ID('media');

        // Set post lock
        if ($last = $this->option_tree_check_post_lock($post_id)) {
            $message = $this->option_tree_notice_post_locked($post_id);
        } else {
            $this->option_tree_set_post_lock($post_id);
        }

        // Grab Options Page
        include(OT_PLUGIN_DIR . '/front-end/options.php');
    }

    /**
     * Settings Page
     *
     * @return string
     * @uses get_option_page_ID()
     * @uses option_tree_check_post_lock()
     * @uses option_tree_check_post_lock()
     * @uses option_tree_notice_post_locked()
     *
     * @access public
     * @since 1.0.0
     *
     * @uses get_option()
     */
    function option_tree_settings_page()
    {
        // Hook before page loads
        do_action('option_tree_admin_header');

        // Set
        $ot_array = $this->option_array;

        // Load Saved Options
        $settings = get_option('option_tree');

        // Load Saved Layouts
        $layouts = get_option('option_tree_layouts');

        // Private page ID
        $post_id = $this->get_option_page_ID('options');

        // Set post lock
        if ($last = $this->option_tree_check_post_lock($post_id)) {
            $message = $this->option_tree_notice_post_locked($post_id);
        } else {
            $this->option_tree_set_post_lock($post_id);
        }

        // Get Settings Page
        include(OT_PLUGIN_DIR . '/front-end/settings.php');
    }

    /**
     * Save Theme Option via AJAX
     *
     * @return void
     * @uses update_option()
     * @uses option_tree_set_post_lock()
     * @uses get_option_page_ID()
     *
     * @access public
     * @since 1.0.0
     *
     * @uses check_ajax_referer()
     */
    function option_tree_array_save()
    {
        // Check AJAX Referer
        check_ajax_referer('_theme_options', '_ajax_nonce');

        // Set option values
        foreach ($this->option_array as $value) {
            $key = trim($value->item_id);
            if (isset($_REQUEST[$key])) {
                $val = $_REQUEST[$key];
                $new_settings[$key] = $val;
            }
        }

        // Update Theme Options
        update_option('option_tree', $new_settings);

        // Update active layout content
        $options_layouts = get_option('option_tree_layouts');
        if (isset($options_layouts['active_layout'])) {
            $options_layouts[$options_layouts['active_layout']] = base64_encode(serialize($new_settings));
            update_option('option_tree_layouts', $options_layouts);
        }

        // Lock post editing
        $this->option_tree_set_post_lock($this->get_option_page_ID('media'));

        // hook before AJAX is returned
        do_action('option_tree_array_save');

        die();
    }

    /**
     * Update XML Theme Option via AJAX
     *
     * @return void
     * @uses update_option()
     * @uses option_tree_set_post_lock()
     * @uses get_option_page_ID()
     *
     * @access public
     * @since 1.0.0
     *
     * @uses check_ajax_referer()
     */
    function option_tree_array_reload()
    {
        // Check AJAX Referer
        check_ajax_referer('_theme_options', '_ajax_nonce');

        global $wpdb;

        $rawdata = file_get_contents($this->theme_options_xml);

        if ($rawdata) {
            $new_options = new SimpleXMLElement($rawdata);

            // Drop table
            if ($wpdb->get_var("show tables like '$this->table_name'") == $this->table_name) {
                $wpdb->query("DROP TABLE $this->table_name");
            }

            // Create table
            $wpdb->query($this->option_tree_table('create'));

            foreach ($new_options->row as $value) {
                $wpdb->insert($this->table_name,
                    array(
                        'item_id' => $value->item_id,
                        'item_title' => $value->item_title,
                        'item_desc' => $value->item_desc,
                        'item_type' => $value->item_type,
                        'item_options' => $value->item_options
                    )
                );
            }

            die('themes.php?page=option_tree&updated=true&cache=buster_' . mt_rand(5, 100));

        } else {
            die('-1');
        }
    }

    /**
     * Reset Theme Option via AJAX
     *
     * @return void
     * @uses update_option()
     *
     * @access public
     * @since 1.0.0
     *
     * @uses check_ajax_referer()
     */
    function option_tree_array_reset()
    {
        // Check AJAX Referer
        check_ajax_referer('_theme_options', '_ajax_nonce');

        // Clear option values
        foreach ($this->option_array as $value) {
            $key = $value->item_id;
            $new_options[$key] = '';
        }

        // Update theme Options
        update_option('option_tree', $new_options);

        // Update active layout content
        $options_layouts = get_option('option_tree_layouts');
        if (isset($options_layouts['active_layout'])) {
            $options_layouts[$options_layouts['active_layout']] = base64_encode(serialize($new_options));
            update_option('option_tree_layouts', $options_layouts);
        }

        die();
    }

    /**
     * Insert Row into Option Setting Table via AJAX
     *
     * @return void
     * @uses get_results()
     * @uses insert()
     * @uses get_var()
     *
     * @access public
     * @since 1.0.0
     *
     * @uses check_ajax_referer()
     */
    function option_tree_add()
    {
        global $wpdb;

        // Check AJAX referer
        check_ajax_referer('inlineeditnonce', '_ajax_nonce');

        // Grab fresh options array
        $ot_array = $wpdb->get_results("SELECT * FROM {$this->table_name}");

        // Get form data
        $id = $_POST['id'];
        $item_id = htmlspecialchars(stripslashes(trim($_POST['item_id'])), ENT_QUOTES, 'UTF-8', true);
        $item_title = htmlspecialchars(stripslashes(trim($_POST['item_title'])), ENT_QUOTES, 'UTF-8', true);
        $item_desc = htmlspecialchars(stripslashes(trim($_POST['item_desc'])), ENT_QUOTES, 'UTF-8', true);
        $item_type = htmlspecialchars(stripslashes(trim($_POST['item_type'])), ENT_QUOTES, 'UTF-8', true);
        $item_options = htmlspecialchars(stripslashes(trim($_POST['item_options'])), ENT_QUOTES, 'UTF-8', true);

        // Validate item key
        foreach ($ot_array as $value) {
            if ($item_id == $value->item_id) {
                die("That option key is already in use.");
            }
        }

        // Verify key is alphanumeric
        if (preg_match('/[^a-z0-9_]/', $item_id))
            die("You must enter a valid option key.");

        // Verify title
        if (strlen($item_title) < 1)
            die("You must give your option a title.");

        if ($item_type == 'textarea' && !is_numeric($item_options))
            die("The row value must be numeric.");

        // Update row
        $wpdb->insert($this->table_name,
            array(
                'item_id' => $item_id,
                'item_title' => $item_title,
                'item_desc' => $item_desc,
                'item_type' => $item_type,
                'item_options' => $item_options,
                'item_sort' => $id
            )
        );

        // Verify values in the DB are updated
        $updated = $wpdb->get_var("
      SELECT id 
      FROM {$this->table_name}
      WHERE item_id = '$item_id'
      AND item_title = '$item_title'
      AND item_type = '$item_type'
      AND item_options = '$item_options'
    ");

        // If updated
        if ($updated) {
            die('updated');
        } else {
            die("There was an error, please try again.");
        }
    }

    /**
     * Update Option Setting Table via AJAX
     *
     * @return void
     * @uses get_results()
     * @uses update()
     * @uses get_var()
     *
     * @access public
     * @since 1.0.0
     *
     * @uses check_ajax_referer()
     */
    function option_tree_edit()
    {
        global $wpdb;

        // Check AJAX Referer
        check_ajax_referer('inlineeditnonce', '_ajax_nonce');

        // Grab fresh options array
        $ot_array = $wpdb->get_results("SELECT * FROM {$this->table_name}");

        // Get form data
        $id = $_POST['id'];
        $item_id = htmlspecialchars(stripslashes(trim($_POST['item_id'])), ENT_QUOTES, 'UTF-8', true);
        $item_title = htmlspecialchars(stripslashes(trim($_POST['item_title'])), ENT_QUOTES, 'UTF-8', true);
        $item_desc = htmlspecialchars(stripslashes(trim($_POST['item_desc'])), ENT_QUOTES, 'UTF-8', true);
        $item_type = htmlspecialchars(stripslashes(trim($_POST['item_type'])), ENT_QUOTES, 'UTF-8', true);
        $item_options = htmlspecialchars(stripslashes(trim($_POST['item_options'])), ENT_QUOTES, 'UTF-8', true);

        // Validate item key
        foreach ($ot_array as $value) {
            if ($value->item_sort == $id) {
                if ($item_id == $value->item_id && $value->item_sort != $id) {
                    die("That option key is already in use.");
                }
            } else if ($item_id == $value->item_id && $value->id != $id) {
                die("That option key is already in use.");
            }
        }

        // Verify key is alphanumeric
        if (preg_match('/[^a-z0-9_]/', $item_id))
            die("You must enter a valid option key.");

        // Verify title
        if (strlen($item_title) < 1)
            die("You must give your option a title.");

        if ($item_type == 'textarea' && !is_numeric($item_options))
            die("The row value must be numeric.");

        // Update row
        $wpdb->update($this->table_name,
            array(
                'item_id' => $item_id,
                'item_title' => $item_title,
                'item_desc' => $item_desc,
                'item_type' => $item_type,
                'item_options' => $item_options
            ),
            array(
                'id' => $id
            )
        );

        // Verify values in the DB are updated
        $updated = $wpdb->get_var("
      SELECT id 
      FROM {$this->table_name}
      WHERE item_id = '$item_id'
      AND item_title = '$item_title'
      AND item_type = '$item_type'
      AND item_options = '$item_options'
      ");

        // If updated
        if ($updated) {
            die('updated');
        } else {
            die("There was an error, please try again.");
        }
    }

    /**
     * Remove Option via AJAX
     *
     * @return void
     * @uses query()
     *
     * @access public
     * @since 1.0.0
     *
     * @uses check_ajax_referer()
     */
    function option_tree_delete()
    {
        global $wpdb;

        // Check AJAX referer
        check_ajax_referer('inlineeditnonce', '_ajax_nonce');

        // Grab ID
        $id = $_REQUEST['id'];

        // Delete item
        $wpdb->query("
      DELETE FROM $this->table_name 
      WHERE id = '$id'
    ");

        die('removed');
    }

    /**
     * Get Option ID via AJAX
     *
     * @return void
     * @uses delete_post_meta()
     *
     * @access public
     * @since 1.0.0
     *
     * @uses check_ajax_referer()
     */
    function option_tree_next_id()
    {
        global $wpdb;

        // Check AJAX referer
        check_ajax_referer('inlineeditnonce', '_ajax_nonce');

        // Get ID
        $id = $wpdb->get_var("SELECT id FROM {$this->table_name} ORDER BY id DESC LIMIT 1");

        // Return ID
        die($id);
    }

    /**
     * Update Sort Order via AJAX
     *
     * @return void
     * @uses update()
     *
     * @access public
     * @since 1.0.0
     *
     * @uses check_ajax_referer()
     */
    function option_tree_sort()
    {
        global $wpdb;

        // Check AJAX referer
        check_ajax_referer('inlineeditnonce', '_ajax_nonce');

        // Create an array of IDs
        $fields = explode('&', $_REQUEST['id']);

        // Set order
        $order = 0;

        // Update the sort order
        foreach ($fields as $field) {
            $order++;
            $key = explode('=', $field);
            $id = urldecode($key[1]);
            $wpdb->update($this->table_name,
                array(
                    'item_sort' => $order
                ),
                array(
                    'id' => $id
                )
            );
        }

        die();
    }

    /**
     * Upload XML Option Data
     *
     * @access public
     * @return void
     * @since 1.0.0
     *
     */
    function option_tree_import_xml()
    {
        global $wpdb;

        // Check for multisite and add xml mime type if needed
        if (is_multisite()) {
            $xml_ext = false;

            // Build ext array
            $site_exts = explode(' ', get_site_option('upload_filetypes'));

            // Check for xml ext
            foreach ($site_exts as $ext) {
                if ($ext == 'xml')
                    $xml_ext = true;
            }

            // Add xml to mime types
            if ($xml_ext == false) {
                $new_site_exts = implode(' ', $site_exts);
                update_site_option('upload_filetypes', $new_site_exts . ' xml');
            }
        }

        // Action == upload
        if (isset($_GET['action']) && $_GET['action'] == 'ot-upload-xml') {

            // Fail no file
            if ($_FILES["import"]['name'] == null) {
                header("Location: admin.php?page=option_tree_settings&nofile=true#import_options");
                die();
            }

            // Fail errors
            else if ($_FILES["import"]["error"] > 0) {
                header("Location: admin.php?page=option_tree_settings&error=true#import_options");
                die();
            } else {
                // Success - it's XML
                if (preg_match("/(.xml)$/i", $_FILES["import"]['name'])) {

                    $mimes = apply_filters('upload_mimes', array(
                        'xml' => 'text/xml'
                    ));

                    $overrides = array('test_form' => false, 'mimes' => $mimes);
                    $import = wp_handle_upload($_FILES['import'], $overrides);

                    if (!empty($import['error'])) {
                        header("Location: admin.php?page=option_tree_settings&error=true#import_options");
                        die();
                    }

                    $rawdata = file_get_contents($import['file']);
                    $new_options = new SimpleXMLElement($rawdata);

                    // Drop table
                    if ($wpdb->get_var("show tables like '$this->table_name'") == $this->table_name) {
                        $wpdb->query("DROP TABLE $this->table_name");
                    }

                    // Create table
                    $wpdb->query($this->option_tree_table('create'));

                    // Insert data
                    foreach ($new_options->row as $value) {
                        $wpdb->insert($this->table_name,
                            array(
                                'item_id' => $value->item_id,
                                'item_title' => $value->item_title,
                                'item_desc' => $value->item_desc,
                                'item_type' => $value->item_type,
                                'item_options' => $value->item_options
                            )
                        );
                    }

                    // Success redirect
                    header("Location: admin.php?page=option_tree_settings&xml=true#import_options");
                    die();
                }

                // Fail
                else {
                    // Redirect
                    header("Location: admin.php?page=option_tree_settings&error=true#import_options");
                    die();
                }
            }
        }
    }

    /**
     * Import Option Data via AJAX
     *
     * @return void
     * @uses update()
     *
     * @access public
     * @since 1.0.0
     *
     * @uses check_ajax_referer()
     */
    function option_tree_import_data()
    {
        global $wpdb;

        // Check AJAX referer
        check_ajax_referer('_import_data', '_ajax_nonce');

        // Get Data
        $string = $_REQUEST['import_options_data'];

        // Un-serialize The Array
        $new_options = unserialize(base64_decode($string));

        // Check if array()
        if (is_array($new_options)) {

            // Delete old options
            delete_option('option_tree');

            // Create new options
            add_option('option_tree', $new_options);

            // Update active layout content
            $options_layouts = get_option('option_tree_layouts');
            if (isset($options_layouts['active_layout'])) {
                $options_layouts[$options_layouts['active_layout']] = base64_encode(serialize($new_options));
                update_option('option_tree_layouts', $options_layouts);
            }

            // Hook after import, before AJAX is returned
            do_action('option_tree_import_data');

            // Redirect
            die();
        }

        // Failed
        die('-1');
    }

    /**
     * Update Layouts data via AJAX
     *
     * @return void
     * @uses get_option()
     *
     * @access public
     * @since 1.1.7
     *
     * @uses check_ajax_referer()
     */
    function option_tree_update_export_data()
    {
        global $wpdb;

        // Check AJAX referer
        check_ajax_referer('inlineeditnonce', '_ajax_nonce');

        $saved = $_REQUEST['saved'];
        $updated = base64_encode(serialize(get_option('option_tree')));

        // Check if array()
        if ($saved != $updated) {
            die($updated);
        }

        // Failed
        die('-1');
    }

    /**
     * Save Layout via AJAX
     *
     * @return void
     * @uses get_option()
     * @uses update_option()
     * @uses add_option()
     *
     * @access public
     * @since 1.1.7
     *
     * @uses check_ajax_referer()
     */
    function option_tree_save_layout()
    {
        global $wpdb;

        // Check AJAX referer
        if (isset($_REQUEST['themes']) && $_REQUEST['themes'] == true) {
            // Check AJAX Referer
            check_ajax_referer('_theme_options', '_ajax_nonce');
        } else {
            // Check AJAX referer
            check_ajax_referer('_save_layout', '_ajax_nonce');
        }

        // Get Data
        $string = $_REQUEST['options_name'];

        // Set default layout name
        if (!$string)
            $string = 'default';

        // Replace whitespace and set to lower case
        $string = str_replace(' ', '-', strtolower($string));

        // Get options and encode
        $options = get_option('option_tree');
        $options = base64_encode(serialize($options));

        // Get saved layouts
        $options_layouts = get_option('option_tree_layouts');

        if (is_array($options_layouts)) {
            $options_layouts['active_layout'] = $string;
            $options_layouts[$string] = $options;
            update_option('option_tree_layouts', $options_layouts);
        } else {
            delete_option('option_tree_layouts');
            add_option('option_tree_layouts', array('active_layout' => $string, $string => $options));
        }

        // Hook after save, before AJAX is returned
        do_action('option_tree_save_layout');

        // Redirect
        if (isset($_REQUEST['themes']) && $_REQUEST['themes'] == true) {
            die('admin.php?page=option_tree&layout_saved=true');
        } else {
            die($options);
        }
    }

    /**
     * Delete Layout via AJAX
     *
     * @return void
     * @uses get_option()
     * @uses update_option()
     * @uses add_option()
     *
     * @access public
     * @since 1.1.7
     *
     * @uses check_ajax_referer()
     */
    function option_tree_delete_layout()
    {
        global $wpdb;

        // Check AJAX referer
        check_ajax_referer('inlineeditnonce', '_ajax_nonce');

        // Grab ID
        $id = $_REQUEST['id'];

        $options_layouts = get_option('option_tree_layouts');

        // Remove the item
        unset($options_layouts[$id]);

        // Check active layout and unset if deleted
        if ($options_layouts['active_layout'] == $id) {
            unset($options_layouts['active_layout']);
        }

        update_option('option_tree_layouts', $options_layouts);

        // Hook after delete, before AJAX is returned
        do_action('option_tree_delete_layout');

        die('removed');
    }

    /**
     * Activate Layout via AJAX
     *
     * @return void
     * @uses get_option()
     * @uses update_option()
     * @uses add_option()
     *
     * @access public
     * @since 1.1.7
     *
     * @uses check_ajax_referer()
     */
    function option_tree_activate_layout()
    {
        global $wpdb;

        if (isset($_REQUEST['themes']) && $_REQUEST['themes'] == true) {
            // Check AJAX Referer
            check_ajax_referer('_theme_options', '_ajax_nonce');
        } else {
            // Check AJAX referer
            check_ajax_referer('inlineeditnonce', '_ajax_nonce');
        }

        // Grab ID
        $id = $_REQUEST['id'];

        // Get Saved Options
        $options_layouts = get_option('option_tree_layouts');

        // Un-serialize The Array
        $new_options = unserialize(base64_decode($options_layouts[$id]));

        // Check if array()
        if (is_array($new_options)) {

            // Delete old options
            delete_option('option_tree');

            // Set active layout
            $options_layouts['active_layout'] = $id;
            update_option('option_tree_layouts', $options_layouts);

            // Create new options
            add_option('option_tree', $new_options);

            // Hook after activate, before AJAX is returned
            do_action('option_tree_activate_layout');

            // Redirect
            if ($this->has_xml == true && $this->show_docs == false) {
                die('themes.php?page=option_tree&layout=true');
            } else if (isset($_REQUEST['themes']) && $_REQUEST['themes'] == true) {
                die('admin.php?page=option_tree&layout=true');
            } else {
                die('activated');
            }
        }

        // Failed
        die('-1');
    }

    /**
     * Import Layouts via AJAX
     *
     * @return void
     * @uses delete_option()
     * @uses add_option()
     *
     * @access public
     * @since 1.1.7
     *
     * @uses check_ajax_referer()
     */
    function option_tree_import_layout()
    {
        global $wpdb;

        // Check AJAX referer
        check_ajax_referer('_import_layout', '_ajax_nonce');

        // Get Data
        $string = $_REQUEST['import_option_layouts'];

        // Unserialize The Array
        $new_options = unserialize(base64_decode($string));

        // Check if array()
        if (is_array($new_options)) {

            // Delete old layouts
            delete_option('option_tree_layouts');

            // Create new layouts
            add_option('option_tree_layouts', $new_options);

            // Hook after import, before redirect
            do_action('option_tree_import_layout');

            // Redirect
            die('admin.php?page=option_tree_settings&layout=true&cache=buster_' . mt_rand(5, 100) . '#layout_options');
        }

        // Failed
        die('-1');
    }

    /**
     * Update Layouts data via AJAX
     *
     * @return void
     * @uses get_option()
     *
     * @access public
     * @since 1.1.7
     *
     * @uses check_ajax_referer()
     */
    function option_tree_update_export_layout()
    {
        global $wpdb;

        // Check AJAX referer
        check_ajax_referer('inlineeditnonce', '_ajax_nonce');

        $saved = $_REQUEST['saved'];
        $updated = base64_encode(serialize(get_option('option_tree_layouts')));

        // Check if array()
        if ($saved != $updated) {
            die($updated);
        }

        // Failed
        die('-1');
    }

    function option_tree_add_slider()
    {
        $count = $_GET['count'] + 1;
        $id = $_GET['slide_id'];
        $image = array(
            'order' => $count,
            'title' => '',
            'image' => '',
            'link' => '',
            'description' => ''
        );
        option_tree_slider_view($id, $image, $this->get_option_page_ID('media'), $count);
        die();
    }

    function option_tree_add_font()
    {
        $count = $_GET['count'] + 1;
        $id = $_GET['font_id'];
        $image = array(
            'order' => $count,
            'title' => '',
            'font' => '',
            'link' => '',
            'description' => ''
        );
        option_tree_fontmanager_view($id, $image, $this->get_option_page_ID('media'), $count);
        die();
    }

    function option_tree_add_social()
    {
        $count = $_GET['count'] + 1;
        $id = $_GET['social_id'];
        $image = array(
            'order' => $count,
            'title' => '',
            'image' => '',
            'link' => ''

        );
        option_tree_social_view($id, $image, $this->get_option_page_ID('media'), $count);
        die();
    }

    function option_tree_add_clients()
    {
        $count = $_GET['count'] + 1;
        $id = $_GET['clients_id'];
        $image = array(
            'order' => $count,
            'title' => '',
            'image' => '',
            'link' => ''

        );
        option_tree_clients_view($id, $image, $this->get_option_page_ID('media'), $count);
        die();
    }

    function option_tree_add_sidebar()
    {
        $count = $_GET['count'] + 1;
        $id = $_GET['sidebar_id'];
        $image = array(
            'order' => $count,
            'title' => ''
        );
        option_tree_sidebar_view($id, $image, $this->get_option_page_ID('media'), $count);
        die();
    }

    /**
     * Returns the ID of a cutom post tpye
     *
     * @param string $page_title
     *
     * @return int
     * @uses get_results()
     *
     * @access public
     * @since 1.0.0
     *
     */
    function get_option_page_ID($page_title = '')
    {
        global $wpdb;
        return $wpdb->get_var("SELECT ID FROM $wpdb->posts WHERE `post_name` = '{$page_title}' AND `post_type` = 'option-tree' AND `post_status` = 'private'");
    }

    /**
     * Register custom post type & create two posts
     *
     * @return void
     * @since 1.0.0
     *
     * @uses get_results()
     *
     * @access public
     */
    function create_option_post()
    {
        global $current_user;

        // Profile show docs & settings checkbox
        $this->show_docs = (get_the_author_meta('show_docs', $current_user->ID) == "Yes") ? true : false;

        register_post_type('option-tree', array(
            'labels' => array(
                'name' => __('Options', UT_THEME_NAME),
            ),
            'public' => true,
            'show_ui' => false,
            'capability_type' => 'post',
            'exclude_from_search' => true,
            'hierarchical' => false,
            'rewrite' => false,
            'supports' => array('title', 'editor'),
            'can_export' => true,
            'show_in_nav_menus' => false,
        ));

        // Create a private page to attach media to
        if (isset($_GET['page']) && $_GET['page'] == 'option_tree') {

            // Look for custom page
            $page_id = $this->get_option_page_ID('media');

            // No page create it
            if (!$page_id) {

                // Create post object
                $_p = array();
                $_p['post_title'] = 'Media';
                $_p['post_status'] = 'private';
                $_p['post_type'] = 'option-tree';
                $_p['comment_status'] = 'closed';
                $_p['ping_status'] = 'closed';

                // Insert the post into the database
                $page_id = wp_insert_post($_p);
            }
        }

        // Create a private page for settings page
        if (isset($_GET['page']) && $_GET['page'] == 'option_tree_settings') {

            // Look for custom page
            $page_id = $this->get_option_page_ID('options');

            // No page create it
            if (!$page_id) {

                // Create post object
                $_p = array();
                $_p['post_title'] = 'Options';
                $_p['post_status'] = 'private';
                $_p['post_type'] = 'option-tree';
                $_p['comment_status'] = 'closed';
                $_p['ping_status'] = 'closed';

                // Insert the post into the database
                $page_id = wp_insert_post($_p);
            }
        }
    }

    /**
     * Outputs the notice message to say that someone else is editing this post at the moment.
     *
     * @param int $post_id
     *
     * @return string
     * @uses esc_html()
     *
     * @access public
     * @since 1.0.0
     *
     * @uses get_userdata()
     * @uses get_post_meta()
     */
    function option_tree_notice_post_locked($post_id)
    {
        if (!$post_id)
            return false;

        $last_user = get_userdata(get_post_meta($post_id, '_edit_last', true));
        $last_user_name = $last_user ? $last_user->display_name : __('Somebody', UT_THEME_NAME);
        $the_page = ($_GET['page'] == 'option_tree') ? __('Theme Options', UT_THEME_NAME) : __('Settings', UT_THEME_NAME);

        $message = sprintf(__('Warning: %s is currently editing the %s.', UT_THEME_NAME), esc_html($last_user_name), $the_page);
        return '<div class="message warning"><span>&nbsp;</span>' . $message . '</div>';
    }

    /**
     * Check to see if the post is currently being edited by another user.
     *
     * @param int $post_id
     *
     * @return bool
     * @uses get_current_user_id()
     *
     * @access public
     * @since 1.0.0
     *
     * @uses get_post_meta()
     * @uses apply_filters()
     */
    function option_tree_check_post_lock($post_id)
    {
        if (!$post_id)
            return false;

        $lock = get_post_meta($post_id, '_edit_lock', true);
        $last = get_post_meta($post_id, '_edit_last', true);

        $time_window = apply_filters('wp_check_post_lock_window', 30);

        if ($lock && $lock > time() - $time_window && $last != get_current_user_id())
            return $last;

        return false;
    }

    /**
     * Mark the post as currently being edited by the current user
     *
     * @param int $post_id
     *
     * @return bool
     * @since 1.0.0
     *
     * @uses update_post_meta()
     * @uses get_current_user_id()
     *
     * @access public
     */
    function option_tree_set_post_lock($post_id)
    {
        if (!$post_id)
            return false;

        if (0 == get_current_user_id())
            return false;

        $now = time();

        update_post_meta($post_id, '_edit_lock', $now);
        update_post_meta($post_id, '_edit_last', get_current_user_id());
    }

    /**
     * Remove the post lock
     *
     * @param int $post_id
     *
     * @return bool
     * @uses delete_post_meta()
     *
     * @access public
     * @since 1.0.0
     *
     */
    function option_tree_remove_post_lock($post_id)
    {
        if (!$post_id)
            return false;

        delete_post_meta($post_id, '_edit_lock');
        delete_post_meta($post_id, '_edit_last');
    }

    /**
     * Extra Profile Fields
     *
     * @param option_tree
     *
     * @return void
     * @uses get_the_author_meta()
     *
     * @access public
     * @since 1.8
     *
     */
    function option_tree_extra_profile_fields($user)
    {
        ?>
        <h3>Option Tree</h3>
        <table class="form-table">
            <tr>
                <th scope="row"><?php _e('Show Settings', 'option-tree'); ?></th>
                <td>
                    <input type="checkbox" name="show_docs"
                           value="<?php echo esc_attr(get_the_author_meta('show_docs', $user->ID)); ?>"<?php if (esc_attr(get_the_author_meta('show_docs', $user->ID)) == "Yes") {
                        echo ' checked="checked"';
                    } ?> />
                    <label for="show_docs"><?php _e('Yes', 'option-tree'); ?></label>
                </td>
            </tr>
        </table>
        <?php
    }


    /**
     * Extra Profile Fields Save
     *
     * @param option_tree
     *
     * @return void
     * @uses current_user_can()
     *
     * @access public
     * @since 1.8
     *
     */
    function option_tree_save_extra_profile_fields($user_id)
    {
        if (!current_user_can('edit_user', $user_id))
            return false;

        $ot_view = isset($_POST['show_docs']) ? 'Yes' : 'No';
        update_user_meta($user_id, 'show_docs', $ot_view);
    }

}