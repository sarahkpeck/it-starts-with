<?php
if(preg_match('#' . basename(__FILE__) . '#', $_SERVER['PHP_SELF'])) { die('You are not allowed to call this page directly.'); }

global $wpdb;
global $easy_gallery_table;
global $easy_gallery_image_table;

$imageResults = null;

$galleryResults = $wpdb->get_results( "SELECT * FROM $easy_gallery_table" );

//Select gallery
if(isset($_POST['select_gallery']) || isset($_POST['galleryId'])) {
	if(check_admin_referer('wpeg_gallery','wpeg_gallery')) {
	  $gid = intval((isset($_POST['select_gallery'])) ? esc_sql($_POST['select_gallery']) : esc_sql($_POST['galleryId']));
	  $imageResults = $wpdb->get_results( "SELECT * FROM $easy_gallery_image_table WHERE gid = $gid ORDER BY sortOrder ASC" );
	  $gallery = $wpdb->get_row( "SELECT * FROM $easy_gallery_table WHERE Id = $gid" );
	}
}

//Add image
if(isset($_POST['galleryId']) && !isset($_POST['switch'])) {
	if(check_admin_referer('wpeg_gallery','wpeg_gallery')) {
	  $gid = intval($_POST['galleryId']);
	  $imagePath = $_POST['upload_image'];
	  $imageTitle = $_POST['image_title'];
	  $imageDescription = $_POST['image_description'];
	  $sortOrder = intval($_POST['image_sortOrder']);
	  $imageAdded = $wpdb->insert( $easy_gallery_image_table, array( 'gid' => $gid, 'imagePath' => $imagePath, 'title' => $imageTitle, 'description' => $imageDescription, 'sortOrder' => $sortOrder ) );
	  
	  if($imageAdded) {
	  ?>
		  <div class="updated"><p><strong><?php _e('Image saved.' ); ?></strong></p></div>  
	  <?php }
	  //Reload images
	  $imageResults = $wpdb->get_results( "SELECT * FROM $easy_gallery_image_table WHERE gid = $gid ORDER BY sortOrder ASC" );
	}
}

//Edit/Delete Images
if(isset($_POST['editing_images'])) {
	if(check_admin_referer('wpeg_gallery','wpeg_gallery')) {	
		$editImageIds = $_POST['edit_imageId'];
		$imagePaths = $_POST['edit_imagePath'];
		$imageTitles = $_POST['edit_imageTitle'];
		$imageDescriptions = $_POST['edit_imageDescription'];
		$sortOrders = $_POST['edit_imageSort'];
		$imagesToDelete = isset($_POST['edit_imageDelete']) ? $_POST['edit_imageDelete'] : array();
	
		$i = 0;
		foreach($editImageIds as $editImageId) {
			if(in_array($editImageId, $imagesToDelete)) {
				$wpdb->query( "DELETE FROM $easy_gallery_image_table WHERE Id = '".$editImageId."'" );
				echo "Deleted: ".$imageTitles[$i];
			}
			else {
				$imageEdited = $wpdb->update( $easy_gallery_image_table, array( 'imagePath' => $imagePaths[$i], 'title' => $imageTitles[$i], 'description' => $imageDescriptions[$i], 'sortOrder' => $sortOrders[$i] ), array( 'Id' => $editImageId ) );
			}		
			$i++;
		}		  
	  ?>  
	  <div class="updated"><p><strong><?php _e('Images have been edited.' ); ?></strong></p></div>  
	  <?php		
	}
}
if(isset($_POST['editing_gid'])) {
	if(check_admin_referer('wpeg_gallery','wpeg_gallery')) {
	  $gid = intval($_POST['editing_gid']);
	  $imageResults = $wpdb->get_results( "SELECT * FROM $easy_gallery_image_table WHERE gid = $gid ORDER BY sortOrder ASC" );
	  $gallery = $wpdb->get_row( "SELECT * FROM $easy_gallery_table WHERE Id = $gid" );
	}
}

?>

