<?php

/**
 * Plugin Name:       Color Variation Switcher WooCommerce
 * Plugin URI:        faridmia
 * Description:       Color Switcher plugin for WooCommerce. It provides a well-designed it uses product single page to your users.
 * Version:           1.0.0
 * Author:            faridmia
 * Author URI:        faridmia
 * License:           GPL-2.0+
 * License URI:       http://www.gnu.org/licenses/gpl-2.0.txt
 * Text Domain:       cvsw
 * Domain Path:       /languages
 */

// If this file is called directly, abort.
if ( !defined( 'WPINC' ) ) {
    die;
}

# Defines CONSTANTS for Whole plugins.

define( 'CVSW_WOO_FILE', __FILE__ );
define( 'CVSW_WOO_VERSION', '1.0.0' );
define( 'CVSW_WOO_URL', plugins_url( '/', __FILE__ ) );
define( 'CVSW_WOO_PATH', plugin_dir_path( __FILE__ ) );
define( 'CVSW_WOO_DIR_URL', plugin_dir_url( __FILE__ ) );
define( 'CVSW_WOO_BASENAME', plugin_basename( __FILE__ ) );
define( 'CVSW_WOO_ASSETS', CVSW_WOO_URL );
define( 'CVSW_WOO_ASSETS_PATH', CVSW_WOO_PATH );

#Admin notice when not WooCommerce plugin not acitvated

function cvsw_admin_notices() {?>
    <div class="error">
        <p><?php _e( '<strong>Color Switcher WooCommerce requires WooCommerce to be installed and active. You can download <a href="https://woocommerce.com/" target="_blank">WooCommerce</a> here.</strong>', 'cvsw' );?></p>
    </div>
    <?php
}

# WooCommerce plugin dependency

function cvsw_install_woocommerce_dependency() {
    if ( !function_exists( 'WC' ) ) {
        add_action( 'admin_notices', 'cvsw_admin_notices' );
    }
}

add_action( 'plugins_loaded', 'cvsw_install_woocommerce_dependency', 99 );

# Enqueue scripts

add_action( 'admin_enqueue_scripts', 'cvsw_product_addons_scripts' );
add_action( 'wp_enqueue_scripts', 'cvsw_product_addon_script', 99 );
add_action( 'init', 'cvsw_i18n' );

# plugin init function
function cvsw_i18n() {
    load_plugin_textdomain( 'cvsw', false, dirname( plugin_basename( CVSW_WOO_FILE ) ) . '/i18n/' );
}

function cvsw_product_addons_scripts() {
    wp_enqueue_style( 'wp-color-picker' );
    wp_enqueue_script( 'wp-color-picker' );
    wp_enqueue_style( 'ecaw-admin-css', CVSW_WOO_URL . 'assets/css/admin.css', array(), time() );

}

function cvsw_product_addon_script() {
    wp_enqueue_style( 'ecaw-color-front-css-switcher', CVSW_WOO_URL . 'assets/css/style.css', array(), time() );
    wp_enqueue_script( 'ecaw-color-switcher-js', CVSW_WOO_URL . 'assets/js/ecaw-color-switcher.js', array( 'jquery' ), time(), true );
    wp_enqueue_style( 'wp-color-picker' );
    wp_enqueue_script( 'wp-color-picker' );
}

add_filter( 'plugin_action_links_' . CVSW_WOO_BASENAME, 'cvsw_action_links' );

function cvsw_action_links( $links ) {
    $custom_links = array( '<a href="' . admin_url( 'admin.php?page=wc-settings&tab=settings_tab_ecaw' ) . '">' . __( 'Settings', 'woocommerce' ) . '</a>' );

    return array_merge( $custom_links, $links );
}

