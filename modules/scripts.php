<?php 
add_action('wp_print_scripts', 'wpd_add_script_fn');
function wpd_add_script_fn(){

	$prefix = 'wpd';

	wp_enqueue_style( $prefix.'_bootsrap_css', plugins_url('/inc/assets/css/boot-cont.css', __FILE__ ) ) ;
	wp_enqueue_style( $prefix.'awesome.min.css', plugins_url('/inc/fa/css/font-awesome.min.css', __FILE__ ) ) ;
	
	
	if(is_admin()){	
		wp_enqueue_media();
		
		wp_enqueue_script( $prefix.'jquery.fancybox.js', plugins_url('/inc/fancybox/jquery.fancybox.js', __FILE__ ), array( 'jquery' )  ) ;
		wp_enqueue_style( $prefix.'fancybox.css', plugins_url('/inc/fancybox/jquery.fancybox.css', __FILE__ ) ) ;  
		
		
		wp_enqueue_script( $prefix.'_admi11n_js', plugins_url('/js/admin.js', __FILE__ ), array('jquery'  ) ) ;
		
		$localize_script = array(
			'add_url' => get_option('home'),	 
		);
	 
		wp_localize_script( $prefix.'admi11n_js', 'local_data', $localize_script );
		
		
		wp_enqueue_style( $prefix.'_admin_css', plugins_url('/css/admin.css', __FILE__ ) ) ;	
	  }else{

		wp_enqueue_script( $prefix.'_front_js', plugins_url('/js/front.js', __FILE__ ), array( 'jquery' ) ) ;
		wp_enqueue_style( $prefix.'_front_css', plugins_url('/css/front.css', __FILE__ ) ) ;	
			
		
	  }
}
?>