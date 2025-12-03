<?php
/**
 * Organisation logic and endpoints.
 */
class KB_FP_Orgs extends KB_FP_REST_Controller {
    protected $resource     = 'orgs';
    protected $feature_flag = 'features.orgs.enabled';

    protected function get_mock_response() {
        return array(
            array(
                'id'          => 1,
                'name'        => 'Exempelförening',
                'campaigns'   => 0,
                'provision'   => 0,
                'status'      => 'draft',
                'featureFlag' => $this->feature_flag,
            ),
        );
    }
}

