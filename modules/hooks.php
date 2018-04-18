<?php 
 

 //var_dump( wpd_reprocess_content( file_get_contents('http://php.net/manual/en/function.html-entity-decode.php') ) );
 
//add_Action('init', 'wpd_rpc_request');
function wpd_rpc_request(){
	include("inc/q2a-xml-rpc-master/IXR_Library.php");
	
	$username = 'admin';
	$password = 'vood00911';
	$url = "http://plugin.voodoopress.net/xmlrpc.php";
	
	$image_url = 'http://dalia.com.ua/wp-content/uploads/2018/03/26/19/203211_20180326042356_0751_600x600-300x300.jpg';

	$client = new IXR_Client($url);
        $content = array(
            'post_status' => 'draft',
            'post_type' => 'post',
            'post_title' => 'Title',
            'post_content' => 'Message',
       
        );
        $params = array(0, $username, $password, $content);
        $client->query('wp.newPost', $params);
        $post_id = $client->getResponse();

        $content = array(
            'name' => basename( $image_url ),
            'type' => wpd_get_image_type( $image_url ),
            'bits' => new IXR_Base64(file_get_contents( $image_url )),
            true
        );
        $client->query('metaWeblog.newMediaObject', 1, $username, $password, $content);
        $media = $client->getResponse();
		
		var_dump( $media );
		
        $content = array(
            'post_status' => 'publish',
            // Tags
            'mt_keywords' => 'tag1, tag2, tag3',
            'wp_post_thumbnail' => $media['id']
        );
        $client->query('metaWeblog.editPost', $post_id, $username, $password, $content, true);
/*
	
	include("inc/phpxmlrpc/lib/xmlrpc.inc");
    $function_name = "wp.newPost";
    $url = "http://plugin.voodoopress.net/xmlrpc.php";
 
    $client = new xmlrpc_client($url);
	$client->return_type = 'phpvals';

	$message = new xmlrpcmsg(
			$function_name, 
			array(
				new xmlrpcval(0, "int"), 
				new xmlrpcval("admin", "string"), 
				new xmlrpcval("vood00911", "string"), 
				new xmlrpcval(
					array(
						"post_type" => new xmlrpcval("post", "string"), 
						"post_status" => new xmlrpcval("publish", "string"), 
						"post_title" => new xmlrpcval("Sitepoint is Awesome !!!", "string"), 
						"post_author" => new xmlrpcval(1, "int"), 
						"post_excerpt" => new xmlrpcval("excerpt", "string"), 
						"post_content" => new xmlrpcval( mb_convert_encoding("conten asd asd віаіваіва t", "UTF-8" ), "string")
						), 
					"struct"
					)
				)
			);

	$resp = $client->send($message);
 
   if ($resp->faultCode()) echo 'KO. Error: '.$resp->faultString(); else echo "Post id is: " . $resp->value();
   
   
   $function_name = "wp.uploadFile";
   $message = new xmlrpcmsg(
			$function_name, 
			array(
				new xmlrpcval(0, "int"), 
				new xmlrpcval("admin", "string"), 
				new xmlrpcval("vood00911", "string"), 
				new xmlrpcval(
					array(
						"post_type" => new xmlrpcval("post", "string"), 
						"post_status" => new xmlrpcval("publish", "string"), 
						"post_title" => new xmlrpcval("Sitepoint is Awesome !!!", "string"), 
						"post_author" => new xmlrpcval(1, "int"), 
						"post_excerpt" => new xmlrpcval("excerpt", "string"), 
						"post_content" => new xmlrpcval( mb_convert_encoding("conten asd asd віаіваіва t", "UTF-8" ), "string")
						), 
					"struct"
					)
				)
			);

	$resp = $client->send($message);
 
   if ($resp->faultCode()) echo 'KO. Error: '.$resp->faultString(); else echo "Post id is: " . $resp->value();
   
   var_dump( $resp->value() );
   */
}


add_action( 'init', 'wpd_buttons' );
function wpd_buttons() {
    add_filter( "mce_external_plugins", "wpd_add_buttons" );
    add_filter( 'mce_buttons', 'wpd_register_buttons' );
}
function wpd_add_buttons( $plugin_array ) {
    $plugin_array['wpd'] =  plugins_url('/js/custom_button.js', __FILE__ );
    return $plugin_array;
}
function wpd_register_buttons( $buttons ) {
    array_push( $buttons, 'dropcap'  ); // dropcap', 'recentposts
    return $buttons;
}


