<?php
/**
 * Provision logic and endpoints.
 */
class KB_FP_Provision extends KB_FP_REST_Controller {
    protected $feature_flag = 'features.provision.enabled';

    public function get_feature_flag() {
        return $this->feature_flag;
    }

    public function register_routes() {
        register_rest_route( $this->get_namespace(), '/provision', array(
            array(
                'methods'             => WP_REST_Server::READABLE,
                'callback'            => array( $this, 'get_summary' ),
                'permission_callback' => array( $this, 'permission_callback' ),
            ),
        ) );
    }

    public function get_summary( WP_REST_Request $request ) {
        return rest_ensure_response( array( 'org_provision' => 0, 'admin_provision' => 0 ) );
    }
}