<div class='wrap wp-easy-gallery-admin'>
	<h2>Easy Gallery</h2>    
    <p>Add new images to gallery</p>
	<?php if(!isset($_POST['select_gallery']) && !isset($_POST['galleryId']) && !isset($_POST['editing_images'])) { ?>
    <p>Select a galley</p>		
    <form name="gallery" method="post" action="<?php echo str_replace( '%7E', '~', $_SERVER['REQUEST_URI']); ?>">
    	<?php wp_nonce_field('wpeg_gallery','wpeg_gallery'); ?>
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
    <?php } else if(isset($_POST['select_gallery']) || isset($_POST['galleryId']) || isset($_POST['editing_images'])) { ?>    
    <h3>Gallery: <?php _e($gallery->name); ?></h3>
    <form name="switch_gallery" method="post" action="<?php echo str_replace( '%7E', '~', $_SERVER['REQUEST_URI']); ?>">
    <input type="hidden" name="switch" value="true" />
    <p style="float: left;"><input type="submit" name="Submit" class="button-primary" value="Switch Gallery" /></p>
    </form>
    <p style="float: right;"><a href="http://labs.hahncreativegroup.com/wordpress-plugins/wp-easy-gallery-pro-simple-wordpress-gallery-plugin/?src=wpeg" target="_blank"><strong><em>Try WP Easy Gallery Pro</em></strong></a></p>
    <div style="Clear: both;"></div>
    
    <form name="add_image_form" action="<?php echo str_replace( '%7E', '~', $_SERVER['REQUEST_URI']); ?>" method="post">
    <input type="hidden" name="galleryId" value="<?php _e($gallery->Id); ?>" />
    <?php wp_nonce_field('wpeg_gallery','wpeg_gallery'); ?>
    <table class="widefat post fixed eg-table">
    	<thead>
        <tr>
            <th class="eg-cell-spacer-340">Image Path</th>
            <th class="eg-cell-spacer-150">Image Title</th>
            <th>Image Description</th>
            <th class="eg-cell-spacer-90">Sort Order</th>
            <th class="eg-cell-spacer-115"></th>
        </tr>
        </thead>
        <tfoot>
        <tr>
            <th>Image Path</th>
            <th>Image Title</th>
            <th>Image Description</th>
            <th>Sort Order</th>
            <th></th>
        </tr>
        </tfoot>
        <tbody>
        	<tr>
            	<td><input id="upload_image" type="text" size="36" name="upload_image" value="" />
					<input id="upload_image_button" type="button" value="Upload Image" /></td>
                <td><input type="text" name="image_title" size="20" value="" /></td>
                <td><input type="text" name="image_description" size="45" value="" /></td>
                <td><input type="text" name="image_sortOrder" size="10" value="" /></td>
                <td class="major-publishing-actions"><input type="submit" name="Submit" class="button-primary" value="Add Image" /></td>
            </tr>        	
        </tbody>
     </table>
     </form>
     <?php } ?>
     <?php
	 if(count($imageResults) > 0) {
	 ?>
     <br />
     <hr />
     <p>Edit existing images in this gallery</p>
    <table class="widefat post fixed eg-table">
    	<thead>
        <tr>
        	<th class="eg-cell-spacer-80">Image Preview</th>
            <th class="eg-cell-spacer-700">Image Info</th>
            <th></th>            
        </tr>
        </thead>        
        <tbody>
<form name="edit_image_form" action="<?php echo str_replace( '%7E', '~', $_SERVER['REQUEST_URI']); ?>" method="post">	
<input type="hidden" name="editing_gid" value="<?php _e($gallery->Id); ?>" />
<input type="hidden" name="editing_images" value="true" />
<?php wp_nonce_field('wpeg_gallery', 'wpeg_gallery'); ?>	
        	<?php foreach($imageResults as $image) { ?>				
            <tr>
            	<td><a onclick="var images=['<?php _e($image->imagePath); ?>']; var titles=['<?php _e($image->title); ?>']; var descriptions=['<?php _e($image->description); ?>']; jQuery.prettyPhoto.open(images,titles,descriptions);" style="cursor: pointer;"><img src="<?php _e($image->imagePath); ?>" width="75" alt="<?php _e($image->title); ?>" /></a><br /><i><?php _e('Click to preview', 'wp-easy-gallery-pro'); ?></i></td>
                <td>                	
                	<input type="hidden" name="edit_gId[]" value="<?php _e($image->gid); ?>" />
					<input type="hidden" name="edit_imageId[]" value="<?php _e($image->Id); ?>" />                                        
                	<p><strong>Image Path:</strong> <input type="text" name="edit_imagePath[]" size="75" value="<?php _e($image->imagePath); ?>" /></p>
                    <p><strong>Image Title:</strong> <input type="text" name="edit_imageTitle[]" size="20" value="<?php _e($image->title); ?>" /></p>
                    <p><strong>Image Description:</strong> <input type="text" name="edit_imageDescription[]" size="75" value="<?php _e($image->description); ?>" /></p>
                    <p><strong>Sort Order:</strong> <input type="text" name="edit_imageSort[]" size="10" value="<?php _e($image->sortOrder); ?>" /></p>
					<p><strong>Delete Image?</strong> <input type="checkbox" name="edit_imageDelete[]" value="<?php _e($image->Id); ?>" /></p>
                </td>
                <td></td>                
            </tr>
			<?php } ?>
        </tbody>		
     </table>
	 <p class="major-publishing-actions left-float eg-right-margin"><input type="submit" name="Submit" class="button-primary" value="Save Changes" /></p>
     </form>
	 <div style="clear:both;"></div>
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