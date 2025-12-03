<div class="wrap">
    <h1><?php esc_html_e( 'KB Föreningsportal – Setup', 'kb-foreningsportal' ); ?></h1>
    <form method="post" action="options.php">
        <?php settings_fields( 'kb_fp_features' ); ?>
        <table class="form-table" role="presentation">
            <tbody>
                <?php foreach ( $features as $flag => $enabled ) : ?>
                    <tr>
                        <th scope="row"><label for="<?php echo esc_attr( $flag ); ?>"><?php echo esc_html( $flag ); ?></label></th>
                        <td>
                            <input type="checkbox" id="<?php echo esc_attr( $flag ); ?>" name="kb_fp_features[<?php echo esc_attr( $flag ); ?>]" value="1" <?php checked( $enabled ); ?> />
                        </td>
                    </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
        <?php submit_button(); ?>
    </form>
</div>
