<?php
class ScrollBox_admin
{
    private $defaults;
    private $defaultHTML;

    function __construct($defaults = null,$html = '')
    {
        $this->defaults = $defaults;
        $this->defaultHTML = $html;

        if (is_admin()) {
            add_action('admin_menu', array($this, 'stb_admin_menu'));
            add_action('admin_init', array($this, 'stb_register_settings'));
            add_filter('plugin_row_meta', array($this, 'donate_link'), 10, 2);
            add_action('admin_enqueue_scripts', array($this, 'load_admin_scripts'));
        }
    }

    function load_admin_scripts()
    {
        wp_enqueue_script('jquery');
        wp_enqueue_script('jquery-ui-tabs');
		wp_enqueue_script('jquery_cookie', plugin_dir_url(__FILE__) . 'jquery . cookie . js', array('jquery'),'1.3',false);
        wp_enqueue_script('stb_admin_script', plugin_dir_url(__FILE__) . 'stb_admin.js', array('jquery-ui-tabs'));
        wp_enqueue_style('jquery-ui', 'http://ajax.googleapis.com/ajax/libs/jqueryui/1.8.2/themes/base/jquery-ui.css');
    }


    function donate_link($links, $file)
    {
        if ($file == plugin_basename(__FILE__)) {
            $donate_link = '<a target="_blank" href=" https://www.paypal.com/cgi-bin/webscr?cmd=_s-xclick&hosted_button_id=B4NCTTDR9MEPW">Donate</a>';
            $links[] = $donate_link;
        }
        return $links;
    }

    // Register settings
    function stb_register_settings()
    {
        register_setting('stb_options', 'stb_settings');
        register_setting('stb_options', 'stb_html');
    }

    function get_templates($current)
    {
        $dir = plugin_dir_path(__FILE__) . 'templates/';
        if ($handle = opendir($dir)) {
            $templates = '';
            while (false !== ($entry = readdir($handle))) {
                if ($entry != '.' && $entry != '..')
                    if (is_dir(($dir . $entry)))
                        $templates .= '<option ' . selected($entry, $current) . ' value="' . $entry . '">' . ucfirst(str_replace('_', ' ', $entry)) . '</option>';
            }
            closedir($handle);
            return $templates;
        }
        return false;
    }

