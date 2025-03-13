( function ( $ ) {
	// See https://github.com/hallowelt/mwstake-mediawiki-component-commonuserinterface/blob/master/tests/phpunit/TreeDataGeneratorTest.php#L32
	// and BlueSpiceDiscovery/src/Component/EnhancedSidebarTree.php

	// This script is responsible for setting the cookie. Functionality of the tree is done in
	// https://github.com/hallowelt/mwstake-mediawiki-component-commonuserinterface/blob/master/resources/tree/tree.js
	$( '.tree-component' ).on( 'click', '.mws-tree-expander', function () {
		const cntId = getContainerId( this );
		const cookieContent = getCookieContent( cntId );
		const expandedBeforeClick = $( this ).attr( 'aria-expanded' );
		const $items = $( this ).parents( 'li' );
		const path = makePath( $items );

		let newCookieContent = [];
		if ( expandedBeforeClick === 'false' ) {
			newCookieContent = addPath( path, cookieContent );
		} else {
			newCookieContent = removePath( path, cookieContent );
		}

		setCookieContent( cntId, newCookieContent );
	} );

	function getContainerId( clickEl ) {
		const cnt = $( clickEl ).parents( '.mws-tree-cnt.root ' ).first();
		const cntId = $( cnt ).attr( 'id' );

		return cntId;
	}

	function getCookieContent( cntId ) {
		let cookieContent = discovery_cookie.get( cntId );

		if ( !cookieContent ) {
			cookieContent = [];
		}

		return cookieContent;
	}

	function setCookieContent( cntId, cookieContent ) {
		discovery_cookie.set( cntId, cookieContent );
	}

	function makePath( $items ) {
		const paths = [];
		for ( let index = ( $items.length - 1 ); index >= 0; index-- ) {
			const item = $items[ index ];
			const id = $( item ).attr( 'id' );

			paths.push( id );
		}

		const path = paths.join( '/' );

		return path;
	}

	function addPath( path, cookieContent ) {
		const paths = removePath( path, cookieContent );
		paths.push( path );

		return paths;
	}

	function removePath( path, cookieContent ) {
		const paths = [];
		for ( let index = 0; index < cookieContent.length; index++ ) {
			const curPath = cookieContent[ index ];
			if ( curPath.slice( 0, path.length ) !== path ) {
				paths.push( curPath );
			}
		}

		return paths;
	}
}( jQuery ) );
