$( () => {
	const $gaBtn = $( '#ga-btn' );
	const $gaMenu = $( '#ga-btn-menu' );

	if ( !$gaBtn.length ) {
		return;
	}

	$gaBtn.one( 'click', () => {

		const api = new mw.Rest();
		api.get( '/bluespice/discovery/global-actions', {
			title: mw.config.get( 'wgRelevantPageName' ) || mw.config.get( 'wgPageName' )
		} ).then( ( response ) => {
			$gaMenu.html( response.html );
		} );
	} );
} );
