(function( $ ) {
	'use strict';

	$.each( wp_relevant_ads_params.dom_ads, function( dom, ad_obj ) {

		$.each( ad_obj, function( key, value ) {

			var manipulation = value['position'];
			var ad = value['ad'];

			if ( ! $(dom).length ) {
				return true; //continue
			}

			if ( 'append' == manipulation ) {
				$( dom ).append( ad );
			} else if ( 'prepend' == manipulation ) {
				$( dom ).prepend( ad );
			} else if ( 'before' == manipulation ) {
				$( dom ).before( ad );
			} else if ( 'after' == manipulation ) {
				$( dom ).after( ad );
			}

		});

	});

	if ( $('.wp_relevant_ads_ad').length ) {

		// wrap Ads if wrapper is provided
		var wrapper = wp_relevant_ads_params.ads_wrapper;

		if ( wrapper ) {

			wrapper = wp_relevant_ads_params.ads_wrapper.replace( '_AD_', '' );

			var terms = wp_relevant_ads_params.ads_terms;

			$.each( terms, function( i, term ) {

				var wrap_ads = false;

				if ( 'undefined' !== typeof wp_relevant_ads_params.terms_meta[ term.term_id ] && wp_relevant_ads_params.terms_meta[ term.term_id ]['wrap_ads'] ) {
					wrap_ads = true;
				}

				// check if ads should be wrapped
				if ( wrap_ads ) {

					var title = wp_relevant_ads_params.ads_wrapper_title;

					if ( title ) {
						title = wp_relevant_ads_params.ads_wrapper_title.replace( '_TITLE_', term.description );
						console.log('title = ' + title);
					}

					// wrap Ads by their category and prepend the title if provided
					$( '.wp_rel_cat_' + term.slug ).wrapAll('<span class="wp-relevant-ads-block block-'+term.slug+'">').wrapAll( wrapper );

					if ( title ) {
						$( '.wp_rel_cat_' + term.slug + ':first' ).before( title );
					}

				}

			});

		}

	}

	if ( $('.widget_wp_relevant_ads_ad').length ) {
		$('.widget_wp_relevant_ads_ad .wp_relevant_ads.no_content').closest('.widget_wp_relevant_ads_ad').hide();
	}

})( jQuery );