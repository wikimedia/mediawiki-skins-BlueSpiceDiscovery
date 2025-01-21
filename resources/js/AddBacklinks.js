( function( mw, $, d, undefined ){
	$( d ).on( 'click', '.ns-special #content a', function ( e ) {
		if ( e.currentTarget.title === mw.config.get( 'wgPageName' ) ) {
			return;
		}
		if ( e.currentTarget.href.length === 0 ) {
			return;
		}
		e.preventDefault();
		var url = e.currentTarget.href;
		var backTo = 'backTo=' + mw.config.get( 'wgPageName' );
		var connector = '?';
		if ( url.indexOf( '?' ) > 0 ) {
			connector = '&';
		}
		window.location.href = url + connector + backTo;
	} );

} )( mediaWiki, jQuery, document );
