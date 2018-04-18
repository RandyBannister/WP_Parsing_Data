<?php 
		
/*
add_action( 'add_meta_boxes', 'wpd_add_custom_box' );
function wpd_add_custom_box() {
	global $post;
	global $current_user;
		add_meta_box( 
			'wpd_system_editor',
			__( 'System Data', 'wl' ),
			'wfd_system_editor',
			'post' , 'advanced', 'high'
		);

	
	
		
}
function wfd_system_editor(){
	global $post;

	$out .= '

<div class="tw-bs">
	<div class="form-horizontal ">
	
		<div class="control-group">  
            <label class="control-label" for="input01">System URL</label>  
            <div class="controls">  
              <input type="text" class="input-xlarge" name="s_u" id="s_u" value="'.get_post_meta( $post->ID, 's_u', true ).'">  
            </div>  
          </div> 
		<div class="control-group">  
            <label class="control-label" for="input01">Forex Robot Logo</label>  
            <div class="controls">  
              <input type="text" class="input-xlarge" name="f_r_l" id="f_r_l" value="'.get_post_meta( $post->ID, 'f_r_l', true ).'">  
            </div>  
          </div>   
		  
		<div class="control-group">  
            <label class="control-label" for="input01">Forex Robot Name</label>  
            <div class="controls">  
              <input type="text" class="input-xlarge" name="f_r_n" id="f_r_n" value="'.get_post_meta( $post->ID, 'f_r_n', true ).'">  
            </div>  
          </div> 
	
		<div class="control-group">  
            <label class="control-label" for="input01">Forex Robot URL</label>  
            <div class="controls">  
              <input type="text" class="input-xlarge" name="f_r_u" id="f_r_u" value="'.get_post_meta( $post->ID, 'f_r_u', true ).'">  
            </div>  
          </div> 
		  		  
		  
		  <div class="control-group">  
            <label class="control-label" for="input01">Review Name</label>  
            <div class="controls">  
              <input type="text" class="input-xlarge" name="r_n" id="r_n" value="'.get_post_meta( $post->ID, 'r_n', true ).'">  
            </div>  
          </div> 
		  
		  <div class="control-group">  
            <label class="control-label" for="input01">Forex Robot Review URL </label>  
            <div class="controls">  
              <input type="text" class="input-xlarge" name="f_r_r_u" id="f_r_r_u" value="'.get_post_meta( $post->ID, 'f_r_r_u', true ).'">  
            </div>  
          </div> 
		  
			<div class="control-group">  
            <label class="control-label" for="input01">Account Type</label>  
            <div class="controls">  
              <input type="text" class="input-xlarge" name="a_t" id="a_t" value="'.get_post_meta( $post->ID, 'a_t', true ).'">  
            </div>  
          </div> 

		</div>	
	</div>
	';	
	echo $out;
}
*/

add_action( 'save_post', 'wpd_save_postdata' );
function wpd_save_postdata( $post_id ) {
global $current_user; 
$config = get_option('wpd_options'); 

 if ( defined( 'DOING_AUTOSAVE' ) && DOING_AUTOSAVE ) 
      return;

  if ( 'page' == $_POST['post_type'] ) 
  {
    if ( !current_user_can( 'edit_page', $post_id ) )
        return;
  }
  else
  {
    if ( !current_user_can( 'edit_post', $post_id ) )
        return;
  }
  /// User editotions
  
  
	remove_action( 'save_post', 'wpd_save_postdata' );
	if( $_POST['new_feat_image'] && $_POST['new_feat_image'] != '' ){		
		wpd_set_featured_image(  $post_id , $_POST['new_feat_image'] );
	}
  
  
	if( $_POST['remote_sites_to_publish'] ){
		foreach( $_POST['remote_sites_to_publish'] as $single_hash ){
			for( $i=0; $i<count( $config['site_url'] ); $i++ ){
				
				// if cache is right start appliing code
				if( md5( $config['site_url'][$i] ) == $single_hash ){
					$postdata = array(
						'post_title' => stripslashes( $_POST['post_title'] ),
						'post_content' => stripslashes( $_POST['content'] ),
					
					);
					  $src_full = wp_get_attachment_image_src(get_post_thumbnail_id($post_id), 'full' ); 
					if( $_POST['new_feat_image'] ){
						$postdata['post_image'] = stripslashes( $_POST['new_feat_image'] );
					}
					if( $src_full[0] ){
						$postdata['post_image'] = $src_full[0];
					}
					wpd_postdata_import( $postdata, md5( $config['site_url'][$i] ) );
				}
			}
		}
	 
	}
	//DIE();
}

?>