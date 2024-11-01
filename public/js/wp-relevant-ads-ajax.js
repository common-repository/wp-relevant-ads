jQuery( function( $ ) {

	var clicks = 0;

	$( document ).on( 'click', '.wp_relevant_ads_ad', function( e ){

		if ( clicks ) {
			return;
		}

		$.ajax( {
			 url: wp_relevant_ads_params.ajax_url, // URL to the local file
			 type: 'POST', // POST or GET
			 data: {
				action      : 'wp_relevant_ads_count_clicks',
				wp_taxad_id : $(this).data('ad-id'),
				_ajax_nonce : wp_relevant_ads_params.nonce
			 },
			 success: function( data, status ) {
				// count clicks to avoid duplicates
				if ( data.clicks === 1 ) {
					clicks++;
				}

			 },
			 error: function( request, status, error ) {
				console.log( status + ' - ' + error );
			 }
		} );
		return true;
	});

});

