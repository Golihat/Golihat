<?php
/**
 * Public-facing hooks and shortcodes.
 */
class KB_FP_Public {

    public function register_shortcodes() {
        add_shortcode( 'kb_foreningsportal', array( $this, 'render_portal_root' ) );
    }

    public function render_portal_root( $atts ) {
        $atts = shortcode_atts( array( 'mode' => 'org' ), $atts );
        $this->enqueue_assets( $atts['mode'] );
        ob_start();
        ?>
        <div id="kb-portal-root" data-mode="<?php echo esc_attr( $atts['mode'] ); ?>"></div>
        <?php
        return ob_get_clean();
    }

    protected function enqueue_assets( $mode ) {
        wp_enqueue_style( 'kb-fp-portal', KB_FP_PLUGIN_URL . 'assets/css/portal.css', array(), KB_FP_VERSION );

        $script = 'admin' === $mode ? 'assets/js/portal-admin.bundle.js' : 'assets/js/portal-org.bundle.js';
        wp_enqueue_script( 'kb-fp-portal', KB_FP_PLUGIN_URL . $script, array(), KB_FP_VERSION, true );
    }
}

