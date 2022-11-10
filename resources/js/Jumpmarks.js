( function( mw, $, d ){

	$( d ).ready( function(){
		var hash = window.location.hash;
		var topHeight = $( '#content' ).position().top;

		if ( hash !== '' ) {
			var jumpmarkId = hash.replace( '#', '' );

			var jumpmark = d.getElementById( jumpmarkId );
			if ( !jumpmark ) {
				return;
			}

			var position = $( jumpmark ).position().top;
			$( 'body, html').animate( {
					scrollTop: position - topHeight
				},
				100
			);
		};

		$( '#content' ).on( 'click',
			'#mw-content-text a:not( [id] ):not( [class] ),#mw-content-text map area',
			function( e ) {
			var hash = this.hash;

			if( this.pathname + this.search !== mw.util.getUrl() ) {
				return;
			}

			if ( hash !== '' ) {
				var jumpmarkId = hash.replace( '#', '' );

				var jumpmark = d.getElementById( jumpmarkId );
				if ( !jumpmark ) {
					return;
				}

				var position = $( jumpmark ).position().top;
				if ( position === 0 ) {
					return;
				}
				topHeight = $( '#content' ).position().top;

				$( 'body,html' ).animate( {
					scrollTop: position - topHeight
				} );
			}
		});
	});

} )( mediaWiki, jQuery, document );