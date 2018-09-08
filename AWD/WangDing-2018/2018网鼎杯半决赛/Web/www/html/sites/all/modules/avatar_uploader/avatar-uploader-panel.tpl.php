<?php 
/**
 * I place upload/crop js into this template file, which is not good, but it is an separate part and easy to edit and replace,
 * also the js files won't be loaded in all pages. Anyway, let me know if there is good way.
 * @author robbin robbin.joe@gmail.com
 * 
 * 
 * depends on au object,
 * au is an controller for popup window, used one method, trigger, to trigger the event to notify popup window
 */
?>
<div id="<?php echo $options['id'];?>" class="uploader-avatar-box">
  <span class="close_btn"></span>
   <div id="upload-panel">
     <input id="upload-btn" type="button" class="btn btn-large clearfix" value="<?php echo t('Please choose file');?>" />
     <span class="upload-description"><i><?php echo t('PNG, JPG, or GIF (!size max file size)', array('!size' => format_size($options['max_size'] * 1024)));?></i></span>
   </div>
   <div id="errormsg" class="clearfix redtext"></div>	              
   <div id="pic-progress-wrap" class="progress-wrap" style="margin:10px 0;"></div>	
   <div id="picpanel">
     <div class="pic-wrap"><div id="picbox"></div></div>
     <div id="previewBox" style="width: <?php echo $options['width'];?>px; height: <?php echo $options['height'];?>px; overflow: hidden"></div>
   </div>
   <div id="crop_btns">
     <span id="crop_submit"><?php echo t('Submit');?></span>
     <span id="crop_cancel"><?php echo t('Cancel');?></span>
   </div>
  <div class="clear"/>
</div>
    
<script type="text/javascript" src="<?php echo $options['js_uploader'];?>" id="simple-ajax-uploader-js"></script>
<script type="text/javascript">
 (function() {
    function safe_tags(str) {
      return str.replace(/&/g,'&amp;').replace(/</g,'&lt;').replace(/>/g,'&gt;') ;
    }
 
    var options = <?php echo drupal_json_encode($options)?>;
    
    var p_width = options['width'], p_height = options['height'];

    var btn  = document.getElementById('upload-btn'),
      wrap   = document.getElementById('pic-progress-wrap'),
      picBox = document.getElementById('picbox'),
      previewBox = document.getElementById('previewBox'),
      errBox     = document.getElementById('errormsg');
	
    var uploader = new ss.SimpleUpload({
        button: btn,
        url: options['url_upload'],
        progressUrl: options['url_progress'],
        name: 'imgfile',
        multiple: false,
        //maxUploads: 2,
        maxSize: options['max_size'],
        allowedExtensions: ['jpg', 'jpeg', 'png', 'gif'],
        accept: 'image/*',
        //debug: true,
        hoverClass: 'btn-hover',
        focusClass: 'active',
        disabledClass: 'disabled',
        responseType: 'json',
        onExtError: function(filename, extension) {
          alert(Drupal.t('Only PNG, JPG, and GIF files are allowed!'));
        },
        onSizeError: function(filename, fileSize) {
          alert(Drupal.t('File is too big!'));
        },        
        onSubmit: function(filename, ext) {
          //console.log(filename);          
          var prog  = document.createElement('div'),
              outer = document.createElement('div'),
              bar   = document.createElement('div'),
              size  = document.createElement('span'),
              self  = this;     
    
            prog.className = 'prog';
            size.className = 'size';
            outer.className = 'progress';
            bar.className = 'bar';
            
            outer.appendChild(bar);
            prog.innerHTML = '<span style="vertical-align:middle; class="filename">'+safe_tags(filename)+' - </span>';
            prog.appendChild(size);
            prog.appendChild(outer);
            wrap.appendChild(prog); // 'wrap' is an element on the page
            
            self.setProgressBar(bar);
            self.setProgressContainer(prog);
            self.setFileSizeBox(size);                
            
            errBox.innerHTML = '';
            
            jQuery('#upload-panel').hide();
            au.trigger('upload', filename);
            //btn.value = 'Choose another file';
          },
        onComplete: function(file, response) {
            //console.log(file, response);
            if (response.success === true) {
              picBox.innerHTML     = '<img id="cropimg" src="' + response.file_url + '" data-file="' + response.new_file + '" data-id="' + options['id'] + '">';
              previewBox.innerHTML = '<img id="preview" src="' + response.file_url + '">';
              image_crop_init();
            } else {
              errBox.innerHTML = response.msg;
            }
        }
	  });
  
  //reset panel
  var reset = function() {
    picBox.innerHTML = previewBox.innerHTML = '';
    jQuery('#crop_btns, #picpanel').hide();
    jQuery('#upload-panel').show();
  };
  
  ////////////Crop Start
  var avatar_data = {};
  var image_crop_init = function () {
    jQuery('#crop_btns, #picpanel').show();
    jQuery('#upload-panel').hide();
      
    jQuery('#cropimg').Jcrop({
      boxWidth: 400,
      boxHeight: 400,
      aspectRatio: p_width/p_height,
      setSelect: [0, 0, p_width, p_height],
      onChange: showCoords//,
      //onSelect: showCoords  //no ',' here due to IE issue
      //onChange: showPreview,
      //onReleas: release
    });
    au.panel.centerMe();
  }
  jQuery('#crop_submit').click(function(){
     var data  = avatar_data;
     if (!data['h']) {
       alert(Drupal.t('Please choose image file'));
       return false;
     }
     
     data.file = jQuery('#cropimg').attr('data-file');
     data.id   = jQuery('#cropimg').attr('data-id');
     data.p_width  = p_width;
     data.p_height = p_height;
     
     au.trigger('presubmit', data);
     jQuery.post(Drupal.settings.basePath + 'au/crop', data , function(res){
       au.trigger('submit', res);
       reset();//reset panel
     }, 'json');
     au.trigger('dosubmit', data);
  });
  
  jQuery('#crop_cancel').click(function(){
     au.trigger('cancel');
     reset();
  });
  
  jQuery('.close_btn').click(function(){
    au.trigger('cancel');
    reset();
  });
  
  var showCoords = function (coords) {
    avatar_data = coords;

    //preview
    var rx = p_width / coords.w;
    var ry = p_height / coords.h;

    jQuery('#preview').css({
      width:  Math.round(rx * jQuery('#cropimg').width()) + 'px',
      height: Math.round(ry * jQuery('#cropimg').height()) + 'px',
      marginLeft: '-' + Math.round(rx * coords.x) + 'px',
      marginTop:  '-' + Math.round(ry * coords.y) + 'px',
      //fixed img preview bug in sometime you set the max-width or max-height
      'max-width': 'none',
      'max-height': 'none'
    });
  }
 
})();
</script>
<script type="text/javascript" src="<?php echo $options['js_jcrop']?>" id="jcrop-js"></script>
<link type="text/css" href="<?php echo $options['css_jcrop'];?>" rel="stylesheet" media="all"/>
<!--///Crop End-->
