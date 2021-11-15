( function( d, $, mw ) {
	$( d ).on( 'click', '.sb-toggle', function( e ){

		// TODO: This has to be improved. It will only work as long
		// bootstrap scripts run before this script runs

		var controls = $( this ).attr( 'aria-controls' );
		var sidebarMap = 'undefined';
		if ( controls === 'sb-pri-cnt' ) {
			sidebarMap = 'primary';
		}
		if ( controls === 'sb-sec-cnt' ) {
			sidebarMap = 'secondary';
		}

		if ( sidebarMap != 'undefined' ) {
			var expanded = $( this ).attr( 'aria-expanded' );
			if ( expanded === 'true' ) {
				$( '.sb-toggle[aria-controls='+controls+']' ).attr(
					'title',
					mw.message( 'bs-discovery-sidebar-'+sidebarMap+'-toggle-hide-title' ).text()
				);
				$( '.sb-toggle[aria-controls='+controls+']' ).attr(
					'aria-label',
					mw.message( 'bs-discovery-sidebar-'+sidebarMap+'-toggle-hide-aria-label' ).text()
				);
				discovery_cookie.set( controls, 'true' );
			}
			if ( expanded === 'false' ) {
				$( '.sb-toggle[aria-controls='+controls+']' ).attr(
					'title',
					mw.message( 'bs-discovery-sidebar-'+sidebarMap+'-toggle-show-title' ).text()
				);
				$( '.sb-toggle[aria-controls='+controls+']' ).attr(
					'aria-label',
					mw.message( 'bs-discovery-sidebar-'+sidebarMap+'-toggle-show-aria-label' ).text()
				);
				discovery_cookie.set( controls, 'false' );
			}
		}
	});

	$( d ).on( 'click', '#full-screen-btn', function( e ){
		e.preventDefault();

		if ( $( this ).hasClass( 'fs-mode-enabled' ) ) {
			$( this ).removeClass( 'fs-mode-enabled' );
			$( this ).removeClass( 'bi-chevron-contract' );
			$( this ).addClass( 'bi-chevron-expand' );
			$( this ).attr(
				'title',
				mw.message( 'bs-discovery-navbar-full-screen-button-enable-title' ).text()
			);
			$( this ).attr(
				'aria-label',
				mw.message( 'bs-discovery-navbar-full-screen-button-enable-aria-label' ).text()
			);
		} else {
			$( this ).addClass( 'fs-mode-enabled' );
			$( this ).removeClass( 'bi-chevron-expand' );
			$( this ).addClass( 'bi-chevron-contract' );
			$( this ).attr(
				'title',
				mw.message( 'bs-discovery-navbar-full-screen-button-disable-title' ).text()
			);
			$( this ).attr(
				'aria-label',
				mw.message( 'bs-discovery-navbar-full-screen-button-disable-aria-label' ).text()
			);

		}

		if ( $( 'body' ).hasClass( 'fs-mode-enabled' ) ) {
			$( 'body' ).removeClass( 'fs-mode-enabled' );
			discovery_cookie.set( 'fsMode', 'false' );

			var classes = discovery_cookie.get( 'bodyClasses' );
			if ( classes ) {
				var classes_obj = JSON.parse( classes );
				if ( classes_obj.hasOwnProperty( 'fsMode' ) ) {
					delete classes_obj.fsMode;
					discovery_cookie.set(
						'bodyClasses',
						JSON.stringify( classes_obj )
					);
				}
			}
		} else {
			$sidebars = $( '.sb-toggle[aria-expanded=true]' );
			for ( var i = 0; i < $sidebars.length; i++ ) {
				$sidebars[i].click();
			}

			$( 'body' ).addClass( 'fs-mode-enabled' );
			discovery_cookie.set( 'fsMode', 'true' );

			var classes = discovery_cookie.get( 'bodyClasses' );
			if ( classes ) {
				var classes_obj = JSON.parse( classes );
				Object.assign( classes_obj, { 'fsMode': 'fs-mode-enabled' } );
				discovery_cookie.set(
					'bodyClasses',
					JSON.stringify( classes_obj )
				);
			}
		}
	} );
})( document, jQuery, mediaWiki );