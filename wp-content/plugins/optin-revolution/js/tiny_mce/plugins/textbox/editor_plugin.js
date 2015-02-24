// php.rcena@gmail.com
(function() {
tinymce.create('tinymce.plugins.TextBox', {    
    
   init : function(ed, url) {
   var t = this, o;
   
	 ed.addCommand('mceInsertTextBox', function( ui, v )
   {    
      //default insert location
      if ( txt = ed.dom.get('simplemodal-data') ) {      
      ed.dom.add(txt, 'div', {style : {position: 'absolute', left: 10, top: 10, 'z-index': 2 }}, '<span style="font-family: verdana, geneva; font-size: 8pt;">Some text here...</span>');
      }
      
      is_editing = true;
      
      ed.undoManager.add();        		
	 });
   
   ed.addButton('textbox',
   {
   title : 'Insert TextBox',
   image : wtp + 'images/textbox-ic.png',
   cmd : 'mceInsertTextBox'
   });
   
   }
  });

// Register plugin with a short name
tinymce.PluginManager.add('textbox', tinymce.plugins.TextBox);
})();