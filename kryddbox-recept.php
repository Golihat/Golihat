<?php
/**
 * Plugin Name: Kryddbox Recept och Kylskåpskock
 * Description: Provides external recipe bank and fridge chef shortcodes with legal-first features.
 * Version: 0.1.0
 * Author: Golihat
 * Text Domain: kryddbox
 */

if ( ! defined( 'ABSPATH' ) ) {
    exit;
}

class Kryddbox_Recept_Plugin {
    private static $instance = null;
    private $footer_needed   = false;

    /**
     * Singleton instance.
     */
    public static function instance() {
        if ( null === self::$instance ) {
            self::$instance = new self();
        }
        return self::$instance;
    }

    private function __construct() {
        add_action( 'init', [ $this, 'register_post_type' ] );
        add_action( 'init', [ $this, 'register_taxonomies' ] );
        add_action( 'admin_menu', [ $this, 'register_admin_menu' ] );
        add_shortcode( 'kb_receptbank', [ $this, 'shortcode_receptbank' ] );
        add_shortcode( 'kb_kylskap', [ $this, 'shortcode_kylskap' ] );
        add_action( 'wp_footer', [ $this, 'maybe_print_footer_notice' ] );
    }

    /**
     * Register custom post type.
     */
    public function register_post_type() {
        $labels = [
            'name'          => __( 'Externa Recept', 'kryddbox' ),
            'singular_name' => __( 'Externt Recept', 'kryddbox' ),
        ];
        $args   = [
            'labels'      => $labels,
            'public'      => false,
            'show_ui'     => true,
            'supports'    => [ 'title' ],
            'show_in_menu' => false,
        ];
        register_post_type( 'kb_ext_recipe', $args );

        $meta_keys = [
            '_kb_source',
            '_kb_source_url',
            '_kb_canonical',
            '_kb_title',
            '_kb_description',
            '_kb_ingredients',
            '_kb_total_time',
            '_kb_prep_time',
            '_kb_cook_time',
            '_kb_yield',
            '_kb_author',
            '_kb_publisher',
            '_kb_date_published',
            '_kb_image_url',
            '_kb_hash',
        ];

        foreach ( $meta_keys as $key ) {
            register_post_meta( 'kb_ext_recipe', $key, [
                'show_in_rest'  => true,
                'single'        => true,
                'type'          => 'string',
                'auth_callback' => function () {
                    return current_user_can( 'edit_posts' );
                },
            ] );
        }
    }

    /**
     * Register taxonomies.
     */
    public function register_taxonomies() {
        register_taxonomy( 'kb_ingredient', 'kb_ext_recipe', [
            'label'        => __( 'Ingredienser', 'kryddbox' ),
            'public'       => false,
            'show_ui'      => true,
            'hierarchical' => false,
        ] );
        register_taxonomy( 'kb_cuisine', 'kb_ext_recipe', [
            'label'        => __( 'Kök', 'kryddbox' ),
            'public'       => false,
            'show_ui'      => true,
            'hierarchical' => true,
        ] );
        register_taxonomy( 'kb_diet', 'kb_ext_recipe', [
            'label'        => __( 'Diet', 'kryddbox' ),
            'public'       => false,
            'show_ui'      => true,
            'hierarchical' => true,
        ] );
    }

