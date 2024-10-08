<?php
/**
 * Plugin Name: Conditional Logic Highlighter for Gravity Forms
 * Plugin URI: https://wordpress.org/plugins/conditional-logic-highlighter-for-gravity-forms
 * Description: Highlights fields in Gravity Forms that have Conditional Logic active.
 * Version: 1.0.0
 * Author: Fatih Gürsu
 * Author URI: https://webdevels.de
 * Text Domain: conditional-logic-highlighter-for-gravity-forms
 * Domain Path: /languages
 * License: GPL-2.0+
 * License URI: http://www.gnu.org/licenses/gpl-2.0.txt
 */

// If this file is called directly, abort.
if (!defined('ABSPATH')) {
    exit; // Exit if accessed directly
}

// Define plugin constants
define('GFCLH_VERSION', '1.0.0');
define('GFCLH_PLUGIN_DIR', plugin_dir_path(__FILE__));
define('GFCLH_PLUGIN_URL', plugin_dir_url(__FILE__));

// Include necessary files
require_once GFCLH_PLUGIN_DIR . 'includes/class-clhgf-settings.php';
require_once GFCLH_PLUGIN_DIR . 'includes/class-clhgf-highlighter.php';

// Hook for plugin activation
register_activation_hook(__FILE__, 'gfclh_activate');

function gfclh_activate() {
    // Set default options
    $default_options = array(
        'highlight_admin' => 1,
        'highlight_frontend' => 1,
        'admin_css' => 'background-color: #ffffcc !important; border: 1px solid #ffeb3b !important; border-radius: 6px !important;',
        'frontend_css' => 'background-color: #e6f3ff !important; border: 1px solid #2196f3 !important; border-radius: 6px !important;'
    );
    add_option('gfclh_options', $default_options);
}

// Hook for plugin deactivation
register_deactivation_hook(__FILE__, 'gfclh_deactivate');

function gfclh_deactivate() {
    // Clean up if necessary
}

// Initialize the plugin
function gfclh_init() {
    // Load text domain for translations
    load_plugin_textdomain('conditional-logic-highlighter-for-gravity-forms', false, dirname(plugin_basename(__FILE__)) . '/languages');

    // Initialize settings
    new GFCLH_Settings();

    // Initialize highlighter
    new GFCLH_Highlighter();
}
add_action('plugins_loaded', 'gfclh_init');