if ( !class_exists( 'Ecaw_Core_Wc' ) ) {

    class Ecaw_Core_Wc {

        /* Bootstraps the class and hooks required actions & filters.
         *
         */
        public static function init() {
            add_filter( 'woocommerce_settings_tabs_array', __CLASS__ . '::add_settings_tab', 50 );
            add_action( 'woocommerce_settings_tabs_settings_tab_ecaw', __CLASS__ . '::settings_tab' );
            add_action( 'woocommerce_update_options_settings_tab_ecaw', __CLASS__ . '::update_settings' );
            self::manage_attribute_term();
        }

        /* Add a new settings tab to the WooCommerce settings tabs array.
         *
         * @param array $settings_tabs Array of WooCommerce setting tabs & their labels, excluding the Subscription tab.
         * @return array $settings_tabs Array of WooCommerce setting tabs & their labels, including the Subscription tab.
         */
        public static function add_settings_tab( $settings_tabs ) {
            $settings_tabs['settings_tab_ecaw'] = __( 'ECAW Variation Switches', 'woocommerce-settings-tab-demo' );
            return $settings_tabs;
        }

        /* Uses the WooCommerce admin fields API to output settings via the @see woocommerce_admin_fields() function.
         *
         * @uses woocommerce_admin_fields()
         * @uses self::get_settings()
         */
        public static function settings_tab() {
            woocommerce_admin_fields( self::get_settings() );
        }

        /* Uses the WooCommerce options API to save settings via the @see woocommerce_update_options() function.
         *
         * @uses woocommerce_update_options()
         * @uses self::get_settings()
         */
        public static function update_settings() {
            woocommerce_update_options( self::get_settings() );
        }

        /* Get all the settings for this plugin for @see woocommerce_admin_fields() function.
         *
         * @return array Array of settings for @see woocommerce_admin_fields() function.
         */
        public static function get_settings() {

            $settings = array(
                'section_title' => array(
                    'name' => __( 'Color Switcher Opiton', 'cvsw' ),
                    'type' => 'title',
                    'desc' => '',
                    'id'   => 'wc_settings_tab_demo_section_title',
                ),
                'title'         => array(
                    'name'    => __( 'Switcher Enable', 'cvsw' ),
                    'type'    => 'checkbox',
                    'desc'    => __( 'Enable "Color Variation Swatches for WooCommerce" plugin ', 'cvsw' ),
                    'id'      => 'wc_settings_tab_ecaw_color_variation',
                    'default' => true, // Set the default value to true (enabled)
                ),
                'section_end'   => array(
                    'type' => 'sectionend',
                    'id'   => 'wc_settings_tab_demo_section_end',
                ),
            );

            return apply_filters( 'wc_settings_tab_demo_settings', $settings );
        }

        public static function add_attribute_hooks( $taxonomy_name, $column_callback ) {
            add_filter( 'manage_edit-' . $taxonomy_name . '_columns', array( static::class, $column_callback ) );
            add_filter( 'manage_' . $taxonomy_name . '_custom_column', array( static::class, 'create_attribute_column_content' ), 10, 3 );
            // add_action( $taxonomy_name . '_add_form_fields', array( static::class, 'create_attribute_field' ) );
            add_action( $taxonomy_name . '_edit_form_fields', array( static::class, 'ecaw_edit_fields' ), 10, 2 );

        }

        public static function manage_attribute_term() {
            $COLOR = 'color_type';
            $IMAGE = 'image_type';
            $LABEL = 'label_type';

            // Get all WooCommerce attribute taxonomies
            $attribute_taxonomies = wc_get_attribute_taxonomies();

            if ( empty( $attribute_taxonomies ) ) {
                return;
            }

            foreach ( $attribute_taxonomies as $taxonomy ) {
                $taxonomy_name = wc_attribute_taxonomy_name( $taxonomy->attribute_name );

                switch ( $taxonomy->attribute_type ) {
                case $COLOR:
                    static::add_attribute_hooks( $taxonomy_name, 'color_attribute_column' );
                    break;

                case $IMAGE:
                    static::add_attribute_hooks( $taxonomy_name, 'image_attribute_column' );
                    break;

                case $LABEL:
                    static::add_attribute_hooks( $taxonomy_name, 'label_attribute_column' );
                    break;

                default:
                    break;
                }
            }
        }

        public static function add_custom_attribute_column( $columns, $column_key, $column_label ) {
            $new_columns = array();

            if ( isset( $columns['cb'] ) ) {
                $new_columns['cb'] = $columns['cb'];
                unset( $columns['cb'] );
            }

            $new_columns[$column_key] = __( $column_label, 'your-text-domain' );

            if ( isset( $columns['name'] ) ) {
                $new_columns['name'] = $columns['name'];
                unset( $columns['name'] );
            }

            return array_merge( $new_columns, $columns );
        }

        public static function color_attribute_column( $columns ) {
            return self::add_custom_attribute_column( $columns, 'color', 'Color' );
        }

        public static function image_attribute_column( $columns ) {
            return self::add_custom_attribute_column( $columns, 'image', 'Image' );
        }

        public static function label_attribute_column( $columns ) {
            return self::add_custom_attribute_column( $columns, 'label', 'Label' );
        }

        public static function create_attribute_column_content( $content, $column_name, $term_id ) {

            $color = get_term_meta( $term_id, 'color_type', true ) ?: 'N/A';
            $get_image = get_term_meta( $term_id, 'image_type', true );
            $label_type = get_term_meta( $term_id, 'label_type', true ) ?: 'N/A';

            if ( empty( $get_image ) ) {
                $get_image = CVSW_WOO_ASSETS . 'assets/images/fallback-placeholder.png';
            }

            $content = '';
            if ( $column_name === 'color' ) {
                $content .= '<span style="display: inline-block; width: 30px; height: 30px; background-color: ' . esc_attr( $color ) . ';"></span>';
            } elseif ( $column_name === 'image' ) {
                $content .= '<img src="' . esc_url( $get_image ) . '" style="width: 30px; height: 30px;">';
            } elseif ( $column_name === 'label' ) {
                $content .= esc_html( $label_type );
            }

            return $content;
        }

        public static function ecaw_edit_fields( $term, $taxonomy ) {

            // do nothing if this term isn't the Color type
            global $wpdb;

            $attribute_type = $wpdb->get_var(
                $wpdb->prepare(
                    "
			SELECT attribute_type
			FROM " . $wpdb->prefix . "woocommerce_attribute_taxonomies
			WHERE attribute_name = '%s'
			",
                    substr( $taxonomy, 3 ) // remove "pa_" prefix
                )
            );

            // if it is not a color attribute, just do nothing
            if ( 'color_type' !== $attribute_type && 'image_type' != $attribute_type && 'label_type' != $attribute_type ) {
                return;
            }

            // we can use attribute type as a meta key why not
            $color = get_term_meta( $term->term_id, 'color_type', true );
            $image_type = get_term_meta( $term->term_id, 'image_type', true );
            $label_type = get_term_meta( $term->term_id, 'label_type', true );

            switch ( $attribute_type ) {
            case 'color_type':
                ?>
                <tr class="form-field">
                    <th><label for="term-color_type"><?php echo esc_html__( "Color", "cvsw" ); ?></label></th>
                    <td><input type="text" id="term-color_type" name="color_type" class="term_color_ecaw" value="<?php echo esc_attr( $color ) ?>" /></td>
                </tr>
                <?php
break;
            case 'image_type':
                ?>
                <tr class="form-field">
                    <th><label for="term-image_type"><?php echo esc_html__( "Image", "cvsw" ); ?></label></th>
                    <td>
                        <div class="upow-term-image" id="upow-swatch-term-image">
                            <img src="<?php echo esc_url( $image_type ); ?>" id="upow-term-image-preview" style="<?php echo empty( $image_type ) ? 'display:none;' : ''; ?>"/>
                        </div>
                        <div>
                            <input type="hidden" id="upow-swatch-term-img-input" name="image_type" value="<?php echo esc_attr( $image_type ); ?>"/>
                            <a class="button" id="upow-swatch-term-upload-img-btn">
                                <?php esc_html_e( 'Upload Image', 'ultimate-product-option-for-woocommerce' );?>
                            </a>
                            <a class="button <?php echo empty( $image_type ) ? 'upow-d-none' : ''; ?>" id="upow-swatch-term-img-remove-btn">
                                <?php esc_html_e( 'Remove', 'ultimate-product-option-for-woocommerce' );?>
                            </a>
                        </div>
                    </td>
                </tr>
                <?php
break;
            case 'label_type':
                ?>
                <tr class="form-field">
                    <th><label for="term-<?php echo esc_attr( $label_type ) ?>"><?php echo esc_html__( "Label Name", "cvsw" ); ?></label></th>
                    <td><input type="text" id="term-label_type" name="label_type" class="term_label_ecaw" value="<?php echo esc_attr( $label_type ) ?>" /></td>
                </tr>
                <?php
break;
            default:
            }

            ?>
    <style>
        .upow-term-image-preview {
            width: 150px;
            height: 150px;
        }
    </style>
        <script>
            jQuery(document).ready(function($) {
                jQuery('#term-color_type').wpColorPicker();
            });

            jQuery(document).ready(function($) {
                var mediaUploader;

                $('#upow-swatch-term-upload-img-btn').on('click', function(e) {
                    e.preventDefault();

                    if (mediaUploader) {
                        mediaUploader.open();
                        return;
                    }

                    mediaUploader = wp.media.frames.file_frame = wp.media({
                        title: 'Choose Image',
                        button: {
                            text: 'Choose Image'
                        },
                        multiple: false
                    });

                    mediaUploader.on('select', function() {
                        var attachment = mediaUploader.state().get('selection').first().toJSON();
                        $('#upow-swatch-term-img-input').val(attachment.url);
                        $('#upow-term-image-preview').attr('src', attachment.url).show();
                        $('#upow-swatch-term-img-remove-btn').removeClass('upow-d-none');
                    });

                    mediaUploader.open();
                });

                $('#upow-swatch-term-img-remove-btn').on('click', function(e) {
                    e.preventDefault();
                    $('#upow-swatch-term-img-input').val('');
                    $('#upow-term-image-preview').hide();
                    $(this).addClass('upow-d-none');
                });
            });

        </script>

    <?php

        }

    }
}

