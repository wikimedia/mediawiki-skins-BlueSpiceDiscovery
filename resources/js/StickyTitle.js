( function( mw, $, d, undefined ){

	if( !$( 'body' ).hasClass('ns-special') ) {
		$( window ).scroll( function() {
			var top = $( '#title-section' ).offset().top;
			var windowTop = $(this).scrollTop();

			if ( windowTop > top && window.innerWidth >= 767 ) {
				var titleWidth = $( '#main' ).innerWidth();
				$( '#title-line' ).addClass( 'title-fixed' );
				$( '#title-line' ).width( titleWidth );
			} else {
				$( '#title-line' ).removeClass( 'title-fixed' );
				$( '#title-line' ).width( '100%' );
			}
		});
	}

} )( mediaWiki, jQuery, document );