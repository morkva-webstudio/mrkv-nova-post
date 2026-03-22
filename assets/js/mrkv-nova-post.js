'use strict';

(function (jQuery) 
{
  if (jQuery('form[name="checkout"]').length == 0) return;

	// Get novapost fields
	let jQueryshippingBox = jQuery('#mrkv-nova-post_fields');

	let setLoadingState = function () {
    jQueryshippingBox.addClass('wcus-state-loading');
  };

  let unsetLoadingState = function () {
    jQueryshippingBox.removeClass('wcus-state-loading');
  };

  jQuery('.woocommerce-shipping-fields').css('display', 'none'); 

  let get_nova_post_current_shipping_method = function() {
    let currentShippingMethod,
      currentShipping = jQuery('.shipping_method').length;

    if (1 == currentShipping) {
      currentShippingMethod = jQuery('.shipping_method').val();
    } else {
      currentShippingMethod = jQuery('.shipping_method:checked').val()
    }

      return currentShippingMethod;
  }

  let is_novapost_shipping_selected = function () {
    let currentShipping = jQuery('.shipping_method').length > 1 ?
      jQuery('.shipping_method:checked').val() :
      jQuery('.shipping_method').val();

    return currentShipping && currentShipping == 'mrkv-nova-post';
  };

  let selectShipping = function () {
    if (is_novapost_shipping_selected()) {
      jQuery('#mrkv-nova-post_fields').css('display', 'block');
        jQuery('.woocommerce-shipping-fields').css('display', 'none');
    }
    else {
      jQuery('#mrkv-nova-post_fields').css('display', 'none');
      jQuery('.woocommerce-shipping-fields').css('display', 'block');
    }
  };

  let disableDefaultBillingFieldsforup = function () {
    if (is_novapost_shipping_selected()) {
      jQuery('#billing_address_1_field').css('display', 'none');
      jQuery('#billing_address_2_field').css('display', 'none');
      jQuery('#billing_city_field').css('display', 'none');
      jQuery('#billing_state_field').css('display', 'none');
      jQuery('#billing_postcode_field').css('display', 'none');
    }
    else
    {
      /*jQuery('.woocommerce-billing-fields').css('display', 'block');
        jQuery('#billing_address_1_field').css('display', 'block');
        jQuery('#billing_address_2_field').css('display', 'block');
        jQuery('#billing_city_field').css('display', 'block');
        jQuery('#billing_state_field').css('display', 'block');
        jQuery('#billing_postcode_field').css('display', 'block');*/
    }
  };

    jQuery(function() {
        jQuery('#mrkv-nova-post_fields').css('display', 'none');

        jQuery(document.body).on('update_checkout', function (event, args) {
            setLoadingState();
        });

        jQuery(document.body).on('updated_checkout', function (event, args) {
            selectShipping();
            disableDefaultBillingFieldsforup();
            unsetLoadingState();
        });

        jQuery(document.body).on('change', '#billing_country', function (event, args) {
          selectShipping();
          disableDefaultBillingFieldsforup();
          unsetLoadingState();

           setTimeout(function(){
            let curShippingMethod = get_nova_post_current_shipping_method();
      
            if (curShippingMethod.indexOf('mrkv-nova-post') >= 0) {
                autoSelectCityPo();
            }
            }, 1000);
        });
        //initialize();
    });

    let autoSelectCityPo = function() {

    var novapost_delivery_list = {};

    // Autocomplete for 'Населений пункт Отримувача' field on Checkout
    jQuery('#mrkv-nova-post_warehouse_select').autocomplete({

    source: function(request, response) { // Get city data from API-УП

      if(request.term.length > 2){
        jQuery('#mrkv-nova-post_fields').addClass('novapost-loading');
        const country = jQuery('#billing_country').length ? jQuery('#billing_country').val() : 'UA';

        jQuery.ajax({
            method: 'POST',
            url: mrkv_nova_globals.ajaxUrl,
            dataType: 'json',
            data: {
              term: request.term,
              action: 'novapost_warehouse_autocomplete',
              mrkvup_country_suggestion: country,
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

              
              jQuery('#mrkv-nova-post_fields').removeClass('novapost-loading'); // Remove spinner

              let cityInputWidth = jQuery('#mrkv-nova-post-shipping-info').width();
              jQuery('.ui-autocomplete').css('width', cityInputWidth+'px');
            },
                error: function(xhr, status, error) {
                    jQuery('#mrkv-nova-post_fields').addClass('novapost-loading');
                    console.log(xhr.responseText);
                    //alert(xhr.responseText);
                },
          });
      }
    },
    select: function(event, ui) { // After city name selected
      event.preventDefault();
      jQuery(this).val( ui.item.label );
      jQuery( "#mrkv-nova-post_warehouse_selected_id" ).val( ui.item.value );
      jQuery( "#mrkv-nova-post_warehouse_selected_number" ).val( novapost_delivery_list[ui.item.value].number );
      jQuery( "#mrkv-nova-post_warehouse_selected_city" ).val( novapost_delivery_list[ui.item.value].city );
      jQuery( "#mrkv-nova-post_warehouse_selected_region" ).val( novapost_delivery_list[ui.item.value].region );

      jQuery('#mrkv-nova-post_fields').removeClass('novapost-loading'); // Remove spinner
        jQuery('body').trigger('update_checkout');
      },
      minLength: 0,
      delay: 0,
    }).focus(function(){            
            // As noted by Jonny in his answer, with newer versions use uiAutocomplete
            jQuery(this).data("uiAutocomplete").search(jQuery(this).val());
        });
  } 

    let curShippingMethod = get_nova_post_current_shipping_method();
    
    if (curShippingMethod && curShippingMethod.indexOf('mrkv-nova-post') >= 0) {
        autoSelectCityPo();
    }

    jQuery(document.body).on('updated_checkout', function (event, args) {
        var curShippingMethod_new = get_nova_post_current_shipping_method();

        if (curShippingMethod_new && curShippingMethod_new.indexOf('mrkv-nova-post') >= 0) {
          if (document.body.classList.contains('mrkvnp-plugin-is-active')) {
            // If PRO-НП is active on the site
            jQuery('#billing_nova_poshta_region').attr('disabled', 'disabled').closest('.form-row').hide();
            jQuery('#billing_nova_poshta_city').attr('disabled', 'disabled').closest('.form-row').hide();
            jQuery('#billing_nova_poshta_warehouse').attr('disabled', 'disabled').closest('.form-row').hide();
            jQuery('#billing_mrkvnp_street').attr('disabled', 'disabled').closest('.form-row').hide();
            jQuery('#billing_mrkvnp_house').attr('disabled', 'disabled').closest('.form-row').hide();
            jQuery('#billing_mrkvnp_flat').attr('disabled', 'disabled').closest('.form-row').hide();
            jQuery('#billing_mrkvnp_patronymics').attr('disabled', 'disabled').closest('.form-row').hide();
          }
            autoSelectCityPo();
        }
    });

})(jQuery);