function cvsw_swatches_for_wc() {
    return Ecaw_Core_Wc::init();
}

// Start plugin
add_action( 'plugins_loaded', 'cvsw_start_plugin' );
if ( !function_exists( 'cvsw_start_plugin' ) ) {

    function cvsw_start_plugin() {
        cvsw_swatches_for_wc();
    }
}

add_action( 'woocommerce_product_option_terms', 'ecaw_attr_select', 10, 3 );

function ecaw_attr_select( $attribute_taxonomy, $i, $attribute ) {

    // do nothing if it is not our custom attribute type

    // get current values
    $options = $attribute->get_options();
    $options = !empty( $options ) ? $options : array();

    ?>
        <select multiple="multiple" data-placeholder="Select color" class="multiselect attribute_values wc-enhanced-select" name="attribute_values[<?php echo $i ?>][]">
            <?php
$colors = get_terms( 'pa_color', array( 'hide_empty' => 0 ) );
    if ( $colors ) {
        foreach ( $colors as $color ) {
            echo '<option value="' . $color->term_id . '"' . wc_selected( $color->term_id, $options ) . '>' . $color->name . '</option>';
        }
    }
    ?>
        </select>
        <button class="button plus select_all_attributes"><?php echo esc_html__( "Select all", "cvsw" ); ?></button>
        <button class="button minus select_no_attributes"><?php echo esc_html__( "Select none", "cvsw" ); ?></button>
    <?php
}

