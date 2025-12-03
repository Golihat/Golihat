<?php
/**
 * Plugin Name: KB Föreningsportal
 * Description: Modular föreningsportal för Kryddbox med React-frontend och REST-API.
 * Version: 0.1.0
 * Author: Team Nattum
 * License: GPL2+
 */

if ( ! defined( 'WPINC' ) ) {
    die;
}

// Plugin constants.
define( 'KB_FP_VERSION', '0.1.0' );
define( 'KB_FP_PLUGIN_FILE', __FILE__ );
define( 'KB_FP_PLUGIN_DIR', plugin_dir_path( __FILE__ ) );
define( 'KB_FP_PLUGIN_URL', plugin_dir_url( __FILE__ ) );

// Shared helpers must be available for activation callbacks.
require_once KB_FP_PLUGIN_DIR . 'includes/functions-kb-helpers.php';

// Autoload core files.
require_once KB_FP_PLUGIN_DIR . 'includes/class-kb-loader.php';
require_once KB_FP_PLUGIN_DIR . 'includes/class-kb-activator.php';
require_once KB_FP_PLUGIN_DIR . 'includes/class-kb-deactivator.php';

register_activation_hook( __FILE__, array( 'KB_FP_Activator', 'activate' ) );
register_deactivation_hook( __FILE__, array( 'KB_FP_Deactivator', 'deactivate' ) );

/**
 * Begins execution of the plugin.
 */
function run_kb_foreningsportal() {
    $loader = new KB_FP_Loader();
    $loader->run();
}
run_kb_foreningsportal();

