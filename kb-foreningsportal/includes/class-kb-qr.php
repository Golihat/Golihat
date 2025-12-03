<?php
/**
 * QR generation.
 */
class KB_FP_QR extends KB_FP_REST_Controller {
    protected $resource     = 'qr';
    protected $feature_flag = 'features.qr.auto_generate';

    protected function get_mock_response() {
        return array(
            array(
                'id'          => 1,
                'org_id'      => 1,
                'code'        => 'QR123',
                'url'         => 'https://example.com/org/qr123',
                'featureFlag' => $this->feature_flag,
            ),
        );
    }
}

