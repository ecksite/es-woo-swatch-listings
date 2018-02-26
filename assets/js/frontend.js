!(function(w, $) {
	'use strict';

	/* DOM Ready */
	$(function() {

		//FUNKTIONIERT
		var oldsrc = '', oldsrcset = '';

		$('body')
			.on('mouseenter.es-woocommerce-swatches', '[data-es-woo-swatches-variation-image]', function(e) {
				e.preventDefault();
				var $thisEl = $(this),
					ImageUrl = $thisEl.data('es-woo-swatches-variation-image')
				;

				if(! ImageUrl || ImageUrl == '') return;

				$thisEl
					.closest('.product.has-post-thumbnail')
					.find('img.attachment-woocommerce_thumbnail')
					.attr('src', function(i, attr){
						oldsrc = attr; return ImageUrl;
					})
					.attr('srcset', function(i, attr){
						oldsrcset = attr; return ImageUrl;
					});

				$thisEl
					.find('.es-swatch');
					//.addClass('selected');
			})
			.on('mouseleave.es-woocommerce-swatches', '[data-es-woo-swatches-variation-image]', function(e) {
				e.preventDefault();
				var $thisEl = $(this);
				$thisEl
					.closest('.product.has-post-thumbnail')
					.find('img.attachment-woocommerce_thumbnail')
					.attr('src', oldsrc )
					.attr('srcset',oldsrcset);
				$thisEl
					.find('.es-swatch');
					//.removeClass('selected');

			});
  })
})(window, jQuery)
