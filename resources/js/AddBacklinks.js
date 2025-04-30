( function ( mw, $, d ) {
	$( d ).on( 'click', '.ns-special #content a', function ( e ) {
		if ( e.currentTarget.title === mw.config.get( 'wgPageName' ) ) {
			return;
		}
		const href = e.currentTarget.href;
		if ( href.length === 0 ) {
			return;
		}
		if ( href.includes( 'returnto' ) ) {
			return;
		}
		if ( href.includes( mw.config.get( 'wgPageName' ) ) ) {
			return;
		}
		const url = e.currentTarget.href;
		const backTo = 'backTo=' + mw.util.rawurlencode( mw.util.rawurlencode( mw.config.get( 'wgPageName' ) ) );
		let connector = '?';
		if ( url.indexOf( '?' ) > 0 ) {
			connector = '&';
		}
		this.href = url + connector + backTo;
	} );

}( mediaWiki, jQuery, document ) );
