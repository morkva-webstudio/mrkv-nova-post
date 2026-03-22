<?php
# Exit if accessed directly
if ( ! defined( 'ABSPATH' ) ) exit; 

# Include nova post menu
require_once 'settings/class-mrkv-nova-settings.php'; 
# Include woo settings
require_once 'woocommerce/class-mrkv-nova-woocommerce.php'; 

# Check if class exist
if (!class_exists('MRKV_NOVA_SETUP'))
{
	/**
	 * Class for setup plugin setup
	 */
	class MRKV_NOVA_SETUP
	{
		/**
		 * Constructor for plugin settings
		 * */
		function __construct()
		{
			# Setup woo plugin settings
			new MRKV_NOVA_SETTINGS();
			
			# Setup woo plugin settings woocommerce
			new MRKV_NOVA_WOOCOMMERCE();
		}
	}
}