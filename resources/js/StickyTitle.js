( function( mw, $, d, undefined ){

	if( !$( 'body' ).hasClass('ns-special') ) {
		$( window ).scroll( function() {
			var top = $( '#title-section' ).offset().top;
			var windowTop = $(this).scrollTop();

			if ( windowTop > top && window.innerWidth >= 767 ) {
				$( '#title-line' ).addClass( 'title-fixed' );
			} else {
				$( '#title-line' ).removeClass( 'title-fixed' );
			}
		});
	}

} )( mediaWiki, jQuery, document );