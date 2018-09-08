*********************************************
* USAGE [robbin<robbin.joe@gmail.com>]
**********************************************

1. Enable module
2. Go to user settings page 
   Enable picture for user, 
   Set 'Picture upload dimensions', please use 150x150 by default.
3. Go to profile page, and test it.


***************************
** JavaScrip API
***************************
4. Use javascript to invoke upload popup window
   au.show([uid])

   example: au.show(1), uid is a require augment.
   you can use firebug console to run this javascript, au.show(1) and test it.

5. Uploader will update avatar file by default, if you are using another system, you can add your custom function
   
   Use this PHP Code to deal with file update:
   *****************************************************
   function avatar_uploader_custom_process ($file, $uid)
   *****************************************************
   
   Use Javascript code to add your custom callback:
   au.Events.upload.your_submit_callback = function(param) {}
   au.Events.cancel.your_cancel_callback = function(param) {}   
   au.Events.submit.your_submit_callback = function(param) {
       $('.profile-new-avatar img').attr('src', param.new_avatar);
   }

****************************************************
** Example
*****************************************************

Drupal.behaviors.update_avatar = function() {
  var img = ('#avatar img');

  img.click(function(){
    var uid = get_user_id();////get user id
    au.show(uid);
  });

  //when done, update img
  au.Events.submit.update_avatar = function(param) {
    img.attr('src', param.new_avatar);
  }
}
======================================================

That is OK!










BUT,

if you are use content_profile or other system to save avatar for users, 
please use custom function to process image file, like this.

/**
 * Custom uploader process, use content profile.
 */
function avatar_uploader_custom_process($file, $uid) {
  $content_type = 'profile_picture';
  
  $profile = content_profile_load($content_type, $uid);
  if (!$profile) {
    $profile = new stdClass();
    $profile->nid  = 0;
    $profile->uid  = $uid;
    $profile->type = $content_type;
    $profile->title   = $content_type;
    $profile->status  = 1;
    $profile->promote = 0;
    $profile->sticky  = 0;
    $profile->created = time();
    $profile = node_prepare($profile);
  }
  avatar_save_cck_field($profile, $file, 'field_profile_image');
  node_save($profile);
  
  return imagecache_create_url('120x120', $profile->field_profile_image[0]['filepath']);
}