$ecaw_switcher_enable = get_option( 'wc_settings_tab_ecaw_color_variation' );

if ( $ecaw_switcher_enable == 'yes' ) {
    add_filter( 'product_attributes_type_selector', 'ecaw_add_attr_type' );

    function ecaw_add_attr_type( $types ) {

        // let's add a color here!
        $types['color_type'] = 'Color'; // "color_type" // is just a custom slug
        $types['image_type'] = 'Image'; // "color_type" // is just a custom slug
        $types['label_type'] = 'Label'; // "color_type" // is just a custom slug
        return $types;
    }

    function ecaw_save_color( $term_id ) {

        $color_type = !empty( $_POST['color_type'] ) ? $_POST['color_type'] : '';
        update_term_meta( $term_id, 'color_type', sanitize_hex_color( $color_type ) );

        $image_type = !empty( $_POST['image_type'] ) ? $_POST['image_type'] : '';
        update_term_meta( $term_id, 'image_type', esc_url_raw( $image_type ) );

        $label_type = !empty( $_POST['label_type'] ) ? $_POST['label_type'] : '';
        update_term_meta( $term_id, 'label_type', sanitize_text_field( $label_type ) );

    }

    add_filter( 'woocommerce_dropdown_variation_attribute_options_html', 'ecaw_swatches_html', 20, 2 );

    function ecaw_swatches_html($html, $args) {
        global $wpdb;
    
        $taxonomy = $args['attribute'];
        $options = $args['options'];
        $product = $args['product'];
    
        $attribute_type = $wpdb->get_var(
            $wpdb->prepare(
                "
                SELECT attribute_type
                FROM " . $wpdb->prefix . "woocommerce_attribute_taxonomies
                WHERE attribute_name = '%s'
                ",
                substr($taxonomy, 3)
            )
        );
    
        $html = '<div class="upow-variation-default-wrapper">' . $html . '</div>';
    
    
        $terms = wc_get_product_terms($product->get_id(), $taxonomy, array('fields' => 'all'));
    
        $swatches_html = '<div class="upow-swatch-wrapper" data-attribute_name="attribute_' . $taxonomy . '">';
    
        foreach ($terms as $term) {
            
            if (in_array($term->slug, $options)) {
                $hex_color = get_term_meta($term->term_id, 'color_type', true);
                $get_image = get_term_meta($term->term_id, 'image_type', true);
                $label_type = get_term_meta($term->term_id, 'label_type', true);
                $selected = $args['selected'] === $term->slug ? 'selected' : '';

                $enabled_variation = get_enabled_variations($product->get_id(),$taxonomy,$term );

                if(!empty($enabled_variation) && $enabled_variation['enabled_variations'] && empty($enabled_variation['variation_id'])) {
                    continue;
                }
    
                // Check if the term ID matches any enabled variation IDs
               // if (in_array( strtolower( $term->name),$enabled_variation_name)) {
                    switch ($attribute_type) {
                        case 'image_type':
                            if (empty($get_image)) {
                                $get_image = CVSW_WOO_ASSETS . 'assets/images/fallback-placeholder.png';
                            }
                            $image = '<img src="' . esc_url($get_image) . '" alt="variation image"/>';
                            $swatch_type_class = 'upow-variations-image';
                            break;
    
                        case 'color_type':
                            $hex_color = empty($hex_color) ? '#e5e5e5' : $hex_color;
                            $image = sprintf('style="background-color:%s;"', esc_attr($hex_color));
                            $swatch_type_class = 'upow-variations-color';
                            break;
    
                        case 'label_type':
                            $label_text = !empty($label_type) ? $label_type : $term->name;
                            $image = esc_html($label_text);
                            $swatch_type_class = 'upow-variations-label';
                            break;
    
                        default:
                            $image = '';
                            break;
                    }
    
                    $swatches_html .= sprintf(
                        '<span class="upow-swatch-item %s %s" data-title="%s" data-value="%s" %s data-variation_id="%s">%s
                            <span class="upow-variation-tooltip">%s</span>
                        </span>',
                        esc_attr($swatch_type_class),
                        esc_attr($selected),
                        esc_attr($term->name),
                        esc_attr($term->slug),
                        ('color_type' == $attribute_type && !empty($hex_color)) ? $image : '',
                        esc_attr($term->term_id),
                        ('label_type' == $attribute_type || 'image_type' == $attribute_type) ? $image : '',
                        esc_html($term->name)
                    );
                }
            //}
        }
    
        // Additional code for Logo taxonomy
        if ($taxonomy == 'Logo') {
            foreach ($options as $value) {
                $selected = ($value == $args['selected']) ? 'selected' : '';
                $swatch_type_class = 'switch-logo';
                $swatches_html .= sprintf(
                    '<span class="upow-swatch-item %s %s" data-title="%s" data-value="%s">%s
                        <span class="upow-variation-tooltip">%s</span>
                    </span>',
                    esc_attr($swatch_type_class),
                    esc_attr($selected),
                    esc_attr($taxonomy),
                    esc_attr($value),
                    esc_attr($value),
                    esc_attr($value)
                );
            }
        }
    
        return $html . $swatches_html . '</div>';
    }
}
    

    function get_enabled_variations($product_id,$texonomy,$term ) {
        $product = wc_get_product($product_id);

        if (!$product->is_type('variable')) {
            return [];
        }

        $enabled_variations = true;
        $variation_id = '';

        $available_variations = $product->get_available_variations();

        foreach ($product->get_available_variations() as $variation) {
        
            if( isset($variation['attributes']['attribute_' . strtolower($texonomy)])) {
                $attr_name = $variation['attributes']['attribute_' . strtolower($texonomy)];
                if( ( is_object($term) && $term->slug == $attr_name )  || ( is_string($term) && $term == $attr_name) || empty($attr_name) ) {
                    $variation_id = $variation['variation_id'];
                }
                
            } else {
                $enabled_variations = false;
            }
        }

        return array( 
            'enabled_variations' => $enabled_variations,
            'variation_id' => $variation_id
        );
    }


