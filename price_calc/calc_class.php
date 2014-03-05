<?php
if( !class_exists('TintCalc') ) {

	class TintCalc {


		function __construct() {
			add_action('init', array(&$this,'parse_plugin_request'));
			add_action('init', array(&$this, 'register_scripts_frontend'));
			add_action('init', array(&$this,'register_styles_frontend'));
			add_action('wp_enqueue_scripts', array(&$this, 'add_scripts_frontend'));
			add_action('wp_enqueue_scripts', array(&$this,'add_styles_frontend'));
			
			add_action( 'wp_ajax_st_setup_pricecalc', array(&$this, 'process_pricecalc_jax'));
			add_action( 'wp_ajax_nopriv_st_setup_pricecalc', array(&$this, 'process_pricecalc_jax'));
			add_shortcode('calc_button', array(&$this, 'calc_button'));
			add_action('wp_footer', array(&$this, 'calc_form') );
		}
		
		
		function calc_form(){
			$template_name = 'form-calc.php';
			$template = CALC_TPL_DIR . '/' . $template_name;
			ob_start();
			include ( $template );
			$form = ob_get_contents();
			ob_end_clean();
			echo $form;
		}
		
		
		function calc_button( $atts, $content = null ){
			extract(
				shortcode_atts(
					array(
						'link' => '#',
						'text' => __('Pricing Calculator')
					), 
					$atts
				));
			return '<a class="overlay-pricing button" href="'.$link.'">' .do_shortcode( $content ). '</a>';			
		}


		// ajax all the things
		function process_pricecalc_jax(){
			self::process_pricecalc($ajax = true);
		}

				

		function process_pricecalc($ajax = false){
			$raw_data = $_POST;
			$errors = array();
			$response = array();
			$calc_colorcost_otr = $calc_tubesize_otr = $calc_coloramt_otr = $calc_colorservices_otr = '';
			
			// sanitize the data
			$data = CalcUtils::sanitize_data_array($raw_data);
			
			// if the bot field is filled in, just return
			if('' !== $data['pctob'] ){ return; }			
			
			// check all required fields
			$required_fields = array(
				'calc_name' => __("Please enter your name"),
				'calc_title' => __("Please enter your title"),
				'calc_email' => __("Please enter your email address"),
				'calc_phone' => __("Please enter your phone number"),
				'calc_salon' => __("Please enter the name of your salon"),
				'calc_address' => __("Please enter the address of your salon"),
				'calc_address_city' => __("Please enter the city of your salon"),
				'calc_address_state' => __("Please enter the state of your salon"),
				'calc_address_zip' => __("Please enter the zip of your salon"),
				'calc_pos' => __("Please enter the name of your POS system"),
				'calc_colors' => __("Please enter your color lines"),
				'calc_colorcost' => __("Please enter your average cost of color"),
				'calc_tubesize' => __("Please enter your average tube/bottle size"),
				'calc_coloramt' => __("Please enter the average amount of color used per service"),
				'calc_colorservices' => __("Please enter the average number of color services per week")								
			);


			// if they select other for their color cost
			if('otr' === $data['calc_colorcost']){
				$required_fields['calc_colorcost_otr'] = __("Please enter your average cost of color");
			}
			
			// if they select other for their tube size
			if('otr' === $data['calc_tubesize']){
				$required_fields['calc_tubesize_otr'] = __("Please enter your average tube/bottle size");
			}

			// if they select other for their avg amount of color used
			if('otr' === $data['calc_coloramt']){
				$required_fields['calc_coloramt_otr'] =__("Please enter the average amount of color used per service");
			}

			// if they select other for their avg number of color services
			if('otr' === $data['calc_colorservices']){
				$required_fields['calc_colorservices_otr'] = __("Please enter the average number of color services per week");
			}	
			
			$errors = CalcUtils::check_required_fields($required_fields, $data);
						
			// if they didn't enter just a number: $data[calc_colorcost_otr]
			if( !empty($data['calc_colorcost_otr']) && !is_numeric($data['calc_colorcost_otr']) ){
				$errors['nonum_calc_colorcost_otr'] = __('Please enter only numbers for Average Cost of Color');
			}			

			// if they didn't enter just a number: $data[calc_tubesize_otr]
			if( !empty($data['calc_tubesize_otr']) && !is_numeric($data['calc_tubesize_otr']) ){
				$errors['nonum_calc_tubesize_otr'] = __('Please enter only numbers for Average Tube/Bottle Size');
			}

			// if they didn't enter just a number: $data[calc_coloramt_otr]
			if( !empty($data['calc_coloramt_otr']) && !is_numeric($data['calc_coloramt_otr']) ){
				$errors['nonum_calc_coloramt_otr'] = __('Please enter only numbers for Average Amount of Color Used');
			}

			// if they didn't enter just a number: $data[calc_colorservices_otr]
			if( !empty($data['calc_colorservices_otr']) && !is_numeric($data['calc_colorservices_otr']) ){
				$errors['nonum_calc_colorservices_otr'] = __('Please enter only numbers for Average Number of Total Salon Color Services');
			}
			
			extract($data);
			
			
			
			// if there's no errors, do the calculations
			if( empty($errors) ){
				// #1
				$one = ( '' !== $calc_colorcost_otr ) ? $calc_colorcost_otr : $calc_colorcost;
				// #2
				$two = ( '' !== $calc_tubesize_otr ) ? $calc_tubesize_otr : $calc_tubesize;
				// #3
				$three = ( '' !== $calc_coloramt_otr ) ? $calc_coloramt_otr : $calc_coloramt;
				// #4
				$four = ( '' !== $calc_colorservices_otr ) ? $calc_colorservices_otr : $calc_colorservices;	

				// Estimated Cost Savings
				$three = absint($three);
				$estimated_cost_savings = (($three - 1.75) / $three);
				$estimated_cost_savings_percent = round(($estimated_cost_savings * 100), 2);
				$estimated_cost_savings_text = '<p>Estimated Cost Savings Using SureTint: ' .$estimated_cost_savings_percent. '%</p>';
				
				// Estimated Net Annual Savings
				$estimated_net_annual_savings = (((($one/$two)*$three)*$four)*$estimated_cost_savings)*52;
				$estimated_net_annual_savings = round($estimated_net_annual_savings);
				$estimated_net_annual_savings_text = ($estimated_net_annual_savings > 12000) ? '<p>Estimated Net Annual Savings Using SureTint: $' .number_format($estimated_net_annual_savings).'</p>' : '';
			
			}
						
			// if there's errors
			if( !empty($errors) ){
				$notice = '';
				foreach( $errors as $k => $v )
					$notice .= $v . '<br />';

				$error_notice = '<p>'.$notice.'</p>';
				
				if( true === $ajax ) {
					$response['code'] = '-1';
					$response['notice'] = $error_notice ;
					die(json_encode($response));
				} else {
					CalcUtils::$notice_args = array( 'class' => 'alert alert-danger account-notice', 'notice' => $error_notice );
					add_action('hcs_notices', array('CalcUtils', 'show_notice') );
				}
			}
			
			// if there's no errors 
			if( empty($errors) ){
			
				// get the custom results message
				$calc_form_messages = get_option('calc_form_messages');
				$pre_results = ( isset($calc_form_messages['pre_results']) ) ? $calc_form_messages['pre_results'] : '';
				$post_results = ( isset($calc_form_messages['post_results']) ) ? $calc_form_messages['post_results'] : '';

				$calc_results = $estimated_cost_savings_text;
				$calc_results .= $estimated_net_annual_savings_text;
				$notice = '<div id="calc-results-header">'.wpautop($pre_results).'</div>';
				$notice .= '<div id="calc-results-body">'.$calc_results.'</div>';
				$notice .= '<div id="calc-results-footer">'.wpautop($post_results).'</div>';
				
				
				// build/send the email
				$calc_email_settings = get_option('calc_email_settings');
				$calc_email_subject_line = ( isset($calc_email_settings['subject_line']) ) ? $calc_email_settings['subject_line'] : 'Your Suretint Cost Savings Results';
				$calc_email_email_recipients = ( isset($calc_email_settings['email_recipients']) ) ? $calc_email_settings['email_recipients'] : get_option('admin_email');
				$calc_email_from_email = ( isset($calc_email_settings['from_email']) ) ? $calc_email_settings['from_email'] : get_option('admin_email');
				$calc_email_from_name = ( isset($calc_email_settings['from_name']) ) ? $calc_email_settings['from_name'] : get_bloginfo( 'name' );			
				$calc_email_messages_pre_results = ( isset($calc_email_settings['pre_results']) && '' !== $calc_email_settings['pre_results'] ) ? $calc_email_settings['pre_results'] : $pre_results;
				$calc_email_messages_post_results = ( isset($calc_email_settings['post_results']) && '' !== $calc_email_settings['post_results'] ) ? $calc_email_settings['post_results'] : $post_results;
				
				$headers = "From: ".$calc_email_from_name." <".$calc_email_from_email."> \r\n";
				$headers .= "Return-Path: ".$calc_email_from_email." \r\n";
				$headers .= "MIME-Version: 1.0\r\n";
				$headers .= "Content-Type: text/html; charset=UTF-8\r\n";
												
				// user email
				$pre_results_user = wpautop($calc_email_messages_pre_results);
				$post_results_user = wpautop($calc_email_messages_post_results);
				
				$to_user = $calc_email;
				$subject_user = $calc_email_subject_line;
				
				// we use params from extract($data);
				ob_start();
					include(CALC_TPL_DIR . '/tpl_email_user.html');
					$message_user = ob_get_contents();
				ob_end_clean();
				
				
				@wp_mail( $to_user, $subject_user, $message_user, $headers);
				
				
				// admin email
				//  these are the params from the form: 
				/*
				$calc_name;
				$calc_title;
				$calc_email;
				$calc_phone;
				$calc_salon;
				$calc_address;
				$calc_address_city;
				$calc_address_state;
				$calc_address_zip;
				$calc_pos;
				$calc_colors;
				$calc_colorcost;
				$calc_colorcost_otr;
				$calc_tubesize;
				$calc_tubesize_otr;
				$calc_coloramt;
				$calc_coloramt_otr;
				$calc_colorservices;
				$calc_colorservices_otr;
				calc_partial_percentage;
				*/
				
				
				$calc_colorcost = ('' !== $calc_colorcost_otr ) ? $calc_colorcost_otr : $calc_colorcost;
				$calc_tubesize = ('' !== $calc_tubesize_otr ) ? $calc_tubesize_otr : $calc_tubesize;
				$calc_coloramt = ('' !== $calc_coloramt_otr ) ? $calc_coloramt_otr : $calc_coloramt;
				$calc_colorservices = ('' !== $calc_colorservices_otr ) ? $calc_colorservices_otr : $calc_colorservices;
				
				
				$admin_email_msg = json_encode($data);
				$admin_email_msg .= '<div id="calc-results-body">'.$calc_results.'</div>';
				
				$to_admin = explode(',', $calc_email_email_recipients);
				$subject_admin = __('New Cost Savings Calculator Submission');
				
				// we use params from extract($data);
				ob_start();
					include(CALC_TPL_DIR . '/tpl_email_admin.html');
					$message_admin = ob_get_contents();
				ob_end_clean();				
				
				@wp_mail( $to_admin, $subject_admin, $message_admin, $headers);
				
				
				
				
				
				
				
				if( true === $ajax ) { 
					$response['code'] = '1';
					$response['notice'] = $notice;
					die(json_encode($response));
				} else {					
					CalcUtils::$notice_args = array( 'class' => 'alert alert-success account-notice', 'notice' => $notice );
					add_action('hcs_notices', array('CalcUtils', 'show_notice') );					
				}
			}
			
			if( true === $ajax ) {  die('0'); }	
						
		}

		/**
		 * Register stylesheets in the front end
		 *
		 * @uses wp_register_style()
		 */
		function register_styles_frontend(){
			wp_register_style('calc-fonts', "http://fast.fonts.net/cssapi/4b48a6cf-5251-4631-920f-e61b3fe82937.css", array(), '1', 'all');
			wp_register_style( 'calc-front', CALC_CSS_URL . '/calc.css', array('calc-fonts'), '1.0.0', 'all' );
		}
		

		/**
		 * load CSS in the front end
		 *
		 * @uses wp_enqueue_style()
		 */
		function add_styles_frontend() {
			if( !is_admin() ) {
				wp_enqueue_style( 'calc-front' );
			}
		}
		

		/**
		 * Register scripts in the front end
		 *
		 * @uses wp_register_script()
		 */
		function register_scripts_frontend(){
			if( !is_admin() ) {
				wp_register_script( 'calc_scripts', CALC_JS_URL  . '/calc.js', array( 'jquery' ), 1.0, true );
				wp_register_script( 'jquery.validate', CALC_JS_URL  . '/jquery.validate.min.js', array( 'jquery' ), 1.0, true );
				wp_register_script( 'jquery.validate-additional', CALC_JS_URL  . '/additional-methods.min.js', array( 'jquery' ), 1.0, true );
				wp_register_script( 'jquery.inputmask', CALC_JS_URL  . '/jquery.inputmask.bundle.min.js', array( 'jquery' ), 1.0, true );
			}
		}

		
		/**
		 * load scripts in the front end
		 *
		 * @uses wp_enqueue_script()
		 * @uses wp_localize_script()
		 */
		function add_scripts_frontend() {
			wp_enqueue_script('jquery.validate');
			wp_enqueue_script('jquery.validate-additional');
			wp_enqueue_script('jquery.inputmask');
			wp_enqueue_script('calc_scripts');

			wp_localize_script(
				'calc_scripts',
				'calc_jax',
				array(
					'ajaxurl' => admin_url( 'admin-ajax.php' ),
					'calcJSurl' => CALC_JS_URL
					)
			);

		}



        /**
		 * Process standalone $_GET or $_POST requests
		 */
        function parse_plugin_request() {

			// if ajax is processing, don't do anything
			if( defined('DOING_AJAX') && DOING_AJAX ){
				return;
			}

			// parameters
			$action = CalcUtils::get_param('action');
			$util_action = CalcUtils::get_param('util_action');

			if( 'st_pricecalc' === $util_action ){
				self::process_pricecalc();
			}

		}


	}
}



