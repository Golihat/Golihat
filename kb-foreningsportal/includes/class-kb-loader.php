<?php
/**
 * Main loader that wires up admin/public hooks and module registration.
 */
class KB_FP_Loader {

    /** @var KB_FP_Admin_UI */
    protected $admin_ui;

    /** @var KB_FP_Public */
    protected $public_ui;

    /** @var array */
    protected $modules = array();

    public function __construct() {
        require_once KB_FP_PLUGIN_DIR . 'includes/functions-kb-helpers.php';
        require_once KB_FP_PLUGIN_DIR . 'includes/functions-kb-hooks.php';
        require_once KB_FP_PLUGIN_DIR . 'includes/class-kb-rest-controller.php';
        require_once KB_FP_PLUGIN_DIR . 'includes/class-kb-orgs.php';
        require_once KB_FP_PLUGIN_DIR . 'includes/class-kb-admins.php';
        require_once KB_FP_PLUGIN_DIR . 'includes/class-kb-campaigns.php';
        require_once KB_FP_PLUGIN_DIR . 'includes/class-kb-provision.php';
        require_once KB_FP_PLUGIN_DIR . 'includes/class-kb-orders.php';
        require_once KB_FP_PLUGIN_DIR . 'includes/class-kb-members.php';
        require_once KB_FP_PLUGIN_DIR . 'includes/class-kb-material.php';
        require_once KB_FP_PLUGIN_DIR . 'includes/class-kb-qr.php';
        require_once KB_FP_PLUGIN_DIR . 'includes/class-kb-logs.php';
        require_once KB_FP_PLUGIN_DIR . 'includes/class-kb-notifications.php';
        require_once KB_FP_PLUGIN_DIR . 'admin/class-kb-admin-ui.php';
        require_once KB_FP_PLUGIN_DIR . 'public/class-kb-public.php';
    }

    public function run() {
        $this->register_modules();
        $this->admin_ui = new KB_FP_Admin_UI( $this->modules );
        $this->public_ui = new KB_FP_Public();

        add_action( 'init', array( $this, 'register_rest_controllers' ) );
        add_action( 'init', array( $this, 'register_shortcodes' ) );
        kb_fp_register_common_hooks();
    }

    protected function register_modules() {
        $this->modules = array(
            'orgs'          => new KB_FP_Orgs(),
            'admins'        => new KB_FP_Admins(),
            'campaigns'     => new KB_FP_Campaigns(),
            'provision'     => new KB_FP_Provision(),
            'orders'        => new KB_FP_Orders(),
            'members'       => new KB_FP_Members(),
            'material'      => new KB_FP_Material(),
            'qr'            => new KB_FP_QR(),
            'logs'          => new KB_FP_Logs(),
            'notifications' => new KB_FP_Notifications(),
        );
    }

    public function register_rest_controllers() {
        foreach ( $this->modules as $module ) {
            if ( method_exists( $module, 'register_routes' ) && kb_fp_is_feature_enabled( $module->get_feature_flag() ) ) {
                $module->register_routes();
            }
        }
    }

    public function register_shortcodes() {
        $this->public_ui->register_shortcodes();
    }
}

