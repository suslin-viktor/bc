;( function( $ ) {

	'use strict';

	/*-----------------------------------------------------------------------------------*/
	/*	Font Select Preview
	/*-----------------------------------------------------------------------------------*/
  
	$( '.wolf_font_select' ).change(function(){
		font = $(this).val();
		preview = $(this).parent().find( '.wolf_font_preview' );
		preview.css("font-family", font);
	});

	$( '.wolf_font_select_transform' ).change(function(){
		
		font_transform = $(this).val();
		
		preview = $(this).parents( '.wolf_input' ).next( 'div' ).find( '.wolf_font_preview span' );
		
		var text = preview.text();
		var lowerCase = text.toLowerCase();
		var upperCase = text.toUpperCase();
		
		if(font_transform == 'normal' ){
			preview.removeAttr( 'style' );
			preview.empty();
			preview.html(lowerCase);
		}else if(font_transform == 'uppercase' ){
			preview.removeAttr( 'style' );
			preview.empty();
			preview.html(upperCase);
		}

	});


	/*-----------------------------------------------------------------------------------*/
	/*	Theme options tab cookies
	/*-----------------------------------------------------------------------------------*/    

	$( '.tabs a' ).click(function(){
		return false;	
	});

	var anchor = window.location.hash;
	$( '.tabs' ).each(function( ){

		var current = null;          
		var id = $(this).attr( 'id' );

		if( anchor != '' && $(this).find( 'a[href="'+anchor+'"]' ).length > 0){
			current = anchor;

		}else if($.cookie( 'tab'+id) && $(this).find( 'a[href="'+$.cookie( 'tab'+id)+'"]' ).length > 0){
			current = $.cookie( 'tab'+id);

		}else{
			current = $(this).find( 'a:first' ).attr( 'href' );
		}

		$(this).find( 'a[href="'+current+'"]' ).addClass( 'nav-tab-active' ); 
		
		$(current).siblings().hide();                          
		
		$(this).find( 'a' ).click(function(){
			var link = $(this).attr( 'href' ); 

			if(link == current){
				return false;
			}else{
				
				$(this).addClass( 'nav-tab-active' ).siblings().removeClass( 'nav-tab-active' ); 
				$(link).show().siblings().hide();   
				current = link;                  
				$.cookie( 'tab'+id,current); 
			}
		});

	});

} )( jQuery );