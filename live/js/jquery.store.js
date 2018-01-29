
jQuery(document).ready(function($) {


	/* Store Isotope Filter
	--------------------------------*/

	var $items = $('#store-grid');

	var optionFilter = $('#filter'),
	optionFilterLinks = optionFilter.find('a');
	optionFilterLinks.attr('href', '#');


	$items.imagesLoaded( function(){
		setColumnWidth('store-item-container', '#store-grid', 1, 3, 3, 3, 3);
		$items.isotope({
			itemSelector : '.store-item-container',
			resizable : false,
		});
	});


	optionFilterLinks.click( function(){
		var selector = $(this).attr('data-filter');
		$items.isotope({ 
			filter : '.' + selector, 
			itemSelector : '.store-item-container',
			//layoutMode : 'fitRows',
			animationEngine : 'best-available'
		});

		// Highlight the correct filter
		optionFilterLinks.removeClass('active');
		$(this).addClass('active');
		return false;
	});

	$(window).smartresize(function(){
		setColumnWidth('store-item-container', '#store-grid', 1, 3, 3, 3, 3);
		$items.isotope('reLayout');
	});



/*-----------------------------------------------------------------------------------*/
/*	Functions for Isotope
/*-----------------------------------------------------------------------------------*/

	function getNumColumns(container, a, b, c, d, e){
		var winWidth = $(container).width();
		var column = 3;
		if(winWidth<500) column = a;		
		else if(winWidth >=500 && winWidth<767) column = b;
		else if(winWidth>=767 && winWidth<1024) column = c;
		else if(winWidth>=1024  && winWidth<1200) column = d;
		else if(winWidth>=1200  && winWidth<1600) column = d;
		else if(winWidth>=1600) column = e;	
		return column;
	}
	
	function getColumnWidth(container, a, b, c, d, e){
		var columns = getNumColumns(container, a, b, c, d, e);
		var bodyWidth = $(container).width();
		var columnWidth = bodyWidth/columns;
		columnWidth = Math.floor(columnWidth);
		return columnWidth;
	}

	function setColumnWidth(selector, container, a , b, c, d , e ){
		a = a || 1;
		b = b || 2;
		c = c || 3;
		d = d || 4;
		e = e || 5;
		var ColumnWidth = getColumnWidth(container, a, b, c, d, e);
		$("."+selector).each(function(index){
			$(this).css({"width":ColumnWidth+"px"});			
		});
	}
                



/*-------------------------------*/
}); // end document ready