<?php
/**
 * Fired during plugin activation.
 */
class KB_FP_Activator {

    public static function activate() {
        // Ensure helper functions are available when the activator is called standalone.
        if ( ! function_exists( 'kb_fp_create_default_options' ) ) {
            require_once KB_FP_PLUGIN_DIR . 'includes/functions-kb-helpers.php';
        }

        kb_fp_create_default_options();
        kb_fp_create_custom_tables();
    }
}

