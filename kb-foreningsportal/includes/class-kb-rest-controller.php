<?php
/**
 * Base REST controller for KB Föreningsportal.
 */
abstract class KB_FP_REST_Controller extends WP_REST_Controller {

    /** @var string */
    protected $resource = '';

    /** @var string */
    protected $feature_flag = '';

    public function __construct() {
        $this->namespace = 'kb-forening/v1';
    }

    public function permission_callback( WP_REST_Request $request ) {
        return current_user_can( 'manage_options' );
    }

    public function register_routes() {
        if ( empty( $this->resource ) ) {
            return;
        }

        register_rest_route( $this->get_namespace(), '/' . $this->resource, array(
            array(
                'methods'             => WP_REST_Server::READABLE,
                'callback'            => array( $this, 'get_items' ),
                'permission_callback' => array( $this, 'permission_callback' ),
            ),
        ) );
    }

    public function get_items( WP_REST_Request $request ) {
        return rest_ensure_response( $this->get_mock_response() );
    }

    public function get_feature_flag() {
        return $this->feature_flag;
    }

    protected function get_mock_response() {
        return array(
            'resource' => $this->resource,
            'items'    => array(),
        );
    }

    protected function get_namespace() {
        return $this->namespace;
    }
}

