<?php
/**
 * Logging and diagnostics.
 */
class KB_FP_Logs extends KB_FP_REST_Controller {
    protected $resource     = 'logs';
    protected $feature_flag = 'features.logs.enabled';

    protected function get_mock_response() {
        return array(
            array(
                'id'          => 1,
                'message'     => 'Loggning initierad.',
                'created_at'  => current_time( 'mysql' ),
                'featureFlag' => $this->feature_flag,
            ),
        );
    }
}

