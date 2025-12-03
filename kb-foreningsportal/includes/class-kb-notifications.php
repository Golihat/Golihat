<?php
/**
 * Internal notifications.
 */
class KB_FP_Notifications extends KB_FP_REST_Controller {
    protected $resource     = 'notifications';
    protected $feature_flag = 'features.notifications.enabled';

    protected function get_mock_response() {
        return array(
            array(
                'id'          => 1,
                'type'        => 'info',
                'message'     => 'Systemet är igång.',
                'created_at'  => current_time( 'mysql' ),
                'featureFlag' => $this->feature_flag,
            ),
        );
    }
}

