<?php 
function wpd_check_image_get( $url ){
	$res = wp_remote_get( $url );
	if( $res["response"]["code"] == 200 ){
		
		$data = $res["body"];
		$image_size =  getimagesizefromstring ( $data ) ;
		if( $image_size[0] > 100 && $image_size[1] > 100 ){
			return true;
		}else{
			return false;
		}
		
	}else{
		return false;
	}
}
function wpd_reprocess_content( $content ){
	include_once('simple_html_dom.php');
	$html = str_get_html($content);
	$content =  $html->find('body', 0)->innertext;
	
	$content = preg_replace('#<script(.*?)>(.*?)</script>#is', '', $content);
	$content = preg_replace('#<style(.*?)>(.*?)</style>#is', '', $content);
	$content = strip_tags( $content);
	$content = preg_replace( "/\r|\n/", " ", $content );
	$content = preg_replace( "/\r|\n/", " ", $content );
	$content = preg_replace('/\s\s+/', ' ', $content);
	$content = preg_replace('/\s\s+/', ' ', $content);
	$content = preg_replace('/\s\s+/', ' ', $content);
	
	return substr( $content, 0, 200 ).'...';
}


function wpd_get_title($str){

  if(strlen($str)>0){
    $str = trim(preg_replace('/\s+/', ' ', $str)); // supports line breaks inside <title>
    preg_match("/\<title\>(.*)\<\/title\>/i",$str,$title); // ignore case
    return $title[1];
  }else{
	 return false; 
  }
}
function wpd_addhttp($url) {
    if (!preg_match("~^(?:f|ht)tps?://~i", $url)) {
        $url = "http://" . $url;
    }
    return $url;
}

function wpd_get_image_type($url) {
    if( substr_count( $url, '.jpg' ) > 0 ){
		$type = 'image/jpeg';
	}
	if( substr_count( $url, '.png' ) > 0 ){
		$type = 'image/png';
	}
	if( substr_count( $url, '.gif' ) > 0 ){
		$type = 'image/gif';
	}
    return $type;
}

