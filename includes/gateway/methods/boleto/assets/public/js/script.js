(function( $ ) {
	'use strict';

	$( function() {
		// Store the installment options.
		$.data( document.body, 'z4money_credit_installments', $( '#z4money-credit-payment-form #z4money-installments' ).html() );

		// Add jQuery.Payment support for Elo and Aura.
		if ( $.payment.cards ) {
			var cards = [];

			$.each( $.payment.cards, function( index, val ) {
				cards.push( val.type );
			});

			if ( typeof $.payment.cards[0].pattern === 'undefined' ) {
				if ( -1 === $.inArray( 'aura', cards ) ) {
					$.payment.cards.unshift({
						type: 'aura',
						patterns: [5078],
						format: /(\d{1,6})(\d{1,2})?(\d{1,11})?/,
						length: [19],
						cvcLength: [3],
						luhn: true
					});
				}
			} else {
				if ( -1 === $.inArray( 'elo', cards ) ) {
					$.payment.cards.push({
						type: 'elo',
						pattern: /^(636[2-3])/,
						format: /(\d{1,4})/g,
						length: [16],
						cvcLength: [3],
						luhn: true
					});
				}

				if ( -1 === $.inArray( 'aura', cards ) ) {
					$.payment.cards.unshift({
						type: 'aura',
						pattern: /^5078/,
						format: /(\d{1,6})(\d{1,2})?(\d{1,11})?/,
						length: [19],
						cvcLength: [3],
						luhn: true
					});
				}				
			}
		}

		/**
		 * Set the installment fields.
		 *
		 * @param {String} card
		 */
		function setInstallmentsFields( card ) {
			var installments = $( '#z4money-credit-payment-form #z4money-installments' );

			$( '#z4money-credit-payment-form #z4money-installments' ).empty();
			$( '#z4money-credit-payment-form #z4money-installments' ).prepend( $.data( document.body, 'z4money_credit_installments' ) );

			if ( 'discover' === card ) {
				$( 'option', installments ).not( '.z4money-at-sight' ).remove();
			}
		}

		// Set on update the checkout fields.
		$( document.body ).on( 'ajaxComplete', function() {
			$.data( document.body, 'z4money_credit_installments', $( '#z4money-credit-payment-form #z4money-installments' ).html() );
			setInstallmentsFields( $( 'body #z4money-credit-payment-form #z4money-card-brand option' ).first().val() );
		});

		// Set on change the card brand.
		$( document.body ).on( 'change', '#z4money-credit-payment-form #z4money-card-number', function() {
			setInstallmentsFields( $.payment.cardType( $( this ).val() ) );
		});

		// Empty all card fields.
		$( document.body ).on( 'checkout_error', function() {
			$( 'body .z4money-payment-form input' ).val( '' );
		});
	});

}( jQuery ));
