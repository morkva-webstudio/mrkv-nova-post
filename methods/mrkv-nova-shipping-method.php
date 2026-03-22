<?php
# Check user access
defined( 'ABSPATH' ) || exit;

# Check if class exist
if (!class_exists('MRKV_NOVA_SHIPPING_METHOD'))
{
    /**
     * Add new delivery method
     * */
    class MRKV_NOVA_SHIPPING_METHOD extends WC_Shipping_Method 
    {
        /**
         * Constructor new shipping method
         * */
        public function __construct($instance_id = 0) 
        {
            $this->instance_id = absint( $instance_id );
            parent::__construct( $instance_id );

            # These title description are display on the configuration page
            $this->id = MRKV_NOVA_DELIVERY_SHIPPING_METHOD_ID;
            $this->method_title = __('Nova Post Delviery Point', 'mrkv-nova-post');
            $this->method_description = __('Nova Post Delivery Point method', 'mrkv-nova-post');

            # Add support zones
            $this->supports = array(
                'shipping-zones',
                'instance-settings',
                'instance-settings-modal',
            );
            
            # Run the initial method
            $this->init();

            # Set title
            $this->title = $this->get_option( 'title' );

            # Enabled method
            $this->enabled = $this->get_option('enabled');
            if (empty($this->enabled)) {
                $this->enabled = 'yes';
            }
            
        }

        /**
         * Load the settings API
         * */
        public function init() 
        {
            # Load the settings API
            $this->init_settings();

            # Add the form fields
            $this->init_form_fields();

            # Save settings in admin if you have any defined
            add_action('woocommerce_update_options_shipping_' . $this->id, array($this, 'process_admin_options'));
        }

        /**
         * Initialize all shipping fields
         * */
        public function init_form_fields() 
        {
             $this->instance_form_fields = array(
                'title' => array(
                    'title' => $this->method_title,
                    'type' => 'text',
                    'description' => $this->method_description,
                    'default' => $this->method_title
                ),
                'enable_cost' => array(
                    'title' => __('Enable Price for Delivery', 'mrkv-nova-post'),
                    'label' => __('If checked, shipping price will be add for delivery', 'mrkv-nova-post'),
                    'type' => 'checkbox',
                    'default' => 'no',
                    'description' => '',
                ),
                'enable_fix_cost' => array(
                    'title' => __('Enable Fixed Price for Delivery', 'mrkv-nova-post'),
                    'label' => __('If checked, fixed price will be set for delivery', 'mrkv-nova-post'),
                    'type' => 'checkbox',
                    'default' => 'no',
                    'description' => '',
                ),
                'fix_cost_total' => array(
                    'title' => __('Fixed shipping price', 'mrkv-nova-post'),
                    'type' => 'text',
                    'placeholder' => __('Enter the amount in numbers', 'mrkv-nova-post'),
                    'description' => '',
                    'default' => 0.00
                ),
                'enable_minimum_cost' => array(
                    'title' => __('Enable Minimum amount for free shipping', 'mrkv-nova-post'),
                    'label' => __('If checked, Minimum amount for free shipping will be set for delivery', 'mrkv-nova-post'),
                    'type' => 'checkbox',
                    'default' => 'no',
                    'description' => '',
                ),
                'minimum_cost_total' => array(
                    'title' => __('Minimum amount for free shipping', 'mrkv-nova-post'),
                    'type' => 'text',
                    'placeholder' => __('Enter the amount in numbers', 'mrkv-nova-post'),
                    'description' => '',
                    'default' => 0.00
                ),
                'free_shipping_text' => array(
                    'title' => __('Text with free delivery', 'mrkv-nova-post'),
                    'type' => 'text',
                    'placeholder' => __('FREE to Nova Post', 'mrkv-nova-post'),
                    'description' => '',
                )
            );
        }

        /**
         * Add rate to delivery
         * @param array Package
         * */
        public function calculate_shipping( $package = array() ) 
        {
            # Create rate
            $rate = array(
                'id' => $this->id,
                'label' => $this->title,
                'cost' => 0.00,
                'calc_tax' => 'per_item'
            );
            if($this->get_option('enable_cost') && $this->get_option('enable_cost') == 'yes' && $this->get_option('enable_fix_cost') != 'yes')
            {
                $apikey = get_option('mrkv_nova_api_token');
                $apiurl_type = get_option('mrkv_nova_api_server');
                $delivery_data = get_option('mrkv_nova_delivery');

                $payer_type = isset($delivery_data['payer_type']) ? $delivery_data['payer_type'] : 'Recipient';
                $payer_contact_number = isset($delivery_data['payer_contect_number']) ? $delivery_data['payer_contect_number'] : '';
                $sender_division_id = isset($delivery_data['division_id']) ? $delivery_data['division_id'] : '';
                $country = 'PL';
                $recipient_division_id = '';
                $weight = 0.00;
                $length = 0.00;
                $cost = WC()->cart->get_subtotal();
                $cargo = isset($delivery_data['cargo_category']) ? $delivery_data['cargo_category'] : 'Parcel';
                $sender_country = isset($delivery_data['sender_country']) ? $delivery_data['sender_country'] : 'UA';

                if(isset( $_POST['post_data'] ))
                {
                    parse_str( $_POST['post_data'], $post_data );
                    
                    if(isset($post_data['billing_country']) && $post_data['billing_country'])
                    {
                        $country = $post_data['billing_country'];
                    }

                    if(isset($post_data['mrkv-nova-post_warehouse_selected_id']) && $post_data['mrkv-nova-post_warehouse_selected_id'])
                    {
                        $recipient_division_id = $post_data['mrkv-nova-post_warehouse_selected_id'];
                    }
                }

                if(isset($delivery_data['shipment']['cart_total']) && $delivery_data['shipment']['cart_total'] == 'total')
                {
                    $cost = WC()->cart->cart_contents_total;
                }

                $volume_weight = 0.00;
                $dimension_unit = get_option( 'woocommerce_dimension_unit' );
                $cargo_item_length = 0.00;
                $cargo_item_width = 0.00;
                $cargo_item_height = 0.00;
                foreach(WC()->cart->get_cart() as $cart_item => $cart_value)
                {
                    $item_length = ( null !== $cart_value['data']->get_length() && $cart_value['data']->get_length()) ? wc_get_dimension( $cart_value['data']->get_length(), 'cm', $dimension_unit ) : 0.00;
                    $item_width = ( null !== $cart_value['data']->get_width() && $cart_value['data']->get_width()) ? wc_get_dimension( $cart_value['data']->get_width(), 'cm', $dimension_unit ) : 0.00;
                    $item_height = ( null !== $cart_value['data']->get_height() && $cart_value['data']->get_height()) ? wc_get_dimension( $cart_value['data']->get_height(), 'cm', $dimension_unit ) : 0.00;

                    $volume_weight += $item_length * $item_width * $item_height / 4000;
                    $cargo_item_length = max( $cargo_item_length, $item_length );
                    $cargo_item_width = max( $cargo_item_width, $item_width );
                    $cargo_item_height = max( $cargo_item_height, $item_height );
                }

                if((!$volume_weight) && isset($delivery_data['shipment']['volume']) && $delivery_data['shipment']['volume'])
                {
                    $volume_weight = floatval($delivery_data['shipment']['volume']);
                }

                $weight_coef = $this->convert_weight_unit();
                $actual_weight = ( WC()->cart->cart_contents_weight > 0 ) ? WC()->cart->cart_contents_weight * $weight_coef : 0.00;

                if((!$actual_weight) && isset($delivery_data['shipment']['volume']) && $delivery_data['shipment']['volume'])
                {
                    $actual_weight = floatval($delivery_data['shipment']['volume']);
                }

                if($country && $recipient_division_id)
                {
                    $args = [
                        "payerType" => $payer_type,
                        "parcels" => [
                            [ 
                                "cargoCategory" => strtolower($cargo),
                                "insuranceCost" => $cost,
                                "rowNumber" => 1,
                                "width" => $cargo_item_width * 10,
                                "length" => $cargo_item_length * 10,
                                "height" => $cargo_item_height * 10,
                                "actualWeight" => $actual_weight * 1000,
                                "volumetricWeight" => $volume_weight
                            ]
                        ],
                        "sender" => [
                            "countryCode" => $sender_country,
                            "divisionId" => $sender_division_id,
                            "addressParts" => new stdClass()
                        ],
                        "recipient" => [
                            "countryCode" => $country,
                            "divisionId" => $recipient_division_id,
                            "addressParts" => new stdClass()
                        ]
                    ];

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

                    if(isset($token['jwt']))
                    {
                        $response = wp_remote_post( $apiurl . 'shipments/calculations', array(
                            'method'      => 'POST',
                            'timeout'     => 30,
                            'headers'     => [
                                'Content-Type' => 'application/json',
                                'Authorization' => $token['jwt']
                            ],
                            'body'        => wp_json_encode( $args ),
                            'data_format' => 'body',
                        ) );

                        if ( !is_wp_error( $response ) ) {
                            $body = wp_remote_retrieve_body( $response );
                            $data = json_decode( $body, true );

                            if(isset($data['services'][0]['price']))
                            {
                                $cost_time = $data['services'][0]['price'];
                                $cost_currncy = $data['services'][0]['currencyCode'];

                                $wp_timezone = wp_timezone();
                                $dt = new DateTime( 'now', $wp_timezone );
                                $dt->setTime( 0, 0, 0, 0 );

                                $dt->setTimezone( new DateTimeZone( 'UTC' ) );



                                $exchange_args = [
                                    'amount' => $cost_time,
                                    'countryCode' => $sender_country,
                                    'currencyCode' => $cost_currncy,
                                    'date' => $dt->format( 'Y-m-d\T00:00:00.000000\Z' )
                                ];

                                $response = wp_remote_post( $apiurl . 'exchange-rates/conversion', array(
                                    'method'      => 'POST',
                                    'timeout'     => 30,
                                    'headers'     => [
                                        'Content-Type' => 'application/json',
                                        'Authorization' => $token['jwt']
                                    ],
                                    'body'        => wp_json_encode( $exchange_args ),
                                    'data_format' => 'body',
                                ) );

                                if ( !is_wp_error( $response ) ) {
                                    $body = wp_remote_retrieve_body( $response );
                                    $data = json_decode( $body, true );

                                    $target_currency = get_woocommerce_currency();
                                    $amount = 0;

                                    if (isset($data['convertedCurrencies']) && is_array($data['convertedCurrencies'])) {
                                        foreach ($data['convertedCurrencies'] as $currency) {
                                            if ($currency['currencyCode'] === $target_currency) {
                                                $amount = $currency['amount'];
                                                break;
                                            }
                                        }
                                    }

                                    $rate['cost'] = $amount;
                                }
                            }
                        }
                    }
                }


            }

            if($this->get_option('enable_fix_cost') && $this->get_option('enable_fix_cost') == 'yes')
            {
                $rate['cost'] = $this->get_option('fix_cost_total');
            }

            if($this->get_option('enable_minimum_cost') && $this->get_option('enable_minimum_cost') == 'yes')
            {
                $woo_cart_total = WC()->cart->get_subtotal();

                if($woo_cart_total >= $this->get_option('minimum_cost_total'))
                {
                    $rate['cost'] = 0.00;

                    if($this->get_option('free_shipping_text'))
                    {
                        $rate['label'] = $this->get_option('free_shipping_text');
                    }
                }
            }

            # Set rate
            $this->add_rate($rate);
        }

        /**
         * Is this method available?
         * @param array $package
         * @return bool
         */
        public function is_available($package)
        {
            # Check shipping enabled
            return $this->is_enabled();
        }

        public function convert_weight_unit() {

            $weight_unit = get_option('woocommerce_weight_unit');

            if ( 'g' == $weight_unit ) return 0.001;
            if ( 'kg' == $weight_unit ) return 1;
            if ( 'lbs' == $weight_unit ) return 0.45359;
            if ( 'oz' == $weight_unit ) return 0.02834;
        }
    }
}