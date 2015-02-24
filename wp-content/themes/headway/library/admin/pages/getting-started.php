<div class="headway-small-wrap headway-getting-started-wrap">
	
	<h3 class="headway-sub-title">
		Woot! You installed Headway. Now you're ready to start making Headway! If you ever run into any problems, jump onto our <a href="http://support.headwaythemes.com/" target="_blank">support forums</a> or give our <a href="http://docs.headwaythemes.com/" target="_blank">documentation</a> a visit.
	</h3>

	<?php if ( is_main_site() ): ?>
		<div id="headway-getting-started-license-notice" class="alert alert-yellow">
			<p>Please be sure to enter your license key in <a href="<?php echo admin_url('admin.php?page=headway-options'); ?>" target="_blank">Headway Options</a> to receive updates and access to <a href="<?php echo admin_url('admin.php?page=headway-extend'); ?>" target="_blank">Headway Extend</a>.</p>
		</div>
	<?php endif; ?>
		
	<div id="headway-getting-started-ve-link-container">
		<input type="submit" value="Ready to jump right in? Enter the Visual Editor!" class="headway-big-button button-secondary action" id="headway-getting-started-ve-link" name="" onclick="window.location.href = '<?php echo home_url() . '/?visual-editor=true'; ?>'" />
	
		<p>
			You can hide this page by changing the <em>Default Admin Page</em> in <a href="<?php echo admin_url('admin.php?page=headway-options'); ?>" target="_blank">Headway » Options</a>.
		</p>
	</div>

</div>