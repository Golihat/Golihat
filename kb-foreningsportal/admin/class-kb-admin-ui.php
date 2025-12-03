<?php
/**
 * Admin UI and setup page for module toggles.
 */
class KB_FP_Admin_UI {
    protected $modules;

    public function __construct( $modules ) {
        $this->modules = $modules;
        add_action( 'admin_menu', array( $this, 'register_menu' ) );
        add_action( 'admin_init', array( $this, 'register_settings' ) );
        add_action( 'admin_enqueue_scripts', array( $this, 'enqueue_assets' ) );
    }

    public function register_menu() {
        add_menu_page(
            __( 'KB Föreningsportal', 'kb-foreningsportal' ),
            __( 'KB Portal', 'kb-foreningsportal' ),
            'manage_options',
            'kb-foreningsportal',
            array( $this, 'render_setup_page' ),
            'dashicons-admin-generic'
        );
    }

    public function register_settings() {
        register_setting( 'kb_fp_features', 'kb_fp_features', 'kb_fp_sanitize_features' );
    }

    public function enqueue_assets( $hook ) {
        if ( 'toplevel_page_kb-foreningsportal' !== $hook ) {
            return;
        }

        wp_enqueue_style( 'kb-fp-portal', KB_FP_PLUGIN_URL . 'assets/css/portal.css', array(), KB_FP_VERSION );
        wp_enqueue_script( 'kb-fp-portal-admin', KB_FP_PLUGIN_URL . 'assets/js/portal-admin.bundle.js', array(), KB_FP_VERSION, true );
    }

    public function render_setup_page() {
        $features = kb_fp_get_feature_options( $this->modules );
        $defaults = kb_fp_get_feature_defaults( $this->modules );
        include KB_FP_PLUGIN_DIR . 'admin/views/admin-settings.php';
    }
}

