<?php
/**
 * Fired during plugin activation.
 */
class KB_FP_Activator {

    public static function activate() {
        kb_fp_create_default_options();
        kb_fp_create_custom_tables();
    }
}

