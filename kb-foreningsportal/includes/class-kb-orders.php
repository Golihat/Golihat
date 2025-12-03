<?php
/**
 * Order linkage to campaigns/orgs.
 */
class KB_FP_Orders extends KB_FP_REST_Controller {
    protected $resource     = 'orders';
    protected $feature_flag = 'features.orders.enabled';

    protected function get_mock_response() {
        return array(
            array(
                'id'          => 1001,
                'org_id'      => 1,
                'admin_id'    => null,
                'status'      => 'pending',
                'synced'      => false,
                'featureFlag' => $this->feature_flag,
            ),
        );
    }
}

