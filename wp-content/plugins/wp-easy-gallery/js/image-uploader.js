// WP Easy Gallery 3.5
// http://labs.hahncreativegroup.com/wordpress-plugins/easy-gallery/

jQuery(document).ready(function() {

jQuery('#upload_image_button').click(function() { 
 formfield = jQuery('#upload_image').attr('name');
 tb_show('', 'media-upload.php?type=image&amp;TB_iframe=true');
 return false;
});

window.send_to_editor = function(html) {
 imgurl = jQuery('img', html).attr('src');
 jQuery('#upload_image').val(imgurl);
 tb_remove();
}

});