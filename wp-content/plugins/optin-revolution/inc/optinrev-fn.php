<?php if (!defined('ABSPATH')) die('You are not allowed to call this page directly.');

  if ( !function_exists('optinrev_cid') ) {
      function optinrev_cid() { return md5(time()); }
  }

  if ( !function_exists('is_optinrev') ) {
      function is_optinrev() {
          if ( isset($_GET['page']) && $page = esc_html($_GET['page']) ){
          if (  in_array( $page, array('optinrevolution', 'optinrevolution/optin1', 'optinrevolution/optin-pro-settings') ) ) return true;
          }
          return false;
      }
  }

  if ( !function_exists('optinrev_post') ) {
      function optinrev_post( $p, $ret = false ) {
        if ( !$ret )
        echo (isset($_POST[ $p ])) ? stripcslashes($_POST[ $p ]) : '';
        else
        return (isset($_POST[ $p ])) ? stripcslashes($_POST[ $p ]) : '';
      }
  }

  if ( !function_exists('optinrev_unique_id') ) {
      function optinrev_unique_id() {
        global $wpdb;
        $tb_options = $wpdb->prefix . 'optinrev';
        $res = $wpdb->get_row( "select max(id) as ccnt from $tb_options" );
        return ( $wpdb->num_rows > 0 ) ? $res->ccnt + 1 : 0;
      }
  }

  if ( !function_exists('optinrev_popups') ) {
      function optinrev_popups() {
        $optin = array( 'optinrevolution/optin1' => 'Optin Popup 1' );
        optinrev_update( 'optinrev_popups', serialize($optin) );
        return $optin;
      }
  }

  if ( !function_exists('optinrev_update') ) {
      function optinrev_update( $name, $value ) {
        global $wpdb;
        $tb_options = $wpdb->prefix . 'optinrev';

        if ( $wpdb->get_row( $wpdb->prepare("select name, content from $tb_options where name = %s", $name) ) ) {
          $wpdb->update( $tb_options, array('content' => $value), array('name' => $name)  );
          } else {
          $wpdb->insert( $tb_options, array('name' => $name, 'content' => $value) );
        }
      }
  }

  if ( !function_exists('optinrev_get') ) {
      function optinrev_get( $name ) {
        global $wpdb;
        $tb_options = $wpdb->prefix . 'optinrev';
        $res = $wpdb->get_row( $wpdb->prepare("select name, content from $tb_options where name = %s", $name) );
        return ( $wpdb->num_rows > 0 ) ? trim($res->content) : false;
      }
  }

  if ( !function_exists('optinrev_delete') ) {
      function optinrev_delete( $name ) {
        global $wpdb;
        $tb_options = $wpdb->prefix . 'optinrev';
        $wpdb->query( $wpdb->prepare("delete from $tb_options where name = %s", $name) );
      }
  }

  if ( !function_exists('optinrev_is_pro_authorized') ) {
      function optinrev_is_pro_authorized() 
      { 
         if ( $auth_info = optinrev_get('optinrev_pro_authorized') ) {
              if ( is_serialized( $auth_info ) )
              {
                $auth_info = @unserialize( $auth_info );
                return ( isset( $auth_info['amember_email'] ) );
                } else {
                optinrev_delete( 'optinrev_pro_authorized' );                  
              }         
         }         
         return false;
      }
  }  

  if ( !function_exists('optinrev_manually_queue_update') ) {
      function optinrev_manually_queue_update()
      {
         $transient = get_site_transient("update_plugins");
         set_site_transient("update_plugins", optinrev_check_for_plugin_update($transient));
      }
  }

  if ( !function_exists('optinrev_load_jquery') ) {
      function optinrev_load_jquery() {
          wp_enqueue_script('jquery');
      }
  }

  if ( !function_exists('optinrev_enqueue') ) {
      function optinrev_enqueue( $option ) {
         global $plugin_page, $wp_version;
         $dir = OPTINREV_DIR;

         $http = ( $_SERVER['HTTP_HOST'] == 'localhost' ) ? 'http:' : '';

         switch( $option ) {
         case 0:
          wp_enqueue_style( 'optinrev_style', $dir . 'css/optinrev-style.css' );
          wp_enqueue_script( 'jibtn', $dir . 'js/jquery.ibutton.js' );
          wp_enqueue_script( 'jsadmin', $dir . 'js/optinrev-admin.js?t='. optinrev_cid() );
          
         break;
         case 1:
         
          //Style
          wp_enqueue_style( 'jqueryui_css', $http . '//ajax.googleapis.com/ajax/libs/jqueryui/1.8.18/themes/ui-lightness/jquery-ui.css' );
          wp_enqueue_style( 'optinrev_css', home_url( '?optinrev_popup=' . $plugin_page ) . '&t=' .  optinrev_cid() );

          wp_enqueue_style( 'optinrev-wp-theme-css', $dir . '/css/optinrev-wp-theme.css?t=' .  optinrev_cid() );
          wp_enqueue_style( 'optinrev-style', $dir . 'css/optinrev-style.css?t='. optinrev_cid() );

          // wp js
          optinrev_load_jquery();

          wp_enqueue_script( 'optinrev-tinymce', $dir . 'js/tiny_mce/tiny_mce.js?t='. optinrev_cid(), array(), $wp_version, false );

          wp_enqueue_script( 'jquery-ui-slider' );
          wp_enqueue_script( 'jquery-form' );
          
          wp_enqueue_script( 'jquery_jscolor', $dir . 'jscolor/jscolor.js', array(), $wp_version, true );
          wp_enqueue_script( 'jibtn', $dir . 'js/jquery.ibutton.js', array(), $wp_version, true );
          wp_enqueue_script( 'jquery_modaljs', $dir . 'js/jquery.simplemodal.js?t='. optinrev_cid(), array(), $wp_version, true );
          
         break;
         case 2:
         wp_enqueue_style( 'optinrev_style', $dir . 'css/optinrev-style.css?t='. optinrev_cid() );
         break;
         }
      }
  }

  if ( !function_exists('optinrev_mail_providers') ) {
      function optinrev_mail_providers() {
        //mail provider inputs
        $mailpro = array (
                    'aweber' => array(
                                 'action' => 'http://www.aweber.com/scripts/addlead.pl',
                                 'hidden' => 'listname,meta_web_form_id,meta_message,meta_adtracking,redirect,meta_redirect_onlist,meta_required,pixel_tracking_id',
                                 'text' => 'email',
                                 'input' => 'email,listname,meta_web_form_id,meta_message,meta_adtracking,redirect,meta_redirect_onlist,pixel_tracking_id'
                                ),

                    'icontact' => array (//specialid:{} will pick the listid
                                 'action' => 'https://app.icontact.com/icp/signup.php',
                                 'hidden' => 'listid,specialid,clientid,formid,reallistid,doubleopt,redirect,errorredirect',
                                 'text' => 'fields_email',
                                 'input' => 'fields_email,listid,specialid,clientid,formid,reallistid,doubleopt,redirect,errorredirect'
                                ),

                    'getresponse' => array (
                                 'action' => 'https://app.getresponse.com/add_contact_webform.html',
                                 'hidden' => 'webform_id',
                                 'text' => 'email',
                                 'input' => 'email,webform_id'
                                ),
                    'mailchimp' => array (
                                 'action' => 'http://google.us5.list-manage1.com/subscribe/post',
                                 'hidden' => 'mcu,mcid,mcaction',
                                 'text' => 'email',
                                 'input' => 'email,mcu,mcid,mcaction'
                                ),
                    'constantcontact' => array (
                                 'action' => 'http://visitor.r20.constantcontact.com/d.jsp',
                                 'hidden' => 'llr,m,p',
                                 'text' => 'email',
                                 'input' => 'email,llr,m,p'
                                ),
                    'wysija' => array(
                                'action' => '#wysija',
                                'hidden' => '',
                                'text' => 'email',
                                'input' => 'email'
                                ),            
                    'virtualsender' => array(
                                'action' => 'http://www.virtualsender.com/e/a.php/sub/2/vp9v7h',
                                'hidden' => '',
                                'text' => 'email_address',
                                'input' => 'email_address'
                                )
                                
                                );
        optinrev_update( 'optinrev_mail_providers', serialize( $mailpro ) );
      }
  }

  if ( !function_exists('optinrev_setcookie') ) {
    function optinrev_setcookie( $cookie_name, $cookie_value, $expire = null ) {
        $expire = ( empty( $expire ) ) ? time() + 3600 * 24 : $expire;
        @setcookie( $cookie_name, $cookie_value, $expire, '/', COOKIE_DOMAIN, false );
    }
  }

  if ( !function_exists('optinrev_visited_ip') ) {
    function optinrev_visited_ip() {
      if (!isset($_COOKIE['optinrev_visited_ip'])) {
      optinrev_setcookie( 'optinrev_visited_ip', $_SERVER['REMOTE_ADDR'] );
      }
    }
  }

  if ( !function_exists('optinrev_visited_once') ) {
    function optinrev_visited_once( $expire = null ) {
      if (!isset($_COOKIE['optinrev_visited_once'])) {
      optinrev_setcookie( 'optinrev_visited_once', $_SERVER['REMOTE_ADDR'], $expire );
      }
    }
  }

  if ( !function_exists('optinrev_session_browser') ) {
    function optinrev_session_browser( $expire = 0 ) {
      if (!isset($_COOKIE['optinrev_session_browser'])) {
      @setcookie( 'optinrev_session_browser', $_SERVER['REMOTE_ADDR'] , $expire, '/', COOKIE_DOMAIN, false );
      }
    }
  }

  if ( !function_exists('optinrev_get_image') ) {
      function optinrev_get_image( $optin ) {
        global $wpdb;
        $tb_options = $wpdb->prefix . 'optinrev';
        $res = $wpdb->get_results( $wpdb->prepare( "select name, content from $tb_options where name like %s", '%'. $optin .'_images_%' ) );
        return ( $wpdb->num_rows > 0 ) ? $res : false;
      }
  }

  if ( !function_exists('optinrev_delete_image') ) {
      function optinrev_delete_image( $optin ) {
        global $wpdb;
        $tb_options = $wpdb->prefix . 'optinrev';
        $res = $wpdb->get_results( $wpdb->prepare( "select name, content from $tb_options where name like %s", '%'. $optin .'_delete_images_%' ) );
        return ( $wpdb->num_rows > 0 ) ? $res : false;
      }
  }

  if ( !function_exists('optinrev_download_url') ) {
  function optinrev_download_url()
  {
    include_once( ABSPATH . 'wp-includes/class-IXR.php' );

    if ( $auth = optinrev_get('optinrev_pro_authorized') )
    {
        $auth = unserialize( $auth );
        $client = new IXR_Client( OPTINREV_XMLRPC_URL );
        
        if( !$client->query( 'proplug.get_download_url', $auth['amember_email'], optinrev_get_host() ) )
        return false;

        return $client->getResponse();
    }

    return false;
  }
  }

  if ( !function_exists('optinrev_pro_current_version') ) {
      function optinrev_pro_current_version()
      {
        include_once( ABSPATH . 'wp-includes/class-IXR.php' );

        if ( $auth = optinrev_get('optinrev_pro_authorized') )
        {
            $client = new IXR_Client( OPTINREV_XMLRPC_URL );

            if( !$client->query( 'proplug.get_current_version' ) )
              return false;

            return $client->getResponse();
        }
      }
  }

  if ( !function_exists('optinrev_get_media') ) {
      function optinrev_get_media( $id ) {
      global $wpdb;
      $tb_posts = $wpdb->prefix . 'posts';
      $res = $wpdb->get_row( $wpdb->prepare( "select ID, post_title, post_name, guid from $tb_posts where ID = %d", $id ) );
      return ( $wpdb->num_rows > 0 ) ? $res : false;
      }
  }


  if ( !function_exists('optinrev_has_optinmedia') ) {
      function optinrev_has_optinmedia( $id, $type = 'action_button' ) {
      return ( $m = optinrev_get( $type .'_'. $id ) );
      }
  }

  //action button
  if ( !function_exists('optinrev_get_action_button') ) {
      function optinrev_get_action_button() {
      global $wpdb;
      $cr_btn = basename(optinrev_get('optinrev_active_action_button'));
      $cr_btn = explode( '.', $cr_btn );
      $tb_posts = $wpdb->prefix . 'posts';
      $res = $wpdb->get_row( $wpdb->prepare( "select ID, post_title, post_name, guid from $tb_posts where guid like %s", '%'.$cr_btn[0].'%' ) );
      return ( $wpdb->num_rows > 0 ) ? $res : false;
      }
  }
  
  if ( !function_exists( 'optinrev_email_support' ) ) {
      function optinrev_email_support() {
      $img_dir = OPTINREV_DIR . 'images/';
      echo '      
      <center>
      <table cellpadding="10" cellspacing="0">
        <tr><td colspan="2" align="center"><a href="https://optinrevolution.com/r/aweber" target="_blank"><img src="'. $img_dir .'aweber.png" border="0" alt="aweber" title="aweber"/></a><br /><a href="https://optinrevolution.com/r/aweber" target="_blank">Money Back Guarantee! Try Aweber for $1</a></td></tr>
        <tr><td><a href="https://optinrevolution.com/r/icontact" target="_blank"><img src="'. $img_dir .'icontact.png" border="0" alt"iContact" title="iContact"/></td><td><a href="https://optinrevolution.com/r/mailchimp" target="_blank"><img src="'. $img_dir .'mailchimp.png" border="0" alt="Mailchimp" title="Mailchimp"/></a></td></tr>
        <tr><td><a href="https://optinrevolution.com/r/getresponse" target="_blank"><img src="'. $img_dir .'getresponse.png" border="0" alt="getResponse" title="getResponse"/></a></td><td><a href="https://optinrevolution.com/r/constantcontact" target="_blank"><img src="'. $img_dir .'constant_contact.png" border="0" alt="Constant Contact" title="Constant Contact"/></a></td></tr>
      </table>
      </center>
      ';
      }
  }  

  if ( !function_exists( 'optinrev_paypal_donate' ) ) {
      function optinrev_paypal_donate() {
      echo '
      <form action="https://www.paypal.com/cgi-bin/webscr" method="post">
      <input type="hidden" name="cmd" value="_s-xclick">
      <input type="hidden" name="encrypted" value="-----BEGIN PKCS7-----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-----END PKCS7-----">
      <input type="image" src="https://www.paypalobjects.com/en_US/i/btn/btn_donateCC_LG.gif" border="0" name="submit" alt="PayPal - The safer, easier way to pay online!">
      <img alt="" border="0" src="https://www.paypalobjects.com/en_AU/i/scr/pixel.gif" width="1" height="1">
      </form>
      ';
      }
  }

  if ( !function_exists( 'optinrev_cssminify' ) ) {
      function optinrev_cssminify($data)
      {
        $data = preg_replace( '#/\*.*?\*/#s', '', $data );
        $data = preg_replace('/(\t|\r|\n)/', '', $data);
        $data = preg_replace('/(\s+)/', ' ', $data);
        $data = preg_replace('/[^}{]+{\s?}/', '', $data);
        $data = preg_replace('/\s*{\s*/', '{', $data);
        $data = preg_replace('/\s*}\s*/', '}', $data);
        $data = str_replace( ';}', '}', $data );
        $data = str_replace( ', ', ',', $data );
        $data = str_replace( '; ', ';', $data );
        $data = str_replace( ': ', ':', $data );
        $data = preg_replace( '#\s+#', ' ', $data );
        return $data;
      }
  }

  if ( !function_exists('optinrev_getbool') ) {
  function optinrev_getbool( $opt ) {return ( optinrev_get( $opt ) == 'true' ) ? true : false;}
  }

  //utils
  if ( !function_exists('optinrev_is_mobile') )
  {
    function optinrev_is_mobile(){
      $detect = new Optinrev_Mobile_Detect;
      return $detect->isMobile();
    }
  }

  if ( !function_exists('hex2dec') ) {
  function hex2dec( $hex ) {$color = str_replace('#', '', $hex);$ret = ARRAY('r' => hexdec(substr($color, 0, 2)),'g' => hexdec(substr($color, 2, 2)),'b' => hexdec(substr($color, 4, 2)));return $ret;}
  }
  if ( !function_exists('optinrev_is_ie') ) {
  function optinrev_is_ie(){
  if (isset($_SERVER['HTTP_USER_AGENT']) && (strpos($_SERVER['HTTP_USER_AGENT'], 'MSIE') !== false)) 
  {
    return true;} else {return false;
  }}
  }

  if ( !function_exists('optinrev_ie_version') ) {
      function optinrev_ie_version()
      {
        if ( !optinrev_is_ie() ) return false;

        preg_match('/MSIE (.*?);/', $_SERVER['HTTP_USER_AGENT'], $matches);
        if ( count($matches) > 1 ) {
          return (int)$matches[1];
        }
        return false;
      }
  }

  if ( !function_exists('getContrast50') ) {
  function getContrast50($hexcolor){return (hexdec($hexcolor) > 0xffffff/2) ? 'black':'white';}
  }

  if ( !function_exists( 'optinrev_delete_cookie' ) ) {
      function optinrev_delete_cookie( $name ) {
          if ( is_array( $name ) ) {
              foreach( $name as $v ) {
                  @setcookie( $v, null, time() - 3600, '/', COOKIE_DOMAIN, false );
                  unset( $_COOKIE[ $v ] );
              }
              } else {
            @setcookie( $name, null, time() - 3600, '/', COOKIE_DOMAIN, false );
            unset( $_COOKIE[ $name ] );
          }
      }
  }
  
  if ( !function_exists( 'optinrev_get_host' ) ) {
      function optinrev_get_host() {
      $site_url = parse_url( site_url() );
      return $site_url['scheme'] . '://' . $site_url['host'];
      }
  }
 
  if ( !function_exists( 'optinrev_wwwrule' ) ) {
      function optinrev_wwwrule( $url ) {   
      $parsed = parse_url( $url );
      //checking www
      $hst = explode( '.', $parsed['host'] );
      if ( count( $hst ) < 3 ) {
          $url = str_replace( '://', '://www.', $url );
      }
      return $url;
      }
   }

  if ( !function_exists( 'optinrev_remote_info' ) ) {
      function optinrev_remote_info( $arg = 'name' ) {
      $response = wp_remote_get( 'http://api.wordpress.org/plugins/info/1.0/optin-revolution' );
      
      if ( !is_wp_error($response) && ($response['response']['code'] == 200) ) {
		      $response = unserialize($response['body']);
          return $response->$arg;
      }
      
      return false;      
      }  
  }
  
 if ( !function_exists( 'optinrev_banner' ) ) {
      function optinrev_banner() {
      
      $site_url = parse_url(site_url()); 
      $response = wp_remote_get( $site_url['scheme'] . '://pub.optinrevolution.com' );
      $pub = null;
      
      if ( !is_wp_error($response) && ($response['response']['code'] == 200) ) {
		      $pub = $response['body'];
          optinrev_update( 'pub.optinrevolution.com', $pub );
          } else {
          $pub = optinrev_get( 'pub.optinrevolution.com' );          
      }
      
      return <<<BANNER
      {$pub}
BANNER;
      }
  }

 if ( !function_exists( 'optinrev_hosted_video' ) ) {
      function optinrev_hosted_video() {
      
      $site_url = parse_url(site_url()); 
      $response = wp_remote_get( $site_url['scheme'] . '://pub.optinrevolution.com/video.html' );
      $pub = null;
      
      if ( !is_wp_error($response) && ($response['response']['code'] == 200) ) {
		      $pub = $response['body'];
          optinrev_update( 'pub.optinrevolution.com/video', $pub );
          } else {
          $pub = optinrev_get( 'pub.optinrevolution.com/video' );          
      }
      
      return <<<VIDEO
      {$pub}
VIDEO;
      }
  }

 if ( !function_exists( 'optinrev_banner2' ) ) {
      function optinrev_banner2() {
      
      $site_url = parse_url(site_url()); 
      $response = wp_remote_get( $site_url['scheme'] . '://pub.optinrevolution.com/text.html' );
      $pub = null;
      
      if ( !is_wp_error($response) && ($response['response']['code'] == 200) ) {
		      $pub = $response['body'];          
          optinrev_update( 'pub.optinrevolution.com/text.html', $pub );
          } else {          
          $pub = optinrev_get( 'pub.optinrevolution.com/text.html' );          
      }      
      
      return <<<BANNER2
      {$pub}
BANNER2;
      }
  }      
     