    function stb_admin_settings_page()
    {
        ?>
    <div class="wrap" style="padding-top: 10px;">
    <div id="icon-options-general" class="icon32"><br></div>
    <h2><?php _e('Scroll Triggered Box', 'stb'); ?></h2>

    <div class="widget-liquid-left">
        <div id="widgets-left">
            <div id="available-widgets" class="widgets-holder-wrap ui-droppable">
                <div class="sidebar-name">
                    <h3><?php _e('Options', 'stb'); ?></h3>
                </div>
                <div class="widget-holder">
                    <form method="post" action="options.php">
                        <?php
                        settings_fields('stb_options');
                        // Namespace fix
                        if(get_option('sdb_settings')){
                            update_option( 'stb_settings', get_option('sdb_settings') );
                            update_option( 'stb_html', get_option('sdb_html') );
                            delete_option( 'sdb_settings' );
                            delete_option( 'sdb_html' );
                        }
                        $options = get_option('stb_settings', $this->defaults);
                        $formHTML = get_option('stb_html', $this->defaultHTML);
                        ?>
                        <table class="form-table">
                            <tbody>
                            <tr valign="top">
                                <th scope="row"><label for="btheme"><?php _e('Theme', 'stb'); ?></label></th>
                                <td>
                                    <select name="stb_settings[theme]" id="btheme">
                                        <?php echo $this->get_templates($options['theme']); ?>
                                    </select>
                                    <?php _e('Theme selection', 'stb'); ?>
                                </td>
                            </tr>
                            <tr valign="top">
                                <th scope="row"><label for="show_admin"><?php _e('Testing', 'stb'); ?></label></th>
                                <td>
                                    <input name="stb_settings[show_admin]" type="checkbox" id="show_admin"
                                           value="1" <?php checked(1, isset($options['show_admin'])); ?> /><label
                                    for="show_admin"><?php _e('Show box to admins only.', 'stb'); ?></label>
									<?php if(isset($_COOKIE['nopopup'])): ?>
										<strong><?php _e('Box is hidden', 'stb'); ?></strong> <a href="#" id="cleanCookie">Click here to make the box visible</a>
									<?php else: ?>
										<strong><?php _e('Box should be visible', 'stb'); ?></strong>
									<?php endif; ?>
                                </td>
                            </tr>
                            <tr valign="top">
                                <th scope="row"><?php _e('Visible at', 'stb'); ?></th>
                                <td>
                                    <input name="stb_settings[show][page]" type="checkbox" id="bpages"
                                           class="tog" <?php checked(1, isset($options['show']['page'])); ?>><label
                                    for="bpages"><?php _e('Pages', 'stb'); ?></label><br/>
                                    <input name="stb_settings[show][post]" type="checkbox" id="bposts"
                                           class="tog" <?php checked(1, isset($options['show']['post'])); ?>><label
                                    for="bposts"><?php _e('Posts', 'stb'); ?></label><br/>
                                    <input name="stb_settings[show][frontpage]" type="checkbox" id="bfpage"
                                           class="tog" <?php checked(1, isset($options['show']['frontpage'])); ?>><label
                                    for="bfpage"><?php _e('Frontpage', 'stb'); ?></label>
                                    <?php $this->stb_get_post_types($options); ?>
                                </td>
                            </tr>
                            <tr valign="top">
                                <th scope="row"><label for="lifetime"><?php _e('Cookie lifetime', 'stb'); ?></label></th>
                                <td><input name="stb_settings[cookie_life]" type="text" id="lifetime"
                                           value="<?php echo $options['cookie_life']; ?>" class="small-text">
                                    <?php _e('Ammount of days for the box to stay hidden, when the user has closed it.', 'stb'); ?>
                                </td>
                            </tr>
                            <tr valign="top">
                                <th scope="row"><label for="trigh"><?php _e('Show box at', 'stb'); ?></label></th>
                                <td><input name="stb_settings[trigger_height]" type="text" id="trigh"
                                           value="<?php echo $options['trigger_height']; ?>" class="small-text">
                                    <?php _e('Box will be shown when the user has scrolled selected percentage of total
                                    document height.', 'stb'); ?>
                                </td>
                            </tr>
                            <tr valign="top">
                                <th scope="row"><label for="trigelem"><?php _e('Show box at element', 'stb'); ?></label></th>
                                <td>
                                    <input name="stb_settings[trigger_element]" type="text" id="trigelem"
                                           value="<?php echo $options['trigger_element']; ?>" class="regular-text">
                                    <?php _e('class or id of the element to show the box at. Leave empty to use the percentage
                                    setting.<br/>
                                    For example: <strong>#comments</strong> - at the beginning of comments section', 'stb'); ?>
                                </td>
                            </tr>
                            <tr valign="top">
                                <th scope="row"><label for="includecss"><?php _e('Include css', 'stb'); ?></label></th>
                                <td>
                                    <input name="stb_settings[include_css]" type="checkbox" id="includecss"
                                           value="1" <?php checked(1, isset($options['include_css'])); ?> />
                                    <?php _e('Uncheck only if you want to style the box by yourself via themes css. CSS file
                                    is located', 'stb'); ?> <a
                                    href="<?php echo plugin_dir_url(__FILE__) . 'templates/' . $options['theme'] . '/style.css' ?>">here</a>
                                </td>
                            </tr>
                            <tr valign="top">
                                <th scope="row"><label for="hidemobile"><?php _e('Hide in mobile', 'stb'); ?></label></th>
                                <td>
                                    <input name="stb_settings[hide_mobile]" type="checkbox" id="hidemobile"
                                           value="1" <?php checked(1, isset($options['hide_mobile'])); ?> />
                                    <?php _e('Hide the box in mobile clients', 'stb'); ?>
                                </td>
                            </tr>
                            <tr valign="top">
                                <th scope="row"><?php _e('Box position', 'stb'); ?></th>
                                <td>
                                    <input name="stb_settings[position]" type="radio" value="right" id="bright"
                                           class="tog" <?php checked('right', $options['position']); ?>><label
                                    for="bright"><?php _e('Right', 'stb'); ?></label><br/>
                                    <input name="stb_settings[position]" type="radio" value="left" id="bleft"
                                           class="tog" <?php checked('left', $options['position']); ?>><label
                                    for="bleft"><?php _e('Left', 'stb'); ?></label><br/>
                                    <input name="stb_settings[position]" type="radio" value="middle" id="bmid"
                                           class="tog" <?php checked('middle', $options['position']); ?>><label
                                    for="bmid"><?php _e('Middle', 'stb'); ?></label>
                                </td>
                            </tr>
                            <tr valign="top">
                                <th scope="row"><label for="bwidth"><?php _e('Box width', 'stb'); ?></label></th>
                                <td><input name="stb_settings[width]" type="text" id="bwidth"
                                           value="<?php echo $options['width']; ?>" class="small-text">
                                    <?php _e('Width of the box in px.', 'stb'); ?>
                                </td>
                            </tr>
                            <tr valign="top">
                                <th scope="row"><label for="receiver_email"><?php _e('Receiver email', 'stb'); ?></label></th>
                                <td><input name="stb_settings[receiver_email]" type="text" id="receiver_email"
                                           value="<?php echo $options['receiver_email']; ?>" class="regular-text">
                                    <?php _e('Default form sends a message to this email', 'stb'); ?>
                                </td>
                            </tr>
                            <tr valign="top">
                                <th scope="row"><label for="moderation_keys"><?php _e('Box html', 'stb'); ?></label></th>
                                <td><?php _e('Content of the box. You are allowed to use HTML.', 'stb'); ?>
                                    <div id="tabs">
                                        <?php
                                        if (function_exists('icl_get_languages')) :
                                            $wpml_options = get_option('icl_sitepress_settings');
                                            $default_lang = $wpml_options['default_language'];
                                            $langs = icl_get_languages('skip_missing=0');
                                            // Move the default language to the beginning of array.
                                            $default_html = $langs[$default_lang];
                                            unset($langs[$default_lang]);
                                            $this->array_unshift_assoc($langs, $default_lang, $default_html);
                                            ?>
                                            <ul>
                                                <?php foreach ($langs as $lang)  : ?>
                                                <li><a
                                                    href="#tab<?php echo $lang['id'] ?>"><?php echo $lang['translated_name'] ?></a>
                                                </li>
                                                <?php endforeach; ?>
                                            </ul>
                                            <?php foreach ($langs as $lang) :
                                            if (is_array($formHTML))
                                                $HTMLcontent = array_key_exists($lang['language_code'], $formHTML) ? $formHTML[$lang['language_code']] : reset($formHTML);
                                            else
                                                $HTMLcontent = $formHTML;

                                            ?>
                                            <div id="tab<?php echo $lang['id'] ?>">
                                                <textarea name="stb_html[<?php echo $lang['language_code'] ?>]"
                                                          rows="10" cols="50"
                                                          id="moderation_keys_<?php echo $lang['id']  ?>"
                                                          class="large-text code"><?php echo htmlspecialchars($HTMLcontent); ?></textarea>
                                            </div>
                                            <?php endforeach; ?>

                                            <?php else :
                                            if (is_array($formHTML)) $formHTML = reset($formHTML);
                                            ?>
                                            <textarea name="stb_html[]" rows="10" cols="50" id="moderation_keys"
                                                      class="large-text code"><?php echo htmlspecialchars($formHTML); ?></textarea>

                                            <?php endif; ?>
                                    </div>
                                </td>
                            </tr>
                            <tr valign="top">
                                <th scope="row"><?php _e('Social buttons', 'stb'); ?></th>
                                <td>
                                    <select name="stb_settings[social][facebook]">
                                        <option value="0" <?php selected(0,$options['social']['facebook']) ?>>Inactive</option>
                                        <option value="standard" <?php selected('standard',$options['social']['facebook']) ?>>Button</option>
                                        <option value="button_count" <?php selected('button_count',$options['social']['facebook']) ?>>Button Count</option>
                                        <option value="box_count" <?php selected('box_count',$options['social']['facebook']) ?>>Box</option>
                                    </select> <label for="bpages">Facebook</label><br />
                                    <select name="stb_settings[social][twitter]">
                                        <option value="0" <?php selected(0,$options['social']['twitter']) ?>>Inactive</option>
                                        <option value="no-count" <?php selected('no-count',$options['social']['twitter']) ?>>Button</option>
                                        <option value="regular" <?php selected('regular',$options['social']['twitter']) ?>>Button Count</option>
                                        <option value="vertical" <?php selected('vertical',$options['social']['twitter']) ?>>Box</option>
                                    </select> <label for="bposts">Twitter</label><br />
                                    <select name="stb_settings[social][google]">
                                        <option value="0" <?php selected(0,$options['social']['google']) ?>>Inactive</option>
                                        <option value="annotation" <?php selected('annotation',$options['social']['google']) ?>>Button</option>
                                        <option value= "medium" <?php selected('medium',$options['social']['google']) ?>>Button Count</option>
                                        <option value="tall" <?php selected('tall',$options['social']['google']) ?>>Box</option>
                                    </select> <label for="bposts">Google+</label><br />
                                    <select name="stb_settings[social][pinterest]">
                                        <option value="0" <?php selected(0,$options['social']['pinterest']) ?>>Inactive</option>
                                        <option value="none" <?php selected('none',$options['social']['pinterest']) ?>>Button</option>
                                        <option value="horizontal" <?php selected('horizontal',$options['social']['pinterest']) ?>>Button Count</option>
                                        <option value="vertical" <?php selected('vertical',$options['social']['pinterest']) ?>>Box</option>
                                    </select> <label for="bposts">Pinterest</label> <small>* Pin it button will only be displayed on the pages that have a featured image.</small><br />
                                    <select name="stb_settings[social][stumbleupon]">
                                        <option value="0" <?php selected(0,$options['social']['stumbleupon']) ?>>Inactive</option>
                                        <option value="1" <?php selected(1,$options['social']['stumbleupon']) ?>>Button</option>
                                        <option value="4" <?php selected(4,$options['social']['stumbleupon']) ?>>Button Count</option>
                                        <option value="5" <?php selected(5,$options['social']['stumbleupon']) ?>>Box</option>
                                    </select> <label for="bposts">Stumbleupon</label><br />
                                    <select name="stb_settings[social][linkedin]">
                                        <option value="0" <?php selected(0,$options['social']['linkedin']) ?>>Inactive</option>
                                        <option value="none" <?php selected('none',$options['social']['linkedin']) ?>>Button</option>
                                        <option value="right" <?php selected('right',$options['social']['linkedin']) ?>>Button Count</option>
                                        <option value="top" <?php selected('top',$options['social']['linkedin']) ?>>Box</option>
                                    </select> <label for="bposts">LinkedIN</label>
                                </td>
                            </tr>
                            </tbody>
                        </table>


                        <p class="button-controls" style="margin-left: 10px;"><input type="submit" name="submit"
                                                                                     id="submit"
                                                                                     class="button-primary"
                                                                                     value="Save Changes"></p>
                    </form>
                </div>

            </div>
            <br class="clear">
        </div>


    </div>
    <?php
        $url = 'https://www.facebook.com/sharer/sharer.php?n=4&s=100';
        $url .= '&p[summary]=' . urlencode('Scroll Triggered Box will boost your conversion rates! The plugin displays a pop-up box with customizable content.');
        $url .= '&p[url]=' . urlencode('http://www.dreamgrow.com/dreamgrow-scroll-triggered-box/');
        $url .= '&p[title]=' . urlencode('Check out this WordPress plugin: Scroll Triggered Box');
    ?>
    <div class="widget-liquid-right" style="width: 250px;">
        <div id="widgets-right">

            <div class="widgets-holder-wrap">
                <div class="sidebar-name">
                    <h3>Donate $10, $20 or $50</h3></div>
                <div id="sidaber-widget" class="widgets-sortables">
                    <div class="sidebar-description">
                        <p>If you like scroll triggered box. Please help to keep it alive by donating. Every cent counts!</p>
                        <form action="https://www.paypal.com/cgi-bin/webscr" method="post"
                              style="text-align: center;">
                            <input type="hidden" name="cmd" value="_s-xclick">
                            <input type="hidden" name="hosted_button_id" value="B4NCTTDR9MEPW">
                            <input type="image" src="https://www.paypalobjects.com/en_US/i/btn/btn_donateCC_LG.gif"
                                   border="0" name="submit" alt="PayPal - The safer, easier way to pay online!">
                            <img alt="" border="0" src="https://www.paypalobjects.com/en_US/i/scr/pixel.gif"
                                 width="1" height="1">
                        </form>
                        <p>How can you support the developement?</p>

                        <p><a href="http://wordpress.org/support/view/plugin-reviews/dreamgrow-scroll-triggered-box?rate=5#postform" target="_blank">Leava a raving review *****</a></p>

                        <p style="word-break: break-all">Link to the plugin's home page from your blog:
                            <a href="http://www.dreamgrow.com/dreamgrow-scroll-triggered-box/" target="_blank">http://www.dreamgrow.com/dreamgrow-scroll-triggered-box/</a></p>

                        <p>Spread the word on <a target="_blank" href="http://twitter.com/intent/tweet/?text=Check%20out%20this%20WordPress%20plugin%3A%20Scroll%20Triggered%20Box&via=Dreamgrow&url=http%3A%2F%2Fwww.dreamgrow.com%2Fdreamgrow-scroll-triggered-box%2F">Twitter</a> or <a target="_blank" href="<?php echo $url ?>">Facebook</a></p>
                    </div>
                </div>
            </div>
        </div>
    </div>
    </div>
    <?php

    }

    function stb_get_post_types($options){
        $args = array(
            'public'   => true,
            '_builtin' => false
        );
        $post_types = get_post_types($args);
        if($post_types) :
            foreach ( $post_types as $post_type ) {
                $label = $post_type;
                echo '<br/>
                      <input name="stb_settings[show]['.$label.']" type="checkbox" id="b'.$label.'" class="tog" '.
                      checked(1, isset($options['show'][$label]), false) .'><label for="b'.$label.'">'. $label .'</label>';
            }
        endif;

    }

    //Adds menu link to Settings tab in admin panel
    function stb_admin_menu()
    {
        add_submenu_page('options-general.php', 'Scroll Triggered Box', 'Scroll Triggered Box', 'manage_options', 'stbox', array($this, 'stb_admin_settings_page'));
    }

    function array_unshift_assoc(&$arr, $key, $val)
    {
        $arr = array_reverse($arr, true);
        $arr[$key] = $val;
        return array_reverse($arr, true);
    }

}