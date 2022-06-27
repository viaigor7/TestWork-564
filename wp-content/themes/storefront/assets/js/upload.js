jQuery(document).ready(function() {  
    jQuery('#post_background_button').click(function() {  
         formfield = jQuery('#post_background').attr('name');  
         tb_show('', 'media-upload.php?type=image&TB_iframe=true&ETI_field=post_background');  
      
         window.send_to_editor = function(html) {  
			imgurl = jQuery('img',html).attr('src');  
			jQuery('input[name='+formfield+']').val(imgurl);  
			jQuery('input[name='+formfield+']').after( '<img src="' + imgurl + '">' );
			tb_remove();  
        }  
		
        return false;  
    });  
		
	jQuery('#post_background_delete').click(function() {  
		jQuery('#extra_fields img').remove();
		formfield = jQuery('#post_background').attr('name');  
		jQuery('input[name='+formfield+']').val('');
    });
	
	let publish = jQuery('#publish').val();
	jQuery('input[id=publish_new]').val(publish);
	
	jQuery('#publish_new').click(function() { 
		jQuery('#publish').trigger('click');
    });
});  