/**
 * AJAX Callback action
 */

 if ( !function_exists('optinrev_action_callback') )
 {
     function optinrev_action_callback()
     {
      if ( function_exists('current_user_can') && !current_user_can('manage_options') )
    	die('');//keep silent;

      global $wpdb;

      //saving setup
      if (isset( $_POST['save_setup_settings'] ))
      {
          
          //email form
          $mail_form = array(
            'aweber' => 'Aweber', 'icontact' => 'Icontact', 'getresponse' => 'GetResponse', 'mailchimp' => 'Mailchimp', 'constantcontact' => 'Constant Contact', 'wysija' => 'Wysija'
          );
          
          foreach( $mail_form as $k => $v ) {
              //empty the others;          
              if ( isset( $_POST['optinrev_optin_' . $k ] ) ) {              
                  if ( isset( $_POST['optinrev_foptin_active'] ) && $_POST['optinrev_foptin_active'] !== $k ) {
                  $_POST['optinrev_optin_' . $k ] = null;
                  }              
              }          
          }
          
          if ( !isset( $_POST['optinrev_femail_validate'] ) )
          $_POST['optinrev_femail_validate'] = 'off';
          
          optinrev_update( $_POST['save_setup_settings'], maybe_serialize($_POST) );

          echo 'success';
          exit();
      }

      //enabled/disabled
      if (isset( $_POST['optinrev_popup_enabled'] ))
      {
          optinrev_update( 'optinrev_popup_enabled', $_POST['optinrev_popup_enabled'] );
          exit();
      }
      //enabled/disabled
      if (isset( $_POST['optinrev_popup'] ) && $pop = strip_tags($_POST['optinrev_popup'])) {
          optinrev_update( 'optinrev_optinrevolution/optin1_enabled', $pop );
          exit();
      }

      //optinrev_show_where
      if (isset( $_POST['optinrev_show_where'] ) && $show_on = esc_html( $_POST['optinrev_show_where'] ))
      {
          optinrev_update( 'optinrev_show_where', $show_on );
          exit();
      }

      //showing popup
      if (isset( $_POST['optinrev_show_popup'] ) && $setp = esc_html($_POST['optinrev_show_popup'])) {

          $setp_ar = explode( '|',  $setp );
          $setv = $setp;

          if ( count($setp_ar) > 0 ) {
               if ( $setp_ar[0] == 'show_once_in' )
               {
                  $et = strtotime( '+' . $setp_ar[1] . ' day' );
                  $setv = $setv. '|' . time() .'|'. $et;
               }
          }

          optinrev_update( 'optinrev_show_popup', $setv );

          if ( function_exists('wp_cache_clear_cache') ) {
              wp_cache_clear_cache();
          }

          exit();
      }
      //optinrev_pixel_tracking
      if (isset( $_POST['optinrev_pixel_tracking'] )) {
          optinrev_update( 'optinrev_pixel_tracking', esc_html($_POST['optinrev_pixel_tracking']) );
          exit();
      }

      //delete img
      if ( isset( $_POST['optinrev_remove_img'] ) && $img = esc_html($_POST['optinrev_remove_img']))
      {
         optinrev_delete( $img );
         echo json_encode( array('action' => 'success') );
         exit();
      }

      //add images
      if ( isset( $_POST['optinrev_add_image'] ) && $add_img = esc_html($_POST['optinrev_add_image']) )
      {
          //images from wp/content/uploads
          $img_id = explode( '_', $add_img );
          $img_id = $img_id[2];

          $add_img_id = $_POST['optinrev_curr_page'] . '_img_uid_' . optinrev_unique_id() . '_' . $img_id;

          $img = optinrev_get_media( $img_id );
          $imgurl = parse_url( $img->guid );

          optinrev_update( $add_img, basename( $imgurl['path'] ) .'|'. $add_img );
          echo json_encode( array('action' => 'success', 'image' => 'http://' . $_SERVER['HTTP_HOST'] . $imgurl['path'] ) );
          exit();
      }

      //reset
      if ( isset( $_POST['optinrev_popup_reset'] ) && intval( $_POST['optinrev_popup_reset'] ) ) {

          optinrev_update( 'optinrevolution/optin1', optinrev_get( 'optinrev_default' ) );
          optinrev_update( 'optinrev_active_action_button', 'get_access2.png' );

          $tb_options = $wpdb->prefix . 'optinrev';
          $wpdb->query( $wpdb->prepare("delete from $tb_options where name like %s", '%_img_uid_%' ) );
          $wpdb->query( $wpdb->prepare("delete from $tb_options where name like %s", 'stage_img_%' ) );
          $wpdb->query( $wpdb->prepare("delete from $tb_options where name like %s", 'action_button_%' ) );
          exit('success');

      }

      //get the validator
      if ( isset( $_POST['optinrev_mce_validator'] ) && $page = esc_html($_POST['optinrev_mce_validator']) ) {
          $p = unserialize(optinrev_get( $page ));
          echo json_encode($p['optinrev_input_validator']);
          exit();
      }

      //mail provider form
      if ( isset( $_POST['optinrev_mail_webform'] ) && $cur_page = esc_html($_POST['optinrev_mail_webform']) ) {
          //optin setup
          $optin = unserialize( optinrev_get( $cur_page ) );

          if ( $optin )
          {
              //providers
              $prov = $_POST['optinrev_mail_provider'];
              $mpro = unserialize( optinrev_get( 'optinrev_mail_providers' ) );
              $mdta = explode( ',', $mpro[ $prov ]['input'] );
              //input text
              $inputs = (isset($optin['optinrev_email_form'][ $prov ]))?$optin['optinrev_email_form'][ $prov ]:null;
              $inputs_enabled = (isset($optin['optinrev_input_enabled']))?$optin['optinrev_input_enabled']:'';

              $htm = '';
              foreach( $mdta as $v )
              {
                $fable = (isset($inputs_enabled[ $v ]))?$inputs_enabled[ $v ] : false;

                $vl = ( isset( $inputs[ $v ] ) ) ? $inputs[ $v ] : '';
                
                $lbl = ucwords( str_replace( '_', ' ', $v ) );                
                
                //Input label
                $lbl = str_replace( 'Fields Email', 'Email', $lbl );

                $reqvalid = $req = $autotxt = $ismchimp = '';
                
                $value_email = array( 'email', 'fields_email' );                
                $valid_field = array( 'email', 'fields_email');
                
                if ( in_array( $v, $valid_field ) )
                {
                  $req = ( isset($optin['validate'][$v]) ) ? 'checked' : '';                  
                  $reqvalid = 'Validate&nbsp;<input type="checkbox" name="validate['.$v.']" value="1" '.$req.'/>';                  
                }

                if ( $prov == 'mailchimp' ){
                if ( $lbl == 'Mcaction' )
                $ismchimp = '<div class="row"><label>&nbsp;</label><span class="note">Example Value: <b>mailchimp.us1.list-manage.com</b> ( Replace with your url with your action value information )</span></div>';

                $vl = str_replace( 'http://', '', $vl );
                $lbl = ucfirst(str_replace('Mc', '', $lbl));
                }              
                
                $name_sel = '';                
                $txt = '';
                
                if ( in_array( $v, $value_email ) ) {
                if ( empty( $vl ) ) $vl = 'Enter Your Email...';
                $txt = 'onfocus="wtfn.intips(this, \''. $vl .'\',1);" onblur="wtfn.intips(this, \''. $vl .'\',0);"';
                }
                
                //if has an 'id'
                $lbl = str_replace(' Id', ' ID', $lbl);                

                $htm .= '<div class="row"><label>'.$lbl.'</label><input type="text" name="optinrev_email_form['. $_POST['optinrev_mail_provider'] .']['.$v.']" '.$txt.' value="'.$vl.'" size="40">'.$ismchimp.'&nbsp;'. $reqvalid .'&nbsp;'.$name_sel.'</div>';

                }

              echo $htm;
          }
          exit();
      }
      //Member verification
      if ( isset( $_POST['authenticate'] ) && $user = esc_html($_POST['authenticate']) )
      {
          include_once( ABSPATH . 'wp-includes/class-IXR.php' );

          parse_str( str_replace('amp;','', $user), $post );

          $res = 'invalid_user';
          
          if ( empty($post['amember_email']) ) { $res = 'invalid_user'; exit(); }
          if ( !strpos( $post['amember_email'], '@' ) ) { $res = 'invalid_user'; exit(); }

          $client = new IXR_Client( OPTINREV_XMLRPC_URL );

          if( $client->query( 'proplug.is_user_authorized', $post['amember_email'], optinrev_get_host() ) ) {
          $res = $client->getResponse();
          } else {
          $res = 2;
          }

          $res = ( $res == 1 ) ? 'authorized' : $res;
          $res = ( $res == 2 ) ? 'invalid_member' : $res;
          $res = ( $res == 3 ) ? 'invalid_login' : $res;

          exit( sprintf('%s',$res) );
      }
      //Save the info
      if ( isset( $_POST['pro_authorized'] ) && $pro = esc_html($_POST['pro_authorized']) )
      {
          parse_str( str_replace('amp;','', $pro), $post );
          optinrev_update( 'optinrev_pro_authorized', serialize($post) );
          echo 'valid';
          exit;
      }

      //set autosave
      if ( isset( $_POST['optinrev_autosave'] ) && $autosave = esc_html($_POST['optinrev_autosave']) )
      {
          optinrev_update( 'optinrev_autosave', $autosave );
          exit();
      }
      //set poweredby
      if ( isset( $_POST['optinrev_poweredby'] ) && $poweredby = esc_html($_POST['optinrev_poweredby']) )
      {
          optinrev_update( 'optinrev_poweredby', $poweredby );
          exit();
      }

      if ( isset( $_POST['optinrev_showmobile'] ) && $showmobile = esc_html($_POST['optinrev_showmobile']) )
      {
          optinrev_update( 'optinrev_showmobile', $showmobile );
          exit();
      }

      //optinrev_add_image_briefcase
      if ( isset( $_POST['optinrev_add_image_briefcase'] ) && $img = esc_html($_POST['optinrev_add_image_briefcase']) )
      {
          $img_id = esc_html($_POST['optinrev_curr_page']) . '_images_' . optinrev_unique_id();
          optinrev_update( $img_id, $img );
          exit();
      }

      //optinrev_del_image_briefcase
      if ( isset( $_POST['optinrev_del_image_briefcase'] ) && $img = esc_html($_POST['optinrev_del_image_briefcase']) )
      {
          $img_id = esc_html($_POST['optinrev_curr_page']) . '_delete_images_' . optinrev_unique_id();
          optinrev_update( $img_id, $img );
          exit();
      }

      //optinrev_add_action button_briefcase
      if ( isset( $_POST['optinrev_add_button_briefcase'] ) && $img = esc_html($_POST['optinrev_add_button_briefcase']) )
      {
          optinrev_update( 'optinrev_add_button_briefcase', $img );
          exit();
      }

      //remove an image to the stage
      if ( isset( $_POST['optinrev_remove_object'] ) && $img_id = esc_html( $_POST['optinrev_remove_object'] ) ) {
          optinrev_delete( $img_id );
          exit();
      }

      //getimage size
      if ( isset( $_POST['optinrev_getimagesize'] ) && $img = esc_html( $_POST['optinrev_getimagesize'] ) ) {
          if ( list( $width, $height ) = @getimagesize( $_SERVER['DOCUMENT_ROOT'] . $img ) ) {
              $imgd = array( 'width' => $width, 'height' => $height );
              echo json_encode($imgd);
              } else {
              echo '0';
          }
          exit();
      }
      
      //reset action button
      if ( isset( $_POST['optinrev_reset_action_button'] ) ) {
          optinrev_update( 'optinrev_active_action_button', 'get_access2.png' );
          exit();
      }
      
      //Email form reset
      if ( isset( $_POST['optinrev_emailform_reset'] ) && intval( $_POST['optinrev_emailform_reset'] ) ) {
          $optin = maybe_unserialize(optinrev_get( 'optinrevolution/optin1' ));
          $optin['optinrev_optin_aweber'] = null; 
          $optin['optinrev_optin_icontact'] = null; 
          $optin['optinrev_optin_getresponse'] = null; 
          $optin['optinrev_optin_mailchimp'] = null; 
          $optin['optinrev_optin_constantcontact'] = null; 
          $optin['wysija_list_id'] = null;    
          $optin['optinrev_femail_validate'] = null;    
          $optin['optinrev_foptin_active'] = null;
          $optin['optinrev_foptin_form_active'] = null;          
          optinrev_update( 'optinrevolution/optin1', serialize($optin) );          
          exit();
      }
      //Factory Reset
      if ( isset( $_POST['optinrev_factory_reset'] ) && intval( $_POST['optinrev_factory_reset'] ) ) {          
          
          optinrev_update( 'optinrevolution/optin1', optinrev_get( 'optinrev_default' ) );
          optinrev_update( 'optinrev_active_action_button', 'get_access2.png' );

          $tb_options = $wpdb->prefix . 'optinrev';
          $wpdb->query( $wpdb->prepare("delete from $tb_options where name like %s", '%_img_uid_%' ) );
          $wpdb->query( $wpdb->prepare("delete from $tb_options where name like %s", 'stage_img_%' ) );
          $wpdb->query( $wpdb->prepare("delete from $tb_options where name like %s", 'action_button_%' ) );
                    
          exit();
      }
      
      
      
    }}//end action callback
  
  if ( !function_exists( 'optinrev_footer' ) ) {
      function optinrev_footer() {
      echo '<div id="optinrev-footer"><div id="footer-left" class="alignleft">Optin Revolution Lite <a target="_blank" href="http://wordpress.org/support/plugin/optin-revolution/">Support</a>&nbsp;|&nbsp;<a target="_blank" href="https://www.optinrevolution.com/?utm_source=plugin&utm_medium=footer&utm_campaign=upgrade" title="Upgrade Now">Upgrade</a>
      &nbsp;|&nbsp;Add your&nbsp;<a target="_blank" href="http://wordpress.org/support/view/plugin-reviews/optin-revolution?filter=5#postform">&#9733;&#9733;&#9733;&#9733;&#9733;</a> on
      &nbsp;<a target="_blank" href="http://wordpress.org/plugins/optin-revolution/">wordpress.org</a> and keep this plugin essentially free.</div></div>';
      }
  }

  if ( !function_exists( 'optinrev_popup_css' ) ) {
      function optinrev_popup_css( $is_view = NULL ) {

$optin = optinrev_get( 'optinrevolution/optin1' );

if ( empty($optin) ) return false;

$dir = OPTINREV_DIR;

if ( $optin ) {
   $optin = unserialize( $optin );
  } else {
  //load default
  $optin = array(
    'optinrev_round_border' => '',
    'optinrev_wbg_color' => '#FFFFFF',
    'optinrev_wbg_opacity' => 0,
    'optinrev_border_opacity' => 0,
    'optinrev_pwbg_color' => '#FFFFFF',
    'optinrev_border_color' => '#000000',
    'optinrev_border_radius' => 0,
    'optinrev_border_thickness' => 1,
    'optinrev_top_margin' => 50,
    'optinrev_wwidth' => 700,
    'optinrev_hheight' => 400,
    'optinrev_delay' => 0,
    'optinrev_close_popup_image' => 'close1',
    'optinrev_close_button' => 'top:230px;right:230px;',
    'optinrev_link_color' => '#1122CC',
    'optinrev_link_underline' => ''
   );
}

$pdim = array(
 'width' => $optin['optinrev_wwidth'],
 'height' => $optin['optinrev_hheight']
);

//dynamic close button
$ww = $pdim['width'];
$wh = $pdim['height'];
$bw = $optin['optinrev_border_thickness'];
//goto website button
$loc = ( isset($optin['optinrev_gotowebsite']) && $optin['optinrev_gotowebsite'] == 'bottom' ) ? ( $wh - 56 ) : 20;

$close_btn = array(
  'close1' => array( ($ww - ( 30 / 2 ) ) + $bw, -( 30/2 ) - $bw ),
  'close2' => array( ($ww - ( 45 / 2 ) ) + $bw, -( 45/2 ) - $bw ),
  'close3' => array( ($ww - ( 60 / 2 ) ) + $bw, -( 60/2 ) - $bw ),
  'close4' => array( ($ww - ( 30 / 2 ) ) + $bw, -( 30/2 ) - $bw ),
  'close5' => array( ($ww - ( 45 / 2 ) ) + $bw, -( 45/2 ) - $bw ),
  'close6' => array( ($ww - ( 60 / 2 ) ) + $bw, -( 60/2 ) - $bw ),
  'close7' => array( $ww - 272, $loc ),
  'close8' => array( $ww - 272, $loc ),
  );

$lpos = $close_btn[ $optin['optinrev_close_popup_image'] ];
$close_btn = 'left:'. $lpos[0] .'px;top:'. $lpos[1] .'px;';

$top_margin = ( !$is_view ) ? 'margin-top:45px !important;' : '';
$li_padding = ( $is_view ) ? 'line-height: 35px !important;' : '';

//IE only
$border_color = (optinrev_is_ie()) ? $optin['optinrev_border_color'] : 'rgba('. implode(',',hex2dec($optin['optinrev_border_color'])) .','. ($optin['optinrev_border_opacity']/100) .')';
$htc = ( optinrev_is_ie() ) ? 'behavior: url('. $dir .'css/PIE.php);' : '';

$unrem_css = '
img.wotimg {border: 1px dashed #888 !important;}
.mceImageDrag {padding:0px !important;position:absolute;z-index:9999;}
#close {position:absolute;z-index:1001;text-decoration:none;border: 1px solid transparent;}
.mceImageSelect {position:relative;padding:0px !important;border: 1px dashed #888 !important;}
.mceWotlayer {border: 1px dashed #888 !important;z-index:999;padding:0px !important;margin:0px !important;}
';
if ( $is_view ) $unrem_css = '';

$view_cleaned = '
#zindex {position:absolute;width:50px;padding:0px;font-size:9px;font-weight:normal;background-color:#f0f0f0;color:#404040;padding:2px}
#imglabel {position:absolute;left:0px;bottom:0px;padding:0px;font-size:9px;font-weight:normal;background-color:red;color:#ffffff;padding:0px 2px 0px 2px;}
#mceWotmove {position:absolute;right:-1px;bottom:-1px;width:14px;height:14px; background: url('.$dir.'images/cursor_drag_arrow.png) no-repeat center center;cursor:move;background-color:#fff;z-index:99999;}
#mceDeleteObj {position:absolute;left:-1px;top:-12px;width:12px;height:12px; background: url('.$dir.'images/delete-ic.png) no-repeat center center;cursor:pointer;background-color:#fff;z-index:99999;}
';
if ( $is_view ) $view_cleaned = '';
//is rounded border
$optin['optinrev_border_radius'] = ( $optin['optinrev_round_border'] == 'on' ) ? $optin['optinrev_border_radius'] : '0';

$form_css = ( $is_view ) ? ( ( isset($optin['optinrev_email_form_css']) ) ? stripcslashes($optin['optinrev_email_form_css']) : '') : '';

$wm_hand = ( $is_view ) ? '#simplemodal-container #wm {cursor:pointer;}' : '';

$pwd_color = ( !$is_view ) ? 'black' : getContrast50( $optin['optinrev_wbg_color'] );

$cursor = ( optinrev_is_ie() ) ? 'cursor:crosshair;' : 'cursor: se-resize;';

//resizable
$resizable = '
.ui-icon { width: 18px; height: 18px; background-image: url('.$dir.'images/hgrip.png); }
.ui-icon-gripsmall-diagonal-se { background-repeat: no-repeat; background-position: left top;}
.ui-icon-gripsmall-diagonal-se:hover { background-repeat: no-repeat; background-position: left -18px;}
.ui-resizable-handle { position: absolute;font-size: 0.1px; z-index: 99999; display: block; '.$cursor.'}
.ui-resizable-disabled .ui-resizable-handle, .ui-resizable-autohide .ui-resizable-handle { display: none; }
.ui-resizable-se { '.$cursor.' width: 18px; height: 18px; right: -8px;bottom: -8px;}
';

if ( $is_view ) $resizable = '';

$lnk_under = 'none';
if ( isset( $optin['optinrev_link_underline'] ) )
$lnk_under = ( $optin['optinrev_link_underline'] == 'on' ) ? 'underline' : 'none';

$lnk_color = ( isset($optin['optinrev_link_color']) ) ? $optin['optinrev_link_color'] : '';

$mcebody = ( !$is_view ) ? '#tinymce {overflow:hidden !important;} #tinymce a:link, #tinymce a:visited, #tinymce a:hover, #tinymce a:active {color:'.$lnk_color.';text-decoration:'.$lnk_under.';}' : '';

//Close button
$active_close_button = '#simplemodal-container .close1 {background:url('.$dir.'images/close1b.png) no-repeat; width:39px; height:39px;'.$close_btn.'display:inline; z-index:3200; position:absolute;cursor:pointer;text-decoration:none;}';

//init btn
$popbtn = array(
             'close1' => array( '39px', 'close1b' ),
             'close2' => array( '52px', 'close2b' ),
             'close3' => array( '62px', 'close3b' ),
             'close4' => array( '39px', 'close1r' ),
             'close5' => array( '52px', 'close2r' ),
             'close6' => array( '62px', 'close3r' ),
             'close7' => array( '263px', 'btn1' ),
             'close8' => array( '263px', 'btn2' )
              );

if ( $is_view ) {
if ( isset( $optin['optinrev_close_popup_image'] ) && $sbtn = $optin['optinrev_close_popup_image'] ) {

    if ( in_array( $sbtn, array_keys( $popbtn ) ) )
    {
        $pbtn = $popbtn[ $sbtn ];
        $w = $pbtn[0];
        $h = ( $sbtn == 'close7' || $sbtn == 'close8' ) ? '47px' : $pbtn[0];

        $active_close_button = sprintf( '#simplemodal-container .%s {background:url(%simages/%s.png) no-repeat;
                                        width:%s; height:%s;'.$close_btn.'display:inline; z-index:3200;
                                        position:absolute;cursor:pointer;text-decoration:none;}',
                                        $sbtn, $dir, $pbtn[1], $w, $h  );
    }

}} else {

    foreach( $popbtn as $sbtn => $v )
    {

        $pbtn = $popbtn[ $sbtn ];
        $w = $pbtn[0];
        $h = ( $sbtn == 'close7' || $sbtn == 'close8' ) ? '47px' : $pbtn[0];

        $active_close_button .= sprintf( '#simplemodal-container .%s {background:url(%simages/%s.png) no-repeat;
                                        width:%s; height:%s;'.$close_btn.'display:inline; z-index:3200;
                                        position:absolute;cursor:pointer;text-decoration:none;}',
                                        $sbtn, $dir, $pbtn[1], $w, $h  );

    }

}

$line_height = ( optinrev_is_ie() ) ? '130%' : '110%';
$round_border = ( optinrev_is_ie() && optinrev_ie_version() < 9 ) ? 'border-radius: '.$optin['optinrev_border_radius'].'px;' : '-moz-border-radius: '.$optin['optinrev_border_radius'].'px;-webkit-border-radius: '.$optin['optinrev_border_radius'].'px;border-radius: '.$optin['optinrev_border_radius'].'px;-khtml-border-radius:'.$optin['optinrev_border_radius'].'px;';

//Wait box
$wait_box = 'background-color: #81FF21;color:#000000;font-size:12px !important;border: 1px solid #000000;font-style: normal !important;padding: 6px 10px 6px 10px !important;';

ob_start("ob_gzhandler");
header('Content-Type: text/css; charset=utf-8');
header("cache-control: must-revalidate");
$offset = 60 * 7200;
$expire = "expires: " . gmdate ("D, d M Y H:i:s", time() + $offset) . " GMT";
header($expire);

$modal = <<<LOAD_CSS
#simplemodal-overlay {background-color:{$optin['optinrev_wbg_color']} !important;z-index: 9999 !important;}
#simplemodal-container {box-sizing: content-box !important;-moz-box-sizing: content-box !important;-webkit-box-sizing: content-box !important;}
#simplemodal-container {position:fixed;{$top_margin}height:{$pdim['height']}px;width:{$pdim['width']}px;background-color:{$optin['optinrev_pwbg_color']};border:{$optin['optinrev_border_thickness']}px solid {$border_color};{$round_border}{$htc}z-index:9999 !important;text-shadow: none !important;}
#simplemodal-container img {padding:0px;border:none;-webkit-appearance: none;-webkit-touch-callout: none;-webkit-user-select: none;-khtml-user-select: none;-moz-user-select: none;-ms-user-select: none;user-select: none;}
#simplemodal-container .simplemodal-data a:link,
#simplemodal-container .simplemodal-data a:visited,
#simplemodal-container .simplemodal-data a:hover
#simplemodal-container .simplemodal-data a:active {color:{$lnk_color};text-decoration:{$lnk_under};}
#simplemodal-container .simplemodal-data span {line-height:{$line_height};margin:0px;padding:0px;}
#simplemodal-container .simplemodal-data ol,
#simplemodal-container .simplemodal-data ul {padding:0px;margin:0px;}
#simplemodal-container .simplemodal-data ol li {list-style: decimal inside !important;{$li_padding}}
#simplemodal-container .simplemodal-data ul li {list-style: disc inside !important;{$li_padding}}
#simplemodal-container .simplemodal-data {font-family: arial !important;font-size: 12px;}
#simplemodal-container .simplemodal-data input[type="text"]{text-transform:none !important;}
{$active_close_button}
{$unrem_css}
{$wm_hand}
{$view_cleaned}
{$resizable}
{$mcebody}
#simplemodal-container #wm {cursor:pointer;border: none !important;text-decoration:none !important;}
#waitbox {position:absolute;left:4px;top:4px;{$wait_box}z-index:99999;}
.wp_themeSkin .mceButtonDisabled .mceIcon {opacity:0.5 !important; filter:alpha(opacity=50) !important;}
#no_thanks_btn {cursor:pointer;display:none;position:absolute;width: 263px; height: 47px;background: url({$dir}images/no-thanks.png) no-repeat left top;z-index:999999;}
#poweredby {position:absolute;color: {$pwd_color} !important;text-decoration:none !important;z-index: 9999}
#poweredby a {cursor:pointer;color: {$pwd_color} !important;text-decoration:none !important;}
#poweredby a:hover {text-decoration:underline;}
LOAD_CSS;
echo optinrev_cssminify($modal);

      }}

 if ( !function_exists('optinrev_popup_vars') ) {

  function optinrev_popup_vars( $popup ) {

  global $wp_version, $optinrev_play;

  if ( !intval($popup) ) return false;

  ob_start("ob_gzhandler");
  header('Content-type: text/javascript; charset=utf-8');
  header("cache-control: must-revalidate");
  $offset = 60 * 7200;
  $expire = "expires: " . gmdate ("D, d M Y H:i:s", time() + $offset) . " GMT";
  header($expire);

  $dir = OPTINREV_DIR;

  $optin = optinrev_get( 'optinrevolution/optin' . $popup );
  $optin = unserialize( $optin );

  $content = preg_replace("/'/","\'",preg_replace('/\s+/', ' ',stripcslashes($optin['optinrev_data'])));

  $modal_delay = (isset($optin['optinrev_delay']))?$optin['optinrev_delay']:0;

  $round_corner = (isset($optin['optinrev_border_radius']))?$optin['optinrev_border_radius']:0;

  $validate = '{}';
  if ( isset( $optin['optinrev_femail_validate'] ) && $optin['optinrev_femail_validate'] == 'on' ) {
  $validate = '{email:1}';
  }

  //Wysija
  $wysija_msg = '';
  $wysija_id = '';
  if ( isset($optin['wysija_list_id']) && $wysija_list_id = $optin['wysija_list_id'] )
  {
    $wysija_msg = 'You\'ve successfully subscribed. Check your inbox now to confirm your subscription.';
    $wysija_id = $wysija_list_id;
  }

  $optinrev_visited_once = 0;

  $show_time = optinrev_get('optinrev_show_popup');
  $ts = explode( '|', $show_time );
  $show_time = $ts[0];

  if ( $ts[0] == 'show_once_in' ) {
  $optinrev_visited_once = $ts[1];
  }

  $optinrev_ctcurl = ( isset($optin['optinrev_ctcurl']) && $optin['optinrev_foptin_active']=='constantcontact') ? $optin['optinrev_ctcurl'] : '';

  echo optinrev_jscompress('/* <![CDATA[ */var site_url="'. home_url('/') .'",purl="'. $dir .'", _0a38277f56e2d868e6088d7345f218f7 = document.createElement(\'div\'), ch = jQuery(window).height(), exh = 30, _0a38277f56e2d868e6088d7345f218f7 = jQuery(_0a38277f56e2d868e6088d7345f218f7).html(\''. $content .'\'), tshow = "'. $optinrev_play .'", isvalid = '. $validate .', mail_form_name = \''.$optin['optinrev_foptin_active'].'\',optinrev_close_button_class = \''. $optin['optinrev_close_button_class'].'\', optinrev_top_margin = '.$optin['optinrev_top_margin'].',optinrev_wbg_opacity = '.$optin['optinrev_wbg_opacity'].', modal_delay = '. $modal_delay .', box_started = 0, rnd='.$round_corner.',optinrev_visited_once='.$optinrev_visited_once.', optinrev_show_time="'. $show_time .'", optinrev_isie="'. optinrev_ie_version() .'", optinrev_wysija_id="'. $wysija_id .'" ,optinrev_wysija_msg="'. $wysija_msg .'", optinrev_ctcurl="'. $optinrev_ctcurl .'";/* ]]> */');
  }}//optinrev vars

  //Wysija
  if ( !function_exists('optinrev_wysija') ) {
  function optinrev_wysija( $wy_id, $email ) {

  if ( !defined('WYSIJA') ) return false;
  if ( !isset( $wy_id ) ) return false;
  if ( $wy_id = esc_html( $wy_id ) )
  {
      $data=array(
        'user' => array(
          'email'=> esc_html( $email ),
          'firstname' => 'Firstname',
          'lastname' => 'Lastname'
        ),
        'user_list' => array('list_ids'=> array( $wy_id ))
      );

      $userHelper = WYSIJA::get('user','helper');
      $userHelper->addSubscriber($data);
  }
  return false;
  }}//wysija

  if ( !function_exists('optinrev_ccform') ) {
  function optinrev_ccform() {
  if ( !isset( $_POST['url'] ) ) return false;

  $share_url = strip_tags($_POST['url']);

  if ( !strstr( $share_url, 'constantcontact.com' ) ) return false;

  $curl = curl_init();
  curl_setopt_array($curl, array(
      CURLOPT_RETURNTRANSFER => 1,
      CURLOPT_URL => $share_url,
      CURLOPT_USERAGENT => $_SERVER['HTTP_USER_AGENT'],
      CURLOPT_FOLLOWLOCATION => 1
  ));

  $res = curl_exec($curl);
  curl_close($curl);

  if ( $res ) {

  $res = preg_replace('/\s+/',' ', $res);
  $res = preg_replace('/\n\r/','', $res);
  $res = preg_replace('/<div[^>]+>|<\/div>|<span[^>]+>|<\/span>|<span>|<fieldset>|<\/fieldset>/','', $res);
  //$res = preg_replace('/<p>.*<\/p>/','', $res);
  $res = preg_replace('/\/manage\/optin\?/','http://visitor.r20.constantcontact.com/manage/optin?', $res);

  preg_match('/<form[^>]+>.*<\/form>/i', $res, $m);

  echo ( count($m) > 0 ) ? $m[0] : '0';

  } else echo '0';

  }}

  if ( !function_exists('optinrev_jscompress') ) {
  function optinrev_jscompress( $buffer ) {
        /* remove comments */
        $buffer = preg_replace('!/\*[^*]*\*+([^/][^*]*\*+)*/!', '', $buffer);
        /* remove tabs, spaces, newlines, etc. */
        $buffer = str_replace(array("\r\n","\r","\n","\t",'  ','    ','     '), '', $buffer);
        /* remove other spaces before/after ; */
        $buffer = preg_replace(array('(( )+{)','({( )+)'), '{', $buffer);
        $buffer = preg_replace(array('(( )+})','(}( )+)','(;( )*})'), '}', $buffer);
        $buffer = preg_replace(array('(;( )+)','(( )+;)'), ';', $buffer);
        return $buffer;
  }}
?>
