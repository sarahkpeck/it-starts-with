<?php
if(preg_match('#' . basename(__FILE__) . '#', $_SERVER['PHP_SELF'])) { die('You are not allowed to call this page directly.'); }

global $wpdb;
global $easy_gallery_table;
global $easy_gallery_image_table;

$galleryResults = $wpdb->get_results( "SELECT * FROM $easy_gallery_table" );

//Select gallery
if(isset($_POST['select_gallery']) || isset($_POST['galleryId'])) {
	if(check_admin_referer('wpeg_select_gallery','wpeg_select_gallery')) {
	  $gid = intval((isset($_POST['select_gallery'])) ? esc_sql($_POST['select_gallery']) : esc_sql($_POST['galleryId']));
	  $imageResults = $wpdb->get_results( "SELECT * FROM $easy_gallery_image_table WHERE gid = $gid ORDER BY sortOrder ASC" );
	  $gallery = $wpdb->get_row( "SELECT * FROM $easy_gallery_table WHERE Id = $gid" );
	}
}
	
if(isset($_POST['hcg_edit_gallery']))
{
	if(check_admin_referer('wpeg_edit_gallery','wpeg_edit_gallery')) {
	  if($_POST['galleryName'] != "") {
		$galleryName = $_POST['galleryName'];
		$galleryDescription = $_POST['galleryDescription'];	  
		$slug = mb_convert_case(str_replace(" ", "", $_POST['galleryName']), MB_CASE_LOWER, "UTF-8");
		$imagepath = str_replace("\\", "", $_POST['upload_image']);
		$thumbwidth = $_POST['gallerythumbwidth'];
		$thumbheight = $_POST['gallerythumbheight'];
		
		if(isset($_POST['hcg_edit_gallery'])) {
			$imageEdited = $wpdb->update( $easy_gallery_table, array( 'name' => $galleryName, 'slug' => $slug, 'description' => $galleryDescription, 'thumbnail' => $imagepath, 'thumbwidth' => $thumbwidth, 'thumbheight' => $thumbheight ), array( 'Id' => intval($_POST['hcg_edit_gallery']) ) );
				
				?>  
				<div class="updated"><p><strong><?php _e('Gallery has been edited.' ); ?></strong></p></div>  
				<?php
		}
	  }
	}
}
if(isset($_POST['hcg_edit_gallery'])) {
	if(check_admin_referer('wpeg_edit_gallery','wpeg_edit_gallery')) {
	  $gid = intval($_POST['hcg_edit_gallery']);
	  $imageResults = $wpdb->get_results( "SELECT * FROM $easy_gallery_image_table WHERE gid = $gid ORDER BY sortOrder ASC" );
	  $gallery = $wpdb->get_row( "SELECT * FROM $easy_gallery_table WHERE Id = $gid" );
	}
}
?>
<div class='wrap wp-easy-gallery-admin'>
	<h2>Easy Gallery - Edit Galleries</h2>
    <?php if(!isset($_POST['select_gallery']) && !isset($_POST['galleryId']) && !isset($_POST['hcg_edit_gallery'])) { ?>
    <p>Select a galley</p>		
    <form name="gallery" method="post" action="<?php echo str_replace( '%7E', '~', $_SERVER['REQUEST_URI']); ?>">
    	<?php wp_nonce_field('wpeg_select_gallery','wpeg_select_gallery'); ?>
        <select name="select_gallery" onchange="gallery.submit()">
        	<option> - SELECT A GALLERY - </option>
			<?php
				foreach($galleryResults as $gallery) {
					?><option value="<?php _e($gallery->Id); ?>"><?php _e($gallery->name); ?></option>
                <?php
				}
			?>
        </select>
    </form>
    <?php } else if(isset($_POST['select_gallery']) || isset($_POST['galleryId']) || isset($_POST['hcg_edit_gallery'])) { ?>    
    <h3>Gallery: <?php _e($gallery->name); ?></h3>
    
    <form name="switch_gallery" method="post" action="<?php echo str_replace( '%7E', '~', $_SERVER['REQUEST_URI']); ?>">
    <input type="hidden" name="switch" value="true" />
    <p><input type="submit" name="Submit" class="button-primary" value="Switch Gallery" /></p>
    </form>
	
    <p>This is where you can edit existing galleries.</p>
    <p style="float: right;"><a href="http://labs.hahncreativegroup.com/wordpress-plugins/wp-easy-gallery-pro-simple-wordpress-gallery-plugin/?src=wpeg" target="_blank"><strong><em>Try WP Easy Gallery Pro</em></strong></a></p>
    <div style="Clear: both;"></div>
    <form name="hcg_add_gallery_form" action="<?php echo str_replace( '%7E', '~', $_SERVER['REQUEST_URI']); ?>" method="post">
    <input type="hidden" name="hcg_edit_gallery" value="<?php _e($gid); ?>" />
    <?php wp_nonce_field('wpeg_edit_gallery', 'wpeg_edit_gallery'); ?>
    <table class="widefat post fixed eg-table">
    	<thead>
        <tr>
        	<th class="eg-cell-spacer-250">Field Name</th>
            <th>Entry</th>
            <th>Description</th>
        </tr>
        </thead>
        <tfoot>
        <tr>
        	<th>Field Name</th>
            <th>Entry</th>
            <th>Description</th>
        </tr>
        </tfoot>
        <tbody>
        	<tr>
            	<td><strong>Enter Gallery Name:</strong></td>
                <td><input type="text" size="30" name="galleryName" value="<?php _e($gallery->name); ?>" /></td>
                <td>This name is the internal name for the gallery.<br />Please avoid non-letter characters such as ', ", *, etc.</td>
            </tr>
            <tr>
            	<td><strong>Enter Gallery Description:</strong></td>
                <td><input type="text" size="50" name="galleryDescription" value="<?php _e($gallery->description) ?>" /></td>
                <td>This description is for internal use.</td>
            </tr>
            <tr>
            	<td><strong>Enter Thumbnail Imagepath:</strong></td>
                <td><input id="upload_image" type="text" size="36" name="upload_image" value="<?php _e($gallery->thumbnail); ?>" />
					<input id="upload_image_button" type="button" value="Upload Image" /></td>
                <td>This is the file path for the gallery thumbnail image.</td>
            </tr>
            <tr>
            	<td><strong>Enter Thumbnail Width:</strong></td>
                <td><input type="text" size="10" name="gallerythumbwidth" value="<?php _e($gallery->thumbwidth); ?>" /></td>
                <td>This is the width of the gallery thumbnail image.</td>
            </tr>
            <tr>
            	<td><strong>Enter Thumbnail Height:</strong></td>
                <td><input type="text" size="10" name="gallerythumbheight" value="<?php _e($gallery->thumbheight); ?>" /></td>
                <td>This is the height of the gallery thumbnail image.</td>
            </tr>
            <tr>
            	<td class="major-publishing-actions"><input type="submit" name="Submit" class="button-primary" value="Save Changes" /></td>
                <td></td>
                <td></td>
            </tr>
        </tbody>
	</table>
    </form>
    <?php } ?>
    <br />  
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