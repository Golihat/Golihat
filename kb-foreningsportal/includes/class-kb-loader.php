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
        if ( ! class_exists( 'WP_REST_Controller' ) && file_exists( ABSPATH . 'wp-includes/rest-api/endpoints/class-wp-rest-controller.php' ) ) {
            require_once ABSPATH . 'wp-includes/rest-api/endpoints/class-wp-rest-controller.php';
        }
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
        if ( is_admin() ) {
            $this->admin_ui = new KB_FP_Admin_UI( $this->modules );
        }
        $this->public_ui = new KB_FP_Public();

        add_action( 'rest_api_init', array( $this, 'register_rest_controllers' ) );
        add_action( 'init', array( $this, 'register_shortcodes' ) );
        kb_fp_register_common_hooks();
    }

    protected function register_modules() {
        $this->modules = array(
            'orgs'          => array(
                'instance'     => new KB_FP_Orgs(),
                'label'        => __( 'Föreningar', 'kb-foreningsportal' ),
                'description'  => __( 'Hanterar föreningarnas grunddata, länkar och kopplingar.', 'kb-foreningsportal' ),
                'feature_flag' => 'features.orgs.enabled',
                'depends_on'   => array(),
            ),
            'admins'        => array(
                'instance'     => new KB_FP_Admins(),
                'label'        => __( 'Admins', 'kb-foreningsportal' ),
                'description'  => __( 'Provisioner och behörigheter för administratörer.', 'kb-foreningsportal' ),
                'feature_flag' => 'features.admins.enabled',
                'depends_on'   => array(),
            ),
            'campaigns'     => array(
                'instance'     => new KB_FP_Campaigns(),
                'label'        => __( 'Kampanjer', 'kb-foreningsportal' ),
                'description'  => __( 'Standardkampanjer, datum och status för säljrundor.', 'kb-foreningsportal' ),
                'feature_flag' => 'features.campaigns.enabled',
                'depends_on'   => array( 'orgs' ),
            ),
            'provision'     => array(
                'instance'     => new KB_FP_Provision(),
                'label'        => __( 'Provision', 'kb-foreningsportal' ),
                'description'  => __( 'Provision per box för förening samt adminmodeller.', 'kb-foreningsportal' ),
                'feature_flag' => 'features.provision.enabled',
                'depends_on'   => array( 'orgs', 'admins', 'campaigns' ),
            ),
            'orders'        => array(
                'instance'     => new KB_FP_Orders(),
                'label'        => __( 'Orderkoppling', 'kb-foreningsportal' ),
                'description'  => __( 'Kopplar WooCommerce-ordrar till förening och admin.', 'kb-foreningsportal' ),
                'feature_flag' => 'features.orders.enabled',
                'depends_on'   => array( 'orgs', 'campaigns', 'provision' ),
            ),
            'members'       => array(
                'instance'     => new KB_FP_Members(),
                'label'        => __( 'Medlemmar', 'kb-foreningsportal' ),
                'description'  => __( 'Medlemshantering, statistik och export.', 'kb-foreningsportal' ),
                'feature_flag' => 'features.members.enabled',
                'depends_on'   => array( 'orgs', 'campaigns' ),
            ),
            'material'      => array(
                'instance'     => new KB_FP_Material(),
                'label'        => __( 'Material', 'kb-foreningsportal' ),
                'description'  => __( 'Affischer, flyers, SoMe-texter och genererade filer.', 'kb-foreningsportal' ),
                'feature_flag' => 'features.materials.enabled',
                'depends_on'   => array( 'orgs', 'qr' ),
            ),
            'qr'            => array(
                'instance'     => new KB_FP_QR(),
                'label'        => __( 'QR-koder', 'kb-foreningsportal' ),
                'description'  => __( 'Auto-generering av QR-koder och föreningslänkar.', 'kb-foreningsportal' ),
                'feature_flag' => 'features.qr.auto_generate',
                'depends_on'   => array( 'orgs' ),
            ),
            'logs'          => array(
                'instance'     => new KB_FP_Logs(),
                'label'        => __( 'Loggar', 'kb-foreningsportal' ),
                'description'  => __( 'Ändringslogg, diagnostik och händelser.', 'kb-foreningsportal' ),
                'feature_flag' => 'features.logs.enabled',
                'depends_on'   => array(),
            ),
            'notifications' => array(
                'instance'     => new KB_FP_Notifications(),
                'label'        => __( 'Notiser', 'kb-foreningsportal' ),
                'description'  => __( 'Systemnotiser och larm för viktiga händelser.', 'kb-foreningsportal' ),
                'feature_flag' => 'features.notifications.enabled',
                'depends_on'   => array( 'logs' ),
            ),
        );
    }

    public function register_rest_controllers() {
        foreach ( $this->modules as $module ) {
            if ( ! isset( $module['instance'] ) ) {
                continue;
            }

            $flag = isset( $module['feature_flag'] ) ? $module['feature_flag'] : $module['instance']->get_feature_flag();

            if ( method_exists( $module['instance'], 'register_routes' ) && kb_fp_is_feature_enabled( $flag ) ) {
                $module['instance']->register_routes();
            }
        }
    }

    public function register_shortcodes() {
        $this->public_ui->register_shortcodes();
    }
}

