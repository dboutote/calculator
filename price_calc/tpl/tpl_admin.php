<div class="hcc-wrap clearfix">

	<h3 id="api-keys">Price Calculator Form Settings</h3>
	<p>Use this form to configure the message that will appear after the user submits the calculator.</p>
	<p>There are two messages that can be configured:</p>
	<ul>
		<li>a message to appear <strong>before</strong> the calculated results;</li>
		<li>a message to appear <strong>after</strong> the results</li>
	</ul>
	<form id="hcc-configform-keys" method="post" class="hcc-configform postbox clearfix" action="<?php echo add_query_arg(array('tab'=> 'calc_admin'), CALC_BASE_URL); ?>">
		<div class="clearfix">

			<?php
			$calc_form_messages = get_option('calc_form_messages');
			$calc_form_messages_pre_results = ( isset($calc_form_messages['pre_results']) ) ? $calc_form_messages['pre_results'] : '';
			$calc_form_messages_post_results = ( isset($calc_form_messages['post_results']) ) ? $calc_form_messages['post_results'] : '';
			?>

			<div class="clearfix fields">
				<label for="pre_results"><?php _e('Pre-Results Message:' );?></label>
				<?php
				$content =  wp_unslash( CalcUtils::get_param('calc_form_messages_pre_results', $calc_form_messages_pre_results) );
				$quicktags_settings = array( 'buttons' => 'strong,em,link,ul,li,close' );
				$editor_args = array( 'media_buttons' => false, 'textarea_rows' => 10, 'textarea_name' => 'calc_form_messages_pre_results', 'teeny' => false, 'quicktags' => $quicktags_settings );
				wp_editor( $content, 'calc_form_messages_pre_results', $editor_args );
				?>
			</div>

			<div class="clearfix fields">
				<label for="pre_results"><?php _e('Post-Results Message:' );?></label>
				<?php
				$content =  wp_unslash( CalcUtils::get_param('calc_form_messages_post_results', $calc_form_messages_post_results) );
				$quicktags_settings = array( 'buttons' => 'strong,em,link,ul,li,close' );
				$editor_args = array( 'media_buttons' => false, 'textarea_rows' => 10, 'textarea_name' => 'calc_form_messages_post_results', 'teeny' => false, 'quicktags' => $quicktags_settings );
				wp_editor( $content, 'calc_form_messages_post_results', $editor_args );
				?>
			</div>

			<input type="hidden" name="action" value="calc_setup_config" />
			<input type="hidden" name="util_action" value="calc_form_config" />
			<input type="hidden" name="plugin" value="<?php echo CALC_BASE_NAME;?>" />
			<?php wp_nonce_field(); ?>

			<?php submit_button('Update'); ?>
		</div>
	</form>

</div>  <!-- /hcc-wrap -->