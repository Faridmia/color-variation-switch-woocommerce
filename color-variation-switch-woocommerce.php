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
if (!defined('WPINC')) {
    die;
}

# Defines CONSTANTS for Whole plugins.

define('CVSW_WOO_FILE', __FILE__);
define('CVSW_WOO_VERSION', '1.0.0');
define('CVSW_WOO_URL', plugins_url('/', __FILE__));
define('CVSW_WOO_PATH', plugin_dir_path(__FILE__));
define('CVSW_WOO_DIR_URL', plugin_dir_url(__FILE__));
define('CVSW_WOO_BASENAME', plugin_basename(__FILE__));
define('CVSW_WOO_ASSETS', CVSW_WOO_URL);
define('CVSW_WOO_ASSETS_PATH', CVSW_WOO_PATH);


#Admin notice when not WooCommerce plugin not acitvated

function cvsw_admin_notices()
{ ?>
    <div class="error">
        <p><?php _e('<strong>Color Switcher WooCommerce requires WooCommerce to be installed and active. You can download <a href="https://woocommerce.com/" target="_blank">WooCommerce</a> here.</strong>', 'cvsw'); ?></p>
    </div>
<?php
}

# WooCommerce plugin dependency

function cvsw_install_woocommerce_dependency()
{
    if (!function_exists('WC')) {
        add_action('admin_notices', 'cvsw_admin_notices');
    }
}

add_action('plugins_loaded',  'cvsw_install_woocommerce_dependency', 99);

# Enqueue scripts

add_action('admin_enqueue_scripts', 'cvsw_product_addons_scripts');
add_action('wp_enqueue_scripts', 'cvsw_product_addon_script', 99);
add_action('init', 'cvsw_i18n');






# plugin init function 
function cvsw_i18n()
{
    load_plugin_textdomain('cvsw', false, dirname(plugin_basename(CVSW_WOO_FILE)) . '/i18n/');
}

function cvsw_product_addons_scripts()
{
    wp_enqueue_style('wp-color-picker');
    wp_enqueue_script('wp-color-picker');
}

function cvsw_product_addon_script()
{
    wp_enqueue_style('ecaw-color-front-css-switcher', CVSW_WOO_URL . 'assets/css/style.css', [], time());
    wp_enqueue_script('ecaw-color-switcher-js', CVSW_WOO_URL . 'assets/js/ecaw-color-switcher.js', array('jquery'), time(), true);
    wp_enqueue_style('wp-color-picker');
    wp_enqueue_script('wp-color-picker');
}

add_filter('plugin_action_links_' . CVSW_WOO_BASENAME,  'cvsw_action_links');

function cvsw_action_links($links)
{
    $custom_links = array('<a href="' . admin_url('admin.php?page=wc-settings&tab=settings_tab_ecaw') . '">' . __('Settings', 'woocommerce') . '</a>');

    return array_merge($custom_links, $links);
}


if (!class_exists('Ecaw_Core_Wc')) {

    class Ecaw_Core_Wc
    {


        /* Bootstraps the class and hooks required actions & filters.
        *
        */
        public static function init()
        {
            add_filter('woocommerce_settings_tabs_array', __CLASS__ . '::add_settings_tab', 50);
            add_action('woocommerce_settings_tabs_settings_tab_ecaw', __CLASS__ . '::settings_tab');
            add_action('woocommerce_update_options_settings_tab_ecaw', __CLASS__ . '::update_settings');
        }


        /* Add a new settings tab to the WooCommerce settings tabs array.
        *
        * @param array $settings_tabs Array of WooCommerce setting tabs & their labels, excluding the Subscription tab.
        * @return array $settings_tabs Array of WooCommerce setting tabs & their labels, including the Subscription tab.
        */
        public static function add_settings_tab($settings_tabs)
        {
            $settings_tabs['settings_tab_ecaw'] = __('ECAW Variation Switches', 'woocommerce-settings-tab-demo');
            return $settings_tabs;
        }


        /* Uses the WooCommerce admin fields API to output settings via the @see woocommerce_admin_fields() function.
        *
        * @uses woocommerce_admin_fields()
        * @uses self::get_settings()
        */
        public static function settings_tab()
        {
            woocommerce_admin_fields(self::get_settings());
        }


        /* Uses the WooCommerce options API to save settings via the @see woocommerce_update_options() function.
        *
        * @uses woocommerce_update_options()
        * @uses self::get_settings()
        */
        public static function update_settings()
        {
            woocommerce_update_options(self::get_settings());
        }


        /* Get all the settings for this plugin for @see woocommerce_admin_fields() function.
        *
        * @return array Array of settings for @see woocommerce_admin_fields() function.
        */
        public static function get_settings()
        {

            $settings = array(
                'section_title' => array(
                    'name'     => __('Color Switcher Opiton', 'cvsw'),
                    'type'     => 'title',
                    'desc'     => '',
                    'id'       => 'wc_settings_tab_demo_section_title'
                ),
                'title' => array(
                    'name' => __('Switcher Enable', 'cvsw'),
                    'type' => 'checkbox',
                    'desc' => __('Enable "Color Variation Swatches for WooCommerce" plugin ', 'cvsw'),
                    'id'   => 'wc_settings_tab_ecaw_color_variation'
                ),
                'section_end' => array(
                    'type' => 'sectionend',
                    'id' => 'wc_settings_tab_demo_section_end'
                )
            );

            return apply_filters('wc_settings_tab_demo_settings', $settings);
        }
    }
}


