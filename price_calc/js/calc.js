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
		var validator = {};

		//$pricingOverlay.css({'height':(($(document).height())+100)+'px'});

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
				validator.resetForm();
				$calcResponse.hide();
				$calcResults.empty();
				$calcForm.css('display', '').reset();
				$('#calc-title').text($calcTitleText);
				$('.gfield_description', $calcForm).remove();
				$('.error', $calcForm).removeClass('err').removeClass("error");
				$('.form-header .validation_error', $calcForm).remove();
				$('.response-errors', $calcForm).empty();
				$('.otr-control', $calcForm).val('').hide();
			});

			return false;
		});


		//Handle Select With Other
		$("select.with_other").on('change', function(){
			var option_type = $(this).val();
			var $otherInput = $(this).parent().find("input.otr-control");
			switch(option_type) {
				case "otr":
					$otherInput.val('').fadeIn();
					break;
				default:
					$otherInput.val('').fadeOut();
			};
			
		});

		//Initialize validation
		validator = $calcForm.validate({ rules: {
			calc_colorcost_otr: {
		    	required: true,
		    	min: 2.75
		    },
		    calc_coloramt_otr: {
		      required: true,
		      min: 2.2
		    },
		    calc_colorservices_otr: {
		    	required: true,
		    	min: 50
		    }

		  }});

        $("#calc_phone").inputmask("mask", {"mask": "(999) 999-9999"});

		// submitting the form
		$calcForm.on('submit', function(event){
			var $form = $(this);
			var $formHeader = $('.form-header', $form);
			var $subBtn = $('input[type=submit]', $form);
			$calcResults.empty();
			var input_errors = 0;
			var $loader = $('.loading', $form);

			//Bail if invalid
			if(!$form.valid()) { 
				if( $formHeader.children('.validation_error').length < 1 ){
					$formHeader.append($('<div />', {class: 'validation_error', text: 'There was a problem with your submission. Errors have been highlighted below.'}));
				}
				$subBtn.attr('disabled', false);
				$loader.hide();
				$("html, body").animate({ scrollTop: 0 }, "slow");
				return false; 
			} 

			// Disable the submit button to prevent repeated clicks
			$subBtn.attr('disabled', true);
			$loader.show();


			// ajax all the things
			var request = $.ajax({
				type : "POST",
				url : calc_jax.ajaxurl,
				data : $(this).serialize(),
				dataType : 'json'
			}).done(function( response ) {
				// -1 means an error, 1 means success
				if('-1' == response.code){					
					var $msg = $('<div>').addClass("err").html(response.notice);
					$responseErrors.empty().html($msg).fadeIn('fast', function() {});
				} else if('1' === response.code) {
					var $msg = $('<div />', {class: "alert alert-success", html: response.notice});
					$pricingOverlay.css('height', '150%');
					$('#calc-title').text('Your Potential Savings With SureTint');
					$form.fadeOut( function(){
						$calcResults.empty().append($msg).fadeIn('fast', function(){
							$calcResponse.fadeIn();
						});					
					});
					$("html, body").animate({ scrollTop: 0 }, "slow");
				}
			}).fail(function(response){				
				$responseErrors.addClass('err').html('Error connecting to server').fadeIn();				
			}).always(function(){
				$loader.hide();
			});	
			
			$subBtn.attr('disabled', false);
			
			
			// Prevent the form from submitting with the default action
			return false;
						
		});		
	}
});