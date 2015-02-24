<?php
add_action('admin_init', 'qoate_sb_settings_init' );
add_action('admin_print_styles','add_qoate_box_dashboard_style');
add_action('admin_menu', 'qoate_add_sb_options_page');

function add_qoate_box_dashboard_style(){
	wp_enqueue_style('qoate_admin_style', WP_CONTENT_URL .'/plugins/'. plugin_basename(dirname(__FILE__)).'/'. 'qoate_dashboard_layout.css');
}

// Add menu page
function qoate_add_sb_options_page() {
	add_options_page('Qoate Scroll Triggered Box', 'Qoate Scroll Triggered Box', 'manage_options', 'qoate-sb-settings', 'qoate_create_sb_options_page');
}

// Init plugin options to white list our options
function qoate_sb_settings_init(){
	register_setting('qoate_sb_options', 'qoate_sb_holder');
	register_setting('qoate_sb_options','qoate_sb_settings');
}

// Draw the menu page itself
function qoate_create_sb_options_page() {
global $social_sites;
	?>
<div class="wrap">
	<h2>Qoate Scroll Triggered Box Settings</h2>
	<div class="postbox-container" style="width:70%; margin-right: 20px;">
		<div class="metabox-holder">	
			<div class="meta-box-sortables">
				<div class="postbox">
					<h3 class="hndle"><span>Placement, animation and content settings.</span></h3>
					<div class="inside">
					<p style="margin:5px">Here you can configure the content, the placement and the animation settings of the Qoate Scroll Triggered Box. Scroll below to do some advanced styling..</p>
					<form method="post" action="options.php">
						<?php settings_fields('qoate_sb_options');  $options = get_option('qoate_sb_holder',array('disable_mobile' => false, 'times' => 0, 'height'=>90,'vplacement'=>'bottom','hplacement'=>'right','animation'=>'slide','percentage'=>75,'text'=>'Your HTML-content goes here..','bgcolor'=>'white','textcolor'=>'black','do_on_posts'=>'1')); ?>
						<table class="form-table">
            <tr>
              <th scope="row"><label for="disable_mobile">Disable for mobile devices</label></th>
              <td>
                <input type="checkbox" id="disable_mobile" name="qoate_sb_holder[disable_mobile]" value="1"<?php echo ($options['disable_mobile']?' checked':''); ?> />
              </td>
            </tr>
						<tr valign="top"><th scope="row">Number of times to show per visitor</th>
							<td>
                <input type="text" size="1" id="times" name="qoate_sb_holder[times]" value="<?php echo $options['times']; ?>" title="Leave blank if you don't want use this feature." />
              </td>
						</tr>
						<tr valign="top"><th scope="row">Show on</th>
							<td>
								<input type="checkbox" id="do_on_posts" name="qoate_sb_holder[do_on_posts]" value="1"<?php if($options['do_on_posts']=='1') echo ' CHECKED';?> />
                <label for="do_on_posts">Posts</label><br /> 
								<input type="checkbox" id="do_on_pages" name="qoate_sb_holder[do_on_pages]" value="1"<?php if($options['do_on_pages']=='1') echo ' CHECKED';?> />
                <label for="do_on_pages">Pages</label><br /> 
								<input type="checkbox" id="do_on_home" name="qoate_sb_holder[do_on_home]" value="1"<?php if($options['do_on_home']=='1') echo ' CHECKED';?> />
                <label for="do_on_home">Home</label><br /> 
								<input type="checkbox" id="do_on_archive" name="qoate_sb_holder[do_on_archive]" value="1"<?php if($options['do_on_archive']=='1') echo ' CHECKED';?> />
                <label for="do_on_archive">Archives</label>
							</td>
						</tr>
						<tr valign="top"><th scope="row">Height</th>
							<td><input type="text" size="1" id="height" name="qoate_sb_holder[height]" value="<?php echo $options['height']; ?>" /> <label for="height">px</label> </td>
						</tr>
						<tr valign="top"><th scope="row"><label for="with_minimize">Give visitors the option to minimize?</label></th>
							<td><input type="checkbox" id="with_minimize" name="qoate_sb_holder[with_minimize]" value="1"<?php if($options['with_minimize']=='1') echo ' CHECKED';?> /></td>
						</tr>
						<tr valign="top"><th scope="row">Vertical Placement</th>
							<td>
                <input type="radio" id="vplacement0" name="qoate_sb_holder[vplacement]" value="top"<?php if($options['vplacement']=='top') echo ' CHECKED';?> />
                <label for="vplacement0">Top</label><br />
							  <input type="radio" id="vplacement1" name="qoate_sb_holder[vplacement]" value="bottom"<?php if($options['vplacement']=='bottom') echo ' CHECKED';?> />
                <label for="vplacement1">Bottom</label>
              </td>                
						</tr>
						<tr valign="top"><th scope="row">Horizontal Placement</th>
							<td>
                <input type="radio" id="hplacement0" name="qoate_sb_holder[hplacement]" value="left"<?php if($options['hplacement']=='left') echo ' CHECKED';?> />
                <label for="hplacement0">Left</label><br />
							  <input type="radio" id="hplacement1" name="qoate_sb_holder[hplacement]" value="right"<?php if($options['hplacement']=='right') echo ' CHECKED';?> />
                <label for="hplacement1">Right</label>
              </td>
						</tr>
						<tr valign="top"><th scope="row">Animation</th>
							<td>
                <input type="radio" id="animation0" name="qoate_sb_holder[animation]" value="slide"<?php if($options['animation']=='slide') echo ' CHECKED';?> />
                <label for="animation0">Slide</label><br />
							  <input type="radio" id="animation1" name="qoate_sb_holder[animation]" value="fade"<?php if($options['animation']=='fade') echo ' CHECKED';?> />
                <label for="animation1">Fade</label>
              </td>
						</tr>
						<tr valign="top"><th scope="row">When to show?</th>
							<td>
                <input type="text" size="1" id="percentage" name="qoate_sb_holder[percentage]" value="<?php echo $options['percentage'];?>" /> <label for="percentage">% of total page height</label><br />
                <b>OR SHOW ON POSTS/PAGES at:</b><br />
                <input type="radio" id="show_at_comments0" name="qoate_sb_holder[show_at_comments]" value="0"<?php if(!$options['show_at_comments']) echo ' CHECKED';?> />
                <label for="show_at_comments0">none</label><br />
                <input type="radio" id="show_at_comments1" name="qoate_sb_holder[show_at_comments]" value="1"<?php if($options['show_at_comments']=='1') echo ' CHECKED';?> />
                <label for="show_at_comments1">comments</label><br />
                <input type="radio" id="show_at_comments2" name="qoate_sb_holder[show_at_comments]" value="2"<?php if($options['show_at_comments']=='2') echo ' CHECKED';?> />
                <label for="show_at_comments2">end of post</label>
              </td>
						</tr>
						<tr valign="top"><th scope="row">HTML Content (optional)<br /><small><a href="#qoate_social_bookmark_settings">(You can also show social bookmarks!)</a></small></th>
							<td><textarea rows="8" cols="20" style="width: 98%" name="qoate_sb_holder[text]"><?php echo $options['text'];  ?></textarea></td>
						</tr>
					</table>
					<p class="submit">
							<input type="submit" class="button-primary" style="margin:5px;" value="<?php _e('Save Changes') ?>" />
					</p>
				
				</div>				
			</div>
			<div class="postbox">
			<h3 class="hndle" id="qoate_social_bookmark_settings"><span>Social Bookmark Options</span></h3>
				<div class="inside">
				<?php $sb_options = get_option('qoate_sb_settings',array('text'=>'Liked this post? Share it!')); ?>
					<p style="margin:5px">Here you can choose to use social bookmarks where visitors can share your content. Note that if you use this, the HTML-content from above gets overruled.</p>
					<table class="form-table">
						<tr valign="top"><th scope="row"><label for="use_bookmarks">Use Social Bookmarking</label></th>
							<td><input type="checkbox" id="use_bookmarks" name="qoate_sb_settings[use_bookmarks]" value="1"<?php if($sb_options['use_bookmarks']=='1') echo ' CHECKED';?> /></td>
						</tr>
						<tr valign="top"><th scope="row">Heading</th>
							<td><input type="text" class="regular-text" name="qoate_sb_settings[text]" value="<?php echo $sb_options['text'];?>" /></td>
						</tr>
						<tr valign="top"><th scope="row">Share options</th>
						<td>
						<?php foreach($social_sites as $name=>$site) { ?>
							<span style="display:block"><input type="checkbox" value="1" id="checkbox_<?php echo $name; ?>" name="qoate_sb_settings[<?php echo $name; ?>]"<?php if($sb_options[$name]=='1') echo ' CHECKED';?> />
              <?php echo '<label for="checkbox_'.$name.'"><img width="18" style="vertical-align:top;" height="18" src="'.WP_CONTENT_URL.'/plugins/'.plugin_basename(dirname(__FILE__)).'/images/'.strtolower($name).'.png" alt='.$name.' /> '.$name.'</label>'; ?>
              </span>
						<?php } ?>
						</td></tr>
					</table>
					<p class="submit">
							<input type="submit" class="button-primary" style="margin:5px;" value="<?php _e('Save Changes') ?>" />
					</p>
				</div>
			</div>
			</form>
</div>
</div>
</div>
	<!-- Qoate Right Sidebar -->
		<?php include('qoate-right-sidebar.php'); ?>
</div>
<?php } ?>