<?php
/*
Plugin Name: Subscribers Only Content
Plugin URI: 
Description: Adds the ability to limit viewing of specific pages and posts only to subscribers
Version: 1.0.1
Author: Michael Pretty (prettyboymp)

*******************************************************************
Copyright 2009-2009 Michael Pretty  (email : mpretty@voceconnect.com)

This program is free software: you can redistribute it and/or modify
it under the terms of the GNU General Public License as published by
the Free Software Foundation, either version 3 of the License, or
(at your option) any later version.

This program is distributed in the hope that it will be useful,
but WITHOUT ANY WARRANTY; without even the implied warranty of
MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
GNU General Public License for more details.

You should have received a copy of the GNU General Public License
along with this program.  If not, see <http://www.gnu.org/licenses/>.
*******************************************************************
*/

class SubscribersOnlyContent
{
	const META_KEY = 'subscribers_only';
	const NONCE_NAME = 'subscribers_only_nonce';
	
	/**
	 * registers the meta boxes for the edit screens
	 *
	 */
	public function action_admin_menu()
	{
		add_meta_box('subscribers_only', 'Subscribers Only', array($this, 'meta_box_subscribers_only'), 'post', 'side');
		add_meta_box('subscribers_only', 'Subscribers Only', array($this, 'meta_box_subscribers_only'), 'page', 'side');
	}

	/**
	 * Prints the meta box for the edit post/page screens
	 *
	 * @param unknown_type $post
	 */
	public function meta_box_subscribers_only($post)
	{
		$hide = (bool) get_post_meta($post->ID, self::META_KEY, true);
		?>
		<input type="checkbox" name="chk_subscribers_only" id="chk_subscribers_only" value="1" <?php checked($hide) ?> />
		<label for="chk_subscribers_only">Hide this item from non-subscribers</label>
		<input type="hidden" name="<?php echo self::NONCE_NAME?>" id="<?php echo self::NONCE_NAME?>" value="<? echo wp_create_nonce(plugin_basename(__FILE__))?>" />
		<?php
	}

	/**
	 * Saves the meta value for the page or post
	 *
	 * @param int $post_id
	 * @return int
	 */
	public function action_save_content_meta($post_id)
	{
		if ( wp_is_post_revision($post_id) || wp_is_post_autosave($post_id))
		{
			return $post_id;
		}
		if(!wp_verify_nonce($_POST[self::NONCE_NAME], plugin_basename(__FILE__)))
		{
			return $post_id;
		}

		if ( 'page' == $_POST['post_type'] ) {
			if ( !current_user_can( 'edit_page', $post_id ))
			return $post_id;
		} else {
			if ( !current_user_can( 'edit_post', $post_id ))
			return $post_id;
		}
		$hide = isset($_POST['chk_subscribers_only']);
		if($hide)
		{
			update_post_meta($post_id, self::META_KEY, true);
		}
		else
		{
			delete_post_meta($post_id, self::META_KEY);
		}
		return $post_id;
	}

	/**
	 * Adds custom where clause to filter out private posts and pages
	 *
	 * @param string $where
	 * @return string
	 */
	public function filter_posts_where($where)
	{
		if(!current_user_can('read'))
		{
			global $wpdb;
			$where.= " AND SO_META.meta_value is null ";
		}
		return $where;
	}

	/**
	 * Adds meta join to filter out private posts and pages
	 *
	 * @param string $join
	 * @return string
	 */
	public function filter_posts_join($join)
	{
		if(!current_user_can('read'))
		{
			global $wpdb;
			$join.= " LEFT JOIN {$wpdb->postmeta} SO_META ON SO_META.meta_key = '". self::META_KEY ."' AND SO_META.post_id = {$wpdb->posts}.ID ";
		}
		return $join;
	}

	/**
	 * Filters pages within the wp_list_pages function
	 *
	 * @param array $pages
	 * @param array $r
	 * @return array
	 */
	public function filter_get_pages($pages, $r)
	{
		if(count($pages) > 0 && !current_user_can('read'))
		{
			$posts = get_posts(array('meta_key'=>self::META_KEY, 'post_type'=>'page'));
			if(count($posts) > 0)
			{
				$exclude = array();
				foreach($posts as $post)
				{
					$exclude[] = $post->ID;
				}
				for($i = 0; $i < count($exclude); $i++)
				{
					if(in_array($pages[$i]->ID, $exclude))
					{
						array_splice($pages, $i, 1);
						$i--;
					}
				}
			}
		}
		return $pages;
	}
}

$so = new SubscribersOnlyContent();
add_action('admin_menu', array($so, 'action_admin_menu'));
add_action('save_post', array($so, 'action_save_content_meta'));
add_action('save_page', array($so, 'action_save_content_meta'));
add_filter('posts_where', array($so, 'filter_posts_where'));
add_filter('posts_join', array($so, 'filter_posts_join'));
add_filter('get_pages', array($so, 'filter_get_pages'), 10, 2);
unset($so); //clear global space