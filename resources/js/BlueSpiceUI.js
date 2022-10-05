( function( d, $, mw ) {

	$( function(e) {
		$( '#sb-tgl-pri' ).removeAttr( 'disabled' );
		var $sbPrimaryToggle = $( '.sb-toggle[aria-controls=sb-pri-cnt]' );
		var $sbPrimary = $( '#sb-pri-cnt' );
		if ( $( window ).width() >= 1400 && discovery_cookie.get( 'sb-pri-cnt' ) != 'false' ) {
			discovery_cookie.set( 'sb-pri-cnt', 'true' );
			$sbPrimary.addClass( 'show' );
			$sbPrimaryToggle.attr( 'title',
				mw.message( 'bs-discovery-sidebar-primary-toggle-hide-title' ).text()
			);
			$sbPrimaryToggle.attr( 'aria-label',
				mw.message( 'bs-discovery-sidebar-primary-toggle-hide-aria-label' ).text()
			);
			$sbPrimaryToggle.attr( 'aria-expanded', 'true' );
		}
		if ( $( window ).width() >= 1400 && $( '#book-navigation-panel.active').length ) {
			discovery_cookie.set( 'sb-pri-cnt', 'true' );
			$sbPrimary.addClass( 'show' );
			$sbPrimaryToggle.attr( 'title',
				mw.message( 'bs-discovery-sidebar-primary-toggle-hide-title' ).text()
			);
			$sbPrimaryToggle.attr( 'aria-label',
				mw.message( 'bs-discovery-sidebar-primary-toggle-hide-aria-label' ).text()
			);
			$sbPrimaryToggle.attr( 'aria-expanded', 'true' );
		}
		if ( $( window ).width() < 1400 ) {
			$sidebars = $( '.sb-toggle[aria-expanded=true]' );
			for ( var i = 0; i < $sidebars.length; i++ ) {
				$sidebars[i].click();
			}
		}
		if( $( '#sb-sec-cnt.show' ).length ) {
			$( '#back-to-top' ).addClass( 'collapsed' );
		}

	});

	$( d ).on( 'click', '.sb-toggle', function( e ) {
		// TODO: This has to be improved. It will only work as long
		// bootstrap scripts run before this script runs
		e.preventDefault();
		e.stopPropagation();
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
				if ( $( window ).width() < 1400 ) {
					$( 'body ').css( 'overflow', '' );
				}
				$( '.sb-toggle[aria-controls='+controls+']' ).attr(
					'title',
					mw.message( 'bs-discovery-sidebar-'+sidebarMap+'-toggle-show-title' ).text()
				);
				$( '.sb-toggle[aria-controls='+controls+']' ).attr(
					'aria-label',
					mw.message( 'bs-discovery-sidebar-'+sidebarMap+'-toggle-show-aria-label' ).text()
				);

				discovery_cookie.set( controls, 'false' );
				$( this ).attr( 'aria-expanded', 'false' );
				$( '#' + controls ).removeClass( 'show' );
				if ( controls === 'sb-sec-cnt' ) {
					$( '#back-to-top' ).removeClass( 'collapsed' );
				}
			}
			if ( expanded === 'false' ) {
				if ( $( window ).width() < 1400 ) {
					$sidebar = $( '.sb-toggle[aria-expanded=true]' );
					$sidebar.click();
					$( 'body ').css( 'overflow', 'hidden' );
				}
				$( '.sb-toggle[aria-controls='+controls+']' ).attr(
					'title',
					mw.message( 'bs-discovery-sidebar-'+sidebarMap+'-toggle-hide-title' ).text()
				);
				$( '.sb-toggle[aria-controls='+controls+']' ).attr(
					'aria-label',
					mw.message( 'bs-discovery-sidebar-'+sidebarMap+'-toggle-hide-aria-label' ).text()
				);
				discovery_cookie.set( controls, 'true' );
				$( '#' + controls ).addClass( 'show' );
				$( this ).attr( 'aria-expanded', 'true' );
				if ( controls === 'sb-sec-cnt' ) {
					$( '#back-to-top' ).addClass( 'collapsed');
				}
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

		// Hide sidebars
		var toggleBtn = $( '.sb-toggle' );
		for ( var i = 0; i < toggleBtn.length; i ++) {
			if ( $( toggleBtn[i] ).attr( 'aria-controls' ) === null ) {
				continue;
			}

			if ( $( toggleBtn ).attr( 'aria-expanded' ) === null ) {
				continue;
			}

			var controls = $( toggleBtn[i] ).attr( 'aria-controls' );
			var sidebarMap = 'undefined';
			if ( controls === 'sb-pri-cnt' ) {
				sidebarMap = 'primary';
			}
			else if ( controls === 'sb-sec-cnt' ) {
				sidebarMap = 'secondary';
			}
			else {
				continue;
			}

			var expanded = $( toggleBtn[i] ).attr( 'aria-expanded' );
			if ( expanded === 'true' ) {
				$( toggleBtn[i] ).click();
			}
		}
	} );

	$( d ).on( 'click', '#sb-pri-cnt, #sb-sec-cnt', function( e ) {
		if ( $( window ).width() < 1400 ) {
			$sidebars = $( '.sb-toggle[aria-expanded=true]' );
			for ( var i = 0; i < $sidebars.length; i++ ) {
				$sidebars[i].click();
			}
		}
	});

})( document, jQuery, mediaWiki );