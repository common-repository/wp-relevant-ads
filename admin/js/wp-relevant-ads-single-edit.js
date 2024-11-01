(function( $ ) {
	'use strict';

	$(function() {

		/* Validate */

		var validator = $('#post').validate({
			errorPlacement: function(error, element) {
				//This bit here is going to make sure the error label is placed after the button.
				var possibleButton = element.siblings("button");
				if (possibleButton.length == 1) {
					element = possibleButton;
				}
				element.after(error);
			},
			messages: {
				"_wp_relevant_ads_hooks[]": {
					 required: wp_relevant_ads_params.hook_required_text,
				},
				"_wp_relevant_ads_trigger_dom[]": {
					 required: wp_relevant_ads_params.dom_required_text,
				}
			},
			rules: {
				"_wp_relevant_ads_hooks[]": {
					required: function(element) {
						if ( 'hook' == $('select[name=_wp_relevant_ads_trigger_rule]').val() ) {
							if ( ! $(element).val() && ! $('#_wp_relevant_ads_hooks_custom').val() ) {
								return true;
							}
						}
						return false;
					}
				},
				"_wp_relevant_ads_trigger_dom": {
					required: function(element) {
						if ( 'dom' == $('select[name=_wp_relevant_ads_trigger_rule]').val() ) {
							return $(element).is(':empty');
						}
						return false;
					}
				},
				"_wp_relevant_ads_pre_sale_page": {
					required: function(element) {
						if ( $('#_wp_relevant_ads_call_action').prop('checked') ) {
							return ! $(element).val();
						}
						return false;
					}
				},
			},
			ignore: ".ignore",
			onfocusout : false,
			onkeyup : false,
			onclick : false
		});


		/* Dynamically build optgroup on dropdowns */

		var sel = $('select[name=_wp_relevant_ads_trigger_other_dom], select[name="_wp_relevant_ads_hooks[]"]');

		$( 'option', sel ).each(function() {

			if ( $(this).text().indexOf('_PARENT_') !== 0) {

				sel.find('optGroup').last().append( $(this) );

				var text = $(this).text();
				var value = $(this).val();

				$(this).text( text.substr( text.indexOf('|')+1 ) );
				$(this).val( value.substr( value.indexOf('|')+1 ) );

			} else {
				$('<optGroup/>').attr( 'label', $(this).text().substr(8) ).appendTo( $(this).parent() );
				$(this).remove();
			}

		});

		/* Multi-Select */

		var ad_tax_terms = wp_relevant_ads_params.ad_tax_terms;

		// map terms with taxonomies
		var tax_terms = [];

		if ( typeof ad_tax_terms !== 'undefined' && ad_tax_terms ) {

			$.each( ad_tax_terms, function( key, value ) {
				tax_terms[ key ] = value;
			});

		}

		var html = '';

		// dynamically build the taxonomies optgroup
		$( 'select.wp_relevant_ads_taxonomies' ).each( function() {

			var tax = $(this).attr('name');

			html = '<optgroup label="'+$(this).attr('label')+'">';
			$( 'option', this ).each( function() {

				var selected = '';
				var term = $(this).val().split('|');

				if ( $.inArray( term[1], tax_terms[ tax ] ) > -1 ) {
					selected = 'selected';
				}

				html += '<option value="'+$(this).val()+'" '+selected+'>'+$(this).text()+'</option>';
			} );
			html += '</optgroup>';

			$( 'select.wp_relevant_ads_multiple.wp_relevant_ads_terms' ).append( html );

			$( this ).parents('tr:first').remove();
		} );


		/* Trigger Rules */

		var ad_hooks = wp_relevant_ads_params.ad_hooks;

		$( 'select[name="_wp_relevant_ads_hooks[]"]' ).val( ad_hooks );

		$( '.wp_relevant_ads_trigger' ).parents('tr').hide();

		$( 'select[name=_wp_relevant_ads_trigger_rule]' ).change( function() {

			$('.wp_relevant_ads_trigger').removeClass('required');

			$( '.wp_relevant_ads_trigger' ).parents('tr').hide();
			$( '.wp_relevant_ads_trigger_'+$(this).val() ).parents('tr').fadeIn();

			$('.tip-show').hide();

			if ( 'widget' == $(this).val() ) {
				$('select[name=_wp_relevant_ads_display]').val('wp_relevant_ads_clear');
				$('select[name=_wp_relevant_ads_display]').closest('tr').hide();
			}

			$('select[name=_wp_relevant_ads_display]').closest('tr').show();

		});

		$( 'select[name=_wp_relevant_ads_trigger_rule]' ).trigger( 'change' );


		var selectorsValues = [];

		$('select[name=_wp_relevant_ads_trigger_other_dom] option').each( function() {
			selectorsValues.push( $(this).val() );
		});

		$( 'select[name=_wp_relevant_ads_trigger_other_dom]').change( function() {

			$( '#_wp_relevant_ads_trigger_dom' ).val( $(this).val() );

		});

		$('.wp_relevant_ads_trigger_dom').keyup( function() {

			if ( $.inArray( $(this).val(), selectorsValues ) < 0 ) {
				$( 'select[name=_wp_relevant_ads_trigger_other_dom] option:last-child').prop( 'selected', true );
			} else {
				$( 'select[name=_wp_relevant_ads_trigger_other_dom]').val( $(this).val() );

			}
		});

		/* Hooks */

		var ad_hooks = wp_relevant_ads_params.ad_hooks;
		$( 'select[name="_wp_relevant_ads_hooks[]"]' ).val( ad_hooks );

		/* Roles */

		var ad_roles = wp_relevant_ads_params.ad_roles;
		$( 'select[name="_wp_relevant_ads_roles[]"]' ).val( ad_roles );

		$( 'select.wp_relevant_ads_multiple.wp_relevant_ads_terms' ).select2( {
			placeholder: wp_relevant_ads_params.select_taxonomies,
			closeOnSelect: false,
			width: '35em',
		} );

		$( 'select.wp_relevant_ads_multiple.wp_relevant_ads_roles' ).select2( {
			placeholder: wp_relevant_ads_params.select_option,
			closeOnSelect: false,
			width: '35em',
		} );

		$( 'select.wp_relevant_ads_trigger_type' ).select2( {
			closeOnSelect: false,
			width: '15em',
		} );

		$( 'select.wp_relevant_ads_trigger_hook' ).select2( {
			placeholder: wp_relevant_ads_params.select_hooks,
			closeOnSelect: false,
			width: '35em',
		} );


		/* Expiry Date */

		$( '#datepicker-field' ).datepicker({
			dateFormat: wp_relevant_ads_params.date_format,
			changeMonth: true,
			changeYear: true,
			showButtonPanel: true

		});

		$( '.clear_expire_date' ).click( function () {
			$( 'input[name=_wp_relevant_ads_expire_date]' ).val('');
			return false;
		} );

	});

})( jQuery );