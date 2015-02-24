// base on css line-height : 10px
// php.rcena@gmail.com
(function(tinymce) {
   var DOM = tinymce.activeEditor;    
   tinymce.create('tinymce.plugins.InputAlign', {    
    
   init : function(ed, url) {

   ed.addButton('input_align_top',
   {
   title : 'Horizontal Align',
   image : wtp + 'images/horizontal-icon.png',
   cmd : 'mceInputAlignTop'
   });
   
   ed.addButton('input_align_left',
   {
   title : 'Vertical Align',
   image : wtp + 'images/vertical-icon.png',
   cmd : 'mceInputAlignLeft'
   });
   

	 ed.addCommand('mceInputAlignLeft', function()
   {
      var dom = this.dom, pf = dom.get('mce_getaccessed'), tpel = {};
      if ( typeof pf == 'undefined' ) return false;      
      //first el top location
      for(var r=0; r < pf.childNodes.length; r++ ) {
          if ( pf.childNodes[r].style.display !== 'none' )
          {
            tpel = {x : parseInt(pf.childNodes[r].style.left), y : parseInt(pf.childNodes[r].style.top), w:pf.childNodes[r].offsetWidth, h:pf.childNodes[r].offsetHeight};
            break;
          }
      }
      //align
      tinymce.each( pf.childNodes, function(v, i){
      if ( v.style.display !== 'none' )
      {
          dom.setStyle( v, 'left', tpel.x );
          dom.setStyle( v, 'top', tpel.y );          
          tpel.y += (tpel.h + 10);
      }      
      });      		
	 });

	 ed.addCommand('mceInputAlignTop', function()
   {
      var dom = this.dom, pf = dom.get('mce_getaccessed'), tpel = {};
      
      if ( typeof pf == 'undefined' ) return false;
            
      //first el top location
      for(var r=0; r < pf.childNodes.length; r++ ) {
          if ( pf.childNodes[r].style.display !== 'none' )
          {
            tpel = {x : parseInt(pf.childNodes[r].style.left), y : parseInt(pf.childNodes[r].style.top), w:pf.childNodes[r].offsetWidth, h:pf.childNodes[r].offsetHeight};
            break;
          }
      }
      //align
      tinymce.each( pf.childNodes, function(v, i){            
      if ( v.style.display !== 'none' )
      {             
          dom.setStyle( v, 'left', tpel.x );
          dom.setStyle( v, 'top', tpel.y );          
          tpel.x += (tpel.w + 10);
      }      
      });         		
	 });   
   
   }
  });

// Register plugin with a short name
tinymce.PluginManager.add('input_align', tinymce.plugins.InputAlign);
})(tinymce);