jQuery(document).ready(function($){
    
    // function to hide form labels
	$.fn.labelHide = function(){
		return this.each(function(){
			var current = $(this);
			var id = current.attr("id");
			var label = $("label[for='"+id+"']:not(.error)");						
			if (id.length && label.length) {			
				var default_text = label.text();
				
				label.hide();
				if ( current.val() === '' ||  current.val() === default_text) {
					current.val(default_text);
				}				
				current.focus(function() {
					if( $(this).val() === default_text ) {
						$(this).val('');
					}
				});
				current.blur(function() {
					if( $(this).val() === '' ) {
						$(this).val(default_text);
					}
				});		
			}
		});
	};
	
    // function to set value to blank if it matches its label
	$.fn.setBlank = function(){
		return this.each(function(){
			var current = $(this);
			var id = current.attr("id");
			var label = $("label[for='"+id+"']:not(.error)");						
			if (id.length && label.length) {			
				var default_text = label.text();					
				if ( current.val() === '' ||  current.val() === default_text) {
					current.val('');
				}	
			}
		});
	};	
	
	$('#prelaunch-signup input.text').labelHide();
	

	// Join List Email Form
    $('#prelaunch-signup').submit(function() {
		
		$messageDiv = $('.ajax-response');
		
		$messageDiv.hide();
	
		parentForm = $(this);
		
		$('input.text', parentForm).setBlank();

        $.post(
            hcs_jax.ajaxurl,
            $(this).serialize(),
            function(r){
                var response = jQuery.parseJSON(r);
				
                // -1 means an error, 1 means success
                if('-1' == response.code){
					msg = '<div class="alert alert-danger account-notice">'+response.notice + '</div>';
                    $messageDiv.empty().html(msg).fadeIn('fast', function() {});
                } else if('1' == response.code) {
					msg = '<div class="alert alert-success account-notice">'+response.notice + '</div>';
                    $messageDiv.empty().html(msg).fadeIn('fast', function() {});
                }
            }
        );

        return false;
    }); 
	
	// if there's a subscribe form
	if ( $('#mc-embedded-subscribe-form').length > 0 ) {
		var mcForm = $('#mc-embedded-subscribe-form');
		
		$('input.text', mcForm).labelHide();
		
		$('#mc-embedded-subscribe-form').submit(function() {
			
			$('input.text', mcForm).setBlank();
			
			$messageDiv = $('.response', mcForm);
			$messageDiv.hide();
			
			$.ajax({
				type: mcForm.attr('method'),
				url: mcForm.attr('action'),
				data: mcForm.serialize(),
				cache       : false,
				dataType    : 'jsonp',
				jsonp       : 'c',
				contentType : "application/json; charset=utf-8",
				error       : function(err) { alert("Could not connect to the registration server. Please try again later."); },
				success     : function(data) {	
					if (data.result != "success") {
						var notice = data.msg;
						var noticeClass = 'alert-danger';						
					} else {
						var notice = 'Great!  Check your email for a confirmation.';	
						var noticeClass = 'alert-success';						
					}
					var container = '<div class="alert '+noticeClass+'">';
					container += '<button type="button" class="close" data-dismiss="alert" aria-hidden="true">&times;</button>';
					container += notice;
					container += '</div>';
					$messageDiv.empty().html(container).fadeIn('fast', function() {});
					
				}
			});
			return false;
		});	
	}

	
});