<?php
  //require_once( '../../../../../../../wp-load.php' );

  $mce = OPTINREV_DIR . 'js/tiny_mce/';
  $jqy = includes_url() . 'js/jquery/';
  header('Content-Type: text/html; charset=' . get_bloginfo('charset'));

  $css_url = site_url( '?optinrev-popup=1' ) . '&t=' .  optinrev_cid();
?>
<!DOCTYPE html>
<html xmlns="http://www.w3.org/1999/xhtml" <?php language_attributes(); ?>>
<head>
<meta http-equiv="Content-Type" content="<?php bloginfo('html_type'); ?>; charset=<?php echo get_option('blog_charset'); ?>" />
<title>Text Editing and Formatting</title>
  <meta name="robots" content="noindex,nofollow,noarchive" />
  <link href="<?php echo OPTINREV_TINYMCE_PLUGINS . '/textedit/'?>css/template.css" rel="stylesheet" type="text/css" />
  <script type="text/javascript" src="<?php echo $mce;?>tiny_mce_popup.js?t=<?php echo optinrev_cid();?>"></script>
  <script type="text/javascript" src="<?php echo $mce;?>tiny_mce.js?t=<?php echo optinrev_cid();?>"></script>
  <script type="text/javascript" src="<?php echo $jqy;?>jquery.js?t=<?php echo optinrev_cid();?>"></script>
  <script type="text/javascript" src="<?php echo OPTINREV_TINYMCE_PLUGINS . '/textedit/'?>js/textedit.js?t=<?php echo optinrev_cid();?>"></script>
  <script type="text/javascript">
    tinyMCE.init({ theme: "advanced", mode : "none", elements: 'mce_textedit', plugins: 'paste,lineheight', theme_advanced_buttons1 : "fontselect,fontsizeselect,forecolor,backcolor,|,lineheight,|,bold,italic,underline,separator,strikethrough,bullist,numlist,undo,redo,link,unlink",theme_advanced_buttons2 : "",theme_advanced_buttons3 : "",theme_advanced_toolbar_location : "top",theme_advanced_toolbar_align : "left",theme_advanced_path : false,invalid_elements : 'p',cleanup : true,force_br_newlines : true,force_p_newlines : false,forced_root_block : '',remove_redundant_brs : false,remove_linebreaks : true,theme_advanced_font_sizes : "8pt,10pt,12pt,14pt,18pt,24pt,30pt,36pt,48pt,60pt,72pt",content_css : "<?php echo $css_url;?>", paste_text_sticky : true, setup : function( ed ) {ed.onInit.add( function(ed, e) { ed.pasteAsPlainText = true; ed.dom.hide('mceDeleteObj');});}});
    jQuery(document).ready(function($){
      //loading
      setTimeout(function(){tinyMCE.execCommand( 'mceAddControl', false, 'mce_textedit' );}, 500);
      setTimeout(function(){$('#loading, #clearb').hide();jQuery('.mceActionPanel').show();}, 1000);
      setTimeout(function(){$('.scn').hide();}, 1500);
    });
	</script>
</head>
<body>
  <div class="scn"></div>
	<form onsubmit="TextEditDialog.insert();return false;">
    <div>
    <div class="spin" id="loading"><em>Editor Loading</em></div><div id="clearb" style="clear:both;"></div>
    <textarea id="mce_textedit" name="mce_textedit" cols="118" rows="16"></textarea>
    </div>
		<div class="mceActionPanel" style="display:none;">
			<input type="submit" id="insert" name="apply" value="Update" />
			<input type="button" id="cancel" name="cancel" value="Cancel" onclick="tinyMCEPopup.close();" />
		</div>
	</form>
</body>
</html>
