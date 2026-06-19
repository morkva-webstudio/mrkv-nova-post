<?php
/**
 * Plugin Name: Morkva Nova Post
 * Plugin URI: https://morkva.co.ua/
 * Description: Nova Post
 * Version: 0.4.1
 * Author: MORKVA
 * Text Domain: mrkv-nova-post
 * Domain Path: /i18n/
 * Tested up to: 6.9
 * WC requires at least: 8.0
 * WC tested up to: 9.8
 * License: GPL v2 or later
 * License URI: http://www.gnu.org/licenses/gpl-2.0.txt
 */

if ( ! defined( 'WPINC' ) ) {
    die;
}

add_action( 'before_woocommerce_init', function() {
    if ( class_exists( \Automattic\WooCommerce\Utilities\FeaturesUtil::class ) ) {
        \Automattic\WooCommerce\Utilities\FeaturesUtil::declare_compatibility( 'custom_order_tables', __FILE__, true );
    }
} );

# Get plugin data
require_once ABSPATH . 'wp-admin/includes/plugin.php';
$plugData = get_plugin_data(__FILE__,false, false);

# All constants
define('MRKV_NOVA_PLUGIN_PATH', plugin_dir_path(__FILE__));
define('MRKV_NOVA_PLUGIN_VESRION', $plugData['Version']);
define('MRKV_NOVA_PLUGIN_NAME', $plugData['Name']);
define('MRKV_NOVA_PLUGIN_TEXT_DOMAIN', 'mrkv-nova-post');
define('MRKV_NOVA_PLUGIN_URL', plugin_dir_url(__FILE__));

# All shipping methods constants
define('MRKV_NOVA_DELIVERY_SHIPPING_METHOD_ID', 'mrkv-nova-post');
define('MRKV_NOVA_DELIVERY_SHIPPING_METHOD_CLASS', 'MRKV_NOVA_SHIPPING_METHOD');

# Check if Woo plugin activated
if (in_array('woocommerce/woocommerce.php', apply_filters('active_plugins', get_option('active_plugins')))) 
{
	# Include plugin settings
	require_once 'classes/class-mrkv-nova-setup.php'; 

	# Setup plugin settings
	new MRKV_NOVA_SETUP();
}
