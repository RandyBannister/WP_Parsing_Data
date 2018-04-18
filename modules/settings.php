<?php 
	
add_action('admin_menu', 'wpd_item_menu');

function wpd_item_menu() {
	add_options_page(   __('Posting Settings', 'sc'), __('Posting Settings', 'sc'), 'edit_published_posts', 'wpd_config', 'wpd_config');
}

function wpd_config(){
	
?>
<div class="wrap tw-bs4">
<h2><?php _e('Settings', 'sc'); ?></h2>
<hr/>
 <?php if(  wp_verify_nonce($_POST['_wpnonce']) ): ?>
  <div id="message" class="updated" ><?php _e('Settings saved successfully', 'sc'); ?></div>  
  <?php 
  $config = get_option('wpd_options'); 

	foreach( $_POST as $key=>$value ){
		$options[$key] = $value;
	}
  update_option('wpd_options', $options );
  ?>
  <?php else:  ?>

  <?php //exit; ?>
  
  <?php endif; ?> 
<form   method="post" action="">
 

<?php wp_nonce_field();  
$config = get_option('wpd_options'); 
 
?>  
	<style>
	.credentials_list 
	</style>
	<table class="table credentials_list">
	  <thead>
		<tr>
		  <th colspan="4" class="text-right" ><input type="button" value="Add Row" class="btn btn-success add_row"  /></th>
		  
		</tr>
		<tr>
		  <th scope="col">Site URL</th>
		  <th scope="col">Username</th>
		  <th scope="col">Password</th>
		  <th scope="col">Actions</th>
		</tr>
	  </thead>
	  <tbody >
	  
		<?php 
		if( count( $config['site_url'] ) > 0 && $config['site_url'] != '' ){
			$count = count( $config['site_url'] ) ;
			for( $i=0; $i<$count; $i++ ){
			?>
			<tr>
			  <td><input class="form-control" type="text" name="site_url[]" value="<?php echo stripslashes( $config['site_url'][$i] ); ?>" ></td>
			  <td><input class="form-control" type="text" name="site_login[]" value="<?php echo stripslashes( $config['site_login'][$i] ); ?>" ></td>
			  <td><input class="form-control" type="text" name="site_pass[]" value="<?php echo stripslashes($config['site_pass'][$i]); ?>" ></td>
			  <td><input type="button" value="Delete" class="btn btn-warning form-control delete_row"  /></td>
			</tr>
			<?php 
			}
		}else{
			?>
		<tr>
		  <td><input class="form-control" type="text" name="site_url[]" ></td>
		  <td><input class="form-control" type="text" name="site_login[]" ></td>
		  <td><input class="form-control" type="text" name="site_pass[]" ></td>
		  <td><input type="button" value="Delete" class="btn btn-warning form-control delete_row"  /></td>
		</tr>	
			<?php
		}
		?>
		
 
	  </tbody>
	  <tfoot>
		<tr>
		  <th colspan="4" class="text-right">
			<button type="submit" class="btn btn-primary">Save Settings</button> 
		  </th>		  
		</tr>
	  </tfoot>
	  
	</table>
 

    
</form>

</div>


<?php 
}
?>