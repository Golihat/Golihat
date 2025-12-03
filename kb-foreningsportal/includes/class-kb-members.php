<?php
/**
 * Member management.
 */
class KB_FP_Members extends KB_FP_REST_Controller {
    protected $feature_flag = 'features.members.enabled';

    public function get_feature_flag() {
        return $this->feature_flag;
    }

    public function register_routes() {
        register_rest_route( $this->get_namespace(), '/members', array(
            array(
                'methods'             => WP_REST_Server::READABLE,
                'callback'            => array( $this, 'get_items' ),
                'permission_callback' => array( $this, 'permission_callback' ),
            ),
        ) );
    }

    public function get_items( WP_REST_Request $request ) {
        return rest_ensure_response( array() );
    }
}

