function setCookie(key, value, expiry) {
    var expires = new Date();
    expires.setTime(expires.getTime() + (expiry * 24 * 60 * 60 * 1000));
    document.cookie = key + '=' + value + ';expires=' + expires.toUTCString();
}

function getCookie(key) {
    var keyValue = document.cookie.match('(^|;) ?' + key + '=([^;]*)(;|$)');
    return keyValue ? keyValue[2] : null;
}

function eraseCookie(key) {
    var keyValue = getCookie(key);
    setCookie(key, keyValue, '-1');
}

var hash = window.location.hash;

if(!hash)
{
	var hash = getCookie('current_page_hash');

	if(hash)
	{
		window.location.hash = hash;
	}
	eraseCookie('current_page_hash');
}

if(hash)
{
	var tab_main = hash.replace('-mrkv','');
	tab_main = tab_main.replace('#','');
	jQuery('.admin_mrkv_ua_shipping__tabs_main__inner .active').removeClass('active');
	jQuery('.mrkv_up_ship_tab_btn[data-tab="' + tab_main + '"]').addClass('active');

	jQuery('.mrkv_up_ship_shipping_tab_block').removeClass('active');
	jQuery('#' + tab_main).addClass('active');
}

jQuery(window).on('load', function() 
{
	jQuery('.mrkv_ua_shipping_method_form').on('submit', function(e) {
		var hash = window.location.hash;

		if(hash)
		{
			setCookie('current_page_hash',hash,'1');
		}
	});
	if(jQuery('.mrkv_up_ship_tab_btn').length != 0)
 	{
		jQuery('.mrkv_up_ship_tab_btn').click(function()
		{
			jQuery('.admin_mrkv_ua_shipping__tabs_main__inner .active').removeClass('active');
			jQuery(this).addClass('active');

			const shipping_tab = jQuery(this).attr('data-tab');

			jQuery('.mrkv_up_ship_shipping_tab_block').removeClass('active');
			jQuery('#' + shipping_tab).addClass('active');
		});
	}

	jQuery('#mrkv_nova_delivery_shipment_length, #mrkv_nova_delivery_shipment_width, #mrkv_nova_delivery_shipment_height, #mrkv_nova_delivery_shipment_weight')
        .on('keyup', function() {
            jQuery('#mrkv_nova_delivery_shipment_volume')
                .val(mrkvnpCalcVolumeWeightSettings());
    });

    jQuery('.admin_ua_ship_morkva_settings_line select').select2({
        width: '100%',
    });

    jQuery('#mrkv_nova_delivery_sender_country').change(function(){
        jQuery('#mrkv_nova_delivery_sender_address').val('');
        jQuery('#mrkv_nova_delivery_division_id').val('');
    });

    let autoSelectCityPo = function() {

    var novapost_delivery_list = {};

    jQuery('#mrkv_nova_delivery_sender_address').autocomplete({

    source: function(request, response) { // Get city data from API-УП

      if(request.term.length > 2){
        //jQuery('#mrkv-nova-post_fields').addClass('novapost-loading');
        var country_sender = jQuery('#mrkv_nova_delivery_sender_country').val();
        jQuery('#mrkv_nova_delivery_sender_address').addClass('ui-autocomplete-loading');
        jQuery.ajax({
            method: 'POST',
            url: mrkv_nova_globals.ajaxUrl,
            dataType: 'json',
            data: {
              term: request.term,
              action: 'novapost_warehouse_autocomplete',
              mrkvup_country_suggestion: country_sender,
              mrkvnovanonce: mrkv_nova_globals.mrkvnovanonce
            },
            success: function(data) {
              if(!Array.isArray(data))
              {
                novapost_delivery_list = data.response_val;

                response(data.response);
              }
              else
              {
                response(data);
              }

              
              jQuery('#mrkv_nova_delivery_sender_address').removeClass('ui-autocomplete-loading'); // Remove spinner

              //let cityInputWidth = jQuery('#mrkv-nova-post-shipping-info').width();
              //jQuery('.ui-autocomplete').css('width', cityInputWidth+'px');
            },
                error: function(xhr, status, error) {
                    //jQuery('#mrkv-nova-post_fields').addClass('novapost-loading');
                    //console.log(xhr.responseText);
                    //alert(xhr.responseText);
                },
          });
      }
    },
    select: function(event, ui) { // After city name selected
      event.preventDefault();
      jQuery(this).val( ui.item.label );
      jQuery( "#mrkv_nova_delivery_division_id" ).val( ui.item.value );

      //jQuery('#mrkv-nova-post_fields').removeClass('novapost-loading'); // Remove spinner

      },
      minLength: 0,
      delay: 0,
    }).focus(function(){            
            // As noted by Jonny in his answer, with newer versions use uiAutocomplete
            jQuery(this).data("uiAutocomplete").search(jQuery(this).val());
        });
  }

  autoSelectCityPo();

	function mrkvnpCalcVolumeWeightSettings() {
	    let length = jQuery('#mrkv_nova_delivery_shipment_length').val();
	    let width = jQuery('#mrkv_nova_delivery_shipment_width').val();
	    let height = jQuery('#mrkv_nova_delivery_shipment_height').val();
	    let weight = jQuery('#mrkv_nova_delivery_shipment_weight').val();
	    let volumeWeight = length * width * height / 4000;
	    if (volumeWeight > weight) {
	        return volumeWeight;
	    } else {
	        return weight;
	    }
	}
});