add_Action('admin_footer', 'wpd_admin_footer');
function wpd_admin_footer(){
	$config = get_option('wpd_options'); 
	
	$out .=  '
	<div class="hidden">
		<div class="select_items tw-bs4">
			<table class="table ">
			  <tbody >';
			  for( $i=0; $i<count( $config['site_url'] ); $i++ ){
				$out .= '
				<tr>
					<td><input type="checkbox" name="remote_sites_to_publish[]" value="'.md5($config['site_url'][$i]).'" ></td>
					<td>'.$config['site_url'][$i].'</td>
				</tr>';
			  }
			$out .= '
			  </tbody>
			</table>
		
		</div>
	</div>
	';
	
	
	$out .=  '
	<style>
	.container_body .loadersmall {
		border: 3px solid #f3f3f3;
		-webkit-animation: spin 1s linear infinite;
		animation: spin 1s linear infinite;
		border-top: 5px solid #555;
		border-radius: 50%;
		width: 15px;
		height: 15px;
		display: inline-block;
		margin-bottom: -3px;
	}
	.container_body .loader_cont{
		display:none;
	}
	#popup_container{
		min-width:500px;
	}
	.preview_container{
		overflow:hidden;
	}
	.preview_container .single_prev_cont{
		float:left;
		height:50px;
		margin:10px;
		width:50px;
		padding:5px;
		border:1px solid #ccc;
		cursor:pointer;
		overflow:hidden;
	}
	.preview_container .single_prev_cont img{
		width:100%;
	}
	.container_body .image_preview{
		text-align:center;
	}
	.container_body .image_preview img{
		max-height:200px;
		margin:10px auto;
	}
	@media screen and (max-width: 505px){
		#popup_container{
			min-width:auto;
		}
	}
	</style>
	<div id="popup_container_link"   data-fancybox href="#popup_container">sss</a>
	<div id="popup_container" class="hidden"  >
		<div class="container_body tw-bs4">
		
		
			<div class="form-group">
				<label for="exampleInputEmail1">URL to parse</label>
				<input type="text" id="url2parse" class="form-control"     placeholder="Enter URL">
			</div>
			
			<div class="form-group">
				<button id="parse_url_button" class="btn btn-success" >Parse URL <span class="loader_cont"><span class="loadersmall"></span></span></button>
			</div>
		
 
			<div class="control_block result_block">
			</div>
		</div>
	</div>
	';
	$out .= "
		<script>
		jQuery(document).ready(function($){
			
			
			jQuery('body').on('click', '.single_prev_cont',  function(){
				var pnt = $(this);
				var url = $('.single_preview', pnt).attr('src');
				$('.image_preview img').attr( 'src', url );
				$('#picked_image').val( url );
			})
			
			
			$('body').on('keypress', '#url2parse', function(e){
				if (e.keyCode == 13) {
					$('#parse_url_button').click();				   
					return false; // prevent the button click from happening
				}
			})
			
			jQuery('body').on('click', '#insert_content_button',  function(){
				
				var content = '<div class=\"block_container\">\
						<div class=\"image_block\">\
							<img id=\"main_image\" src=\"'+$('#picked_image').val()+'\" \/>\
						</div>\
						<div class=\"data_block\">\
							<div id=\"main_title\" class=\"title_block\">'+$('#item_title').val()+'</div>\
							<div id=\"main_content\" class=\"content_block\">'+$('#item_description').val()+'</div>\
						</div>\
					</div>\
					<style>\
					.block_container{\
						overflow:hidden;\
					}\
					.block_container .image_block{\
						float:left;\
						width:30%;\
					}\
					.block_container .image_block img{\
						width:100%;\
					}\
					.block_container .data_block{\
						float:left;\
						width:30%;\
					}\
					</style>\
				';
				
				
				//$('#picked_image').val()+' - '+$('#item_title').val()+' - '+$('#item_description').val();
				
			 
				//tinymce.activeEditor.execCommand('mceInsertContent', false, content );
				$('#title').val( $('#item_title').val() );
				$('#title-prompt-text').replaceWith('');
				
				tinymce.activeEditor.execCommand('mceInsertContent', false, $('#item_description').val() );
				
				
				$('#postimagediv .inside').html('<input type=\"hidden\"   value=\"'+$('#picked_image').val()+'\"  name=\"new_feat_image\" /><img src=\"'+$('#picked_image').val()+'\" />');
				
				$.fancybox.close( true );
			})
			jQuery('body').on('click', '#parse_url_button',  function(){
		
				var pnt = jQuery(this);
				
				var data = {
					url: jQuery('#url2parse').val(),
					action: 'parse_url_data_action',
					security: '".wp_create_nonce('parse_url_data_security_nonce')."'
				};
					jQuery.ajax({url: '".get_option('home')."/wp-admin/admin-ajax.php',
						type: 'POST',
						data: data,            
						beforeSend: function(msg){
							jQuery('.loader_cont').fadeIn();					
							},
							success: function(msg){

								msg = msg.trim();
								console.log( msg );
								jQuery('.loader_cont').fadeOut();	

								var obj = jQuery.parseJSON( msg );
								if( obj.result == 'error' ){
									$('.result_block').html( '<div class=\"alert alert-danger\">'+obj.message+'</div>' );
								}
								if( obj.result == 'success' ){
									$('.result_block').html( obj.content );
								}
								
								
							} , 
							error:  function(msg) {
											
							}          
					});
			});
		})
		</script>";
	echo $out;
}
?>