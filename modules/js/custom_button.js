(function() {
    tinymce.create('tinymce.plugins.wpd', {
        init : function(ed, url) {
			
			console.log( url );
			
            ed.addButton('dropcap', {
                title : 'Post URL',
                cmd : 'dropcap',
                image :  url + '/linkpressCOicon.png'
            });
 
        
            ed.addCommand('dropcap', function() {
		
				jQuery('#picked_image, #url2parse').val('');
				jQuery('.result_block').html('');
				jQuery('#popup_container_link').click();
		 
		 
		 
		 
				/*
                var selected_text = ed.selection.getContent();
                var return_text = '';
                return_text = '[agot]' + selected_text + '[/agot]';
                ed.execCommand('mceInsertContent', 0, return_text);
				*/
            });
 
      
        },
        // ... Hidden code
    });
    // Register plugin
    tinymce.PluginManager.add( 'wpd', tinymce.plugins.wpd );
})();