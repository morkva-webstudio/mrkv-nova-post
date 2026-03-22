<?php
# Check if class exist
if (!class_exists('MRKV_NOVA_CHECKOUT_VALIDATION'))
{
	/**
	 * Class for checkout validation
	 */
	class MRKV_NOVA_CHECKOUT_VALIDATION
	{
		/**
		 * Constructor for checkout validation
		 * */
		function __construct()
		{
			# Validate nova post field
			add_action('woocommerce_checkout_process', array($this, 'mrkv_nova_validate_fields'));
		    add_filter('woocommerce_checkout_fields', array($this, 'mrkv_nova_remove_default_fields_from_validation'));
		    add_filter('woocommerce_checkout_posted_data', array($this, 'mrkv_nova_process_checkout_posted_data'));
		}

		/**
		 * Validate all Nova Post field
		 * */
		public function mrkv_nova_validate_fields()
	    {
	    	# Check current shipping
		    if ( isset( $_POST['shipping_method'],  $_POST['woocommerce-process-checkout-nonce']) && wp_verify_nonce( sanitize_key( $_POST['woocommerce-process-checkout-nonce'] ), 'woocommerce-process_checkout' )) 
		    {
		    	# Stop job
	        	if ( strpos( $_POST['shipping_method'][0], MRKV_NOVA_DELIVERY_SHIPPING_METHOD_ID ) === false) return;
	    	}
		    else
		    {
		    	# Stop job
		      	return;
		    }

		    $chosen_warehouse_address = '';
		    $chosen_warehouse_id = '';
		    $chosen_warehouse_number = '';

		    if ( isset( $_POST[MRKV_NOVA_DELIVERY_SHIPPING_METHOD_ID . '_warehouse_select'] ) ) 
         	{
         		# Get choosen city
	         	$chosen_warehouse_address = sanitize_text_field($_POST[MRKV_NOVA_DELIVERY_SHIPPING_METHOD_ID . '_warehouse_select']);
         	}

         	if ( isset( $_POST[MRKV_NOVA_DELIVERY_SHIPPING_METHOD_ID . '_warehouse_selected_id'] ) ) 
         	{
         		# Get choosen city
	         	$chosen_warehouse_id = sanitize_text_field($_POST[MRKV_NOVA_DELIVERY_SHIPPING_METHOD_ID . '_warehouse_selected_id']);
         	}

         	if ( isset( $_POST[MRKV_NOVA_DELIVERY_SHIPPING_METHOD_ID . '_warehouse_selected_number'] ) ) 
         	{
         		# Get choosen city
	         	$chosen_warehouse_number = sanitize_text_field($_POST[MRKV_NOVA_DELIVERY_SHIPPING_METHOD_ID . '_warehouse_selected_number']);
         	}

         	if(!$chosen_warehouse_address || !$chosen_warehouse_id || !$chosen_warehouse_number)
         	{
         		wc_add_notice(__('Fill in the shipping information', 'mrkv-nova-post'), 'error');
         	}
	    }

	    /**
	     * Remove not needed fields
	     * @param array Fields
	     * 
	     * @return array Fields
	     * */
	    public function mrkv_nova_remove_default_fields_from_validation($fields)
  		{
  			# Check disable fields
		    if ($this->mrkv_nova_maybe_disable_default_fields()) 
		    {
		    	# Disable all fields
		        unset($fields['billing']['billing_address_1']);
		        unset($fields['billing']['billing_address_2']);
		        unset($fields['billing']['billing_city']);
		        unset($fields['billing']['billing_state']);
		        unset($fields['billing']['billing_postcode']);
		    }

		    # Return fields
		    return $fields;
		}

		/**
		 * Check on disabled fields
		 * 
		 * @return bool Answer
		 * */
		private function mrkv_nova_maybe_disable_default_fields()
		{
		    return (isset( $_POST['shipping_method'],  $_POST['woocommerce-process-checkout-nonce']) && wp_verify_nonce( sanitize_key( $_POST['woocommerce-process-checkout-nonce'] ), 'woocommerce-process_checkout' )) &&
		    preg_match('/^' . MRKV_NOVA_DELIVERY_SHIPPING_METHOD_ID . '.*/i', sanitize_text_field($_POST['shipping_method'][0]));
		}

		/**
		 * Unset fields by checkout process
		 * */
		public function mrkv_nova_process_checkout_posted_data($data)
		{
			# Disable ship to another address
		    add_filter( 'woocommerce_ship_to_different_address_checked', '__return_false' );

		    # Check shipping method
			if (isset($data['shipping_method'])) 
			{
				# Check shipping method name
				if (preg_match('/^' . MRKV_NOVA_DELIVERY_SHIPPING_METHOD_ID . '.*/i', $data['shipping_method'][0]) &&
					  isset($_POST['ship_to_different_address'], $_POST['woocommerce-process-checkout-nonce']) && wp_verify_nonce( sanitize_key( $_POST['woocommerce-process-checkout-nonce'] ), 'woocommerce-process_checkout' )) 
				{
					# Disable all fields
				  	unset($data['ship_to_different_address']);
				  	unset($data['shipping_first_name']);
				    unset($data['shipping_last_name']);
				    unset($data['shipping_company']);
				    unset($data['shipping_country']);
				    unset($data['shipping_address_1']);
				    unset($data['shipping_address_2']);
				    unset($data['shipping_city']);
				    unset($data['shipping_state']);
				    unset($data['shipping_postcode']);
				}
			}

			# Return data
		  	return $data;
		}
	}
}