<?php
/*
Plugin Name: WP Hello Bar
Plugin URI: http://andrewnorcross.com/plugins/hello-bar
Description: Insert the HelloBar on your WordPress site
Version: 1.02
Author: norcross
Author URI: http://andrewnorcross.com
License: GPL v2

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

// Start up the engine
class WPHelloBar
{
    /**
     * Static property to hold our singleton instance
     * @var WPHelloBar
     */
    static $instance = false;


    /**
     * This is our constructor, which is private to force the use of
     * getInstance() to make this a Singleton
     *
     * @return WPHelloBar
     */
    private function __construct() {
        add_action      ( 'admin_menu',     array( $this, 'menu_settings'   ) );
        add_action      ( 'admin_init',     array( $this, 'reg_settings'    ) );
        add_action      ( 'admin_head',     array( $this, 'admin_css'       ) );
        add_action      ( 'init',           array( $this, 'hellobar'        ) );
        add_action      ( 'wp_head',        array( $this, 'logged_css'      ) );
    }

    /**
     * If an instance exists, this returns it.  If not, it creates one and
     * retuns it.
     *
     * @return WPHelloBar
     */

    public static function getInstance() {
        if ( !self::$instance )
            self::$instance = new self;
        return self::$instance;
    }



    /**
     * build out settings page and meta boxes
     *
     * @return WPHelloBar
     */

    public function menu_settings() {
        add_submenu_page('options-general.php', 'HelloBar Settings', 'HelloBar Settings', 'manage_options', 'hb-settings', array( $this, 'hb_settings_display' ));
    }

    /**
     * Register settings
     *
     * @return WPHelloBar
     */


    public function reg_settings() {
        register_setting( 'hb_options', 'hb_options');
    }

    /**
     * CSS in the head for the settings page
     *
     * @return WPHelloBar
     */

    public function admin_css() { ?>
        <style type="text/css">

        div#icon-hb {
            background:url(<?php echo plugins_url('/lib/img/hellobar-icon.png', __FILE__); ?>) no-repeat 0 0!important;
        }

        </style>

    <?php }

    /**
     * Trigger the script load based on user settings
     *
     * @return WPHelloBar
     */

    public function hellobar() {

        $hb_options = get_option('hb_options');

        // no values have been entered. bail.
        if( empty($hb_options['digit_one']) || empty($hb_options['digit_two']) )
            return;

        // check for user selection for header or footer load
        $head_load  = (isset($hb_options['header']) && $hb_options['header'] == 'true' ? true : false );

        // they wanted the head
        if($head_load === true)
            add_action ( 'wp_head',    array( $this, 'hellobar_load' ) );

        // they didn't want the head
        if($head_load === false)
            add_action ( 'wp_footer',  array( $this, 'hellobar_load' ) );

    }

    /**
     * Load the actual scripts
     *
     * @return WPHelloBar
     */

    public function hellobar_load() {

        $hb_options = get_option('hb_options');

        // check for user selection regarding logged in users
        $user_load  = (isset($hb_options['users']) && $hb_options['users'] == 'true' ? true : false );

        // bail if logged in
        if(is_user_logged_in() && $user_load === false )
            return;

        // now load the goddamn script
        echo '<script type="text/javascript" src="//www.hellobar.com/hellobar.js"></script>';
        echo '<script type="text/javascript">
                new HelloBar('.$hb_options['digit_one'].','.$hb_options['digit_two'].');
              </script>
              ';

    }

    /**
     * CSS in the head to adjust the HelloBar location for logged in users
     *
     * @return WPHelloBar
     */

    public function logged_css() {

        $hb_options = get_option('hb_options');

        // check for user selection regarding logged in users
        $user_load  = (isset($hb_options['users']) && $hb_options['users'] == 'true' ? true : false );

        // if displaying logged in users, add the CSS so they can actually see it
        if(is_user_logged_in() && $user_load === false )
            return;

        if(is_user_logged_in() && $user_load === true ) {
            echo '<style type="text/css" media="all">';
            echo '#hellobar-wrapper {top:28px!important;}';
            echo '</style>';
        }

    }

    /**
     * Display main options page structure
     *
     * @return WPHelloBar
     */

    public function hb_settings_display() { ?>

        <div class="wrap">
        <div class="icon32" id="icon-hb"><br></div>
        <h2><?php _e('HelloBar Settings') ?></h2>

            <div class="options">
            <p>First, log in to your HelloBar account and get the code provided. In the code, there should be two numbers (see example below)</p>
            <p><img alt="Script Example" title="Script Example" src="<?php echo plugins_url('/lib/img/script-example.jpg', __FILE__); ?>"></p>
            <p>Copy the two highlighted numbers into the fields below.</p>

                <div class="fb_form_options">
                <form method="post" action="options.php">
                <?php
                settings_fields( 'hb_options' );
                $hb_options = get_option('hb_options');
                $head_load  = (isset($hb_options['header']) && $hb_options['header'] == 'true' ? 'checked="checked"' : '');
                $user_load  = (isset($hb_options['users']) && $hb_options['users'] == 'true' ? 'checked="checked"' : '');
                ?>

                <table class="form-table hb-table">
                <tbody>
                    <tr>
                        <th><label for="hb_options[digit_one]"><?php _e('First Number') ?></label></th>
                        <td>
                            <input type="text" class="small-text" value="<?php if(isset($hb_options['digit_one'] )) echo $hb_options['digit_one']; ?>" id="digit_one" name="hb_options[digit_one]">
                            <span class="description"><?php _e('The first number group from your HelloBar code') ?></span>
                        </td>
                    </tr>

                    <tr>
                        <th><label for="hb_options[digit_two]"><?php _e('Second Number') ?></label></th>
                        <td>
                            <input type="text" class="small-text" value="<?php if(isset($hb_options['digit_two'] )) echo $hb_options['digit_two']; ?>" id="digit_two" name="hb_options[digit_two]">
                            <span class="description"><?php _e('The second number group from your HelloBar code') ?></span>
                        </td>
                    </tr>

                    <tr>
                        <th><label for="hb_options[header]"><?php _e('Script Loading') ?></label></th>
                        <td><label for="hb_options[header]"><input type="checkbox" id="header_load" name="hb_options[header]" value="true" <?php echo $head_load; ?> /> Load script in <code>< head ></code> (loads in footer by default)</label>
                        </td>
                    </tr>

                    <tr>
                        <th><label for="hb_options[users]"><?php _e('Logged In Users') ?></label></th>
                        <td><label for="hb_options[users]"><input type="checkbox" id="user_load" name="hb_options[users]" value="true" <?php echo $user_load; ?> /> Display HelloBar for logged in users.</label>
                        </td>
                    </tr>

                </tbody>
                </table>

                <p><input type="submit" class="button-primary" value="<?php _e('Save Changes') ?>" /></p>
                </form>
                </div>

            </div>

        </div>

    <?php }

/// end class
}


// Instantiate our class
$WPHelloBar = WPHelloBar::getInstance();
