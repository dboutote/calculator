jQuery(function($) {

	// reset form
	$.fn.reset = function () {
		$(this).each (function() { this.reset(); });
	};
	
	$('.display-no').hide().removeClass('display-no');
	
	/**
	 * calculator Form
	 */
	if( $('#calculator') && $('#calculator').length > 0 ) {

		var $pricingOverlay = $('.pricing');
		var $calcTitleText = $('#calc-title').text();
		var $calcForm = $('#calculator');
		var $calcResponse = $('#calcresponse'); 
		var $calcResults = $('#calcresults');
		var $responseErrors = $('.response-errors', $calcForm);
		var $calcColorCostOtr = $('#calc_colorcost_otr', $calcForm );
		var $calcTubeSizeOtr = $('#calc_tubesize_otr', $calcForm );
		var $calcColorAmtOtr = $('#calc_coloramt_otr', $calcForm );
		var $calcColorServcsOtr = $('#calc_colorservices_otr', $calcForm );
		$pricingOverlay.css({'height':(($(document).height())+100)+'px'});
		
		// open overlay
		$("a.overlay-pricing").click(function(){
			$pricingOverlay.fadeIn(400);
			return false;		
		});

		/**
		 * When closing the overlay:
		 *
		 * - fade out the overlay
		 * -- hide response div
		 * -- clear response messages
		 * -- reset the inline display css on the form, reset the form fields
		 * -- remove any error messages
		 * -- remove error classes
		 */
		$(".close").click(function(){
			$pricingOverlay.fadeOut(function (){
				$calcResponse.hide();
				$calcResults.empty();
				$calcForm.css('display', '').reset();
				$('#calc-title').text($calcTitleText);
				$('.gfield_description', $calcForm).remove();
				$('.err', $calcForm).removeClass('err');
				$('.form-header .validation_error', $calcForm).remove();
				$('.response-errors', $calcForm).empty();
				$('.otr-control', $calcForm).val('').removeAttr('aria-required').fadeOut();
			});			

			return false;
		});
		
		
		/** 
		 * when selecting an Average Cost of Color
		 * 
		 * - if the user selects "other", add the aria-required attr to the corresponding text field
		 * - show the corresponding text field
		 */
		$('#calc_colorcost').on('change', function(){
			var option_type = $("#calc_colorcost option:selected").val();				
			switch(option_type) {
				case "otr":
					$calcColorCostOtr.val('').attr('aria-required', 'true').removeClass('err').fadeIn();
					break;
				default:
					$calcColorCostOtr.val('').removeAttr('aria-required').fadeOut();
					$(this).closest('.ginput_container').children('.validation_message').fadeOut().remove();
			};		
			$pricingOverlay.css({'height':(($(document).height()) )+'px'});						
		});
		
		$calcColorCostOtr.on('blur', function(){			
			if( parseFloat( $(this).val() ) < 2.75){
				if($(this).closest('.ginput_container').children('.validation_message').length < 1 ) {
					$(this).after('<div class="gfield_description validation_message">Out of range, please re-enter.</div>');
				}	
			} else {
				$(this).closest('.ginput_container').children('.validation_message').fadeOut().remove();
			}
		})

		
		/** 
		 * When selecting an Average Tube Size
		 * 
		 * - if the user selects "other", add the aria-required attr to the corresponding text field
		 * - show the corresponding text field
		 */		
		$('#calc_tubesize').on('change', function(){
			var option_type = $("#calc_tubesize option:selected").val();				
			switch(option_type) {
				case "otr":
					$calcTubeSizeOtr.val('').attr('aria-required', 'true').removeClass('err').fadeIn();
					break;
				default:
					$calcTubeSizeOtr.val('').fadeOut();
					$(this).closest('.ginput_container').children('.validation_message').fadeOut().remove();
			};	
			$pricingOverlay.css({'height':(($(document).height()) )+'px'});			
		});
		
		
		/** 
		 * When selecting an Color Amounts
		 * 
		 * - if the user selects "other", add the aria-required attr to the corresponding text field
		 * - show the corresponding text field
		 */	
		$('#calc_coloramt').on('change', function(){
			var option_type = $("#calc_coloramt option:selected").val();				
			switch(option_type) {
				case "otr":
					$calcColorAmtOtr.val('').attr('aria-required', 'true').removeClass('err').fadeIn();
					break;
				default:
					$calcColorAmtOtr.val('').fadeOut();
					$(this).closest('.ginput_container').children('.validation_message').fadeOut().remove();
			};		
			$pricingOverlay.css({'height':(($(document).height()) )+'px'});						
		});
		
		$calcColorAmtOtr.on('blur', function(){			
			if( parseFloat( $(this).val() ) < 2.20){
				if($(this).closest('.ginput_container').children('.validation_message').length < 1 ) {
					$(this).after('<div class="gfield_description validation_message">Out of range, please re-enter.</div>');
				}				
			} else {
				$(this).closest('.ginput_container').children('.validation_message').fadeOut().remove();
			}
		});

		
		/** 
		 * When selecting an Average Number of Colorings
		 * 
		 * - if the user selects "other", add the aria-required attr to the corresponding text field
		 * - show the corresponding text field
		 */	
		$('#calc_colorservices').on('change', function(){
			var option_type = $("#calc_colorservices option:selected").val();				
			switch(option_type) {
				case "otr":
					$calcColorServcsOtr.val('').attr('aria-required', 'true').removeClass('err').fadeIn();
					break;
				default:
					$calcColorServcsOtr.val('').fadeOut();
					$(this).closest('.ginput_container').children('.validation_message').fadeOut().remove();
			};		
			$pricingOverlay.css({'height':(($(document).height()) )+'px'});						
		});
		
		$calcColorServcsOtr.on('blur', function(){			
			if( parseFloat( $(this).val() ) < 50){
				if($(this).closest('.ginput_container').children('.validation_message').length < 1 ) {
					$(this).after('<div class="gfield_description validation_message">Out of range, please re-enter.</div>');
				}				
			} else {
				$(this).closest('.ginput_container').children('.validation_message').fadeOut().remove();
			}
		});
		
		
		// submitting the form
		$calcForm.on('submit', function(event){
			var $form = $(this);
			var $formHeader = $('.form-header', $form);
			var $subBtn = $('input[type=submit]', $form);
			$calcResults.empty();
			var input_errors = 0;
			var $loader = $('.loading', $form);
			

			// Disable the submit button to prevent repeated clicks
			$subBtn.attr('disabled', true);
			$loader.show();
			
			
			
			// check required fields
			$('[aria-required]', $form).each(function() {
				if( !$.trim($(this).val()) ) {					
					input_errors++;
				   $(this).addClass('err');
					if($(this).closest('.ginput_container').children('.validation_message').length < 1 ) {
						$(this).after('<div class="gfield_description validation_message">This field is required.</div>');
					}				   
				} else {
					$(this).removeClass('err').closest('.ginput_container').children('.validation_message').fadeOut().remove();
				}				
			});
			
			if(input_errors){
				if( $formHeader.children('.validation_error').length < 1 ){
					$formHeader.append('<div class="validation_error">There was a problem with your submission. Errors have been highlighted below.</div>');
				}
				$subBtn.attr('disabled', false);
				$loader.hide();
				$("html, body").animate({ scrollTop: 0 }, "slow");
				$pricingOverlay.css({'height':(($(document).height()) )+'px'});
				return false;
			}

			// ajax all the things
			var request = $.ajax({
				type : "POST",
				url : calc_jax.ajaxurl,
				data : $(this).serialize(),
				dataType : 'json'
			}).done(function( response ) {
				// -1 means an error, 1 means success
				if('-1' == response.code){					
					msg = '<div class="err">'+response.notice + '</div>';
					$responseErrors.empty().html(msg).fadeIn('fast', function() {});
				} else if('1' === response.code) {
					msg = '<div class="alert alert-success">'+response.notice + '</div>';
					//$pricingOverlay.css('height', '150%');
					$('#calc-title').text('Your Results');
					$form.fadeOut( function(){
						$calcResults.empty().html(msg).fadeIn('fast', function(){
							$calcResponse.fadeIn();
						});					
					});
					$("html, body").animate({ scrollTop: 0 }, "slow");
				}
			}).fail(function(response){				
				$responseErrors.addClass('err').html('Error connecting to server').fadeIn();				
			});	
			
			$subBtn.attr('disabled', false);
			
			
			// Prevent the form from submitting with the default action
			return false;
						
		});
			
				
			
	}
	

	
	
	
	
	
	
});