    /**
     * Admin menu structure.
     */
    public function register_admin_menu() {
        add_menu_page(
            __( 'Kryddbox Recept', 'kryddbox' ),
            __( 'Kryddbox Recept', 'kryddbox' ),
            'manage_options',
            'kryddbox-recept',
            [ $this, 'render_overview' ],
            'dashicons-carrot',
            26
        );

        add_submenu_page( 'kryddbox-recept', __( 'Översikt', 'kryddbox' ), __( 'Översikt', 'kryddbox' ), 'manage_options', 'kryddbox-recept', [ $this, 'render_overview' ] );
        add_submenu_page( 'kryddbox-recept', __( 'Källor', 'kryddbox' ), __( 'Källor', 'kryddbox' ), 'manage_options', 'kryddbox-sources', [ $this, 'render_placeholder' ] );
        add_submenu_page( 'kryddbox-recept', __( 'Indexering', 'kryddbox' ), __( 'Indexering', 'kryddbox' ), 'manage_options', 'kryddbox-index', [ $this, 'render_placeholder' ] );
        add_submenu_page( 'kryddbox-recept', __( 'Sök och UX', 'kryddbox' ), __( 'Sök och UX', 'kryddbox' ), 'manage_options', 'kryddbox-search', [ $this, 'render_placeholder' ] );
        add_submenu_page( 'kryddbox-recept', __( 'Kylskåpskock', 'kryddbox' ), __( 'Kylskåpskock', 'kryddbox' ), 'manage_options', 'kryddbox-fridge', [ $this, 'render_placeholder' ] );
        add_submenu_page( 'kryddbox-recept', __( 'Kryddguide', 'kryddbox' ), __( 'Kryddguide', 'kryddbox' ), 'manage_options', 'kryddbox-spices', [ $this, 'render_placeholder' ] );
        add_submenu_page( 'kryddbox-recept', __( 'Juridik', 'kryddbox' ), __( 'Juridik', 'kryddbox' ), 'manage_options', 'kryddbox-legal', [ $this, 'render_placeholder' ] );
        add_submenu_page( 'kryddbox-recept', __( 'Logg', 'kryddbox' ), __( 'Logg', 'kryddbox' ), 'manage_options', 'kryddbox-log', [ $this, 'render_placeholder' ] );
    }

    /**
     * Render overview page.
     */
    public function render_overview() {
        echo '<div class="wrap"><h1>' . esc_html__( 'Kryddbox Recept', 'kryddbox' ) . '</h1><p>' . esc_html__( 'Översikt kommer här.', 'kryddbox' ) . '</p></div>';
    }

    /**
     * Render placeholder for unimplemented pages.
     */
    public function render_placeholder() {
        echo '<div class="wrap"><h1>' . esc_html( get_admin_page_title() ) . '</h1><p>' . esc_html__( 'Inte implementerat ännu.', 'kryddbox' ) . '</p></div>';
    }

    /**
     * Attribution helper.
     */
    private function attribution_text( $domain, $author, $url ) {
        $domain = esc_html( $domain );
        $author = esc_html( $author );
        $url    = esc_url( $url );

        $link = '<a href="' . $url . '" rel="nofollow noopener noreferrer" target="_blank">' . $url . '</a>';
        return sprintf( __( 'Här visar vi ett recept från %1$s, skapat av %2$s. En länk till det fullständiga receptet hittar ni här: %3$s', 'kryddbox' ), $domain, $author, $link );
    }

    /**
     * Receptbank shortcode.
     */
    public function shortcode_receptbank( $atts ) {
        $this->footer_needed = true;
        $atts               = shortcode_atts( [
            'per_page' => 12,
        ], $atts, 'kb_receptbank' );

        $query = new WP_Query( [
            'post_type'      => 'kb_ext_recipe',
            'posts_per_page' => intval( $atts['per_page'] ),
        ] );

        if ( ! $query->have_posts() ) {
            return '<p>' . esc_html__( 'Inga recept hittades.', 'kryddbox' ) . '</p>';
        }

        ob_start();
        echo '<div class="kb-receptbank">';
        while ( $query->have_posts() ) {
            $query->the_post();
            $domain      = get_post_meta( get_the_ID(), '_kb_source', true );
            $author      = get_post_meta( get_the_ID(), '_kb_author', true );
            $url         = get_post_meta( get_the_ID(), '_kb_source_url', true );
            $ingredients = get_post_meta( get_the_ID(), '_kb_ingredients', true );
            $ingredients_list = '';
            if ( $ingredients ) {
                $decoded = json_decode( $ingredients, true );
                if ( is_array( $decoded ) ) {
                    $ingredients_list = '<ul class="kb-ingredients">';
                    foreach ( $decoded as $ing ) {
                        $ingredients_list .= '<li>' . esc_html( $ing ) . '</li>';
                    }
                    $ingredients_list .= '</ul>';
                }
            }

            echo '<article class="kb-recipe">';
            echo '<h3>' . esc_html( get_post_meta( get_the_ID(), '_kb_title', true ) ) . '</h3>';
            echo $ingredients_list;
            echo '<p class="kb-attribution">' . $this->attribution_text( $domain, $author, $url ) . '</p>';
            echo '</article>';
        }
        echo '</div>';
        wp_reset_postdata();
        return ob_get_clean();
    }