function wpd_postdata_import( $postdata, $site_hash ){
	$config = get_option('wpd_options'); 
	$start_date = time();
	// getting credentials
	for( $i=0; $i<count( $config['site_url'] ); $i++ ){
		
		if( md5( $config['site_url'][$i] ) == $site_hash ){

			$site_url = wpd_addhttp( $config['site_url'][$i] );			
			$site_url = rtrim( $site_url, '/' );
			$site_url = $site_url.'/xmlrpc.php';
			$site_credentials = array( 'url' => $site_url, 'user' => $config['site_login'][$i], 'pass' => $config['site_pass'][$i] );
		}
	}
	
	if( $site_credentials ){
	  /*
		include_once("inc/phpxmlrpc/lib/xmlrpc.inc");
		$function_name = "wp.newPost";
		$url = $site_credentials['url'];
	
		$client = new xmlrpc_client($url);
		$client->return_type = 'phpvals';

		$message = new xmlrpcmsg(
				$function_name, 
				array(
					new xmlrpcval(0, "int"), 
					new xmlrpcval( trim($site_credentials['user']), "string"), 
					new xmlrpcval( trim($site_credentials['pass']), "string"), 
					new xmlrpcval(
						array(
							"post_type" => new xmlrpcval("post", "string"), 
							"post_status" => new xmlrpcval("publish", "string"), 
							"post_title" => new xmlrpcval(iconv("cp1251", "UTF-8", $postdata['post_title'] ), "string"), 
							"post_author" => new xmlrpcval(1, "int"), 					 
							"post_content" => new xmlrpcval( iconv("cp1251", "UTF-8", $postdata['post_content'] ), "string")
							), 
						"struct"
						)
					)
				);
		$resp = $client->send($message);
 
		var_dump( array(
							"post_type" => new xmlrpcval("post", "string"), 
							"post_status" => new xmlrpcval("publish", "string"), 
							"post_title" => new xmlrpcval(iconv("cp1251", "UTF-8", $postdata['post_title'] ), "string"), 
							"post_author" => new xmlrpcval(1, "int"), 					 
							"post_content" => new xmlrpcval( iconv("cp1251", "UTF-8", $postdata['post_content'] ), "string")
							) );
 
 
		if ($resp->faultCode()) echo 'KO. Error: '.$resp->faultString(); else echo "Post id is: " . $resp->value();
		 die();	
		*/
		/*
		include_once('simple_html_dom.php');
		$html = str_get_html( $postdata['post_content'] );
		$content =  $html->find('body', 0)->innertext;
		*/
		 
	 
		$image_url = $postdata['post_image'];
		$image_url = explode( '?', $image_url );
		$image_url = $image_url[0];
		
		//include_once("inc/q2a-xml-rpc-master/IXR_Library.php");		
		
		require_once ABSPATH . 'wp-includes/class-IXR.php';
		
		$username = trim($site_credentials['user']);
		$password = trim($site_credentials['pass']);
		
		$url = $site_credentials['url'];
		
		$client = new IXR_Client($url);
		
	 
		// adding image
		$arrContextOptions=array(
			"ssl"=>array(
				"verify_peer"=>false,
				"verify_peer_name"=>false,
			),
		);  
	
        $content = array(
            'name' => basename( $image_url ),
            'type' => wpd_get_image_type( $image_url ),
            'bits' => new IXR_Base64(file_get_contents( $image_url, false, stream_context_create($arrContextOptions) )),
            true
        );
         
		$client->query('metaWeblog.newMediaObject', 1, $username, $password, $content);
        $media = $client->getResponse();
		 
	
		// creating post
        $content = array(
			//'post_thumbnail' => $media['id'],
            'post_status' => 'publish',
            'post_type' => 'post',
			'post_author' => 1,
            'post_title' => iconv("cp1251", "UTF-8", $postdata['post_title'] ),
            'post_content' => iconv("cp1251", "UTF-8", $postdata['post_content'] ),
       
        );
        $params = array(0, $username, $password, $content);
		
        $client->query('wp.newPost', $params);
        $post_id = $client->getResponse();
		
	 
		// update post with media
        $content = array(
            'post_status' => 'publish',
            'wp_post_thumbnail' => $media['id']
        );
		
        $client->query('metaWeblog.editPost', $post_id, $username, $password, $content, true);
		$res = $client->getResponse();
		
	}
}


function wpd_set_featured_image( $post_id, $image_url ){
		remove_action( 'save_post', 'wpd_save_postdata' );
		require_once(ABSPATH . "wp-admin" . '/includes/image.php');
		require_once(ABSPATH . "wp-admin" . '/includes/file.php');
		require_once(ABSPATH . "wp-admin" . '/includes/media.php');
		
		$upload_dir = wp_upload_dir();
		
		$arrContextOptions=array(
			"ssl"=>array(
				"verify_peer"=>false,
				"verify_peer_name"=>false,
			),
		);  
		$image_url = explode( '?', $image_url );
		$image_url = $image_url[0];
	 
		$image_data = file_get_contents($image_url, false, stream_context_create($arrContextOptions) );
		$filename = basename($image_url);
		
		$filename_ar = explode('.', $filename);
		$filename = sanitize_file_name( $filename_ar[0] ).time().'.'.$filename_ar[1];
		if(wp_mkdir_p($upload_dir['path']))
			$file = $upload_dir['path'] . '/' . $filename;
		else
			$file = $upload_dir['basedir'] . '/' . $filename;
		file_put_contents($file, $image_data);

		$wp_filetype = wp_check_filetype($filename, null );
		$attachment = array(
			'post_mime_type' => $wp_filetype['type'],
			'post_title' => sanitize_file_name($filename),
			'post_content' => '',
			'post_status' => 'inherit'
		);
		$attach_id = wp_insert_attachment( $attachment, $file, $post_id );
		require_once(ABSPATH . 'wp-admin/includes/image.php');
		$attach_data = wp_generate_attachment_metadata( $attach_id, $file );
	 
		wp_update_attachment_metadata( $attach_id, $attach_data );

		set_post_thumbnail( $post_id, $attach_id );
	
}

?>