add_action( 'edited_pa_color', 'ecaw_save_color' );

add_action( 'woocommerce_before_variations_form', 'upow_add_custom_class_to_variations_table' );

function upow_add_custom_class_to_variations_table() {
    ob_start(); // Start output buffering
}

add_action( 'woocommerce_after_variations_form', 'upow_modify_variations_table_class' );

function upow_modify_variations_table_class() {
    $html = ob_get_clean(); // Get the buffered output

    // Modify the HTML to add your custom class
    $html = str_replace( 'class="variations"', 'class="variations upow-variation-form"', $html );

    echo $html; // Output the modified HTML
}

function upow_display_swatch_variations( $product_id ) {
    global $product;

    // Ensure $product is defined and is of the correct type.
    if ( !$product || !is_a( $product, 'WC_Product' ) ) {
        $product = wc_get_product( $product_id );
    }

    if ( is_shop() || is_archive() ) {

        if ( $product && $product->is_type( 'variable' ) ) {
            $attributes = $product->get_variation_attributes();
            $available_variations = $product->get_available_variations();
            $switch_html = '';
            $switch_html .= '<form class="variations_form cart"
            action="' . esc_url( apply_filters( 'woocommerce_add_to_cart_form_action', $product->get_permalink() ) ) . '"
            method="post" enctype="multipart/form-data" data-product_id="' . absint( $product->get_id() ) . '"
            data-product_variations="' . htmlspecialchars( wp_json_encode( $available_variations ) ) . '">';
            $switch_html .= '<table class="variations upow-variation-form" cellspacing="0" role="presentation">';
            $switch_html .= '<tbody>';

            foreach ( $attributes as $attribute_name => $options ) {
                $switch_html .= '<tr>';
                $switch_html .= '<td class="value">';

                $selected = isset( $_REQUEST['attribute_' . $attribute_name] )
                ? wc_clean( urldecode( wp_unslash( $_REQUEST['attribute_' . $attribute_name] ) ) )
                : $product->get_variation_default_attribute( $attribute_name );
                ob_start();
                wc_dropdown_variation_attribute_options( array(
                    'options'   => $options,
                    'attribute' => $attribute_name,
                    'product'   => $product,
                    'selected'  => $selected,
                ) );
                $switch_html .= ob_get_clean();

                if ( end( array_keys( $attributes ) ) === $attribute_name ) {
                    $switch_html .= '<a class="reset_variations" href="#">' . esc_html__( 'Clear', 'woocommerce' ) . '</a>';
                }

                $switch_html .= '</td>';
                $switch_html .= '</tr>';
            }

            $switch_html .= '</tbody>';
            $switch_html .= '</table>';
            $switch_html .= '</from>';

            // Add a script to handle the variation changes.

            echo $switch_html;
        }
    }
}

add_action( 'woocommerce_after_shop_loop_item', 'upow_display_swatch_variations', 20 );
