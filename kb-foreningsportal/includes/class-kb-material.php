<?php
/**
 * Material generation and storage.
 */
class KB_FP_Material extends KB_FP_REST_Controller {
    protected $feature_flag = 'features.materials.enabled';

    public function get_feature_flag() {
        return $this->feature_flag;
    }

    public function register_routes() {
        register_rest_route( $this->get_namespace(), '/materials', array(
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

