<div class="wrap">
    <h1><?php esc_html_e( 'KB Föreningsportal – Setup', 'kb-foreningsportal' ); ?></h1>
    <p><?php esc_html_e( 'Slå av/på moduler och funktioner. Endast Superadmin (manage_options) kan uppdatera.', 'kb-foreningsportal' ); ?></p>

    <form method="post" action="options.php" class="kb-fp-settings">
        <?php settings_fields( 'kb_fp_features' ); ?>
        <?php $module_flags = array_map( function( $module ) { return isset( $module['feature_flag'] ) ? $module['feature_flag'] : ''; }, $this->modules ); ?>

        <h2><?php esc_html_e( 'Moduler', 'kb-foreningsportal' ); ?></h2>
        <div class="kb-fp-modules" style="display:grid;gap:16px;grid-template-columns:repeat(auto-fit,minmax(260px,1fr));">
            <?php foreach ( $this->modules as $id => $module ) :
                $flag       = isset( $module['feature_flag'] ) ? $module['feature_flag'] : '';
                $enabled    = isset( $features[ $flag ] ) ? (bool) $features[ $flag ] : true;
                $depends_on = isset( $module['depends_on'] ) ? $module['depends_on'] : array();
            ?>
                <div class="kb-fp-module-card" style="border:1px solid #ccd0d4;border-radius:6px;padding:12px;background:#fff;">
                    <div style="display:flex;justify-content:space-between;align-items:center;">
                        <strong><?php echo esc_html( $module['label'] ); ?></strong>
                        <?php if ( $flag ) : ?>
                            <label>
                                <input type="checkbox" name="kb_fp_features[<?php echo esc_attr( $flag ); ?>]" value="1" <?php checked( $enabled ); ?> />
                                <?php esc_html_e( 'Aktiv', 'kb-foreningsportal' ); ?>
                            </label>
                        <?php endif; ?>
                    </div>
                    <p style="margin:8px 0 4px;"><?php echo esc_html( $module['description'] ); ?></p>
                    <?php if ( ! empty( $depends_on ) ) : ?>
                        <p style="margin:4px 0 0;font-size:12px;color:#555;">
                            <?php esc_html_e( 'Beroenden:', 'kb-foreningsportal' ); ?>
                            <?php echo esc_html( implode( ', ', $depends_on ) ); ?>
                        </p>
                    <?php endif; ?>
                </div>
            <?php endforeach; ?>
        </div>

        <h2 style="margin-top:24px;"><?php esc_html_e( 'Övriga toggles', 'kb-foreningsportal' ); ?></h2>
        <table class="form-table" role="presentation">
            <tbody>
                <?php foreach ( $defaults as $flag => $default_value ) :
                    if ( in_array( $flag, $module_flags, true ) ) {
                        continue;
                    }
                    $enabled = isset( $features[ $flag ] ) ? $features[ $flag ] : $default_value;
                ?>
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
