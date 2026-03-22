<?php
# Exit if accessed directly
if ( ! defined( 'ABSPATH' ) ) exit; 

# Include woo settings checkout
require_once 'checkout/class-mrkv-nova-checkout-settings.php'; 
# Include woo widgets nova post
require_once 'widgets/class-mrkv-nova-widgets.php'; 

# Check if class exist
if (!class_exists('MRKV_NOVA_WOOCOMMERCE'))
{
	/**
	 * Class for setup plugin woocommerce
	 */
	class MRKV_NOVA_WOOCOMMERCE
	{
		/**
		 * Constructor for woocommerce settings
		 * */
		function __construct()
		{
			# Include new shipping method
			add_action( 'woocommerce_shipping_init', array($this, 'mrkv_nova_include_shipping_method') );

			add_filter( 'woocommerce_shipping_methods', array($this, 'mrkv_nova_add_shipping_method_woo') );

			# Setup checkout settings
			new MRKV_NOVA_CHECKOUT_SETTINGS();

			# Setup nova post woo widgets
			new MRKV_NOVA_WIDGETS();

		}

		/**
		 * Include Nova Post shipping file.
		 */
		public function mrkv_nova_include_shipping_method() 
		{
			# Include Shipping method
		    require_once MRKV_NOVA_PLUGIN_PATH . 'methods/mrkv-nova-shipping-method.php';
		}

		/**
		 * Add Nova Post shipping method class in the shipping list
		 * @param array All shipping methods
		 * 
		 * @return array All shipping methods
		 * */
		public function mrkv_nova_add_shipping_method_woo($methods)
		{
			# Add new shipping method
			$methods[MRKV_NOVA_DELIVERY_SHIPPING_METHOD_ID] = MRKV_NOVA_DELIVERY_SHIPPING_METHOD_CLASS;

			# Return all methods
  			return $methods;
		}
	}
}