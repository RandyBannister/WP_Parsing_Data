<?php 

add_action('wp_ajax_parse_url_data_action', 'wpd_parse_url_data_action');
add_action('wp_ajax_nopriv_parse_url_data_action', 'wpd_parse_url_data_action');

function wpd_parse_url_data_action(){
	global $current_user, $wpdb;
	if( check_ajax_referer( 'parse_url_data_security_nonce', 'security') ){
		$url = $_POST['url'] ;	
		
		$url  = wpd_addhttp($url);
		$parse = parse_url($url);
		
		
		$domain = $parse["scheme"].'://'.$parse["host"];
		
		
		$args = array(
		'timeout'     => 5,
		'redirection' => 5,
		'httpversion' => '1.0',
		'user-agent'  => 'WordPress/' . $wp_version . '; ' . home_url(),
		'blocking'    => true,
		'headers'     => array(),
		'cookies'     => array(),
		'body'        => null,
		'compress'    => false,
		'decompress'  => true,
		'sslverify'   => true,
		'stream'      => false,
		'filename'    => null
	); 
		
		
		$res = wp_remote_get( $url, $args );
	 
		if( !is_wp_error( $res  ) ){
			if( $res["response"]["code"] == 200 ){
 
				$tags = get_meta_tags( $url );
				//var_dump( $tags );
				// title process
				$title = null;				
				if( $tags['og:title'] ){
					$title = $tags['og:title'];
				}
				if( !$title && $tags['twitter:title'] ){
					$title = $tags['twitter:title'];
				}
				if( !$title ){
					$title = wpd_get_title( $res["body"] );
				}
				if( !$title ){
					$title = 'Unknown';
				}
				
				$title = html_entity_decode ( $title, ENT_QUOTES );
				
				// description
				$description = null;
 			 
				if( $tags['og:description'] ){
					$description = $tags['og:description'];
				}
				if( !$description && $tags['twitter:description'] ){
					$description = $tags['twitter:description'];
				}
				if( !$description ){
					$description = $tags['description'];
				}
				if( !$description ){
					$description = wpd_reprocess_content( $res["body"] );
				}

				if( !$description ){
					$description = 'Unknown';
				}
	 
				// descr html entities
				$description = html_entity_decode ( $description, ENT_QUOTES );
	 
				// images		
				if( $tags['og:image'] ){
					$images = $tags['og:image'];
				}
				if( !$images && $tags['twitter:image'] ){
					$images = $tags['twitter:image'];
				}
			 
		  
				// get 5 images
				include_once('simple_html_dom.php');
				$extra_images = array();
				$html = str_get_html( $res["body"] );
				foreach($html->find('img') as $element){				
					$this_str =  $element->src;
					
					if( substr_count( $this_str, 'facebook.com/tr' ) > 0 ){
						continue;
					}
					/*
					if( substr_count( $this_str, 'gravatar' ) > 0 ){
						continue;
					}
					*/
					
					if( substr_count( $element->src, $parse['host'] ) == 0 && substr_count( $element->src, 'http' ) == 0 ){
						$this_str = $domain.'/'.ltrim( $this_str, '/' );
					}
					
					if( wpd_check_image_get( $this_str ) && !in_array( $this_str, $extra_images ) ){
						$extra_images[] = $this_str;
					}
					if( count($extra_images) > 5 ){
						break;
					}
					
				}
				/*
				if( count( $extra_images ) > 0 ){
					$extra_images = array_slice($extra_images, 0, 5);
				}
				*/
			 
				$final_images_list = array();
				$final_img_urls = array(); 
				if( $images ){
					$final_images_list[] = '<div class="single_prev_cont"><img class="single_preview" src="'.$images.'" /></div>';;
					$final_img_urls[] = $images;
				}
				if( count($extra_images) > 0 ){
					foreach( $extra_images as $single_image ){
						$final_images_list[] = '<div class="single_prev_cont"><img class="single_preview" src="'.$single_image.'" /></div>';
						$final_img_urls[] = $single_image;
					}
				}
			 
				$images = '<div class="preview_container">'.implode( '', $final_images_list ).'</div>';
			 
			 
				$content = '
				<div class="form-group ">
					<input type="hidden" id="picked_image" value="'.$final_img_urls[0].'" />
					<div class="image_preview">
						<img src="'.$final_img_urls[0].'" />
					</div>
				</div>
				<div class="form-group">
				'.$images.'
				</div>
				<div class="form-group">
					<label for="exampleInputEmail1">Title</label>
					<input type="text" id="item_title" class="form-control"     placeholder="URL Title" value="'.htmlentities( $title ).'">
				</div>
				<div class="form-group">
					<label for="exampleInputEmail1">Description</label>
					<textarea  id="item_description" class="form-control"     placeholder="URL Description"  >'.htmlentities( $description ).'</textarea>
				</div>
				<div class="form-group">
					<button id="insert_content_button" class="btn btn-success" >Insert Content</button>
				</div>
				';
			 
			 
				echo json_encode( array( 'result' => 'success', 'content' => $content ) );	
				
			}else{
				echo json_encode( array( 'result' => 'error', 'message' => 'We cant get responce form remote server' ) );
			}
		}else{
			echo json_encode( array( 'result' => 'error', 'message' => 'Something wrong with your URL' ) );
		}
		 
	}
	die();
}
 
?>