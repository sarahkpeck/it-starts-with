<?php
	if (isset($_POST['pf_save'])) {
		
		if (!wp_verify_nonce($_POST['wp_nonce'], 'pf_save')) {
			die('Security error!');
		}
		
		update_option('pf_feed1', $_POST['pf_feed1']);
		update_option('pf_feed2', $_POST['pf_feed2']);
		update_option('pf_enable', $_POST['pf_enable']);
		
		echo '<div id="message" class="updated"><p>Primary Feedburner settings saved.</p></div>';
	}
	
	$htaccess = trailingslashit(ABSPATH).'.htaccess';
	
	$rewriteRules = "RewriteEngine On\n" . 
		"RewriteBase /\n" . 
		"RewriteCond %{HTTP_USER_AGENT} !^(FeedBurner|FeedValidator) [NC]\n" .
		"RewriteRule ^feed/?([_0-9a-z-]+)?/?$ http://feeds.feedburner.com/" . get_option('pf_feed1') . " [R=301,NC,L]\n";
	
	if (strlen(get_option('pf_feed2')) > 0) {
		$rewriteRules .= "\nRewriteEngine On\n" . 
		"RewriteBase /\n" . 
		"RewriteCond %{HTTP_USER_AGENT} !^(FeedBurner|FeedValidator) [NC]\n" .
		"RewriteRule ^comments/feed/?([_0-9a-z-]+)?/?$ http://feeds.feedburner.com/" . get_option('pf_feed2') . " [R=301,NC,L]\n";
	}
			
	if (pf_canwrite($htaccess)) { 
		if (get_option('pf_enable') == 1 && strlen(get_option('pf_feed1')) > 0) {
		
			$wprules = implode("\n", extract_from_markers($htaccess, 'WordPress' ));
			
			pf_remove($htaccess, 'WordPress');
			pf_remove($htaccess, 'Primary Feedburner');
			insert_with_markers($htaccess,'Primary Feedburner', explode( "\n", $rewriteRules));
			insert_with_markers($htaccess,'WordPress', explode( "\n", $wprules));	
		} else {
			pf_remove($htaccess, 'Primary Feedburner');
		}
	} else {
		if (!$errorHandler) {
			$errorHandler = new WP_Error();
		}
			
		$errorHandler->add("1", __("Unable to update htaccess rules. You cannot use this plugin without the ability to write to your htaccess file. Contact your server administrator for more help."));
	}
	
	if (get_option('pf_enable') == 1 && strlen(get_option('pf_feed1')) == 0) {
		if (!$errorHandler) {
			$errorHandler = new WP_Error();
		}
			
		$errorHandler->add("1", __("Although you have turned the plugin on, you have not specified a feedburner feed for your primary feed. Your feeds will not redirect"));
	}
	
	if (isset($errorHandler)) {
		echo '<div id="message" class="error"><p>' . $errorHandler->get_error_message() . '</p></div>';
	} 	
?>

<div class="wrap" >

	<h2>Primary Feedburner Options</h2>
	
	<div id="poststuff" class="ui-sortable">
		
		<div class="postbox-container" style="width:70%">	
			<div class="postbox opened">
				<h3>Primary Feedburner Options</h3>	
				<div class="inside">
					<form method="post">
						<?php wp_nonce_field('pf_save','wp_nonce') ?>
						<table class="form-table">
							<tbody>
								<h4>Enable Primary Feedburner</h4>	
								<tr valign="top">
									<th scope="row">
										<label for="pf_enable">Enable Primary Feedburner</label>
									</th>
									<td>
										<label><input name="pf_enable" id="pf_enable" value="1" <?php if (get_option('pf_enable') == 1) echo 'checked="checked"'; ?> type="radio" /> On</label>
										<label><input name="pf_enable" value="0" <?php if (get_option('pf_enable') == 0) echo 'checked="checked"'; ?> type="radio" /> Off</label>
									</td>
								</tr>
							</tbody>
						</table>	
						<table class="form-table">
							<h4>Main Site Feeds</h4>
							<tbody>
								<tr valign="top">
									<th scope="row">
										<label for="pf_feed1"><strong><a href="<?php echo home_url() . "/feed"; ?>" target="_blank"><?php echo home_url() . "/feed"; ?></a></strong></label>
									</th>
									<td>
										<em style="color: #777;">http://feeds.feedburner.com/<input type="text" name="pf_feed1" id="pf_feed1" value="<?php echo get_option('pf_feed1'); ?>"></em>
									</td>
								</tr>
								<tr valign="top">
									<th scope="row">
										<label for="pf_feed2"><strong><a href="<?php echo home_url() . "/comments/feed"; ?>" target="_blank"><?php echo home_url() . "/comments/feed"; ?></a></strong></label>
									</th>
									<td>
										<em style="color: #777;">http://feeds.feedburner.com/<input type="text" name="pf_feed2" id="pf_feed2" value="<?php echo get_option('pf_feed2'); ?>"></em>
									</td>
								</tr>
							</tbody>
						</table>	
						<p class="submit"><input type="submit" name="pf_save" value="<?php _e('save', 'primary-feedburner'); ?>"></p>
					</form>
				</div>
			</div>
		</div>
	
		<div class="postbox-container" style="width:29%">
			<div class="postbox opened">
				<h3>Please Donate</h3>
				<div class="inside">
					<span style="text-align: center">
						<p><strong>If you find this plugin useful please consider a small donation.</strong></p>
						<form action="https://www.paypal.com/cgi-bin/webscr" method="post">
							<input type="hidden" name="cmd" value="_s-xclick">
							<input type="hidden" name="hosted_button_id" value="6Q7VXQMUV8QX2">
							<input type="image" src="https://www.paypalobjects.com/en_US/i/btn/btn_donateCC_LG.gif" border="0" name="submit" alt="PayPal - The safer, easier way to pay online!">
							<img alt="" border="0" src="https://www.paypalobjects.com/en_US/i/scr/pixel.gif" width="1" height="1">
						</form>
						<p>Users who have made a donation will receive priority for both support and feature requests.</p>
					</span>
				</div>
			</div>
		</div>
	</div>
</div>