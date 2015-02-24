<?php
/*
Plugin Name: Subscribe Here Widget
Plugin URI: http://www.improveseo.info/subscribe-here-widget-for-wordpress/
Description: Sidebar widget to display feed icon and subscribe by email controls
Author: Adrian Ianculescu
Version: 1.0
Author URI: http://improveseo.info/
*/

$subscribeHereShowTitle = false;

function subscribeHere($feedId) 
{
	echo '<a href="' . get_bloginfo('rss2_url') . '"><div class="rss-widget">Subscribe by RSS</div></a>';
	
	if ($feedId == null)
		return;

echo <<<block
	<form class="feedemail-form" action="http://feedburner.google.com/fb/a/mailverify" method="post" target="popupwindow" onsubmit="window.open('http://feedburner.google.com/fb/a/mailverify?uri=$feedId', 'popupwindow', 'scrollbars=yes,width=550,height=520');return true">
		<div class="feedemail-label">Subscribe by Email:</div>
		<input type="text" class="feedemail-input" name="email" value="your email here" onclick="this.focus();this.select();"/>
		<input type="hidden" value="$feedId" name="uri"/>
		<input type="hidden" name="loc" value="en_US"/>
		<input type="submit" value="Subscribe" class="feedemail-button"/>
		<div><span class="feedemail-footer">Delivered by <a href="http://feedburner.google.com" target="_blank">FeedBurner</a></span></div>
	</form>
block;
}

function widget_subscribeHere($args) {
	extract($args);

	$options = get_option("widget_subscribeHere");
	if (is_array( $options ) && (isset($options['feedId'])))
	{
		$feedId = $options['feedId'];
	}
	else
	{
		$feedId = null;
	}
  
	echo $before_widget;
	if ($subscribeHereShowTitle == true)
		echo $before_title . "My Widget Title" . $after_title;
		
	subscribeHere($feedId);
	echo $after_widget;
}

function subscribeHere_init()
{
	register_sidebar_widget(__('Subscribe Here'), 'widget_subscribeHere');     
	register_widget_control(   'Subscribe Here', 'subscribeHere_control');     
}
add_action("plugins_loaded", "subscribeHere_init");


// admin file
function subscribeHere_control() 
{
	$options = get_option("widget_subscribeHere");
	if (!is_array( $options ))
	{
		$options = array(
			'feedId' => 'Enter Your Feed Id'
		); 
	}

	if ($_POST['subscribeHere-Submit']) 
	{
		$options['feedId'] = htmlspecialchars($_POST['subscribeHere-WidgetTitle']);
		update_option("widget_subscribeHere", $options);
	}	
	
	$feedburner = $options['feedId'];

	echo '<p>
			<label for="subscribeHere-WidgetTitle">Feedburner Feed Id: </label>
			<input type="text" id="subscribeHere-WidgetTitle" name="subscribeHere-WidgetTitle" value="'.$feedburner.'" />
			<input type="hidden" id="subscribeHere-Submit" name="subscribeHere-Submit" value="1" />
		 </p>';
}


// load the css file
function subscribeHere_wp_head() 
{
	echo '<!-- Required by Subscribe Here Plugin 1.0 plugin -->';
	echo '<link rel="stylesheet" type="text/css" href="' . get_bloginfo('wpurl') . '/wp-content/plugins/subscribe-here-widget/subscribe-here-widget.css" media="screen" />';
}	

add_action('wp_head', 'subscribeHere_wp_head');
?>