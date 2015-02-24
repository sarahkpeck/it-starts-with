<?php
if(preg_match('#' . basename(__FILE__) . '#', $_SERVER['PHP_SELF'])) { die('You are not allowed to call this page directly.'); }

global $wpdb;
global $easy_gallery_table;

if(isset($_POST['galleryId'])) {
	if(check_admin_referer('wpeg_delete_gallery','wpeg_delete_gallery')) {
	  $wpdb->query( "DELETE FROM $easy_gallery_table WHERE Id = '".intval($_POST['galleryId'])."'" );
		  
	  ?>  
	  <div class="updated"><p><strong><?php _e('Gallery has been deleted.' ); ?></strong></p></div>  
	  <?php
	}
}

$galleryResults = $wpdb->get_results( "SELECT * FROM $easy_gallery_table" );

if (isset($_POST['defaultSettings'])) {
	if(check_admin_referer('wpeg_settings','wpeg_settings')) {
	  $temp_defaults = get_option('wp_easy_gallery_defaults');
	  $temp_defaults['hide_social'] = isset($_POST['hide_social']) ? $_POST['hide_social'] : 'false';
	  	  
	  update_option('wp_easy_gallery_defaults', $temp_defaults);
	  
	  ?>  
	  <div class="updated"><p><strong><?php _e('Options saved.', 'wp-easy-gallery'); ?></strong></p></div>  
	  <?php
	}
}
$default_options = get_option('wp_easy_gallery_defaults');
?>
<div class='wrap wp-easy-gallery-admin'>
	<h2>WP Easy Gallery</h2>
    <p>This is a listing of all galleries.</p>
    <p style="float: right;"><a href="http://labs.hahncreativegroup.com/wordpress-plugins/wp-easy-gallery-pro-simple-wordpress-gallery-plugin/?src=wpeg" target="_blank"><strong><em>Try WP Easy Gallery Pro</em></strong></a></p>
    <div style="Clear: both;"></div>
    <table class="widefat post fixed eg-table">
    	<thead>
        <tr>
        	<th>Gallery Name</th>
            <th>Gallery Short Code</th>
            <th>Description</th>
            <th class="eg-cell-spacer-136"></th>
        </tr>
        </thead>
        <tfoot>
        <tr>
        	<th>Gallery Name</th>
            <th>Gallery Short Code</th>
            <th>Description</th>
            <th></th>
        </tr>
        </tfoot>
        <tbody>
        	<?php foreach($galleryResults as $gallery) { ?>				
            <tr>
            	<td><?php _e($gallery->name); ?></td>
                <td><input type="text" size="40" value="[EasyGallery key='<?php _e($gallery->Id); ?>']" /></td>
                <td><?php _e($gallery->description); ?></td>
                <td class="major-publishing-actions">
                <form name="delete_gallery_<?php _e($gallery->Id); ?>" method ="post" action="<?php echo str_replace( '%7E', '~', $_SERVER['REQUEST_URI']); ?>">
                	<input type="hidden" name="galleryId" value="<?php _e($gallery->Id); ?>" />
                    <?php wp_nonce_field('wpeg_delete_gallery', 'wpeg_delete_gallery'); ?>
                    <input type="submit" name="Submit" class="button-primary" value="Delete Gallery" />
                </form>
                </td>
            </tr>
			<?php } ?>
        </tbody>
     </table>
     <br />
     <h3><?php _e('Default Options', 'wp-easy-gallery'); ?></h3>
     <p>Go to: <a href="?page=settings">Settings</a> page.</p>
     <hr />     
     <br />
<div style="float: left; width: 60%; min-width: 488px;">     
<p><strong>Try WP Easy Gallery Pro</strong><br /><em>Pro Features include: Multi-image uploader, Enhanced admin section for easier navigation, Image preview pop-up, and more...</em></p>
<p><a href="http://labs.hahncreativegroup.com/wordpress-gallery-plugin/?src=wpeg" target="_blank"><img title="WP-Easy-Gallery-Pro_468x88" src="../wp-content/plugins/wp-easy-gallery/images/WP-Easy-Gallery-Pro_468x88.gif" alt="" width="468" height="88" /></a></p>
<p><strong>Try WP Easy Gallery Premium</strong><br /><em>Premium Features all of the Pro features plus unlimited upgrades.</em><br />
<a href="http://labs.hahncreativegroup.com/wp-easy-gallery-premium/" target="_blank">WP Easy Gallery Premium</a></p>
<p><strong>Try Custom Post Donations Pro</strong><br /><em>This WordPress plugin will allow you to create unique customized PayPal donation widgets to insert into your WordPress posts or pages and accept donations. Features include: Multiple Currencies, Multiple PayPal accounts, Custom donation form display titles, and more.</em></p>
<p><a href="http://labs.hahncreativegroup.com/wordpress-paypal-plugin/?src=wpeg"><img src="../wp-content/plugins/wp-easy-gallery/images/CustomPostDonationsPro-Banner.gif" width="374" height="60" alt="Custom Post Donations Pro" /></a></p>
<p><strong>Try ReFlex Gallery</strong><br /><em>A fully responsive WordPress image gallery plugin that is actually two galleries in one.</em><br />
<a href="http://wordpress-photo-gallery.com/" target="_blank">ReFlex Gallery</a></p>
<p><strong>Try Email Obfuscate</strong><br /><em>Email Obfuscate is a Lightweight WordPress/jQuery plugin that prevents spam-bots from harvesting your email addresses by dynamically obfuscating email addresses on your site.</em><br /><a href="http://codecanyon.net/item/wordpressjquery-email-obfuscate-plugin/721738?ref=HahnCreativeGroup" target="_blank">Email Obfuscate Plugin</a></p>
<p><a href="http://codecanyon.net/item/wordpressjquery-email-obfuscate-plugin/721738?ref=HahnCreativeGroup" target="_blank"><img alt="WordPress/jQuery Email Obfuscate Plugin - CodeCanyon Item for Sale" border="0" class="landscape-image-magnifier preload no_preview" data-item-author="HahnCreativeGroup" data-item-category="JavaScript / Miscellaneous" data-item-cost="4" data-item-name="WordPress/jQuery Email Obfuscate Plugin" data-preview-height="" data-preview-url="http://0.s3.envato.com/files/92331839/WordPress-Email-Obfuscate_item_page_image_590x300_v1.jpg" data-preview-width="" height="80" src="http://2.s3.envato.com/files/92331838/WordPress-Email-Obfuscate_thumb_80x80.jpg" title="" width="80" data-tooltip="WordPress/jQuery Email Obfuscate Plugin"></a></p>
<br />
<p><em>Please consider making a donatation for the continued development of this plugin. Thanks.</em></p>
<p><a href="https://www.paypal.com/cgi-bin/webscr?cmd=_s-xclick&amp;hosted_button_id=PMZ2FPNJPH59U" target="_blank"><img src="https://www.paypalobjects.com/en_US/i/btn/btn_donateCC_LG.gif" alt="PayPal - The safer, easier way to pay online!"><img alt="" src="https://www.paypalobjects.com/en_US/i/scr/pixel.gif" width="1" height="1"></a></p>
</div>
<div id="rss" style="float: right; width: 25%; height: 700px; padding: 10px; min-width: 165px;">
</div>
<script type="text/javascript">
jQuery(document).ready(function(){			
	jQuery.ajax({url:"../wp-content/plugins/wp-easy-gallery/admin/rss.php",success:function(result){
		jQuery("#rss").html(result);
  }});
});
</script>
</div>