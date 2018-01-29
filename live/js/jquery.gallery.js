/**
 * Masonry Gallery
 *
 */
gallery = function ( $ ) {

	"use strict";

	return {

		init : function () {
			this.addOverlay();
			this.masonry();
		},

		addOverlay : function () { 
			$( '.masonry-gallery ul li a' ).append( '<div class="gallery-item-overlay"></div>' );
		},

		masonry : function () {
			var $this = this;
			var $gallery = $( '.masonry-gallery ul' );

			$gallery.imagesLoaded( function() {
				$this.setColumnWidth( '.masonry-gallery ul li' );
				$gallery.isotope( {
					itemSelector : '.masonry-gallery ul li',
				} );
			} );

			$( window ).resize( function() {
				$this.setColumnWidth( '.masonry-gallery ul li' );
				$gallery.isotope( {
					itemSelector : '.masonry-gallery ul li',
				} );
			} ).resize();
		},

		getNumColumns : function() {
			var winWidth = $( '#main' ).width();
			var column = 3;		
			if ( winWidth < 767 ) {
				column = 2;
			} else if ( winWidth >= 767 && winWidth < 1200 ) { 
				column = 3;
			} else if ( winWidth >=1200 && winWidth < 1600 ) { 
				column = 4;
			} else if ( winWidth >=1600 ) {
				column = 5;
			}	
			return column;
		},
		
		getColumnWidth : function() {
			var columns = this.getNumColumns();	
			var offset = $( 'body' ).hasClass( 'wrapped' ) ? 60 : 0;	
			offset = 1030 < $( '#main' ).width() ? offset : 0;
			var wrapperWidth = $( '#main' ).width() - offset;	
			var columnWidth = wrapperWidth/columns;
			columnWidth = Math.floor( columnWidth );
			return columnWidth;
		},

		setColumnWidth : function( selector ) {
			var ColumnWidth = this.getColumnWidth();
			$( selector ).each( function() {
				$(this).css( { 'width' : ColumnWidth + 'px' } );			
			} );
		}
	};
}( jQuery );

var gallery = gallery || {};

;( function( $ ) {

	"use strict";
	gallery.init();
	
} )( jQuery );