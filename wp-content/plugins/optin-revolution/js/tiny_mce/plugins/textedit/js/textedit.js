tinyMCEPopup.requireLangPack();
var TextEditDialog = {
  bullets_get_color: function( ul ) {
  return jQuery('li strong, li span', ul).css('color');
  },
  bullets_edit: function(cc) {
  var mn = jQuery(cc), c;

  jQuery('ul,ol', mn).each(function(i, el){
  c = TextEditDialog.bullets_get_color(el);
  jQuery('li',el).css( 'color', c );
  });

  },
	init : function() {
		var ed = tinyMCEPopup.editor;    
    jQuery('#mce_textedit').css({ 'width':'100%', 'height':'440px' }).val( jQuery('.mceWotlayer',ed.getDoc()).html() );
    jQuery('.scn').css({ 'width': jQuery(window).width() - 1, 'height': jQuery(window).height() - 7 });    
	},  
 	insert : function() {       
    var ed = tinyMCEPopup.editor, pcont = tinyMCE.activeEditor.getContent(), lh = '0';
    pcont = ( pcont.indexOf('span') != -1 ) ? pcont : '<span>'+pcont+'</span><div id="mceDeleteObj"></div>';
    
    jQuery('span', tinyMCE.activeEditor.getBody()).each(function(i, e){
        lh = jQuery( e ).css('line-height');
        styl = jQuery( e ).attr('style');    
        
        if ( typeof styl !== 'undefined' ) {
        styl = styl.replace(/line-height:.*?;/g, 'line-height:' + lh + ' !important;' );
        }
        
        jQuery(e).attr( 'style', styl );
    });
    
    ed.dom.setHTML( 'mceWotlayer', pcont.replace(/line-height:.*?;/g, 'line-height:' + lh + ' !important;' ) );
    jQuery( ed.dom.get('mceWotlayer') ).css('line-height', lh + ' !important');

    jQuery('a', ed.getDoc()).click(function(e){ e.preventDefault();return false;});

    this.bullets_edit( ed.getDoc() );
    
    ed.dom.show('mceDeleteObj');
		tinyMCEPopup.close();
	}  
};
tinyMCEPopup.onInit.add(TextEditDialog.init, TextEditDialog);
