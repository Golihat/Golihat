<?php
/**
 * Common WordPress hooks for the plugin.
 */

function kb_fp_register_common_hooks() {
    add_action( 'rest_api_init', 'kb_fp_register_diagnostics' );
}

function kb_fp_register_diagnostics() {
    register_rest_route( 'kb-forening/v1', '/health', array(
        'methods'             => WP_REST_Server::READABLE,
        'callback'            => function () {
            return rest_ensure_response( array( 'status' => 'ok' ) );
        },
        'permission_callback' => '__return_true',
    ) );
}

