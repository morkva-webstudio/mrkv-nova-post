<?php
# Check if class exist
if (!class_exists('MRKV_NOVA_OPTIONS'))
{
	/**
	 * Class for setup plugin setup options
	 */
	class MRKV_NOVA_OPTIONS
	{
		/**
		 * Constructor for plugin settings options
		 * */
		function __construct()
		{
			# Register settings
			add_action('admin_init', array($this, 'mrkv_nova_register_settings'));
		}

		/**
		 * Register plugin options
		 * 
		 * */
	    public function mrkv_nova_register_settings()
	    {
	    	# List of plugin options
	        $options = array(
	            'mrkv_nova_api_server',
	            'mrkv_nova_api_token',
	            'mrkv_nova_delivery',
	        );

	        # Loop of option
	        foreach ($options as $option) 
	        {
	        	# Register option
	            register_setting('mrkv-nova-settings-group', $option);
	        }
	    }
	}
}