<?php
if ( !class_exists("CalcUtils") ) {

	class CalcUtils {

		public static $notice_args = array();
			
		/**
		 * utility function for displaying notices.
		 */
		public function show_notice(){
			$class = self::$notice_args['class'];
			$notice = self::$notice_args['notice'];
			echo '<div class="'.$class.'">'.$notice.'</div>';
		}	
		
		
		/**
		 * utility function to clean data arrays
		 * 
		 * @uses wp_kses() to filter all html/script from input
		 * @uses esc_attr() to encode
		 */
		function sanitize_data_array($submited_data){

			$in = array(&$submited_data);

			while ( list($k,$v) = each($in) ) {
				foreach ( $v as $key => $val ) {
					if ( !is_array($val) ) {
						$in[$k][$key] = trim(stripslashes($val));
						$in[$k][$key] = wp_kses( $in[$k][$key], $allowed_html = array() );
						$in[$k][$key] = esc_attr($in[$k][$key]);
						$in[$k][$key] = trim($in[$k][$key]);

						continue;
					};
					if ( is_array($val) ) {
						$in[$k][$key] = array_filter($val);
					};
					$in[] =& $in[$k][$key];
				};
			};

			unset($in);
			return $submited_data;

		}
		
		
		/**
		 * utility function to grab the $_GET or $_POST parameter
		 */
		public function get_param($param, $default='') {
			return (isset($_POST[$param])?$_POST[$param]:(isset($_GET[$param])?$_GET[$param]:$default));
		}
		
	
		/**
		 * utility function to check an array of values against an array of required values
		 * 
		 */
		public function check_required_fields($required_fields='', $data=''){

			$errors = array();

			if(!$required_fields)
				$errors['required_fields'] = 'required fields not provided';

			if(!$data)
				$errors['data'] = 'data values not provided';

			// compare required with actual keys
			foreach ($required_fields as $k => $v) {
				if( !isset($data[$k]) || '' === $data[$k]  ) {
					$errors[$k] = __( $v );
				}
			};

			do_action('calc_check_required_fields', $required_fields, $data, $errors);
			
			$errors = apply_filters('calc_check_required_fields_errors', $errors, $required_fields, $data);

			return $errors;
		}
		
	}
}