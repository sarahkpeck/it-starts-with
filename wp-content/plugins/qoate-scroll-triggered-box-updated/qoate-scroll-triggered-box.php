<?php
/*
Plugin Name: Qoate Scroll Triggered Box (Updated)
Plugin URI: http://qoate.com/wordpress-plugins/scroll-triggered-box/
Description: A scroll triggered box for easy social bookmarks or a newsletter sign-up form. Great call to action!
Version: 2.3.1
Author: Danny van Kooten
Author URI: http://qoate.com
Updates done by: http://conversionxl.com
License: GPL2
*/

/*  Copyright 2010  Danny van Kooten  (email : danny@qoate.com)

    This program is free software; you can redistribute it and/or modify
    it under the terms of the GNU General Public License, version 2, as 
    published by the Free Software Foundation.

    This program is distributed in the hope that it will be useful,
    but WITHOUT ANY WARRANTY; without even the implied warranty of
    MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
    GNU General Public License for more details.

    You should have received a copy of the GNU General Public License
    along with this program; if not, write to the Free Software
    Foundation, Inc., 51 Franklin St, Fifth Floor, Boston, MA  02110-1301  USA
*/
define('QOATE_NS_PLUGIN_PATH',WP_CONTENT_URL.'/plugins/'. plugin_basename(dirname(__FILE__).'/')); 

$social_sites = Array(
	'del.icio.us' => Array(
		'url' => 'http://delicious.com/post?url=PERMALINK&amp;title=TITLE&amp;notes=EXCERPT',
	),
	'Digg' => Array(
		'url' => 'http://digg.com/submit?phase=2&amp;url=PERMALINK&amp;title=TITLE&amp;bodytext=EXCERPT',
	),
	'Facebook' => Array(
		'url' => 'http://www.facebook.com/share.php?u=PERMALINK&amp;t=TITLE',
	),
	'FriendFeed' => Array(
		'url' => 'http://www.friendfeed.com/share?title=TITLE&amp;link=PERMALINK',
	),
	'Google' => Array(
		'url' => 'http://www.google.com/bookmarks/mark?op=edit&amp;bkmk=PERMALINK&amp;title=TITLE&amp;annotation=EXCERPT',
	),
	'LinkedIn' => Array(
		'url' => 'http://www.linkedin.com/shareArticle?mini=true&amp;url=PERMALINK&amp;title=TITLE&amp;source=BLOGNAME&amp;summary=EXCERPT',
	),
	'Mixx' => Array(
		'url' => 'http://www.mixx.com/submit?page_url=PERMALINK&amp;title=TITLE',
	),
	'MySpace' => Array(
		'url' => 'http://www.myspace.com/Modules/PostTo/Pages/?u=PERMALINK&amp;t=TITLE',
	),
	'Netvibes' => Array(
		'url' => 'http://www.netvibes.com/share?title=TITLE&amp;url=PERMALINK',
	),
	'Posterous' => Array(
		'url' => 'http://posterous.com/share?linkto=PERMALINK&amp;title=TITLE&amp;selection=EXCERPT',
	),
	'Reddit' => Array(
		'url' => 'http://reddit.com/submit?url=PERMALINK&amp;title=TITLE',
	),
	'RSS' => Array(
		'url' => 'FEEDLINK',
	),
	'StumbleUpon' => Array(
		'url' => 'http://www.stumbleupon.com/submit?url=PERMALINK&amp;title=TITLE',
	),
	'Technorati' => Array(
		'url' => 'http://technorati.com/faves?add=PERMALINK',
	),
	'Tumblr' => Array(
		'url' => 'http://www.tumblr.com/share?v=3&amp;u=PERMALINK&amp;t=TITLE&amp;s=EXCERPT',
	),
	'Twitter' => Array(
		'url' => 'http://twitter.com/home?status=TITLE%20-%20PERMALINK',
	),
);


