<?php
# Exit if accessed directly
if ( ! defined( 'ABSPATH' ) ) exit; 

# Include nova post woo checkout content
require_once 'class-mrkv-nova-checkout.php'; 
# Include woo checkout validation
require_once 'class-mrkv-nova-checkout-validation.php'; 
# Include woo save orders data
require_once 'class-mrkv-nova-order.php'; 

# Check if class exist
if (!class_exists('MRKV_NOVA_CHECKOUT_SETTINGS'))
{
	/**
	 * Class for setup plugin setup checkout settings
	 */
	class MRKV_NOVA_CHECKOUT_SETTINGS
	{
		/**
		 * Constructor for plugin checkout settings
		 * */
		function __construct()
		{
			# Setup woo checkout content
			new MRKV_NOVA_CHECKOUT();
			
			# Setup woo checkout validation
			new MRKV_NOVA_CHECKOUT_VALIDATION();

			# Setup woo checkout save orders data
			new MRKV_NOVA_ORDER();
		}
	}
}