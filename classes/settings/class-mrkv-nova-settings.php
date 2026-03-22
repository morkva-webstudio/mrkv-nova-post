<?php
# Exit if accessed directly
if ( ! defined( 'ABSPATH' ) ) exit; 

# Include nova post options
require_once 'class-mrkv-nova-options.php';
# Include nova post admin assets
require_once 'class-mrkv-nova-admin-assets.php'; 
# Include nova post menu
require_once 'class-mrkv-nova-menu.php'; 

# Check if class exist
if (!class_exists('MRKV_NOVA_SETTINGS'))
{
	/**
	 * Class for setup plugin settings
	 */
	class MRKV_NOVA_SETTINGS
	{
		/**
		 * Constructor for plugin settings
		 * */
		function __construct()
		{
			# Setup woo plugin options
			new MRKV_NOVA_OPTIONS();

			# Setup woo plugin admin assets
			new MRKV_NOVA_ADMIN_ASSETS();

			# Setup woo plugin menu
			new MRKV_NOVA_MENU();
		}
	}
}