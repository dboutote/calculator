<?php

if ( !class_exists("CALC_Backend") ) {

	class CALC_Backend {

		//private $notice_args = array();
		private $tab = '';
		private $plugin_page_hook = '';

		public function __construct() {
			add_action('init', array(&$this,'get_plugin_request'));
			add_action('admin_init', array(&$this,'get_current_tab'));
			add_action('admin_enqueue_scripts', array(&$this,'admin_scripts')); // Use registered $page handle to hook stylesheet loading
			add_action('admin_enqueue_scripts', array(&$this,'admin_styles')); // Use registered $page handle to hook stylesheet loading
			add_filter('plugin_action_links_'.CALC_PLUGIN_HOOK, array(&$this, 'plugin_add_settings_link'));
			add_action('admin_menu', array(&$this, 'register_options_page') );
			add_action('calc_admin_notices', array(&$this, 'success_msg'));

			//add_filter('plugin_action_links_'.CALC_PLUGIN_HOOK, array(&$this, 'disable_plugin_deactivation'), 10, 4 );
		}

		// utility function to show success message
		function success_msg(){

			$class = 'success';
			$notice = '';
			$msg = CalcUtils::get_param('msg');

			switch($msg){
				case 'fmsg-updated':
					$class = "updated success account-notice";
					$notice = apply_filters('calc_pcadded',  '<p>Form settings updated</p>');
					break;
				case 'emsg-updated':
					$class = "updated success account-notice";
					$notice = apply_filters('calc_pcadded',  '<p>Email settings updated</p>');
					break;
				case 'updated':
					$class = "updated success account-notice";
					$notice = apply_filters('calc_pcadded',  '<p>Settings updated</p>');
					break;					
				default :
					$class = "updated success account-notice";
					$notice = "";
			}

			if( '' !== $notice ){
				CalcUtils::$notice_args = array( 'class' => $class, 'notice' => $notice );
				CalcUtils::show_notice();
			}

			return;
		}

		// Adds a link to the settings page on the plugins table
		function plugin_add_settings_link($links) {
			$settings_link = '<a id="calc-settings-link" href="'.CALC_BASE_URL.'">Settings</a>';
			array_push( $links, $settings_link );
			return $links;
		}


		// Shows help pointers on Plugins page
		function admin_pointers() {
			$pointer_content = '<h3>Pricing Calculator</h3>';
			$pointer_content .= '<p>Head over to the <a href="'.CALC_BASE_URL.'">Settings Page</a> to finish setting up the plugin.</p>';
			?>
		   <script type="text/javascript">
		   //<![CDATA[
			jQuery(document).ready( function($) {
				var topZ = 0;
				$('.wp-pointer').live('click', function(){
					$('.wp-pointer').each(function(){
						var thisZ = parseInt($(this).css('zIndex'), 10);
						if (thisZ > topZ){
							topZ = thisZ;
						}
					});
					$(this).css('zIndex', topZ+1);
				})
				$('#calc-settings-link').pointer({
					content: '<?php echo $pointer_content; ?>',
					position: {
						edge: 'left',
						align: 'left'
					},
					close: function() {
						$.post( ajaxurl, {
							pointer: 'calc_config_pointer',
							action: 'dismiss-wp-pointer'
						});
					}
				}).pointer('open');
			});
		   //]]>
		   </script>
			<?php
		}

		// utility function to load admin scripts
		function admin_scripts($hook){

			if( "plugins.php" === $hook ){
				$dismissed = explode( ',', (string) get_user_meta( get_current_user_id(), 'dismissed_wp_pointers', true ) );
				if ( ! in_array( 'calc_config_pointer', $dismissed ) ) {
					wp_enqueue_style( 'wp-pointer' );
					wp_enqueue_script( 'wp-pointer' );
					add_action( 'admin_print_footer_scripts', array(&$this, 'admin_pointers') );
				}
			}

			if( $hook !== $this->plugin_page_hook )
				return;

			wp_enqueue_script( 'calc-admin', CALC_JS_URL . '/calc-admin.js', array( 'jquery' ), '1.0.0' );

			wp_localize_script(
				'calc-admin',
				'calc_jax',
				array(
					'ajaxurl' => admin_url( 'admin-ajax.php' ),
					'calcJSurl' => CALC_JS_URL
					)
			);

		}

		
		// Load admin styles
		function admin_styles($hook){
			if($hook !== $this->plugin_page_hook && $hook !== "profile.php" && $hook !== "user-edit.php")
				return;

			wp_enqueue_style( 'calc-admin', CALC_CSS_URL . '/calc-admin.css', false, '1.0.0' );
		}

		
		// Register new options page
		function register_options_page(){
			$this->plugin_page_hook = add_menu_page( 'Calculator Settings', 'Pricing Calculator', 'manage_options', CALC_BASE_NAME, array(&$this, 'show_options_page'));
		}

		
		// Display the options page in the Dashboard
		function show_options_page() {

			if (!current_user_can('manage_options'))  {
				wp_die( __('You do not have sufficient permissions to access this page.') );
			}; ?>

			<div class="wrap">
				<h2>Cost Savings Calculator Settings</h2>

				<?php do_action('calc_admin_notices'); ?>
				<div class="ajax-response display-no"></div>

				<h2 class="nav-tab-wrapper">
					<a href="?page=<?php echo CALC_BASE_NAME; ?>&tab=calc_admin" class="nav-tab <?php echo ($this->tab === 'calc_admin') ? 'nav-tab-active' : ''; ?>">Calculator Settings</a>
					<a href="?page=<?php echo CALC_BASE_NAME; ?>&tab=calc_email" class="nav-tab <?php echo ($this->tab === 'calc_email') ? 'nav-tab-active' : ''; ?>">Email Settings</a>
				</h2>

				<?php
				switch($this->tab){
					case 'calc_admin':
						include ( CALC_TPL_DIR . '/tpl_admin.php' );
						break;
					case 'calc_email':
						include ( CALC_TPL_DIR . '/tpl_admin_email-config.php' );
						break;
					default:
						include ( CALC_TPL_DIR . '/tpl_admin.php' );
							break;
				}; ?>

			</div>  <!-- wrap -->

			<?php
		}

		// Get the current tab on options page
		function get_current_tab(){
			$this->tab = CalcUtils::get_param('tab', 'calc_admin');
			return $this->tab;
		}

		
		// updates a WP option
		function update_option($key, $value){
			return update_option($key, $value);
		}


		// Parses $_GET or $_POST requests
		function get_plugin_request() {

			// if ajax is processing, don't do anything
			if( defined('DOING_AJAX') && DOING_AJAX ){
				return;
			}

			// parameters
			$action = CalcUtils::get_param('action');

			// if we're updating form options in the backend
			if('calc_setup_config'=== $action){
				$util_action = CalcUtils::get_param('util_action');
				self::update_plugin_settings( $util_action );
			}
		}



		// process the plugin settings screen
		function update_plugin_settings($util_action){
			if(!$util_action)
				return;

			$errors = array();
			$raw_data = $_POST;

			// sanitize the data
			$data = CalcUtils::sanitize_data_array($raw_data);
			
			if( 'calc_form_config' === $util_action ){
				// CalcUtils::sanitize_data_array() strips ALL HTML, we need some for the message
				$data['calc_form_messages_pre_results'] = wp_kses($raw_data['calc_form_messages_pre_results'], 'post');			
				$data['calc_form_messages_post_results'] = wp_kses($raw_data['calc_form_messages_post_results'], 'post');				
			}
			
			if( 'calc_email_config' === $util_action ){
				// CalcUtils::sanitize_data_array() strips ALL HTML, we need some for the message
				$data['calc_email_messages_pre_results'] = wp_kses($raw_data['calc_email_messages_pre_results'], 'post');			
				$data['calc_email_messages_post_results'] = wp_kses($raw_data['calc_email_messages_post_results'], 'post');				
			}	

			extract($data);

			// if it passes validation, update the option
			if( empty($errors) ){

				if( 'calc_form_config' === $util_action ){
					// update the pre-results message
					$pre_value = ( isset($data['calc_form_messages_pre_results']) ) ? $data['calc_form_messages_pre_results'] : '';

					// update the post-results message
					$post_value = ( isset($data['calc_form_messages_post_results']) ) ? $data['calc_form_messages_post_results'] : '';

					$key = 'calc_form_messages';
					$value = array(
						'pre_results' => $pre_value,
						'post_results' => $post_value,
					);
				}
				
				if( 'calc_email_config' === $util_action ){
					// update the pre-results message
					$pre_value = ( isset($data['calc_email_messages_pre_results']) ) ? $data['calc_email_messages_pre_results'] : '';

					// update the post-results message
					$post_value = ( isset($data['calc_email_messages_post_results']) ) ? $data['calc_email_messages_post_results'] : '';

					$key = 'calc_email_settings';
					$value = array(
						'subject_line' => $calc_email_subject_line,
						'email_recipients' => $calc_email_email_recipients,
						'from_email' => $calc_email_from_email,
						'from_name' => $calc_email_from_name,
						'pre_results' => $pre_value,
						'post_results' => $post_value,
					);
				}
				$updated = self::update_option($key, $value);
				
			}

			// if there's errors
			if( !empty($errors) ){
				$notice = '';
				foreach( $errors as $k => $v )
					$notice .= $v . '<br />';

				$error_notice = '<p>'.$notice.'</p>';
				CalcUtils::$notice_args = array( 'class' => 'error', 'notice' => $error_notice );
				add_action('calc_admin_notices', array('CalcUtils', 'show_notice') );
			}

			// if there's no errors redirect them back to the page with a query arg
			if( empty($errors) ){
				$referrer = wp_get_referer();
				$redirect_to = remove_query_arg( array('msg'), $referrer );				
				$redirect_to = add_query_arg( array('msg'=>'updated'), $redirect_to );
				wp_redirect($redirect_to);
				exit;
			}

		}


		// remove the deactivate and edit links on the plugins.php screen
		function disable_plugin_deactivation( $actions, $plugin_file, $plugin_data, $context ) {

			// Remove edit link for all
			if ( array_key_exists( 'edit', $actions ) ) {
				unset( $actions['edit'] );
			}

			// Remove deactivate link for crucial plugins
			if ( array_key_exists( 'deactivate', $actions ) ) {
				unset( $actions['deactivate'] );
			}

			return $actions;
		}


		// unregister settings on plugin deactivation
		function plugin_deactivate(){
			return true;
		}


		// remove settings on plugin uninstall
		function plugin_uninstall() {
			return true;
		}


		// functionality to fire when the plugin is activated
		function plugin_activate() {}











	}
}