<?php 
	# Exit if accessed directly
	if ( ! defined( 'ABSPATH' ) ) exit; 

	# Include template
	include MRKV_NOVA_PLUGIN_PATH . 'templates/template-mrkv-nova-post-admin-header.php'; 
?>
<div class="admin_mrkv_ua_shipping_page">
	<div class="mrkv-nova__section-tab">
	<div class="admin_mrkv_ua_shipping__tabs_main mrkv_block_rounded">
		<h2>
		<?php echo __('Settings Nova Post', 'mrkv-nova-post'); ?>
			<img src="<?php echo MRKV_NOVA_PLUGIN_URL . '/assets/img/logo-settings.svg' ?>" alt="<?php echo __('Settings Nova Post', 'mrkv-nova-post'); ?>" title="<?php echo __('Settings Nova Post', 'mrkv-nova-post'); ?>">
		</h2>
		<div class="admin_mrkv_ua_shipping__tabs_main__inner">
			<?php 
				$counter = 0;
				foreach($tabs as $id => $name)
				{
					?>
						<a href="#<?php echo $id; ?>-mrkv" data-tab="<?php echo $id; ?>" class="mrkv_up_ship_tab_btn <?php if($counter == 0){echo 'active'; } ?>"><?php echo $name; ?></a>
					<?php

					++$counter;
				}
			?>
		</div>
	</div>
</div>
<?php
	$api_works = 'empty';

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

	if($apikey && $apiurl)
	{
		$country = 'PL';
		$novapost_term_suggestion = 'War';

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

		if(isset($city_body['items']) && !empty($city_body['items']))
		{
			$api_works = 'success';
		}
		else{
			$api_works = 'error';
		}
	}
	
?>
<div class="mrkv-nova__section-tab">
	<?php settings_errors(); ?>