    /**
     * Kylskåpskock shortcode.
     */
    public function shortcode_kylskap( $atts ) {
        $this->footer_needed = true;
        $selected            = [];
        if ( ! empty( $_GET['kb_ing'] ) ) {
            $selected = array_map( 'sanitize_text_field', (array) $_GET['kb_ing'] );
        }
        $terms = get_terms( [
            'taxonomy'   => 'kb_ingredient',
            'hide_empty' => false,
        ] );
        ob_start();
        echo '<form method="get" class="kb-kylskap-form">';
        foreach ( $terms as $term ) {
            $checked = in_array( $term->slug, $selected, true ) ? 'checked' : '';
            printf( '<label><input type="checkbox" name="kb_ing[]" value="%1$s" %3$s> %2$s</label><br/>', esc_attr( $term->slug ), esc_html( $term->name ), $checked );
        }
        echo '<button type="submit">' . esc_html__( 'Visa recept', 'kryddbox' ) . '</button>';
        echo '</form>';

        if ( $selected ) {
            $tax_query = [];
            foreach ( $selected as $slug ) {
                $tax_query[] = [
                    'taxonomy' => 'kb_ingredient',
                    'field'    => 'slug',
                    'terms'    => $slug,
                ];
            }
            $query = new WP_Query( [
                'post_type' => 'kb_ext_recipe',
                'tax_query' => array_merge( [ 'relation' => 'AND' ], $tax_query ),
                'posts_per_page' => 20,
            ] );

            if ( $query->have_posts() ) {
                echo '<div class="kb-receptbank">';
                while ( $query->have_posts() ) {
                    $query->the_post();
                    $domain = get_post_meta( get_the_ID(), '_kb_source', true );
                    $author = get_post_meta( get_the_ID(), '_kb_author', true );
                    $url    = get_post_meta( get_the_ID(), '_kb_source_url', true );
                    echo '<article class="kb-recipe">';
                    echo '<h3>' . esc_html( get_post_meta( get_the_ID(), '_kb_title', true ) ) . '</h3>';
                    echo '<p class="kb-attribution">' . $this->attribution_text( $domain, $author, $url ) . '</p>';
                    echo '</article>';
                }
                echo '</div>';
                wp_reset_postdata();
            } else {
                echo '<p>' . esc_html__( 'Inga recept hittades.', 'kryddbox' ) . '</p>';
            }
        }

        return ob_get_clean();
    }

    /**
     * Output footer notice when needed.
     */
    public function maybe_print_footer_notice() {
        if ( ! $this->footer_needed ) {
            return;
        }
        if ( ! get_option( 'kryddbox_footer_notice_enabled', true ) ) {
            return;
        }
        echo '<div class="kb-footer-notice" style="max-width:600px;margin:1em auto;font-size:0.9em;" aria-label="Kryddbox footernotis">';
        echo esc_html__( 'Vi återspeglar enbart recept, inga recept vi visar är våra egna, utan de kommer från kända aktörer. Vår sida finns för att stötta alla receptskapare och ge er användare ett lätthanterligt verktyg att hitta dem. Vi har inget ekonomiskt intresse i att ha denna funktion. Denna är till er, från oss på Kryddbox.', 'kryddbox' );
        echo '</div>';
    }
}

add_action( 'plugins_loaded', [ 'Kryddbox_Recept_Plugin', 'instance' ] );
