<div id="cost-savings-quote" class="overlay pricing">

<span class="close"><span>Close</span></span>

<div class="container">

<h3 id="calc-title">Calculate My Color Savings</h3>

<form id="calculator" action="#" method="post" role="form">

	<div class="form-header">
		<p class="lead">Fill in the information below for your instant personal cost savings calculation and see how SureTint can positively impact your business.</p>
	</div>

	<div class="gform_body">
		<ul id="calculator-contactinfo" class="gform_fields">
			<li class="gfield gfield_contains_required">
				<label class="gfield_label" for="calc_name">Name<span class="gfield_required">*</span></label>
				<div class="ginput_container">
					<input id="calc_name" class="medium required" type="text" value="" name="calc_name" />
				</div>
			</li>
			<li class="gfield gfield_contains_required">
				<label class="gfield_label" for="calc_title">Title<span class="gfield_required">*</span></label>
				<div class="ginput_container">
					<input id="calc_title" class="medium required" type="text" value="" name="calc_title" />
				</div>
			</li>				
			<li class="gfield gfield_contains_required">
				<label class="gfield_label" for="calc_email">Email<span class="gfield_required">*</span></label>
				<div class="ginput_container">
					<input id="calc_email" class="medium required email" type="text" value="" name="calc_email" placeholder="name@email.com" />
				</div>
			</li>	
			<li class="gfield gfield_contains_required">
				<label class="gfield_label" for="calc_phone">Phone Number<span class="gfield_required">*</span></label>
				<div class="ginput_container">
				<input id="calc_phone" class="medium required phoneUS" type="text" value="" name="calc_phone" placeholder="(###) ###-####" />
				</div>
			</li>	
			<li class="gfield gfield_contains_required">
				<label class="gfield_label" for="calc_salon">Salon Name<span class="gfield_required">*</span></label>
				<div class="ginput_container">
					<input id="calc_salon" class="medium required" type="text" value="" name="calc_salon"  />
				</div>
			</li>	
			<li class="gfield gfield_contains_required">
				<label class="gfield_label" for="calc_address">Salon Address<span class="gfield_required">*</span></label>
				<div class="ginput_container">
					<input id="calc_address" class="medium required" type="text" value="" name="calc_address"  placeholder="5 Main St. City, ST 000000"/> 
				</div>
				<label class="gfield_label" for="calc_address2"></label>
				<div class="ginput_container">
					<input id="calc_address_city" class="medium required city" type="text" value="" name="calc_address_city" placeholder="City"/>
					<input id="calc_address_state" class="medium required state" type="text" value="" name="calc_address_state" placeholder="State"/>
					<input id="calc_address_zip" class="medium required zip zipcodeUS" type="text" value="" name="calc_address_zip" placeholder="Zip"/>
				</div>
			</li>	

			<li class="gfield gfield_contains_required">
				<label class="gfield_label" for="calc_pos">POS System<span class="gfield_required">*</span></label>
				<div class="ginput_container">
					<input id="calc_pos" class="medium required" type="text" value="" name="calc_pos" />
				</div>
			</li>						
			<li class="gfield gfield_contains_required">
				<label class="gfield_label" for="calc_colors">Color Line(s)<span class="gfield_required">*</span></label>
				<div class="ginput_container">
					<input id="calc_colors" class="medium required" type="text" value="" name="calc_colors" />
				</div>
			</li>				
		</ul>

		<ol id="calculator-qtys" class="gform_fields">
			<li class="gfield gfield_contains_required">
				<label class="gfield_label" for="calc_colorcost">Average Cost of color (tubes/bottles) used in your salon:<span class="gfield_required">*</span></label>
				<div class="ginput_container">
					<select id="calc_colorcost" name="calc_colorcost" class="required with_other">
						<?php 
						$color_costs = array(
							'5' => '$5.00',
							'5.5' => '$5.50',
							'6' => '$6.00',
							'6.5' => '$6.50',
							'7' => '$7.00',
							'7.5' => '$7.50',
							'8' => '$8.00',
							'8.5' => '$8.50',
							'otr' => 'Other'						
						);
						foreach($color_costs as $cost => $label) { ?>
							<option value="<?php echo $cost;?>"><?php echo $label; ?></option>					
						<?php } ?>				
					</select>
					<input placeholder="#.##" type="text" id="calc_colorcost_otr" name="calc_colorcost_otr" class="display-no otr-control required" />
				</div>
			</li>
			
			<li class="gfield gfield_contains_required">
				<label class="gfield_label" for="calc_tubesize">Average Tube/Bottle size in ounces:<span class="gfield_required">*</span></label>
				<div class="ginput_container">
					<select id="calc_tubesize" name="calc_tubesize" class="required with_other">
						<?php 
						$tube_sizes = array(
							'1.7' => '1.7oz',
							'2' => '2.0oz',
							'3' => '3.0oz',
							'5.1' => '5.1oz',
							'otr' => 'Other'						
						);
						foreach($tube_sizes as $size => $label) { ?>
							<option value="<?php echo $size;?>"><?php echo $label; ?></option>					
						<?php } ?>				
					</select>
					<input placeholder="#.##" type="text" id="calc_tubesize_otr" name="calc_tubesize_otr" class="display-no otr-control required" />
				</div>
			</li>
			
			<li class="gfield gfield_contains_required ">
				<label class="gfield_label" for="calc_coloramt">Average amount of color used per color service in ounces:<span class="gfield_required">*</span></label>
				<div class="ginput_container">
					<select id="calc_coloramt" name="calc_coloramt" class="required with_other">
						<?php 
						$color_amts = array(
							'2.75' => '2.75oz',
							'3' => '3.0oz',
							'3.25' => '3.25oz',
							'3.5' => '3.5oz',
							'otr' => 'Other'						
						);
						foreach($color_amts as $amt => $label) { ?>
							<option value="<?php echo $amt;?>"><?php echo $label; ?></option>					
						<?php } ?>				
					</select>
					<input placeholder="#.##" type="text" id="calc_coloramt_otr" name="calc_coloramt_otr" class="display-no otr-control required" />
				</div>
			</li>
			
			
			<li class="gfield gfield_contains_required">
				<label class="gfield_label" for="calc_colorservices">Average number of total color services per week:<span class="gfield_required">*</span></label>
				<div class="ginput_container">
					<select id="calc_colorservices" name="calc_colorservices" class="required with_other">
						<?php 
						$color_services = array(
							'50' => '50',
							'100' => '100',
							'150' => '150',
							'200' => '200',
							'250' => '250',
							'300' => '300',
							'350' => '350',
							'400' => '400',
							'450' => '450',
							'500' => '500',
							'otr' => 'Other'						
						);
						foreach($color_services as $qty => $label) { ?>
							<option value="<?php echo $qty;?>"><?php echo $label; ?></option>					
						<?php } ?>				
					</select>
					<input placeholder="###" type="text" id="calc_colorservices_otr" name="calc_colorservices_otr" class="display-no otr-control required" />
				</div>
			</li>			
		</ol>


	</div>

	<div class="gform_footer top_label">
		<input type="text" value="" name="pctob" class="assistive-text" />
		<input type="hidden" name="action" value="st_setup_pricecalc" />
		<input type="hidden" name="util_action" value="st_pricecalc" />			
		<input id="gform_submit_button_2" class="button gform_button" type="submit" value="Submit" />
		<div class="loading display-no">(One Moment <span class="spinner">&nbsp;</span>)</div>
		<div class="response-errors"></div>
		<div class="quote">
		<blockquote>
		"SureTint Technologies provided us with an exceptional color room management System. No serious hair color operation or salon should be without it."
        </blockquote>
        <div class="quoteByline">
        - Larry Silvestri, Senior VP & COO,
        Mario Tricoci Hair Salon and Day Spa
        </div>
        </div>
	</div>
	
</form>

	<div id="calcresponse"class="display-no">
		<?php do_action('pc_results_before');?>
		<div id="calcresults"></div>
		<?php do_action('pc_results_after');?>
	</div>	
	
</div>
</div>	