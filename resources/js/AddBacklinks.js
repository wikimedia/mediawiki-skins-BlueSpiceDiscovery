( function( mw, $, d, undefined ){
	$( d ).on( 'click', '.ns-special #content a', function ( e ) {
		if ( e.currentTarget.title === mw.config.get( 'wgPageName' ) ) {
			return;
		}
		e.preventDefault();
		var url = e.currentTarget.href;
		window.location.href = url + '&backTo=' + mw.config.get( 'wgPageName' );
	} );

} )( mediaWiki, jQuery, document );
