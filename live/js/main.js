(function($) {

	$(document).ready(function() {
		pageContentFitWindowHeight();
		initTrackListener();

		(function() {
			$('.wrap-home-block .home-block').matchHeight({
				byRow: false
			});
			$('.page4  .holder-item .btn-red p').matchHeight({
				byRow: false
			});
			$('.page5 .block-status .info-block').matchHeight({
				byRow: false
			});
			// $('.page7 .heading-block').matchHeight({
			// 	byRow: false
			// });
			// $('.page7 .user-name').matchHeight({
			// 	byRow: false
			// });
		}());

		
		
		$('#counter-next-part').backward_timer({
			seconds: 46,
			format: 's%'
		});
		$('#counter-bet').backward_timer({
			seconds: 36,
			format: 's%'
		});
			$('#counter-next-part').backward_timer('start');
		$('#counter-bet').backward_timer('start');

		$('.jqui-select').selectmenu();
	});

	function pageContentFitWindowHeight() {
		var contentHolder = $('#main');
		var windowElemHeight = $(window).height();
		var headerElemHeight = $('header.site-header').outerHeight();
		var footerElemHeight = $('footer.site-footer').outerHeight();
		var calculatedHeight = 0;

		if (!contentHolder.length) { return; }

		calculatedHeight = windowElemHeight - (headerElemHeight + footerElemHeight);

		contentHolder.css('min-height', calculatedHeight + 'px');
	}

	
	
})(jQuery);