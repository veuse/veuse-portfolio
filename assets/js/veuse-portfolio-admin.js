(function ($) {
	
	'use strict';
	
	$(document).ready(function(){
	
		/* Admin widget */
	  
	  $('.portfolioselector-wrapper').each(function(){
		
		actives = $(this).next().val();

		var arr = actives.split(',');

				
		$(this).find('a[data-portfolio-id]').each(function(){
		
			id = $(this).attr('data-portfolio-id');
		
			if( jQuery.inArray(id, arr) >= 0 ){
				$(this).addClass('active');
			}
		
		});	
		
		actives = '';
		arr = '';
	});

	
	jQuery(document).on('click','.portfolioselector-wrapper a', function(){
		
		var ids = '';
		
		$(this).toggleClass('active');
		
		$(this).parent().find('a.active').each(function(){			
			ids += $(this).attr('data-portfolio-id') + ',';
		});
		
		$(this).parent().next().val(ids);
		
		ids = '';
		return false;
	});

		
		
	}); /* Document ready */

}( jQuery ));	