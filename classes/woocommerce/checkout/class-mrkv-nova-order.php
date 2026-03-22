<?php
# Check if class exist
if (!class_exists('MRKV_NOVA_ORDER'))
{
	/**
	 * Class for setup plugin woocommerce order
	 */
	class MRKV_NOVA_ORDER
	{
		/**
		 * Constructor for woocommerce order
		 * */
		function __construct()
		{
			add_action('woocommerce_checkout_create_order', array($this, 'mrkv_nova_create_order'));
		}

		/**
		 * Add field nova post to order
		 * @param object Order
		 * */
		public function mrkv_nova_create_order($order)
	    {
	    	# Check if isset shipping method
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

	        # Set billing_country field value if it is absent on Checkout page
	        $_POST['billing_country'] = isset( $_POST['billing_country'] ) ? sanitize_text_field($_POST['billing_country']) : 'UA';

	        $chosen_warehouse_address = '';
		    $chosen_warehouse_id = '';
		    $chosen_warehouse_number = '';
		    $chosen_warehouse_city = '';
		    $chosen_warehouse_region = '';

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

         	if ( isset( $_POST[MRKV_NOVA_DELIVERY_SHIPPING_METHOD_ID . '_warehouse_selected_city'] ) ) 
         	{
         		# Get choosen city
	         	$chosen_warehouse_city = sanitize_text_field($_POST[MRKV_NOVA_DELIVERY_SHIPPING_METHOD_ID . '_warehouse_selected_city']);
         	}

         	if ( isset( $_POST[MRKV_NOVA_DELIVERY_SHIPPING_METHOD_ID . '_warehouse_selected_region'] ) ) 
         	{
         		# Get choosen city
	         	$chosen_warehouse_region = sanitize_text_field($_POST[MRKV_NOVA_DELIVERY_SHIPPING_METHOD_ID . '_warehouse_selected_region']);
         	}

         	# Add billing and shipping city name
         	$order->set_billing_city( $chosen_warehouse_city );
         	$order->set_shipping_city( $chosen_warehouse_city );

         	# Add billing and shipping region name
         	$order->set_billing_state( $chosen_warehouse_region );
         	$order->set_shipping_state( $chosen_warehouse_region );

         	# Add billing and shipping address name
         	$order->set_billing_address_1( $chosen_warehouse_address );
         	$order->set_shipping_address_1( $chosen_warehouse_address );

         	# Add billing and shipping address name
         	$order->set_billing_address_1( $chosen_warehouse_address );
         	$order->set_shipping_address_1( $chosen_warehouse_address );

         	# Add billing and shipping number name
         	$order->set_billing_address_2($chosen_warehouse_number );
            $order->set_shipping_address_2($chosen_warehouse_number);

            # Add billing and shipping id name
         	$order->set_shipping_postcode($chosen_warehouse_id );
            $order->set_billing_postcode($chosen_warehouse_id);

            # Save data
	        $order->save();
	    }
	}
}