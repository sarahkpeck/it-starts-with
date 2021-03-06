<?php
$add_code = true;
if (isset($options['times']) && $options['times'])
{    
  if (isset($_COOKIE['qoate_times']) && $_COOKIE['qoate_times'])
  {
    $qoate_times = $_COOKIE['qoate_times'];
  }
  else
  {
    $qoate_times = 0;
  } 

  if ($qoate_times >= $options['times'])
  {
    $add_code = false;
  }
  else
  {  
    $qoate_times++;
    setcookie('qoate_times', $qoate_times, time()+3600, '/');
  }    
}

if ($add_code)
{
  add_action('wp_footer','qoate_add_sb',20);
  add_action("wp_print_styles", "qoate_load_sb_stylesheet");
  add_action("wp_print_scripts","qoate_sb_add_scripts");
}

/*Load the neccessary stylesheets and scripts!*/
function qoate_load_sb_stylesheet() {
$options = get_option('qoate_sb_holder',array('height'=>90,'vplacement'=>'bottom','hplacement'=>'right','animation'=>'slide'));
	wp_enqueue_style('qoate_sb_style', WP_CONTENT_URL . '/plugins/' . plugin_basename(dirname(__FILE__)). '/qoate-sb-style.php?vpos='.$options['vplacement'].'&hpos='.$options['hplacement'].'&height='.$options['height']);
}
function qoate_sb_add_scripts() {
global $post;
	$options = get_option('qoate_sb_holder',array('vplacement'=>'bottom','animation'=>'slide','percentage'=>75));
	$sac = 0;
	if((is_single() || is_page()) && (comments_open() && $options['show_at_comments'] == '1')) { $sac = '1'; }
	if((is_single() || is_page()) && $options['show_at_comments'] == '2') { $sac = '2'; }
	wp_enqueue_script('jquery');
	wp_enqueue_script('qoate_sb_script',WP_CONTENT_URL . '/plugins/' . plugin_basename(dirname(__FILE__)).'/qoate-sb-script.php?anim='.$options['animation'].'&vpos='.$options['vplacement'].'&perc='.$options['percentage'].'&sac='.$sac);
}

function qoate_add_sb(){
global $social_sites;
global $post;
$options = get_option('qoate_sb_holder',array('text'=>'Your HTML-content goes here..','vplacement'=>'bottom'));
$sb_options = get_option('qoate_sb_settings',array('text'=>'Liked this page? Share it!'));
$content = '<div id="qoate_social_bookmark" style="background:transparent url('.WP_CONTENT_URL.'/plugins/'.plugin_basename(dirname(__FILE__)).'/overlay.png);">';

if($options['with_minimize']=='1') {
	$content .= '<img style="position:absolute; cursor:pointer; right:0; margin:2px; " id="qoate_close_box" src="'.WP_CONTENT_URL.'/plugins/'.plugin_basename(dirname(__FILE__)).'/close.png" alt="Close" />';
	$content .= '<img style="position:absolute; display:none; cursor:pointer; right:0;" id="qoate_show_box" src="'.WP_CONTENT_URL.'/plugins/'.plugin_basename(dirname(__FILE__)).'/show_'.$options['vplacement'].'.png" alt="Show" />';
}

if($sb_options['use_bookmarks'] != '1') {
	$content .= '<div class="qoate_box_content">';
	$content .= $options['text'];
} else {
	$content.= '<h4>'.$sb_options['text'].'</h4>';
	$content .= '<div class="qoate_box_content">';
	
	/* Credits to Sociable from Blogplay for most of the following piece of code! Check their plugin, it's great to show social bookmarking options below your content! */
	
	// Load the post's and blog's data
	$blogname 	= urlencode(get_bloginfo('name')." ".get_bloginfo('description'));
	$blogrss	= get_bloginfo('rss2_url'); 
	if(is_home() || is_archive()) {
		$excerpt = urlencode(substr(get_bloginfo('description'),0,250));
		$excerpt = str_replace('+','%20',$excerpt);
		$permalink = get_bloginfo('wpurl');
		$title = 'Check out this great website!';
	} else {
		// Grab the excerpt, if there is no excerpt, create one
		$excerpt	= urlencode(strip_tags(strip_shortcodes($post->post_excerpt)));
		if ($excerpt == "") {
			$excerpt = urlencode(substr(strip_tags(strip_shortcodes($post->post_content)),0,250));
		}
		// Clean the excerpt for use with links
		$excerpt	= str_replace('+','%20',$excerpt);
		$permalink 	= urlencode(get_permalink($post->ID));
		$title 		= str_replace('+','%20',urlencode($post->post_title));
	}

	foreach($social_sites as $name => $site) {
		if($sb_options[$name]=='1') {
		$url = $site['url'];
		$url = str_replace('TITLE', $title, $url);
		$url = str_replace('RSS', $rss, $url);
		$url = str_replace('PERMALINK',$permalink,$url);
		$url = str_replace('BLOGNAME', $blogname, $url);
		$url = str_replace('EXCERPT', $excerpt, $url);
		$url = str_replace('FEEDLINK', $blogrss, $url);
		
		$content.= '<a rel="nofollow" target="_blank" href="'.$url.'">';
		$content.= '<img src="'.WP_CONTENT_URL.'/plugins/'.plugin_basename(dirname(__FILE__)).'/images/'.strtolower($name).'.png" alt='.$name.' />';
		$content.='</a>';
		}
	}
}
$content .='</div>';
$content .= '<!-- This Box is generated by Qoate Scroll Triggered Box! -->';
$content .= '</div>';

echo $content;
}




?>