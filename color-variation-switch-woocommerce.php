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

add_action('plugins_loaded',  'cvsw_install_woocommerce_dependency');

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
}

function cvsw_product_addon_script()
{
}



?>