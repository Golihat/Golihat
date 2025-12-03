<?php
/**
 * Provision logic and endpoints.
 */
class KB_FP_Provision extends KB_FP_REST_Controller {
    protected $resource     = 'provision';
    protected $feature_flag = 'features.provision.enabled';

    protected function get_mock_response() {
        return array(
            'org'   => array( 'per_box' => 0 ),
            'admin' => array(
                'model' => 'flat',
                'value' => 0,
            ),
            'featureFlag' => $this->feature_flag,
        );
    }
}

