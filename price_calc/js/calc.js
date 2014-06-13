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
		var UnitOfMeasure = 'oz';
		var gramCheck = 28;

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

		
		// check to see if the value falls outside a given range
		$.validator.addMethod("antirange", function(value, element) {
			var amt = parseFloat(value);
			return ( amt >= 10.1 && amt <= 27.9 ) == 0;	
		}, 'Enter sizes of Color Tubes only.');
		
		// check to see if the value falls within a range (based on ounces or grams)
		$.validator.addMethod("gramcheck", function(value, element) {
			var amt = parseFloat(value);
			UnitOfMeasure = ( amt >= gramCheck ) ? 'gr' : 'oz';	
			if( 'gr' === UnitOfMeasure && amt >= 28 && amt <= 150 ){
				return true;
			}
			if( 'oz' === UnitOfMeasure && amt >= 2.2 && amt <= 10 ){
				return true;
			}			
			return false;	
		}, 
			function(){
				return ( 'oz' === UnitOfMeasure ) ? 'Please enter an amount between 2.2 and 10 (ounces)' : 'Please enter an amount between 28 and 150 (grams)';
			}
		);		
		
		//Initialize validation
		validator = $calcForm.validate({
			rules: {
				calc_colorcost_otr: {
					required: true,
					min: 2.75,
					max: 30
				},
				calc_tubesize_otr : {
					required: true,
					max: 285,
					antirange: true
				},
				calc_coloramt_otr: {
					required: true,
					gramcheck: true
				},
				calc_colorservices_otr: {
					required: true,
					min: 50
				}
			},
			messages: {
				calc_colorcost_otr: {
					max: function ( params, element){
						return 'Enter cost for Color containers only (max $30).';					
					}
				},
				calc_tubesize_otr: {
					max: function ( params, element){
						return 'Enter sizes of Color Tubes only.';					
					}
				},
				calc_coloramt_otr: {
					max: function ( params, element){
						return 'A typical batch size is about 4 oz (115 grams).';					
					}
				}				
			}
		});

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