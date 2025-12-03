<?php
/**
 * Base REST controller for KB Föreningsportal.
 */
abstract class KB_FP_REST_Controller extends WP_REST_Controller {

    public function permission_callback( WP_REST_Request $request ) {
        return current_user_can( 'manage_options' );
    }

    protected function get_namespace() {
        return 'kb-forening/v1';
    }
}

