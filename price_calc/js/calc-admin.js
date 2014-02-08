jQuery(document).ready(function($){

	function createFromMysql(mysql_string) { 
	   if(typeof mysql_string === 'string') {
		  var t = mysql_string.split(/[- :]/);
		  //when t[3], t[4] and t[5] are missing they defaults to zero
		  return new Date(t[0], t[1] - 1, t[2], t[3] || 0, t[4] || 0, t[5] || 0);          
	   }
	   return null;   
	}

	// promo codes form
	if( $('#hcc-configform-promos') && $('#hcc-configform-promos').length > 0 ) {
		
		$('#pc_type').on('change', function(){
			var pc_type = $("#pc_type option:selected").val();
			var pholderText = '';
			
			switch(pc_type) {
				case "hcaf":
					pholderText = "HCAF-USER_ID";
					break;
				case 'hcap':
					pholderText = "HCAP-USER_ID";
					break;
				case 'hcau':
					pholderText = "HCAU-TEXT";
					break;
				case 'hccs':
					pholderText = "HCCS-USER_ID";
					break;		
				default:
					pholderText = "";
			};
			$('#pc_label').attr('placeholder', pholderText);
		})
		
		$('#pc_disc_amt').on('change', function(){
			var disc_amt = $(this).val();
			$('#pc_disc_type option[value="percentage"]').text('Percentage (e.g. -'+disc_amt+'%)');
			$('#pc_disc_type option[value="flat"]').text('Flat (e.g. -$'+disc_amt+')');
		});
		
		/*
		$('#pc_start').on('change', function(){
			var dVal = $(this).val();
			var d = createFromMysql(dVal);
			d.setDate(d.getDate()+30);
			var dYear = d.getFullYear();
			var dDate = d.getDate();
			var dMonth = d.getMonth() + 1;
			// add leading zero if the length equals 1
			if (dMonth < 10) dMonth = "0" + dMonth;
			if (dDate < 10) dDate = "0" + dDate;

			var dNextMonth = dYear + '-' + dMonth + '-' + dDate + ' 23:59:59';
			$('#pc_expire').val(dNextMonth);
		})
		*/
	
	}
	

});
