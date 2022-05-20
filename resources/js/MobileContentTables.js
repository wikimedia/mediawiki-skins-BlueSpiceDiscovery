( function( d, $, mw ) {

	$( document ).ready( function() {
		setResponsive();
	} );

	window.onresize = setResponsive;

	function setResponsive() {
		var doctables = document.getElementsByTagName( 'table' );
		if ( window.innerWidth < 1400 ) {
			for ( var i = 0; i < doctables.length; i++ ) {
				var headers = doctables[i].getElementsByTagName( 'th' );

				if ( headers.length > 0 ) {
					doctables[i].className += " responsive-table";
				}
				else {
					doctables[i].className += " responsive-gallery-table";
				}
			}

			if ( !$( 'div.responsive-table-scrollable' ).length ) {
				$( 'table.responsive-table' ).wrap( '<div class="responsive-table-scrollable"></div>' );
				$( 'table.responsive-gallery-table' ).wrap( '<div class="responsive-table-scrollable"></div>' );
			}
		} else {
			if ( $( 'div.responsive-table-scrollable' ).length ) {
				$( 'table.responsive-table' ).unwrap();
				$( 'table.responsive-gallery-table' ).unwrap();
			}
			for ( var i = 0; i < doctables.length; i++ ) {
				var headers = doctables[i].getElementsByTagName( 'th' );
				if ( headers.length > 0 ) {
					$(doctables[i]).removeClass( "responsive-table" );
				}
				else {
					$(doctables[i]).removeClass( "responsive-gallery-table" );
				}
			}
		}
	}

} )( document, jQuery, mediaWiki );