<?php
if(preg_match('#' . basename(__FILE__) . '#', $_SERVER['PHP_SELF'])) { die('You are not allowed to call this page directly.'); }

if (isset($_POST['defaultSettings'])) {
	if(check_admin_referer('wpeg_settings','wpeg_settings')) {
	  $temp_defaults = get_option('wp_easy_gallery_defaults');	
	  $temp_defaults['hide_overlay'] = isset($_POST['hide_overlay']) ? $_POST['hide_overlay'] : 'false';
	  $temp_defaults['hide_social'] = isset($_POST['hide_social']) ? $_POST['hide_social'] : 'false';
	  $temp_defaults['use_default_style'] = isset($_POST['use_default_style']) ? $_POST['use_default_style'] : 'false';
	  $temp_defaults['custom_style'] = isset($_POST['custom_style']) ? $_POST['custom_style'] : '';
	  $temp_defaults['drop_shadow'] = isset($_POST['drop_shadow']) ? $_POST['drop_shadow'] : 'false';
	  
	  update_option('wp_easy_gallery_defaults', $temp_defaults);
	  
	  ?>  
	  <div class="updated"><p><strong>Options saved.</strong></p></div>  
	  <?php
	}
}
$default_options = get_option('wp_easy_gallery_defaults');

?>
<div class='wrap wp-easy-gallery-admin'>
	<h2>Easy Gallery</h2>
    <p style="width: 50%; float: left;">This is a listing of all galleries.</p>
    <p style="float: right;"><a href="http://labs.hahncreativegroup.com/wordpress-plugins/wp-easy-gallery-pro-simple-wordpress-gallery-plugin/?src=wpeg" target="_blank"><strong><em>Try WP Easy Gallery Pro</em></strong></a></p>
    <div style="Clear: both;"></div>
    <form name="save_default_settings" method="post" action="<?php echo str_replace( '%7E', '~', $_SERVER['REQUEST_URI']); ?>">
    <?php wp_nonce_field('wpeg_settings','wpeg_settings'); ?>
    <table class="widefat post fixed eg-table">
    	<thead>
        <tr>
        	<th>Property or Attribute</th>
            <th>Value</th>
            <th>Description</th>
        </tr>
        </thead>
        <tfoot>
        <tr>
        	<th>Property or Attribute</th>
            <th>Value</th>
            <th>Description</th>
        </tr>
        </tfoot>
        <tbody>        			
            <tr>            	
            	<td>Hide Gallery Overlay</td>
                <td><input type="checkbox" name="hide_overlay" id="hide_overlay"<?php _e(($default_options['hide_overlay'] == 'true') ? "checked='checked'" : ""); ?> value="true" /></td>
                <td>Show or Hide thumbnail gallery overlay in modal window popup. Check to hide the overlay.</td>            
            </tr>
            <tr>            	
            	<td>Hide Gallery Social Buttons</td>
                <td><input type="checkbox" name="hide_social" id="hide_social"<?php _e(($default_options['hide_social'] == 'true') ? "checked='checked'" : ""); ?> value="true" /></td>
                <td>Show or Hide the social sharing buttons in modal window popup. Check to hide the social sharing buttons.</td>            
            </tr>
            <tr>            	
            	<td>Use Default Thumbnail Theme</td>
                <td><input type="checkbox" name="use_default_style" id="use_default_style"<?php _e(($default_options['use_default_style'] == 'true') ? "checked='checked'" : ""); ?> value="true" /></td>
                <td>Use default thumbnail style (uncheck to disable new thumbnail CSS).</td>            
            </tr>
			<tr>            	
            	<td>Thumbnail Dropshadow</td>
                <td><input type="checkbox" name="drop_shadow" id="drop_shadow"<?php _e(($default_options['drop_shadow'] == 'true') ? "checked='checked'" : ""); ?> value="true" /></td>
                <td>Use default thumbnail dropshadow (uncheck to disable dropshadow CSS).</td>            
            </tr>
            <tr>
            	<td>Custom Thumbnail Style</td>
                <td><textarea name="custom_style" id="custom_style" rows="4" cols="50"><?php _e($default_options['custom_style']); ?></textarea></td>
                <td>This is where you would add custom styles for the gallery thumbnails.<br />(ex: border: solid 1px #cccccc; padding: 2px; margin-right: 10px;)</td>
            </tr>
            <tr>
            	<td>                
                	<input type="hidden" name="defaultSettings" value="true" />
                    <input type="submit" name="Submit" class="button-primary" value="Save" />                
                </td>
                <td></td>
                <td></td>
            </tr>			
        </tbody>
     </table>
     <br />
<div style="float: left; width: 60%; min-width: 488px;">     
<p><strong>Try WP Easy Gallery Pro</strong><br /><em>Pro Features include: Multi-image uploader, Enhanced admin section for easier navigation, Image preview pop-up, and more...</em></p>
<p><a href="http://labs.hahncreativegroup.com/wordpress-gallery-plugin/?src=wpeg" target="_blank"><img title="WP-Easy-Gallery-Pro_468x88" src="http://labs.hahncreativegroup.com/wp-content/uploads/2012/02/WP-Easy-Gallery-Pro_468x88.gif" alt="" width="468" height="88" /></a></p>
<p><strong>Try WP Easy Gallery Premium</strong><br /><em>Premium Features all of the Pro features plus unlimited upgrades.</em><br />
<a href="http://labs.hahncreativegroup.com/wp-easy-gallery-premium/" target="_blank">WP Easy Gallery Premium</a></p>
<p><strong>Try Custom Post Donations Pro</strong><br /><em>This WordPress plugin will allow you to create unique customized PayPal donation widgets to insert into your WordPress posts or pages and accept donations. Features include: Multiple Currencies, Multiple PayPal accounts, Custom donation form display titles, and more.</em></p>
<p><a href="http://labs.hahncreativegroup.com/wordpress-paypal-plugin/?src=wpeg"><img src="http://labs.hahncreativegroup.com/wp-content/uploads/2011/10/CustomPostDonationsPro-Banner.gif" width="374" height="60" alt="Custom Post Donations Pro" /></a></p>
<p><strong>Try ReFlex Gallery</strong><br /><em>A fully responsive WordPress image gallery plugin that is actually two galleries in one.</em><br />
<a href="http://wordpress-photo-gallery.com/" target="_blank">ReFlex Gallery</a></p>
<p><strong>Try Email Obfuscate</strong><br /><em>Email Obfuscate is a Lightweight WordPress/jQuery plugin that prevents spam-bots from harvesting your email addresses by dynamically obfuscating email addresses on your site.</em><br /><a href="http://codecanyon.net/item/wordpressjquery-email-obfuscate-plugin/721738?ref=HahnCreativeGroup" target="_blank">Email Obfuscate Plugin</a></p>
<p><a href="http://codecanyon.net/item/wordpressjquery-email-obfuscate-plugin/721738?ref=HahnCreativeGroup" target="_blank"><img alt="WordPress/jQuery Email Obfuscate Plugin - CodeCanyon Item for Sale" border="0" class="landscape-image-magnifier preload no_preview" data-item-author="HahnCreativeGroup" data-item-category="JavaScript / Miscellaneous" data-item-cost="4" data-item-name="WordPress/jQuery Email Obfuscate Plugin" data-preview-height="" data-preview-url="http://0.s3.envato.com/files/92331839/WordPress-Email-Obfuscate_item_page_image_590x300_v1.jpg" data-preview-width="" height="80" src="http://2.s3.envato.com/files/92331838/WordPress-Email-Obfuscate_thumb_80x80.jpg" title="" width="80" data-tooltip="WordPress/jQuery Email Obfuscate Plugin"></a></p>
<br />
<p><em>Please consider making a donatation for the continued development of this plugin. Thanks.</em></p>
<p><a href="https://www.paypal.com/cgi-bin/webscr?cmd=_s-xclick&amp;hosted_button_id=PMZ2FPNJPH59U" target="_blank"><img src="https://www.paypalobjects.com/en_US/i/btn/btn_donateCC_LG.gif" alt="PayPal - The safer, easier way to pay online!"><img alt="" src="https://www.paypalobjects.com/en_US/i/scr/pixel.gif" width="1" height="1"></a></p>
</div>
<div style="float: right; width: 25%; height: 700px; padding: 10px; min-width: 165px;">
<?php
$url = "http://labs.hahncreativegroup.com/feed/";
$rss = simplexml_load_file($url);
if($rss)
{
  echo '<h3>'.$rss->channel->title.'</h3>';
  $items = $rss->channel->item;
  $count = 0;
  foreach($items as $item)
  {
	$count++;	
	$title = $item->title;
	$link = $item->link;
	$published_on = $item->pubDate;
	$description = $item->description;
	echo '<h4><a href="'.$link.'">'.$title.'</a></h4>';
	echo '<p>'.$description.'</p>';
	if ($count >= 5) {
		break;	
	}
  }
}
?>
</div>
</div>