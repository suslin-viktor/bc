/*-----------------------------------------------------------------------------------*/
/*  UI
/*-----------------------------------------------------------------------------------*/
ui = function( $ ) {

	"use strict";

	return {

		isMobile : navigator.userAgent.match( /(iPad)|(iPhone)|(iPod)|(Android)|(PlayBook)|(BB10)|(BlackBerry)|(Opera Mini)|(IEMobile)|(webOS)|(MeeGo)/i ),

		init : function () {

			var $this = this;

			$( window ).trigger( 'resize' ); // trigger resize event to force all window size related calculation

			this.parallax();
			this.mainMenu();
			this.mobileMenu();
			this.stickyMenu();
			this.fluidVideos();
			this.wmode();
			this.smoothScroll();
			this.backToTop();
			this.googleMap();
			this.woocommerceFix();
			this.lightbox();

			if ( this.isMobile ) {
				$( 'html' ).addClass( 'is-mobile' );
			}

			/**
			 * Scroll event
			 */
			$( window ).scroll( function() {
				$this.stickyMenu();
			} );

			/**
			 * Resize event
			 */
			$( window ).resize( function() {
				$this.parallax();
			}).resize();
		},


		/**
		 * Dropdown menu
		 */
		mainMenu : function () {

			var id = '#primary-menu';
			
			$(id+" ul ").css({display: "none"}); // Opera Fix
			$(id+" li").hover(function(){
				$(this).find('ul:first').css({visibility: "visible", display: "none"}).slideDown(350);
				$(this).find('a').addClass('current');
			},function(){
				$(this).find('ul:first').css({visibility: "hidden"});
				$(this).find('a').removeClass('current');
			});

			var homeMenu = $('#primary-menu > .home-menu-item').length;
			if(homeMenu <1 ){
				$('#primary-menu').css({'padding-left' : '0px'});
			}
			
		},

		/**
		 * Mobile menu
		 */
		mobileMenu : function () {


			var mobileMenu = $('#mobile-menu-container #mobile-menu, #mobile-menu-container .default-menu');
			$('#mobile-menu-dropdown').click(function() {

				if(!mobileMenu.hasClass('open')){
					mobileMenu.slideDown().addClass('open');
				}else{
					mobileMenu.slideUp().removeClass('open');
				}

			});

		},

		/**
		 * Sticky Menu
		 */
		stickyMenu : function () {

			var toolbarOffset = $( 'body' ).is( '.admin-bar' ) ? 28 : 0;
			var headerElem = $('.site-header');
			var navOffsetTop = headerElem.height();
			var scrollPos = $(window).scrollTop();
				
			if (scrollPos > navOffsetTop) { 

				$('#primary-menu-container').addClass('fixed-menu').slideDown();

			}  else {

				$('#primary-menu-container').removeClass('fixed-menu').removeAttr( 'style' );
			}


		},

		/**
		 * Fluid Videos
		 */
		fluidVideos : function () {

			var videoSelectors = [
				"iframe[src*='player.vimeo.com']",
				"iframe[src*='youtube.com']",
				"iframe[src*='youtube-nocookie.com']",
				"iframe[src*='kickstarter.com'][src*='video.html']",
				"iframe[src*='screenr.com']",
				"iframe[src*='blip.tv']",
				"iframe[src*='dailymotion.com']",
				"iframe[src*='viddler.com']",
				"iframe[src*='qik.com']",
				"iframe[src*='revision3.com']",
				"iframe[src*='hulu.com']",
				"iframe[src*='funnyordie.com']",
				"iframe[src*='flickr.com']",
				"embed[src*='v.wordpress.com']"
			];

			var allVideos = videoSelectors.join(',');
			$( '#page' ).find( allVideos ).wrap( '<span class="fluid-video" />' );
			$( '.rev_slider_wrapper' ).find( allVideos ).unwrap();


		},

		/**
		 * Fix youtube z-index
		 */
		wmode : function () {

			var iframes = $( 'iframe' );

			if ( iframes.length ) {

				iframes.each(function(){
					
					var url = $( this ).attr( 'src' );

					if ( url.match( /(youtube.com)|(youtu.be)/i ) ) {
						
						if ( url.indexOf( '?' ) !== -1) {

							$( this ).attr( 'src', url + '&wmode=transparent' );

						} else {

							$( this ).attr('src', url + '?wmode=transparent' );

						}
					}
					
						
				} );

			}

		},

		/**
		 * Set lightbox depending on user's theme options
		 */
		lightbox : function() {

			if ( $.isFunction( $.swipebox ) && WolfThemeParams.lightbox === 'swipebox' ) {
				
				$( '.lightbox, .wolf-show-flyer, .wolf-show-flyer-single, .last-photos-thumbnails' ).swipebox();

				if ( WolfThemeParams.videoLightbox !== null ) {
					$( '.video-item-container .entry-link' ).swipebox();
				}
			

			} else if ( $.isFunction( $.fancybox ) && WolfThemeParams.lightbox === 'fancybox' ) {

				$( '.lightbox, .wolf-show-flyer, .wolf-show-flyer-single, .last-photos-thumbnails' ).fancybox();

				if ( WolfThemeParams.videoLightbox !== null ) {
					$( '.video-item-container .entry-link' ).fancybox( {
						padding : 0,
						nextEffect : 'none',
						prevEffect : 'none',
						openEffect  : 'none',
						closeEffect : 'none',
						helpers : {
							media : {},
							title : {
								type : 'outside'
							},
							overlay : {
								opacity: 0.9
							}
						}
					} );
				}
			}

			/**
			 * Add replace entry link by video link
			 */
			if ( $( '.video-item-container' ).length && WolfThemeParams.videoLightbox !== null && WolfThemeParams.lightbox !== 'none' ) {
				
				var videoItem = $( '.video-item-container' ),
					postId,
					data;

				videoItem.each( function() {

					var _this = $( this );

					postId = _this.attr( 'id' ).replace( 'post-', '' );

					data = {
						action: 'wolf_get_video_url_from_post_id',
						id : postId
					};
					
					$.post( WolfThemeParams.ajaxUrl , data, function(response){
						
						// console.log( response );
						if ( response ) {
							_this.find( '.entry-link' ).attr( 'href', response );
						}

					});
				} );

				$( '.video-item-container .entry-link' ).each( function(){ $( this ).attr( 'rel','video-gallery' ); } );
			}

			$( '.gallery .lightbox' ).each( function(){ $( this ).attr( 'rel','gallery' ); } );
			
		},

		/**
		 * Smooth Anchor scroll
		 */
		smoothScroll : function () {

			$('.scroll').bind('click',function(event){
				var anchor = $(this);                   
				$('html, body').stop().animate({
					scrollTop: $(anchor.attr('href')).offset().top - 250
				}, 1000,'swing');
				event.preventDefault();
			});

		},

		/**
		 * Back to top arrow
		 */
		backToTop : function() {

			$(window).scroll(function(){
				var posScroll = $(document).scrollTop();
				if(posScroll >=550)
					$('a#top-arrow').fadeIn(600);
				else
					$('a#top-arrow').fadeOut(600);
			});


		},

		/**
		 * Google map iframe
		 */
		googleMap : function () {

			/**
			 * Bottom Area
			 * If a google map is displayed, make it full width
			 */
			if ( $( '#bottom-holder .wolf-google-map' ).length || $( '#bottom-holder iframe[src^="https://maps.google"]' ).length ){
				$( '#bottom-holder' ).css({ padding : 0 });
				$( '#bottom-holder .wrap' ).css({ 'overflow' : 'hidden', 'max-width' : '100%', width : '100%', height : $( '#bottom-holder iframe[src^="https://maps.google"]' ).height() });
				$( '#bottom-holder .wrap p' ).remove();
			}

		},

		/**
		 *  Parallax
		 */
		parallax : function () {
			if ( ! this.isMobile && 799 < $( window ).width() ) {
				$(' .section-parallax' ).each( function() {
					$( this ).parallax( "50%", 0.1 );
				} );
			}
		},


		shareMobile : function() {


			$( ".wolf-share-mobile a" ).click(function() {
				var url = jQuery(this).attr("href");
				var popup = window.open(url,"null", "height=350,width=570, top=150, left=150");
				if (window.focus) {
					popup.focus();
				}
				return false; 
			} );

		},

		/**
		 * WooCommerce Fix.
		 */
		woocommerceFix : function () {

			$( '.products li' ).removeClass( 'first last' );

			if ( $( '.woocommerce-pagination' ).length ) {
				$( '.woocommerce-pagination' ).removeClass( 'woocommerce-pagination' );
			}
		}

	};

}( jQuery );

var WolfThemeParams =  WolfThemeParams || {},
	ui = ui || {};


;( function( $ ) {

	"use strict";
	ui.init();

	/**
	 * FlexSlider
	 */
	if ( $.isFunction( $.flexslider ) ) {

		/* Post slider */
		$( '.format-gallery .flexslider, .wolf-gallery-slider' ).flexslider( {
			animation: 'fade',
			slideshow : true,
			smoothHeight : true
		} );

	}

	
} )( jQuery );