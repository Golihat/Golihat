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
        ob_start();
        ?>
        <div id="kb-portal-root" data-mode="<?php echo esc_attr( $atts['mode'] ); ?>"></div>
        <?php
        return ob_get_clean();
    }
}

