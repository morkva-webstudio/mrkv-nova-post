<?php
# Check if class exist
if (!class_exists('MRKV_NOVA_CHECKOUT'))
{
	/**
	 * Class for setup plugin checkout data
	 */
	class MRKV_NOVA_CHECKOUT
	{
		/**
		 * Constructor for plugin checkout data
		 * */
		function __construct()
		{
			add_filter('body_class', array($this, 'mrkv_nova_active_body_class'));
		    add_action('wp_enqueue_scripts', array($this, 'mrkv_nova_inject_scripts'));
		    add_action('woocommerce_after_checkout_billing_form', array($this, 'mrkv_nova_inject_shipping_fields'));
		    add_filter('woocommerce_package_rates', array($this, 'mrkv_nova_post_country_filter'), 10, 2);

		    add_action( 'wp_ajax_novapost_warehouse_autocomplete', array($this, 'get_novapost_warehouse'));
			add_action( 'wp_ajax_nopriv_novapost_warehouse_autocomplete', array($this, 'get_novapost_warehouse'));
		}

		/**
		 * @param array Rates
		 * @param array Package
		 * @return array Rates
		 * */
		public function mrkv_nova_post_country_filter($rates, $package)
		{
			$allowed_countries = array('CZ', 'DE', 'EE', 'HU', 'LT', 'LV', 'MD', 'PL', 'RO', 'SK', 'UA');

			$country = $package['destination']['country'];

			if (!in_array($country, $allowed_countries)) {
		        foreach ($rates as $rate_id => $rate) {
		            if ($rate->id === 'mrkv-nova-post') {
		                unset($rates[$rate_id]);
		            }
		        }
		    }

		    return $rates;
		}

		/**
		 * Check morkva plugin activation
		 * @param array Classes list
		 * */
		public function mrkv_nova_active_body_class($classes) 
		{
	        # Add CSS-class mrkvnp-plugin-is-active if PRO-НП is active
	        include_once( ABSPATH . 'wp-admin/includes/plugin.php' );

	        # Check class exist
	        if ( is_plugin_active( 'nova-poshta-ttn-pro/nova-poshta-ttn-pro.php' ) || is_plugin_active( 'woo-ukrposhta-pro/morkvaup-plugin.php' ) || is_plugin_active( 'rozetka-delivery/rozetka-delivery.php' )) 
	        {
	            $classes[] = 'mrkvnp-plugin-is-active';
	        }

	        # Return all class
	        return $classes;
	    }

	    /**
	     * Add scripts data
	     * */
	    public function mrkv_nova_inject_scripts()
	    {
	    	# Check checkout page
	    	if ( ! is_checkout()) 
	    	{
			  return;
		  	}

		  	# Add the Select2 CSS file
    		wp_enqueue_style( 'selectWoocss',MRKV_NOVA_PLUGIN_URL . 'assets/css/selectWoo.min.css', array(), MRKV_NOVA_PLUGIN_VESRION );

    		wp_enqueue_style('mrkv-nova-css', MRKV_NOVA_PLUGIN_URL . 'assets/css/style.css', null, MRKV_NOVA_PLUGIN_VESRION );

    		# Add the Select2 JS file
    		wp_enqueue_script( 'selectWoojs', MRKV_NOVA_PLUGIN_URL . 'assets/js/selectWoo.js', array( 'jquery' ), MRKV_NOVA_PLUGIN_VESRION, true );

    		# Include main script
		  	wp_enqueue_script('mrkv-nova-js', MRKV_NOVA_PLUGIN_URL . 'assets/js/mrkv-nova-post.js',  [ 'jquery', 'jquery-ui-autocomplete' ], MRKV_NOVA_PLUGIN_VESRION, true);

		  	# Add custom fields data
		  	wp_localize_script('mrkv-nova-js', 'mrkv_nova_globals', [
		      'ajaxUrl'                     => admin_url('admin-ajax.php'),
		      'mrkvnovanonce'                 => wp_create_nonce('mrkv_nova_ajax_nonce'),
		      'homeUrl'                     => home_url(),
		    ]);
	    }

	    /**
	     * Add nova post checkout fields
	     * */
	    public function mrkv_nova_inject_shipping_fields()
	    {
	    	# Check checkout page
	    	if ( ! is_checkout()) 
	    	{
			    return;
		    }

		    ?>
		    	<div id="<?php echo esc_html(MRKV_NOVA_DELIVERY_SHIPPING_METHOD_ID); ?>_fields" class="<?php echo esc_html(MRKV_NOVA_DELIVERY_SHIPPING_METHOD_ID); ?>-fields">
		        	<div id="<?php echo esc_html(MRKV_NOVA_DELIVERY_SHIPPING_METHOD_ID); ?>-shipping-info">
		        		<?php 
		        			// City select
				          woocommerce_form_field(MRKV_NOVA_DELIVERY_SHIPPING_METHOD_ID . '_warehouse_select', [
				            'type' => 'text',
				            'autocomplete' => 'on',
				            'required'  => 'true',
				            'input_class' => [
				              MRKV_NOVA_DELIVERY_SHIPPING_METHOD_ID . '-select'
				            ],
				            'label' => esc_html__('Warehouse', 'mrkv-nova-post'),
				            'placeholder' => esc_html__( 'Enter three or more letters of the city name', 'mrkv-nova-post' ),
				          ]);

				          // Selected city id
				          woocommerce_form_field(MRKV_NOVA_DELIVERY_SHIPPING_METHOD_ID . '_warehouse_selected_id', [
				            'type' => 'hidden',
				            'autocomplete' => 'on',
				            'input_class' => [
				              MRKV_NOVA_DELIVERY_SHIPPING_METHOD_ID . '-select'
				            ],
				          ]);

				          // Selected city number
				          woocommerce_form_field(MRKV_NOVA_DELIVERY_SHIPPING_METHOD_ID . '_warehouse_selected_number', [
				            'type' => 'hidden',
				            'autocomplete' => 'on',
				            'input_class' => [
				              MRKV_NOVA_DELIVERY_SHIPPING_METHOD_ID . '-select'
				            ],
				          ]);

				          // Selected city number
				          woocommerce_form_field(MRKV_NOVA_DELIVERY_SHIPPING_METHOD_ID . '_warehouse_selected_city', [
				            'type' => 'hidden',
				            'autocomplete' => 'on',
				            'input_class' => [
				              MRKV_NOVA_DELIVERY_SHIPPING_METHOD_ID . '-select'
				            ],
				          ]);

				           // Selected city number
				          woocommerce_form_field(MRKV_NOVA_DELIVERY_SHIPPING_METHOD_ID . '_warehouse_selected_region', [
				            'type' => 'hidden',
				            'autocomplete' => 'on',
				            'input_class' => [
				              MRKV_NOVA_DELIVERY_SHIPPING_METHOD_ID . '-select'
				            ],
				          ]);
		        		?>
	        		</div>
        		</div>
		    <?php
	    }

	    /**
	     * Get city by name
	     * */
	    public function get_novapost_warehouse()
	    {
	    	# Check for nonce security
			if ( ! wp_verify_nonce( sanitize_text_field(wp_unslash($_POST['mrkvnovanonce'])), 'mrkv_nova_ajax_nonce' ) ) 
			{
				wp_die('Permission Denied.');
			}
			
			# User three-letter input in Checkout
			$novapost_term_suggestion = sanitize_text_field($_POST['term']); 
			$country = sanitize_text_field($_POST['mrkvup_country_suggestion']); 

			# Found city data
			$city_arr = array(); 

			$apikey = get_option('mrkv_nova_api_token');
			$apiurl_type = get_option('mrkv_nova_api_server');
			$apiurl = '';
			
			if($apiurl_type == 'production')
			{
				$apiurl = 'https://api.novapost.com/v.1.0/';
			}
			else
			{
				$apiurl = 'https://api-stage.novapost.pl/v.1.0/';
			}

			# Send request
			$token_json = wp_remote_get($apiurl . 'clients/authorization?apiKey=' . $apikey, [
			    'headers' => [
			      
			    ],
			  'timeout' => 30
			]);

			$token = json_decode($token_json['body'], true);

			# Send request
			$cities = wp_remote_get($apiurl . 'divisions?countryCodes[]=' . $country . '&limit=100&textSearch=' . $novapost_term_suggestion, [
			    'headers' => [
			      'Authorization' => $token['jwt']
			    ],
			  'timeout' => 30
			]);

			$city_body = json_decode($cities['body'], true);

			$city_output = array();

			if(isset($city_body['items']))
			{
				foreach($city_body['items'] as $city){

					$city_output['response'][] = array(
						"label" => $city['address'],
						"value" => $city['id'],
					);
					$city_output['response_val'][$city['id']] = array(
						"number" => $city['number'],
						"city" => $city['settlement']['name'],
						"region" => $city['settlement']['region']['name'],
					);
				}
			}

			echo wp_json_encode( $city_output );
			wp_die();
	    }
	}
}