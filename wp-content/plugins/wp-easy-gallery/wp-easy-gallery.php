<?php
	/*
	Plugin Name: WP Easy Gallery
	Plugin URI: http://labs.hahncreativegroup.com/wordpress-gallery-plugin/
	Description: Wordpress Plugin for creating dynamic photo galleries	
	Author: HahnCreativeGroup
	Version: 4.0.1
	Author URI: http://labs.hahncreativegroup.com/wordpress-plugins/easy-gallery/
	*/	
	
	/* ==================================================================================
	 * Create custom database table
	 * ================================================================================== 
	 */
	
	global $wpdb;
	global $easy_gallery_table;
	global $easy_gallery_image_table;
	global $easy_gallery_db_version;	
	$easy_gallery_table = $wpdb->prefix . 'easy_gallery';
	$easy_gallery_image_table = $wpdb->prefix . 'easy_gallery_images';
	$easy_gallery_db_version = '1.1';
		
	register_activation_hook( __FILE__,  'easy_gallery_install' );
	
	function easy_gallery_install() {
	  global $wpdb;
	  global $easy_gallery_table;
	  global $easy_gallery_image_table;
	  global $easy_gallery_db_version;
	
	  if ( $wpdb->get_var( "show tables like '$easy_gallery_table'" ) != $easy_gallery_table ) {
				
		$sql = "CREATE TABLE $easy_gallery_table (".
			"Id INT NOT NULL AUTO_INCREMENT, ".
			"name VARCHAR( 30 ) NOT NULL, ".
			"slug VARCHAR( 30 ) NOT NULL, ".
			"description TEXT NOT NULL, ".
			"thumbnail LONGTEXT NOT NULL, ".
			"thumbwidth INT, ".
			"thumbheight INT, ".
			"PRIMARY KEY Id (Id) ".
			")";
		
		require_once( ABSPATH . 'wp-admin/includes/upgrade.php' );
		dbDelta( $sql );
		
		$sql = "CREATE TABLE $easy_gallery_image_table (".
				"Id INT NOT NULL AUTO_INCREMENT, ".
				"gid INT NOT NULL, ".
				"imagePath LONGTEXT NOT NULL, ".
				"title VARCHAR( 50 ) NOT NULL, ".
				"description LONGTEXT NOT NULL, ".
				"sortOrder INT NOT NULL, ".
				"PRIMARY KEY Id (Id) ".
				")";

	
		require_once( ABSPATH . 'wp-admin/includes/upgrade.php' );
		dbDelta( $sql );
	
		add_option( "easy_gallery_db_version", $easy_gallery_db_version );
	  }
	}
	
	/* ==================================================================================
	 * Include JS File in Header
	 * ================================================================================== 
	 */
	 
	 function define_options() {
		 if(!get_option('wp_easy_gallery_defaults')) {
				$gallery_options = array(
					'version'		   		=> 'free',
					'thumbnail_width'  		=> 'auto',
					'thumbnail_height' 		=> 'auto',
					'hide_overlay'	   		=> 'false',
					'hide_social'	   		=> 'false',
					'custom_style'	   		=> '',
					'use_default_style'		=> 'true',
					'drop_shadow'			=> 'true',
					'display_mode'	   => 'wp_easy_gallery',
					'num_columns'	   => 3
				);
				
				add_option('wp_easy_gallery_defaults', $gallery_options);
			}
			else {
				$wpEasyGalleryOptions	= get_option('wp_easy_gallery_defaults');
				$keys = array_keys($wpEasyGalleryOptions);
				
				if (!in_array('version', $keys)) {
					$wpEasyGalleryOptions['version'] = $this->plugin_version;	
				}				
				if (!in_array('hide_overlay', $keys)) {
					$wpEasyGalleryOptions['hide_overlay'] = "false";	
				}
				if (!in_array('hide_social', $keys)) {
					$wpEasyGalleryOptions['hide_social'] = "false";	
				}
				if (!in_array('custom_style', $keys)) {
					$wpEasyGalleryOptions['custom_style'] = "";	
				}
				if (!in_array('use_default_style', $keys)) {
					$wpEasyGalleryOptions['use_default_style'] = "true";	
				}
				if (!in_array('drop_shadow', $keys)) {
					$wpEasyGalleryOptions['drop_shadow'] = "true";	
				}
				if (!in_array('display_mode', $keys)) {
					$wpEasyGalleryOptions['display_mode'] = "wp_easy_gallery";	
				}
				if (!in_array('num_columns', $keys)) {
					$wpEasyGalleryOptions['num_columns'] = 3;	
				}
				if (!in_array('thumbnail_height', $keys)) {
					$wpEasyGalleryOptions['thumbnail_height'] = $wpEasyGalleryOptions['thunbnail_height'];
					unset($wpEasyGalleryOptions['thunbnail_height']);
				}
				
				update_option('wp_easy_gallery_defaults', $wpEasyGalleryOptions);	
			}
	 }
	 add_action('init', 'define_options');
	 
	 function wp_custom_style() {
		$styles = get_option('wp_easy_gallery_defaults');
		echo "<!-- WP Easy Gallery: http://labs.hahncreativegroup.com/wordpress-gallery-plugin/ -->\n<style>.wp-easy-gallery img {".$styles['custom_style']."}</style>";		
	}
	add_action('wp_head', 'wp_custom_style');
		
	function attach_EasyGallery_scripts() {
		$wpEasyGalleryOptions = get_option('wp_easy_gallery_defaults');
		wp_enqueue_script('jquery');
		wp_register_script('prettyPhoto', WP_PLUGIN_URL.'/wp-easy-gallery/js/jquery.prettyPhoto.js', array('jquery'));
		if ($wpEasyGalleryOptions['hide_social'] == 'true' && $wpEasyGalleryOptions['hide_overlay'] == 'false') {
			wp_register_script('easyGalleryLoader', WP_PLUGIN_URL.'/wp-easy-gallery/js/EasyGalleryLoader_hideSocial.js', array('prettyPhoto', 'jquery'));
		}
		else if ($wpEasyGalleryOptions['hide_social'] == 'false' && $wpEasyGalleryOptions['hide_overlay'] == 'true') {
			wp_register_script('easyGalleryLoader', WP_PLUGIN_URL.'/wp-easy-gallery/js/EasyGalleryLoader_hideOverlay.js', array('prettyPhoto', 'jquery'));
		}
		else if ($wpEasyGalleryOptions['hide_social'] == 'true' && $wpEasyGalleryOptions['hide_overlay'] == 'true') {
			wp_register_script('easyGalleryLoader', WP_PLUGIN_URL.'/wp-easy-gallery/js/EasyGalleryLoader_hideOverlaySocial.js', array('prettyPhoto', 'jquery'));
		}
		else {
			wp_register_script('easyGalleryLoader', WP_PLUGIN_URL.'/wp-easy-gallery/js/EasyGalleryLoader.js', array('prettyPhoto', 'jquery'));
		}
		wp_enqueue_script('prettyPhoto');
		wp_enqueue_script('easyGalleryLoader');
		wp_register_style( 'prettyPhoto_stylesheet', WP_PLUGIN_URL.'/wp-easy-gallery/css/prettyPhoto.css');
		wp_enqueue_style('prettyPhoto_stylesheet');
		if ($wpEasyGalleryOptions['use_default_style'] == 'true') {
			wp_register_style('easy-gallery-style', WP_PLUGIN_URL.'/wp-easy-gallery/css/default.css');
	  		wp_enqueue_style('easy-gallery-style');
		}
	}
	add_action('wp_enqueue_scripts', 'attach_EasyGallery_scripts');
	
	function attach_Easy_Gallery_JS()
	{
		if ( ! defined( 'HCGGALLERY_PLUGIN_BASENAME' ) )
		define( 'HCGGALLERY_PLUGIN_BASENAME', plugin_basename( __FILE__ ) );
	
		if ( ! defined( 'HCGGALLERY_PLUGIN_NAME' ) )
			define( 'HCGGALLERY_PLUGIN_NAME', trim( dirname( HCGGALLERY_PLUGIN_BASENAME ), '/' ) );
		
		if ( ! defined( 'HCGGALLERY_PLUGIN_DIR' ) )
			define( 'HCGGALLERY_PLUGIN_DIR', WP_PLUGIN_DIR . '/' . HCGGALLERY_PLUGIN_NAME );
	}
	
	add_action ('wp_head', 'attach_Easy_Gallery_JS');
	
	function easy_gallery_admin_scripts() {
	  wp_enqueue_script('media-upload');
	  wp_enqueue_script('thickbox');
	  wp_register_script('easy-gallery-uploader', WP_PLUGIN_URL.'/wp-easy-gallery/js/image-uploader.js', array('jquery','media-upload','thickbox'));
	  wp_enqueue_script('easy-gallery-uploader');
	}
	
	function easy_gallery_admin_styles() {	  
	  wp_enqueue_style('thickbox');
	}
	
	if (isset($_GET['page']) && ($_GET['page'] == 'add-gallery' || $_GET['page'] == 'add-images' || $_GET['page'] == 'edit-gallery')) {
	  add_action('admin_print_scripts', 'easy_gallery_admin_scripts');
	  add_action('admin_print_styles', 'easy_gallery_admin_styles');
	}
	
	// Create Admin Panel
	function add_hcg_menu()
	{
		add_menu_page(__('Easy Gallery','menu-hcg'), __('Easy Gallery','menu-hcg'), 'manage_options', 'hcg-admin', 'showHcgMenu' );

		// Add a submenu to the custom top-level menu:
		add_submenu_page('hcg-admin', __('Easy Gallery >> Add Gallery','menu-hcg'), __('Add Gallery','menu-hcg'), 'manage_options', 'add-gallery', 'add_gallery');
		
		// Add a submenu to the custom top-level menu:
		add_submenu_page('hcg-admin', __('Easy Gallery >> Edit Gallery','menu-hcg'), __('Edit Gallery','menu-hcg'), 'manage_options', 'edit-gallery', 'edit_gallery');

		// Add a second submenu to the custom top-level menu:
		add_submenu_page('hcg-admin', __('Easy Gallery >> Add Images','menu-hcg'), __('Add Images','menu-hcg'), 'manage_options', 'add-images', 'add_images');
		
		// Add a second submenu to the custom top-level menu:
		add_submenu_page('hcg-admin', __('Easy Gallery >> Settings','menu-hcg'), __('Settings','menu-hcg'), 'manage_options', 'wpeg-settings', 'wpeg_settings');
		
		// Add a second submenu to the custom top-level menu:
		add_submenu_page('hcg-admin', __('Easy Gallery >> Help (FAQ)','menu-hcg'), __('Help (FAQ)','menu-hcg'), 'manage_options', 'help', 'help');
		
		wp_register_style('easy-gallery-admin-style', WP_PLUGIN_URL.'/wp-easy-gallery/css/wp-easy-gallery-admin.css');
	  	wp_enqueue_style('easy-gallery-admin-style');
	}
	
	add_action( 'admin_menu', 'add_hcg_menu' );
	
	function showHcgMenu()
	{
		include("admin/overview.php");
	}
	
	function add_gallery()
	{
		include("admin/add-gallery.php");
	}
	
	function edit_gallery()
	{
		include("admin/edit-gallery.php");
	}
	
	function add_images()
	{
		include("admin/add-images.php");
		attach_EasyGallery_scripts();
	}
	
	function wpeg_settings()
	{
		include("admin/wpeg-settings.php");
	}
	
	function help()
	{
		include("admin/help.php");
	}
	
	/* ==================================================================================
	 * Gallery Creation Filter
	 * ================================================================================== 
	 */
	 
	// function creates the gallery
	function createEasyGallery($galleryName, $id)	
	{			
		global $wpdb;
		global $easy_gallery_table;
		global $easy_gallery_image_table;
		
		if ($id != "-1") {
			$gallery = $wpdb->get_row( "SELECT Id, name, thumbnail, thumbwidth, thumbheight FROM $easy_gallery_table WHERE Id = '$id'" );
		}
		else {
			$gallery = $wpdb->get_row( "SELECT Id, name, thumbnail, thumbwidth, thumbheight FROM $easy_gallery_table WHERE slug = '$galleryName'" );
		}
		$imageResults = $wpdb->get_results( "SELECT * FROM $easy_gallery_image_table WHERE gid = $gallery->Id ORDER BY sortOrder ASC" );
		$options = get_option('wp_easy_gallery_defaults');
		$galleryLink = "";
		
		switch($options['display_mode']) {
			case 'wp_easy_gallery':
				$galleryLink = render_wpeg($gallery, $imageResults, $options);
				break;
			case 'wp_default':
				$galleryLink = render_wp_gallery($gallery, $imageResults, $options);
				break;
			default:
				$galleryLink = render_wpeg($gallery, $imageResults, $options);
				break;
		}
		
		return $galleryLink;
	}

	function render_wpeg($gallery, $imageResults, $options) {
		$images = array();
		$descriptions = array();
		$titles = array();
		$i = 0;		
		
		foreach($imageResults as $image)
		{
			$images[$i] = "'".$image->imagePath."'";
			$descriptions[$i] = "'".$image->description."'";
			$titles[$i] = "'".$image->title."'";
			$i++;
		}
		
		$img = implode(", ", $images);
		$desc = implode(", ", $descriptions);
		$ttl = implode(", ", $titles);
		
		$thumbwidth = ($gallery->thumbwidth < 1 || $gallery->thumbwidth == "auto") ? "" : "width='".$gallery->thumbwidth."'";
		$thumbheight = ($gallery->thumbheight < 1 || $gallery->thumbheight == "auto") ? "" : "height='".$gallery->thumbheight."'";		
		
		$dShadow = ($options['drop_shadow'] == "true") ? "class=\"dShadow trans\"" : "";
		
		$galleryMarkup = "<span class=\"wp-easy-gallery\"><a onclick=\"var images=[".$img."]; var titles=[".$ttl."]; var descriptions=[".$desc."]; jQuery.prettyPhoto.open(images,titles,descriptions);\" title=\"".$gallery->name."\" style=\"cursor: pointer;\"><img ".$dShadow." src=\"".$gallery->thumbnail."\" ".$thumbwidth." ".$thumbheight." border=\"0\" alt=\"".$gallery->name."\" /></a></span>";
		
		return $galleryMarkup;
	}
	
	function render_wp_gallery($gallery, $imageResults, $options) {
		$numColumns = $options['num_columns'];
		$galleryMarkup = "<style type='text/css'>#gallery-".$gallery->Id." {margin: auto;}	#gallery-".$gallery->Id." .gallery-item {float: left;margin-top: 10px;text-align: center;width: ".floor(100 / $numColumns)."%;} #gallery-".$gallery->Id." img {border: 2px solid #cfcfcf;}	#gallery-".$gallery->Id." .gallery-caption {margin-left: 0;}</style>";
		$galleryMarkup .= "<div id='gallery-".$gallery->Id."' class='gallery gallery-columns-".$numColumns." gallery-size-thumbnail'>";
		
		foreach($imageResults as $image) {
			$galleryMarkup .= "<dl class=gallery-item>";
			$galleryMarkup .= "<dt class='gallery-icon landscape'>";
			$galleryMarkup .= "<a href='".$image->imagePath."' rel='prettyPhoto' title='".$image->title."'>";
			$galleryMarkup .= "<img width='150' height='150' src='".$image->imagePath."' class='attachment-thumbnail' alt='".$image->title."'>";
			$galleryMarkup .= "</a>";
			$galleryMarkup .= "</dt>";
			$galleryMarkup .= "<dd class='wp-caption-text gallery-caption'>";
			$galleryMarkup .= $image->title;
			$galleryMarkup .= "</dd>";
			$galleryMarkup .= "</dl>";
		}
		
		$galleryMarkup .= "<br style='clear: both'></div>";
		
		return $galleryMarkup;
	}
	
	function EasyGallery_Handler($atts) {
	  $atts = shortcode_atts( array( 'id' => '-1', 'key' => '-1'), $atts );
	  return createEasyGallery($atts['id'], $atts['key']);
	}
	add_shortcode('EasyGallery', 'EasyGallery_Handler');	
  
	add_action( 'init', 'wpeg_code_button' );	
	function wpeg_code_button() {
		add_filter( "mce_external_plugins", "wpeg_code_add_button" );
		add_filter( 'mce_buttons', 'wpeg_code_register_button' );
	}
	function wpeg_code_add_button( $plugin_array ) {
		$plugin_array['wpegbutton'] = $dir = plugins_url( 'js/shortcode.js', __FILE__ );
		return $plugin_array;
	}
	function wpeg_code_register_button( $buttons ) {
		array_push( $buttons, 'wpegselector' );
		return $buttons;
	}
?>