/* Load the plugin files, only when they're needed!*/
add_action("wp", "qoate_load_sb_plugin");
function qoate_load_sb_plugin()
{
  $options = get_option('qoate_sb_holder',array('do_on_posts'=>'1'));
  
  // check for agent if mobile browsers are disabled
  if (isset($options['disable_mobile']) && $options['disable_mobile'])
  {
    $useragent = strtolower($_SERVER['HTTP_USER_AGENT']);
    if (strpos($useragent, 'mobile') !== false || strpos($useragent, 'android') !== false ||
        preg_match('/android.+mobile|avantgo|bada\/|blackberry|blazer|compal|elaine|fennec|hiptop|iemobile|ip(hone|od)|iris|kindle|lge |maemo|midp|mmp|opera m(ob|in)i|palm( os)?|phone|p(ixi|re)\/|plucker|pocket|psp|symbian|treo|up\.(browser|link)|vodafone|wap|windows (ce|phone)|xda|xiino/', $useragent) ||
        preg_match('/(bolt\/[0-9]{1}\.[0-9]{3})|nexian(\s|\-)?nx|(e|k)touch|micromax|obigo|kddi\-|;foma;|netfront/', $useragent) ||
        preg_match('/1207|6310|6590|3gso|4thp|50[1-6]i|770s|802s|a wa|abac|ac(er|oo|s\-)|ai(ko|rn)|al(av|ca|co)|amoi|an(ex|ny|yw)|aptu|ar(ch|go)|as(te|us)|attw|au(di|\-m|r |s )|avan|be(ck|ll|nq)|bi(lb|rd)|bl(ac|az)|br(e|v)w|bumb|bw\-(n|u)|c55\/|capi|ccwa|cdm\-|cell|chtm|cldc|cmd\-|co(mp|nd)|craw|da(it|ll|ng)|dbte|dc\-s|devi|dica|dmob|do(c|p)o|ds(12|\-d)|el(49|ai)|em(l2|ul)|er(ic|k0)|esl8|ez([4-7]0|os|wa|ze)|fetc|fly(\-|_)|g1 u|g560|gene|gf\-5|g\-mo|go(\.w|od)|gr(ad|un)|haie|hcit|hd\-(m|p|t)|hei\-|hi(pt|ta)|hp( i|ip)|hs\-c|ht(c(\-| |_|a|g|p|s|t)|tp)|hu(aw|tc)|i\-(20|go|ma)|i230|iac( |\-|\/)|ibro|idea|ig01|ikom|im1k|inno|ipaq|iris|ja(t|v)a|jbro|jemu|jigs|kddi|keji|kgt( |\/)|klon|kpt |kwc\-|kyo(c|k)|le(no|xi)|lg( g|\/(k|l|u)|50|54|e\-|e\/|\-[a-w])|libw|lynx|m1\-w|m3ga|m50\/|ma(te|ui|xo)|mc(01|21|ca)|m\-cr|me(di|rc|ri)|mi(o8|oa|ts)|mmef|mo(01|02|bi|de|do|t(\-| |o|v)|zz)|mt(50|p1|v )|mwbp|mywa|n10[0-2]|n20[2-3]|n30(0|2)|n50(0|2|5)|n7(0(0|1)|10)|ne((c|m)\-|on|tf|wf|wg|wt)|nok(6|i)|nzph|o2im|op(ti|wv)|oran|owg1|p800|pan(a|d|t)|pdxg|pg(13|\-([1-8]|c))|phil|pire|pl(ay|uc)|pn\-2|po(ck|rt|se)|prox|psio|pt\-g|qa\-a|qc(07|12|21|32|60|\-[2-7]|i\-)|qtek|r380|r600|raks|rim9|ro(ve|zo)|s55\/|sa(ge|ma|mm|ms|ny|va)|sc(01|h\-|oo|p\-)|sdk\/|se(c(\-|0|1)|47|mc|nd|ri)|sgh\-|shar|sie(\-|m)|sk\-0|sl(45|id)|sm(al|ar|b3|it|t5)|so(ft|ny)|sp(01|h\-|v\-|v )|sy(01|mb)|t2(18|50)|t6(00|10|18)|ta(gt|lk)|tcl\-|tdg\-|tel(i|m)|tim\-|t\-mo|to(pl|sh)|ts(70|m\-|m3|m5)|tx\-9|up(\.b|g1|si)|utst|v400|v750|veri|vi(rg|te)|vk(40|5[0-3]|\-v)|vm40|voda|vulc|vx(52|53|60|61|70|80|81|83|85|98)|w3c(\-| )|webc|whit|wi(g |nc|nw)|wmlb|wonu|x700|xda(\-|2|g)|yas\-|your|zeto|zte\-/', substr($useragent,0,4))                
      )
    {
      return;
    }
  }
     
   if($options['do_on_posts']=='1' && is_single()) {
		include("qoate-sb-plugin.php");
   } elseif($options['do_on_pages']=='1' && is_page()) {
		include("qoate-sb-plugin.php");
   } elseif($options['do_on_home']=='1' && is_home()) {
		include("qoate-sb-plugin.php");
   } elseif($options['do_on_archive']=='1' && (is_archive() || is_tag() || is_category() || is_author())) {
		include("qoate-sb-plugin.php");
   }
   
}

if(is_admin()) {
		 include("qoate-sb-dashboard.php");
}

$plugin = plugin_basename(__FILE__); 
add_filter("plugin_action_links_$plugin", 'qoate_sb_settings_link' );
// Add settings link on plugin page
function qoate_sb_settings_link($links) { 
  $settings_link = '<a href="options-general.php?page=qoate-sb-settings">Settings</a>'; 
  array_unshift($links, $settings_link); 
  return $links; 
}
?>