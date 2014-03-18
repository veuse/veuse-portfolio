(function ($) {
	
	'use strict';
	
	$(document).ready(function(){
		
		$(document).veusePortfolio({ speed: 600 });

	}); /* Document ready */
	
	
	$.fn.veusePortfolio = function(options) {
	
		var defaults = {
			speed: 2000
		}
			
		var options = $.extend({}, defaults,options);
		
		var $filterType;
		var $holder = $('.portfolio-list');
		var $link = $('ul.portfolio-filter li a');
		var height;

		
		jQuery('.portfoliofilter li:first').addClass('active');
		
		
		$link.click(function(e) {
		
			$('ul.portfolio-filter li').removeClass('active'); // Remove all active classes
			
			$(this).parent().addClass('active');

			$filterType = $(this).attr('class');

		    if ($filterType == 'showall'){
		       var $filteredData = $('ul.portfolio-list > li');
		    }
		    else {
		    	var $filteredData = $('ul.portfolio-list > li[data-tags~=' + $filterType + ']');
		    }

		    $('ul.portfolio-list > li:visible').fadeOut(options.speed, function(){
			    $($filteredData).fadeIn(options.speed);

		    });

		    return false;
	    });
	
	}
	
	
	
	
}( jQuery ));