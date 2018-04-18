jQuery(document).ready(function($){
	$('.credentials_list .add_row').click(function(){
		var first_row = $('.credentials_list  tbody tr:first-child').clone();
		$('input[type="text"]', first_row).val('');
		$('.credentials_list  tbody').append( first_row );
	})
	$('body').on('click', '.credentials_list .delete_row', function(){
		var pnt = $(this);
		pnt.parents('tr').fadeOut(function(){
			$(this).replaceWith('');
		});		
	})
	
	if( $('.select_items').length > 0 ){
		var table = $('.select_items').clone();
		$('.misc-pub-curtime').after( table );
	}
});