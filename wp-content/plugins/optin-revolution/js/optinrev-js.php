<?php

  if ( !defined( 'ABSPATH' ) ) die('');//keep silent
  if ( !defined( 'OPTINREV_LITE' ) ) die();

  header('Content-type: text/javascript');

 if ( isset($_GET['optinrev-edit-init-js']) && $plugin_page = esc_html( $_GET['optinrev-edit-init-js'] ) )
 {

  $dir = OPTINREV_DIR;

  //$optin = optinrev_popups();
  //mail providers
  $mailpro = json_encode( unserialize(optinrev_get('optinrev_mail_providers')) );
  //is autosave
  $autosave = optinrev_get('optinrev_autosave');
  $poweredby = optinrev_get('optinrev_poweredby');

  if ( $optin = optinrev_get( $plugin_page ) ) {
  $_POST = maybe_unserialize( $optin );
  }

  //mail provider set
  $mail_form_name = (isset($_POST['optinrev_foptin_active']))?trim($_POST['optinrev_foptin_active']):'aweber';

  $optinrev_ctcurl = ( isset($_POST['optinrev_ctcurl']) && $_POST['optinrev_foptin_active']=='constantcontact') ? $_POST['optinrev_ctcurl'] : '';

  //briefcase images - it will insert to the canvas
  $is_bfcase = 0;
  if ( $imgs = optinrev_get_image( $plugin_page )) {

      $is_bfcase = array();
      foreach( $imgs as $v ) {
        $is_bfcase[] = $v->content;
        optinrev_delete( $v->name );
      }
      $is_bfcase = json_encode($is_bfcase);
  }

  $is_delbfcase = 0;
  if ( $imgs = optinrev_delete_image( $plugin_page )) {
      $is_delbfcase = array();
      foreach( $imgs as $v ) {
        $is_delbfcase[] = $v->content;
        optinrev_delete( $v->name );
      }
      $is_delbfcase = json_encode($is_delbfcase);
  }

  //briefcase button - it will insert to the canvas
  $is_actionbtn = 0;
  if ( $case_btn = optinrev_get('optinrev_add_button_briefcase') )
  {
      $is_actionbtn = $case_btn;
      optinrev_update( 'optinrev_active_action_button', $is_actionbtn );
      optinrev_delete( 'optinrev_add_button_briefcase' );
  }

  $is_upload = optinrev_get( 'optinrev_upload' );

?>
/* <![CDATA[ */var j = jQuery.noConflict(),doc_base_url='<?php echo home_url('/');?>',wtp = '<?php echo $dir;?>', defs = {'width': 900, 'height': 600}, jpdata = [], wtpage = '<?php echo $plugin_page;?>', vldator = <?php echo (isset($_POST['optinrev_input_validator']))?json_encode($_POST['optinrev_input_validator']):'{}';?>, apc = false, is_editing = false, redraw=0, isvalid = <?php echo (isset($_POST['validate']))?json_encode($_POST['validate']):'{}';?>, get_close_btn='', is_autosave = '<?php echo $autosave;?>', is_poweredby = '<?php echo $poweredby?>', is_bfcase = '<?php echo $is_bfcase;?>', is_delbfcase = '<?php echo $is_delbfcase;?>', is_actionbtn = '<?php echo $is_actionbtn;?>', mail_form_name = '<?php echo $mail_form_name;?>',optinrev_wbg_opacity = '<?php echo (optinrev_post('optinrev_wbg_opacity', true)) ? optinrev_post('optinrev_wbg_opacity', true) : 0;?>', optinrev_wbg_color = '<?php echo (optinrev_post('optinrev_wbg_color', true)) ? optinrev_post('optinrev_wbg_color', true) : '#ffffff';?>',optinrev_border_opacity = '<?php echo (optinrev_post('optinrev_border_opacity', true)) ? optinrev_post('optinrev_border_opacity', true) : 0;?>',optinrev_border_radius = '<?php echo (optinrev_post('optinrev_border_radius', true)) ? optinrev_post('optinrev_border_radius', true) : 0;?>',optinrev_border_thickness = '<?php echo (optinrev_post('optinrev_border_thickness', true)) ? optinrev_post('optinrev_border_thickness', true) : 1;?>',optinrev_top_margin = '<?php echo (optinrev_post('optinrev_top_margin', true)) ? optinrev_post('optinrev_top_margin', true) : 0;?>',optinrev_wwidth = '<?php echo (optinrev_post('optinrev_wwidth', true)) ? optinrev_post('optinrev_wwidth', true) : 900;?>',optinrev_hheight = '<?php echo (optinrev_post('optinrev_hheight', true)) ? optinrev_post('optinrev_hheight', true) : 600;?>',optinrev_delay = '<?php echo (optinrev_post('optinrev_delay', true)) ? optinrev_post('optinrev_delay', true) : 0;?>',optinrev_inputh = '<?php echo (optinrev_post('optinrev_inputh', true)) ? optinrev_post('optinrev_inputh', true) : 50;?>',optinrev_inputw = '<?php echo (optinrev_post('optinrev_inputw', true)) ? optinrev_post('optinrev_inputw', true) : 160;?>',optinrev_inputbt = '<?php echo (optinrev_post('optinrev_inputbt', true)) ? optinrev_post('optinrev_inputbt', true) : 1;?>',optinrev_inputfz = '<?php echo (optinrev_post('optinrev_inputfz', true)) ? optinrev_post('optinrev_inputfz', true) : 12;?>',mailpro = <?php echo $mailpro;?>, is_upload = '<?php echo $is_upload;?>', optinrev_link_color = '<?php echo (optinrev_post('optinrev_link_color', true)) ? optinrev_post('optinrev_link_color', true) : '#1122CC';?>', optinrev_link_underline = '<?php echo (optinrev_post('optinrev_link_underline', true) == 'on') ? optinrev_post('optinrev_link_underline', true) : '';?>', optinrev_ctcurl='<?php echo $optinrev_ctcurl;?>';/* ]]> */
<?php } else die();?>