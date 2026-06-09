( function ( d, $, mw ) {

	$( ( e ) => { // eslint-disable-line no-unused-vars
		const EDIT_LABEL_CLASS = 'breadcrumb-label-action-edit';
		const SELF_LINK_ATTR = 'data-breadcrumb-self-link-converted';

		const isEditMode = () => {
			const queryParams = new URLSearchParams( window.location.search );
			return queryParams.get( 'veaction' ) === 'edit' ||
				d.body.classList.contains( 've-activated' );
		};

		const getLeafNodeElement = () => {
			const $leafItem = $( '#breadcrumbs .breadcrumb-item' ).not( '.root-node' ).last();
			if ( !$leafItem.length ) {
				return null;
			}
			return $leafItem.children( 'a, span' ).first();
		};

		const makeLeafClickable = () => {
			const $leafNode = getLeafNodeElement();
			if ( !$leafNode || !$leafNode.length || $leafNode.prop( 'tagName' ) !== 'SPAN' ) {
				return;
			}

			const title = mw.config.get( 'wgRelevantPageName' ) || mw.config.get( 'wgPageName' );
			const href = mw.util.getUrl( title );
			const $link = $( '<a>' )
				.attr( 'class', $leafNode.attr( 'class' ) || '' )
				.attr( SELF_LINK_ATTR, '1' )
				.attr( 'href', href )
				.html( $leafNode.html() );

			$leafNode.replaceWith( $link );
		};

		const makeLeafNonClickable = () => {
			const $leafNode = getLeafNodeElement();
			if ( !$leafNode || !$leafNode.length ||
				$leafNode.prop( 'tagName' ) !== 'A' ||
				$leafNode.attr( SELF_LINK_ATTR ) !== '1' ) {
				return;
			}

			const $span = $( '<span>' )
				.attr( 'class', $leafNode.attr( 'class' ) || '' )
				.html( $leafNode.html() );

			$leafNode.replaceWith( $span );
		};

		const ensureEditChip = () => {
			const $labels = $( '.breadcrumb-labels' );
			if ( !$labels.length || $labels.find( '.' + EDIT_LABEL_CLASS ).length ) {
				return;
			}

			// The following classes are used here:
			// * badge
			// * bg-secondary
			// * breadcrumb-label
			// * breadcrumb-label-action-edit
			$labels.append(
				$( '<li>' )
					.addClass( 'badge bg-secondary breadcrumb-label ' + EDIT_LABEL_CLASS )
					.text( mw.msg( 'bs-discovery-breadcrumb-label-action-edit' ) )
			);
		};

		const removeEditChip = () => {
			$( '.breadcrumb-labels .' + EDIT_LABEL_CLASS ).remove();
		};

		const syncBreadcrumbEditState = () => {
			if ( isEditMode() ) {
				ensureEditChip();
				makeLeafClickable();
				return;
			}

			removeEditChip();
			makeLeafNonClickable();
		};

		const dropdownToggles = document.getElementsByClassName( 'breadcrumb-nav-subpages' );

		Array.from( dropdownToggles ).forEach( ( toggle ) => {
			toggle.addEventListener( 'show.bs.dropdown', function () {
				const $breadcrumbItem = $( this ).parent( '.breadcrumb-item' );
				if ( $breadcrumbItem.data( 'loaded' ) === true ) {
					return;
				}
				const path = $( this ).data( 'bs-path' );
				if ( !path ) {
					return;
				}

				const $dropdownMenu = $breadcrumbItem.find( '.dropdown-menu > ul' ).first();

				mw.loader.using( 'mediawiki.api' ).done( () => {
					const api = new mw.Api();
					api.abort();
					api.get( {
						format: 'json',
						action: 'bs-wikisubpage-treestore',
						node: path,
						limit: '-1'
					} )
						.done( ( response ) => {
							for ( let i = 0; i < response.children.length; i++ ) {
								$dropdownMenu.append( '<li>' + response.children[ i ].page_link + '</li>' );
							}
							$breadcrumbItem.data( 'loaded', true );
							const $dropdownItems = $dropdownMenu.find( 'a' );
							for ( let x = 0; x < $dropdownItems.length; x++ ) {
								$( $dropdownItems[ x ] ).addClass( 'dropdown-item' );
							}
						} );
				} );
			} );
		} );

		syncBreadcrumbEditState();

		if ( mw.hook ) {
			mw.hook( 've.activationComplete' ).add( syncBreadcrumbEditState );
			mw.hook( 've.deactivationComplete' ).add( syncBreadcrumbEditState );
		}

		window.addEventListener( 'popstate', syncBreadcrumbEditState );

		new MutationObserver( syncBreadcrumbEditState )
			.observe( d.body, { attributes: true, attributeFilter: [ 'class' ] } );
	} );

}( document, jQuery, mediaWiki ) );
