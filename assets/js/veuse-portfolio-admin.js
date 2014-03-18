(function ($) {
	
	'use strict';
	
	$.fn.veusePortfoliolist = function(options) {
		
		var defaults = {
			handle: '.portfolioselector-wrapper'
		}
			
		var options = $.extend({}, defaults,options);
		
		
			
		
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

	
	}
	
	
	jQuery(document).ready(function($){
		
		$(document).veusePortfoliolist();
		
	});


}( jQuery ));