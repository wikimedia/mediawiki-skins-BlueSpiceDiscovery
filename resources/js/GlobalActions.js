$( () => {
	const $gaBtn = $( '#ga-btn' );
	const $gaMenu = $( '#ga-btn-menu' );

	if ( !$gaBtn.length ) {
		return;
	}

	$gaBtn.one( 'click', () => {

		const api = new mw.Rest();
		api.get( '/bluespice/discovery/global-actions' ).then( ( response ) => {
			$gaMenu.html( response.html );
		} );
	} );
} );
