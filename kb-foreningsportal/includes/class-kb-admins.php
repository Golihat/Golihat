<?php
/**
 * Admin logic and endpoints.
 */
class KB_FP_Admins extends KB_FP_REST_Controller {
    protected $resource     = 'admins';
    protected $feature_flag = 'features.admins.enabled';

    protected function get_mock_response() {
        return array(
            array(
                'id'          => 1,
                'name'        => 'Demo Admin',
                'email'       => 'admin@example.com',
                'commission'  => array(
                    'model' => 'flat',
                    'value' => 0,
                ),
                'featureFlag' => $this->feature_flag,
            ),
        );
    }
}