</div>
<div class="mrkv-nova__section">
	<div class="mrkv-nova__section__inner mrkv-col-7">
		<div class="mrkv-nova__inner__body">
			<form class="mrkv-nova__inner__body__form" method="post" action="options.php">
                <?php settings_fields('mrkv-nova-settings-group'); ?>
                <section id="basic_settings" class="mrkv_up_ship_shipping_tab_block active">
                	<div class="mrkv-nova__form__row">
	                	<div class="mrkv-nova__form__col">
	                		<div class="mrkv-nova__form__line">
	                			<h2><img src="<?php echo MRKV_NOVA_PLUGIN_URL . '/assets/img/settings-icon.svg'; ?>" alt="Basic settings" title="Basic settings"><?php echo __('Basic settings', 'mrkv-nova-post'); ?></h2>
	                			<p><?php echo esc_html__('Integrate the Nova Post API into your website to send orders to customers in a convenient way. Deliver from Europe to Ukraine or from Ukraine to Europe with the help of the largest postal operator network in Ukraine, Nova Post.','mrkv-nova-post'); ?></p>
	                			<hr>
	                			<h4><?php echo esc_html__('Api Server','mrkv-nova-post'); ?></h4>
	                			<div class="mrkv-nova__form__line__content mrkv-nova__form__line__content__radio">
	                				<?php 
	                					$servers = array(
	                						'production' => __('Production', 'mrkv-nova-post'),
	                						'sandbox' => __('Sandbox', 'mrkv-nova-post')
	                					);

	                					$mrkv_nova_api_server = get_option('mrkv_nova_api_server');

	                					if(!$mrkv_nova_api_server)
	                					{
	                						$mrkv_nova_api_server = '';
	                					}

	                					foreach($servers as $server => $name)
	                					{
	                						$checked = '';

	                						if($mrkv_nova_api_server == $server)
	                						{
	                							$checked = 'checked';
	                						}
	                						?>
	                							<div class="mrkv-nova__form__line__radio_val">
	                								<input id="mrkv_nova_api_server-<?php echo esc_html($server); ?>" type="radio" name="mrkv_nova_api_server" value="<?php echo esc_html($server); ?>" <?php echo esc_html($checked); ?>>
	                								<label for="mrkv_nova_api_server-<?php echo esc_html($server); ?>"><?php echo esc_html($name); ?></label>
	                							</div>
	                						<?php
	                					}
	                				?>
	                			</div>
	                			<h4><?php echo esc_html__('Api Token','mrkv-nova-post'); ?>
	                				<?php
	                					if($api_works == 'success')
	            						{
	            							?>
	            								<div class="admin_ua_ship_morkva__notification mrkv-notification-green"><?php echo __('API key correct','mrkv-nova-post'); ?></div>
	            							<?php
	            						}
	            						elseif($api_works == 'error')
	            						{
	            							?>
	            								<div class="admin_ua_ship_morkva__notification mrkv-notification-red"><?php echo __('API key incorrect','mrkv-nova-post'); ?></div>
	            							<?php
	            						}
	                				?>
	                			</h4>
	                			<div class="mrkv-nova__form__line__content">
	                				<div class="mrkv-nova__form__line__text_val">
	                					
	                					<?php
	                						$mrkv_nova_api_token = get_option('mrkv_nova_api_token');

	                						if(!$mrkv_nova_api_token)
	                						{
	                							$mrkv_nova_api_token = '';
	                						}
	                					?>
	                					<input type="text" name="mrkv_nova_api_token" value="<?php echo esc_html($mrkv_nova_api_token); ?>">
	                					<p><?php echo esc_html__('During the registration process, you will receive an API access key. Make sure to store this information in a secure place.', 'mrkv-nova-post'); ?></p>
	                				</div>
	                			</div>
	                		</div>
	                	</div>
	                </div>
				</section>
				<section id="default_settings" class="mrkv_up_ship_shipping_tab_block">
					<h2><img src="<?php echo MRKV_NOVA_PLUGIN_URL . '/assets/img/box-icon.svg'; ?>" alt="Delivery" title="Delivery"><?php echo __('Default values', 'mrkv-nova-post'); ?></h2>
        			<p><?php echo esc_html__('This API method allows you to calculate the estimated delivery cost for your cargo based on various factors such as weight, dimensions, destination, and shipping method.','mrkv-nova-post'); ?></p>
        			<hr>
        			<?php
        				$delivery_data = get_option('mrkv_nova_delivery');
        			?>
        			<div class="admin_ua_ship_morkva_settings_row">
						<div class="col-mrkv-5">
							<div class="admin_ua_ship_morkva_settings_line">
								<h4><?php echo __('Payer of delivery', 'mrkv-nova-post'); ?></h4>
								<?php
									$data = isset($delivery_data['payer_type']) ? $delivery_data['payer_type'] : 'Recipient';
								?>
								<div class="admin_ua_ship_morkva_settings_row">
									<input id="mrkv_nova_delivery_payer_delivery_recipient" type="radio" name="mrkv_nova_delivery[payer_type]" value="Recipient" <?php checked( $data, 'Recipient' ); ?>>
									<label for="mrkv_nova_delivery_payer_delivery_recipient"><?php echo __('Recipient', 'mrkv-nova-post'); ?></label>
									<input id="mrkv_nova_delivery_payer_delivery_sender" type="radio" name="mrkv_nova_delivery[payer_type]" value="Sender" <?php checked( $data, 'Sender' ); ?>>
									<label for="mrkv_nova_delivery_payer_delivery_sender"><?php echo __('Sender', 'mrkv-nova-post'); ?></label>				
								</div>
							</div>
						</div>
						<div class="col-mrkv-5">
							<?php
								$data = isset($delivery_data['payer_contect_number']) ? $delivery_data['payer_contect_number'] : '';
							?>
							<div class="admin_ua_ship_morkva_settings_line">
								<label style="margin-top: 15px;" for="mrkv_nova_delivery_payer_contect_number"><?php echo __('Payer contact number (Sender)', 'mrkv-nova-post'); ?></label>
								<input id="mrkv_nova_delivery_payer_contect_number" type="text" name="mrkv_nova_delivery[payer_contect_number]" placeholder="<?php echo __('Enter number', 'mrkv-nova-post'); ?>" value="<?php echo $data; ?>">
							</div>
						</div>
					</div>
					<div class="admin_ua_ship_morkva_settings_line">
						<label style="margin-top: 15px;" for="mrkv_nova_delivery_shipment_cart_total"><?php echo __('Sender country', 'mrkv-nova-post'); ?></label>
						<?php
							$data = isset($delivery_data['sender_country']) ? $delivery_data['sender_country'] : 'UA';
							$countries = [
							    'CZ' => __('Czechia', 'mrkv-nova-post'),
							    'DE' => __('Germany', 'mrkv-nova-post'),
							    'EE' => __('Estonia', 'mrkv-nova-post'),
							    'ES' => __('Spain', 'mrkv-nova-post'),
							    'FR' => __('France', 'mrkv-nova-post'),
							    'GB' => __('United Kingdom', 'mrkv-nova-post'),
							    'HU' => __('Hungary', 'mrkv-nova-post'),
							    'IT' => __('Italy', 'mrkv-nova-post'),
							    'LT' => __('Lithuania', 'mrkv-nova-post'),
							    'LV' => __('Latvia', 'mrkv-nova-post'),
							    'MD' => __('Moldova', 'mrkv-nova-post'),
							    'NL' => __('Netherlands', 'mrkv-nova-post'),
							    'PL' => __('Poland', 'mrkv-nova-post'),
							    'RO' => __('Romania', 'mrkv-nova-post'),
							    'SK' => __('Slovakia', 'mrkv-nova-post'),
							    'UA' => __('Ukraine', 'mrkv-nova-post'),
							];
						?>
						<select name="mrkv_nova_delivery[sender_country]" id="mrkv_nova_delivery_sender_country">
							<?php 
								foreach($countries as $slug => $country_name)
								{
									?>
									<option <?php echo ($slug == $data) ? 'selected' : ''; ?> value="<?php echo $slug; ?>"><?php echo $country_name; ?></option>
									<?php
								}
							?>
						</select>
					</div>
					<div class="admin_ua_ship_morkva_settings_line">
						<label style="margin-top: 15px;" for="mrkv_nova_delivery_shipment_cart_total"><?php echo __('Sender division', 'mrkv-nova-post'); ?></label>
							<?php
								$data = isset($delivery_data['sender_address']) ? $delivery_data['sender_address'] : '';
								$division_id = isset($delivery_data['division_id']) ? $delivery_data['division_id'] : '';
							?>
						<input style="width: 100%; max-width: 100%;" id="mrkv_nova_delivery_sender_address" type="text" name="mrkv_nova_delivery[sender_address]" value="<?php echo $data; ?>">
						<input id="mrkv_nova_delivery_division_id" type="hidden" name="mrkv_nova_delivery[division_id]" value="<?php echo $division_id; ?>">
					</div>
					<h3><img src="<?php echo MRKV_UA_SHIPPING_ASSETS_URL . '/images/global/tuning-icon.svg'; ?>" alt="Shipment" title="Shipment"><?php echo __('Shipment', 'mrkv-nova-post'); ?></h3>
					<p><?php echo __('Fill in the default shipping data for the shipment', 'mrkv-nova-post'); ?></p>
					<hr class="mrkv-ua-ship__hr">
					<div class="admin_ua_ship_morkva_settings_row">
						<div class="col-mrkv-5">
							<div class="admin_ua_ship_morkva_settings_line">
								<h4><?php echo __('Type', 'mrkv-nova-post'); ?></h4>
								<?php
									$data = isset($delivery_data['cargo_category']) ? $delivery_data['cargo_category'] : 'Parcel';
								?>
								<div class="admin_ua_ship_morkva_settings_row">
									<input id="mrkv_nova_delivery_shipment_type_parcel" type="radio" name="mrkv_nova_delivery[cargo_category]" value="Parcel" <?php checked( $data, 'Parcel' ); ?>>
									<label for="mrkv_nova_delivery_shipment_type_parcel"><?php echo __('Parcel', 'mrkv-nova-post'); ?></label>
									<input id="mrkv_nova_delivery_shipment_type_pallet" type="radio" name="mrkv_nova_delivery[cargo_category]" value="Pallet" <?php checked( $data, 'Pallet' ); ?>>
									<label for="mrkv_nova_delivery_shipment_type_pallet"><?php echo __('Pallet', 'mrkv-nova-post'); ?></label>
									<input id="mrkv_nova_delivery_shipment_type_documents" type="radio" name="mrkv_nova_delivery[cargo_category]" value="Documents" <?php checked( $data, 'Documents' ); ?>>
									<label for="mrkv_nova_delivery_shipment_type_documents"><?php echo __('Documents', 'mrkv-nova-post'); ?></label>				
								</div>
							</div>
						</div>
						<div class="col-mrkv-5">
							<div class="admin_ua_ship_morkva_settings_line">
								<label style="margin-top: 15px;" for="mrkv_nova_delivery_shipment_cart_total"><?php echo __('Free shipping calculation', 'mrkv-nova-post'); ?></label>
								<?php
									$data = isset($delivery_data['shipment']['cart_total']) ? $delivery_data['shipment']['cart_total'] : '';
								?>
								<select id="mrkv_nova_delivery_shipment_cart_total" type="text" name="mrkv_nova_delivery[shipment][cart_total]" >
									<option value=""><?php echo __('Select', 'mrkv-nova-post'); ?></option>
									<option <?php echo ($data == 'subtotal') ? 'selected' : ''; ?> value="subtotal"><?php echo __('From the interim order value (excluding promo codes)', 'mrkv-nova-post'); ?></option>
									<option <?php echo ($data == 'total') ? 'selected' : ''; ?> value="total"><?php echo __('From the total order value (including promo codes)', 'mrkv-nova-post'); ?></option>
								</select>
							</div>
						</div>
					</div>
					<div class="admin_ua_ship_morkva_settings_row">
						<div class="col-mrkv-5">
							<div class="admin_ua_ship_morkva_settings_line">
								<h4><?php echo __('Weight, kg', 'mrkv-nova-post'); ?></h4>
								<?php
									$data = isset($delivery_data['shipment']['weight']) ? $delivery_data['shipment']['weight'] : '0';
								?>
								<div></div><input step="0.01" min="0" id="mrkv_nova_delivery_shipment_weight" type="number" onwheel="this.blur()" name="mrkv_nova_delivery[shipment][weight]" placeholder="" value="<?php echo $data; ?>">			</div>
						</div>
						<div class="col-mrkv-5">
							<div class="admin_ua_ship_morkva_settings_line">
								<h4><?php echo __('Sizes, sm', 'mrkv-nova-post'); ?></h4>
								<div class="adm_morkva_row_size">
									<div class="adm_morkva_row_size__col">
										<span><?php echo __('Lenght', 'mrkv-nova-post'); ?></span>
										<?php
											$data = isset($delivery_data['shipment']['length']) ? $delivery_data['shipment']['length'] : '0';
										?>
										<div></div>
										<input step="0.01" min="0" id="mrkv_nova_delivery_shipment_length" type="number" onwheel="this.blur()" name="mrkv_nova_delivery[shipment][length]" placeholder="" value="<?php echo $data; ?>">
									</div>
									<span>
										<svg width="20px" height="20px" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg"><g id="SVGRepo_bgCarrier" stroke-width="0"></g><g id="SVGRepo_tracerCarrier" stroke-linecap="round" stroke-linejoin="round"></g><g id="SVGRepo_iconCarrier"> <path d="M8 18C5.17157 18 3.75736 18 2.87868 17.1213C2 16.2426 2 14.8284 2 12C2 9.17157 2 7.75736 2.87868 6.87868C3.75736 6 5.17157 6 8 6C10.8284 6 12.2426 6 13.1213 6.87868C14 7.75736 14 9.17157 14 12" stroke="#ed6230" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round"></path> <path d="M10 12C10 14.8284 10 16.2426 10.8787 17.1213C11.7574 18 13.1716 18 16 18C18.8284 18 20.2426 18 21.1213 17.1213C21.4211 16.8215 21.6186 16.4594 21.7487 16M22 12C22 9.17157 22 7.75736 21.1213 6.87868C20.2426 6 18.8284 6 16 6" stroke="#ed6230" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round"></path> </g></svg>
									</span>
									<div class="adm_morkva_row_size__col">
										<span><?php echo __('Width', 'mrkv-nova-post'); ?></span>
										<?php
											$data = isset($delivery_data['shipment']['width']) ? $delivery_data['shipment']['width'] : '0';
										?>
										<div></div><input step="0.01" min="0" id="mrkv_nova_delivery_shipment_width" type="number" onwheel="this.blur()" name="mrkv_nova_delivery[shipment][width]" placeholder="" value="<?php echo $data; ?>">					</div>
									<span>
										<svg width="20px" height="20px" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg"><g id="SVGRepo_bgCarrier" stroke-width="0"></g><g id="SVGRepo_tracerCarrier" stroke-linecap="round" stroke-linejoin="round"></g><g id="SVGRepo_iconCarrier"> <path d="M8 18C5.17157 18 3.75736 18 2.87868 17.1213C2 16.2426 2 14.8284 2 12C2 9.17157 2 7.75736 2.87868 6.87868C3.75736 6 5.17157 6 8 6C10.8284 6 12.2426 6 13.1213 6.87868C14 7.75736 14 9.17157 14 12" stroke="#ed6230" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round"></path> <path d="M10 12C10 14.8284 10 16.2426 10.8787 17.1213C11.7574 18 13.1716 18 16 18C18.8284 18 20.2426 18 21.1213 17.1213C21.4211 16.8215 21.6186 16.4594 21.7487 16M22 12C22 9.17157 22 7.75736 21.1213 6.87868C20.2426 6 18.8284 6 16 6" stroke="#ed6230" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round"></path> </g></svg>
									</span>
									<div class="adm_morkva_row_size__col">
										<span><?php echo __('Height', 'mrkv-nova-post'); ?></span>
										<?php
											$data = isset($delivery_data['shipment']['height']) ? $delivery_data['shipment']['height'] : '0';
										?>
										<div></div><input step="0.01" min="0" id="mrkv_nova_delivery_shipment_height" type="number" onwheel="this.blur()" name="mrkv_nova_delivery[shipment][height]" placeholder="" value="<?php echo $data; ?>">					</div>
								</div>
							</div>
						</div>
					</div>
					<div class="admin_ua_ship_morkva_settings_line admin_ua_ship_morkva_one_data">
						<div>
							<label for="mrkv_nova_delivery_shipment_volume"><?php echo __('Volumetric weight', 'mrkv-nova-post'); ?></label>
							<p class="mrkv-ua-ship-description"><?php echo __('Calculated automatically according to the dimensions in the settings.', 'mrkv-nova-post'); ?></p>
						</div>
						<?php
							$data = isset($delivery_data['shipment']['volume']) ? $delivery_data['shipment']['volume'] : '0';
						?>
						<input step="0.01" min="0" id="mrkv_nova_delivery_shipment_volume" type="number" onwheel="this.blur()" name="mrkv_nova_delivery[shipment][volume]" placeholder="" value="<?php echo $data; ?>" readonly="">		<p><strong><?php echo __('Standard dimensions and weight apply when the goods in the order do not have their own', 'mrkv-nova-post'); ?></strong></p>
					</div>
				</section>
				<?php echo esc_html(submit_button(esc_html__('Save Settings', 'mrkv-nova-post'))); ?>
            </form>
		</div>
	</div>
	<div class="mrkv-nova__section__inner mrkv-col-3">
		<div class="admin_mrkv_ua_shipping__plugin__support">
		<h2><img src="<?php echo MRKV_NOVA_PLUGIN_URL . '/assets/img/question-icon.svg'; ?>" alt="Statistics of shipments" title="Statistics of shipments"><?php echo __('Support', 'mrkv-nova-post'); ?></h2>
		<p><?php echo __('Having trouble creating a shipment? Feel free to contact our support team.', 'mrkv-nova-post'); ?></p>
		<a href="mailto:support@morkva.co.ua" class="button button-primary admin_mrkv_ua_shipping__btn" target="_blank"><?php echo __('E-mail', 'mrkv-nova-post'); ?></a>
		<a href="https://t.me/morkva_support_bot" class="admin_mrkv_ua_shipping__btn admin_mrkv_ua_shipping__btn_icon button button-primary" target="_blank"><?php echo __('Telegram', 'mrkv-nova-post'); ?><img src="<?php echo MRKV_NOVA_PLUGIN_URL . '/assets/img/telegram-logo.svg'; ?>"></a>
	</div>
	</div>
</div>
</div>