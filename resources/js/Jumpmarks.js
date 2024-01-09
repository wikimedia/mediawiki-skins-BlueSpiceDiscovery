( function( mw, $, d ){

	$( d ).ready( function(){
		var hash = window.location.hash;
		if ( hash !== '' ) {
			var jumpmark = getJumpmarkEl( hash );
			if ( !jumpmark ) {
				return;
			}

			var position = getPosition( jumpmark );
			$( 'body, html').animate( {
					scrollTop: position
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
				var jumpmark = getJumpmarkEl( hash );
				if ( !jumpmark ) {
					return;
				}

				var position = getPosition( jumpmark );
				if ( position === 0 ) {
					return;
				}

				$( 'body,html' ).animate( {
					scrollTop: position
				} );
			}
		});
	});

	function getJumpmarkEl( hash ) {
		// Strip the leading # to get the id
		var id = hash.replace( '#', '' ),
			urldecodedId = decodeURIComponent( id ),
			element = d.getElementById( urldecodedId );

		if ( !element ) {
			// MediaWiki may use additional anchors
			id = mw.util.rawurlencode( urldecodedId ).replace( /%/g, '.' );
			element = d.getElementById( id );
		}
		return element;
	}

	function getPosition( jumpmark ) {
		var offset =
				// The maybe fixed title
				$( '#title-line' ).height() +
				// The surely fixed header menu
				$( '#nb-pri' ).height(),
			$jumpmark = $( jumpmark ),
			$heading = $jumpmark.closest( 'h1,h2,h3,h4,h5,h6' ),
			postion = $jumpmark.offset().top;

		if ( $heading.length === 1  ) {
			postion -= $heading.height();
		}

		return postion - offset;
	}

} )( mediaWiki, jQuery, document );
