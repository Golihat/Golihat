<?php
/**
 * Material generation and storage.
 */
class KB_FP_Material extends KB_FP_REST_Controller {
    protected $resource     = 'materials';
    protected $feature_flag = 'features.materials.enabled';

    protected function get_mock_response() {
        return array(
            array(
                'id'          => 1,
                'org_id'      => 1,
                'type'        => 'flyer',
                'url'         => 'https://example.com/flyer.pdf',
                'featureFlag' => $this->feature_flag,
            ),
        );
    }
}

