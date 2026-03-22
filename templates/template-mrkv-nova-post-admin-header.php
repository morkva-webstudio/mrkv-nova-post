<?php
	# Exit if accessed directly
	if ( ! defined( 'ABSPATH' ) ) exit; 
?>
<div class="admin_mrkv_ua_shipping__header mrkv_block_rounded">
	<div class="admin_mrkv_ua_shipping__header__content">
		<a class="admin_mrkv_ua_shipping__header_img" href="<?php echo esc_html('/wp-admin/admin.php?page=mrkv_nova_settings'); ?>">
			<img src="<?php echo MRKV_NOVA_PLUGIN_URL . '/assets/img/delivery-icon.svg'; ?>" alt="MRKV Nova Post" title="MRKV Nova Post">
		</a>
		<a class="active" href="<?php echo esc_html('/wp-admin/admin.php?page=mrkv_nova_settings'); ?>"><?php echo __('Global', 'mrkv-nova-post'); ?></a>
		<a class="admin_mrkv_ua_shipping_morkva-logo" href="https://morkva.co.ua/" target="blanc">
			<img src="<?php echo MRKV_NOVA_PLUGIN_URL . '/assets/img/morkva-logo.svg'; ?>" alt="Morkva" title="Morkva">
		</a>
	</div>
</div>