function cvsw_swatches_for_wc()
{
    return Ecaw_Core_Wc::init();
}


// Start plugin
add_action('plugins_loaded', 'cvsw_start_plugin');
if (!function_exists('cvsw_start_plugin')) {

    function cvsw_start_plugin()
    {
        cvsw_swatches_for_wc();
    }
}


add_filter('product_attributes_type_selector', 'ecaw_add_attr_type');

function ecaw_add_attr_type($types)
{

    // let's add a color here!
    $types['color_type'] = 'Color'; // "color_type" // is just a custom slug
    return $types;
}


add_action('pa_color_edit_form_fields', 'ecaw_edit_fields', 10, 2);

function ecaw_edit_fields($term, $taxonomy)
{

    // do nothing if this term isn't the Color type
    global $wpdb;

    $attribute_type = $wpdb->get_var(
        $wpdb->prepare(
            "
			SELECT attribute_type
			FROM " . $wpdb->prefix . "woocommerce_attribute_taxonomies
			WHERE attribute_name = '%s'
			",
            substr($taxonomy, 3) // remove "pa_" prefix
        )
    );

    // if it is not a color attribute, just do nothing
    if ('color_type' !== $attribute_type) {
        return;
    }

    // otherwise let's display our colorpicker field
    // we can use attribute type as a meta key why not
    $color = get_term_meta($term->term_id, 'color_type', true);

?>
    <tr class="form-field">
        <th><label for="term-color_type"><?php echo esc_html__("Color", "cvsw"); ?></label></th>
        <td><input type="text" id="term-color_type" name="color_type" class="term_color_ecaw" value="<?php echo esc_attr($color) ?>" /></td>
    </tr>

    <script>
        jQuery(document).ready(function($) {
            jQuery('#term-color_type').wpColorPicker();
        });
    </script>

<?php

}

add_action('edited_pa_color', 'ecaw_save_color');
function ecaw_save_color($term_id)
{

    $color_type = !empty($_POST['color_type']) ? $_POST['color_type'] : '';
    update_term_meta($term_id, 'color_type', sanitize_hex_color($color_type));
}



add_action('woocommerce_product_option_terms', 'ecaw_attr_select', 10, 3);

function ecaw_attr_select($attribute_taxonomy, $i, $attribute)
{

    // do nothing if it is not our custom attribute type
    if ('color_type' !== $attribute_taxonomy->attribute_type) {
        return;
    }

    // get current values
    $options = $attribute->get_options();
    $options = !empty($options) ? $options : array();

?>
    <select multiple="multiple" data-placeholder="Select color" class="multiselect attribute_values wc-enhanced-select" name="attribute_values[<?php echo $i ?>][]">
        <?php
        $colors = get_terms('pa_color', array('hide_empty' => 0));
        if ($colors) {
            foreach ($colors as $color) {
                echo '<option value="' . $color->term_id . '"' . wc_selected($color->term_id, $options) . '>' . $color->name . '</option>';
            }
        }
        ?>
    </select>
    <button class="button plus select_all_attributes"><?php echo esc_html__("Select all", "cvsw"); ?></button>
    <button class="button minus select_no_attributes"><?php echo esc_html__("Select none", "cvsw"); ?></button>
<?php
}


add_filter('woocommerce_dropdown_variation_attribute_options_html', 'ecaw_swatches_html', 20, 2);

function ecaw_swatches_html($html, $args)
{

    global $wpdb;

    $taxonomy = $args['attribute'];
    $product = $args['product'];

    $attribute_type = $wpdb->get_var(
        $wpdb->prepare(
            "
			SELECT attribute_type
			FROM " . $wpdb->prefix . "woocommerce_attribute_taxonomies
			WHERE attribute_name = '%s'
			",
            substr($taxonomy, 3) // remove "pa_" prefix
        )
    );

    // if it is not a color attribute, just do nothing
    if ('color_type' !== $attribute_type) {
        return $html;
    }

    // the thing is that we do not remove original dropdown, just hide it
    $html = '<div style="display:none">' . $html . '</div>';

    // then we display the swatches

    // in order to do so we loop all attributes in a taxonomy
    $colors = wc_get_product_terms($product->get_id(), $taxonomy);

    // echo "<pre>";
    // print_r($colors);
    // echo "</pre>";
    foreach ($colors as $color) {
        if (in_array($color->slug, $args['options'])) {
            // get the value of a color picker actually
            $hex_color = get_term_meta($color->term_id, 'color_type', true);
            // add class for a selected color swatch
            $selected = $args['selected'] === $color->slug ? 'color-selected' : '';

            $html .= sprintf(
                '<span class="swatch %s" style="background-color:%s;" title="%s" data-value="%s"></span>',
                $selected,
                $hex_color,
                $color->name,
                $color->slug
            );
        }
    }

    return $html;
}
