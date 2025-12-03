<?php
/**
 * Member management.
 */
class KB_FP_Members extends KB_FP_REST_Controller {
    protected $resource     = 'members';
    protected $feature_flag = 'features.members.enabled';

    protected function get_mock_response() {
        return array(
            array(
                'id'          => 1,
                'org_id'      => 1,
                'name'        => 'Demo Medlem',
                'email'       => 'member@example.com',
                'featureFlag' => $this->feature_flag,
            ),
        );
    }
}

