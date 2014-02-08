<div class="hcc-wrap clearfix">

	<h3 id="api-keys">Email Settings</h3>
	<p>Use this form to configure various email settings for the results email that will be sent after the user submits the calculator.</p>
	<p>The following can be configured:</p>
	<ul>
		<li>Subject line;</li>
		<li>Email recipients;</li>
		<li>"From" Email Address</li>
		<li>"From" Email Name</li>
		<li>a message to appear <strong>before</strong> the calculated results (will default to text set in <a href="<?php echo add_query_arg(array('tab'=> 'calc_admin'), CALC_BASE_URL); ?>">Calculator Settings</a> if left blank);</li>
		<li>a message to appear <strong>after</strong> the results (will default to text set in <a href="<?php echo add_query_arg(array('tab'=> 'calc_admin'), CALC_BASE_URL); ?>">Calculator Settings</a>  if left blank);</li>
	</ul>
	<form id="hcc-configform-keys" method="post" class="hcc-configform postbox clearfix" action="<?php echo add_query_arg(array('tab'=> 'calc_admin'), CALC_BASE_URL); ?>">
		<div class="clearfix">

			<?php
			$calc_email_settings = get_option('calc_email_settings');
			$calc_email_subject_line = ( isset($calc_email_settings['subject_line']) ) ? $calc_email_settings['subject_line'] : 'Your Suretint Cost Savings Results';
			$calc_email_email_recipients = ( isset($calc_email_settings['email_recipients']) ) ? $calc_email_settings['email_recipients'] : get_option('admin_email');
			$calc_email_from_email = ( isset($calc_email_settings['from_email']) ) ? $calc_email_settings['from_email'] : get_option('admin_email');
			$calc_email_from_name = ( isset($calc_email_settings['from_name']) ) ? $calc_email_settings['from_name'] : get_bloginfo( 'name' );			
			$calc_email_messages_pre_results = ( isset($calc_email_settings['pre_results']) ) ? $calc_email_settings['pre_results'] : '';
			$calc_email_messages_post_results = ( isset($calc_email_settings['post_results']) ) ? $calc_email_settings['post_results'] : '';
			?>
			
			<div class="clearfix fields">
				<label for="calc_email_subject_line"><?php _e('Email Subject Line:' );?></label>
				<input id="calc_email_subject_line" type="text" name="calc_email_subject_line" value="<?php echo wp_unslash( CalcUtils::get_param('calc_email_subject_line', $calc_email_subject_line) )?>" />
			</div>	

			<div class="clearfix fields">
				<label for="calc_email_email_recipients"><?php _e('Email Recipients: (Separate multiple addresses with a comma)' );?></label>
				<input id="calc_email_email_recipients" type="text" name="calc_email_email_recipients" value="<?php echo wp_unslash( CalcUtils::get_param('calc_email_email_recipients', $calc_email_email_recipients) )?>" />
			</div>	

			<div class="clearfix fields">	
				<label for="calc_email_from_email"><?php _e('"From" Email Address:' );?></label>
				<input id="calc_email_from_email" type="text" name="calc_email_from_email" value="<?php echo wp_unslash( CalcUtils::get_param('calc_email_from_email', $calc_email_from_email) )?>" />
			</div>	

			<div class="clearfix fields">
				<label for="calc_email_from_name"><?php _e('"From" Email Name:' );?></label>
				<input id="calc_email_from_name" type="text" name="calc_email_from_name" value="<?php echo wp_unslash( CalcUtils::get_param('calc_email_from_name', $calc_email_from_name) )?>" />
			</div>				

			<div class="clearfix fields">
				<label for="pre_results"><?php _e('Pre-Results Message:' );?></label>
				<?php
				$content =  wp_unslash( CalcUtils::get_param('calc_email_messages_pre_results', $calc_email_messages_pre_results) );
				$quicktags_settings = array( 'buttons' => 'strong,em,link,ul,li,close' );
				$editor_args = array( 'media_buttons' => false, 'textarea_rows' => 10, 'textarea_name' => 'calc_email_messages_pre_results', 'teeny' => false, 'quicktags' => $quicktags_settings );
				wp_editor( $content, 'calc_email_messages_pre_results', $editor_args );
				?>
			</div>

			<div class="clearfix fields">
				<label for="pre_results"><?php _e('Post-Results Message:' );?></label>
				<?php
				$content =  wp_unslash( CalcUtils::get_param('calc_email_messages_post_results', $calc_email_messages_post_results) );
				$quicktags_settings = array( 'buttons' => 'strong,em,link,ul,li,close' );
				$editor_args = array( 'media_buttons' => false, 'textarea_rows' => 10, 'textarea_name' => 'calc_email_messages_post_results', 'teeny' => false, 'quicktags' => $quicktags_settings );
				wp_editor( $content, 'calc_email_messages_post_results', $editor_args );
				?>
			</div>

			<input type="hidden" name="action" value="calc_setup_config" />
			<input type="hidden" name="util_action" value="calc_email_config" />
			<input type="hidden" name="plugin" value="<?php echo CALC_BASE_NAME;?>" />
			<?php wp_nonce_field(); ?>

			<?php submit_button('Update'); ?>
		</div>
	</form>

</div>  <!-- /hcc-wrap -->