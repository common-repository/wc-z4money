(function( $ ) {
	'use strict';

	$( function() {
		
		var woopage = $( '#order_data.panel.woocommerce-order-data' ),
			z4order = $( 'input[value="Z4MONEY_ID"]' ).closest( 'tr' ).find( 'textarea' ).val();

		if ( undefined !== z4order && '' !== z4order && 1 === woopage.length ) {
			$( '<p class="woocommerce-order-data__meta order_number">Z4Money ID: <span>' + z4order + '</span></p>' )
				.insertAfter( $( woopage ).find( 'h2' ) );
		}

	});

}( jQuery ));