<?php
/**
 * Campaign logic and endpoints.
 */
class KB_FP_Campaigns extends KB_FP_REST_Controller {
    protected $resource     = 'campaigns';
    protected $feature_flag = 'features.campaigns.enabled';

    protected function get_mock_response() {
        return array(
            array(
                'id'          => 1,
                'org_id'      => 1,
                'name'        => 'Standardkampanj',
                'status'      => 'draft',
                'start_date'  => null,
                'end_date'    => null,
                'featureFlag' => $this->feature_flag,
            ),
        );
    }
}

