bs.util.registerNamespace( 'bs.skin.ui' );

bs.skin.ui.NamespaceTreePanel = function ( cfg ) {
	cfg = cfg || {};
	cfg.namespaceId = cfg.namespaceId || 0;
	const sessionKey = 'namespace-tree-ns-' + cfg.namespaceId;
	cfg.store = new OOJSPlus.ui.data.store.NamespaceTreeStore( {
		path: 'mws/v1/title-tree-store',
		sessionCacheKey: sessionKey,
		filter: [
			{
				operator: 'eq',
				value: cfg.namespaceId,
				property: 'namespace',
				type: 'numeric'
			}
		]
	} );

	this.namespaceId = cfg.namespaceId;
	this.sessionKey = sessionKey;
	bs.skin.ui.NamespaceTreePanel.super.call( this, cfg );
	this.setupSearch();
	$( document ).on( 'click', '#navigation-tree-search', ( event ) => {
		$( event.target ).toggleClass( 'active' );
		$( event.target ).attr( 'aria-pressed', $( event.target ).attr( 'aria-pressed' ) !== 'true' );
		this.searchWidget.toggle( !this.searchWidget.isVisible() );
		if ( this.searchWidget.isVisible() ) {
			this.searchInput.focus();
		} else {
			this.searchInput.setValue( '' );
		}
	} );
};

OO.inheritClass( bs.skin.ui.NamespaceTreePanel, OOJSPlus.ui.panel.NavigationTreePanel );

bs.skin.ui.NamespaceTreePanel.prototype.setupSearch = function () {
	this.$searchCnt = $( '<div>' ).addClass(
		'bs-discovery-nav-tree-search-cnt' );

	this.searchInput = new OO.ui.SearchInputWidget();
	this.searchInput.connect( this, {
		change: 'onSearchInput'
	} );
	this.searchWidget = new OO.ui.FieldLayout( this.searchInput, {
		label: mw.message( 'bs-discovery-namespace-tree-search-label' ).text(),
		align: 'top'
	} );
	this.$searchCnt.append( this.searchWidget.$element );
	this.$element.prepend( this.$searchCnt );
	this.searchWidget.toggle( false );
};

bs.skin.ui.NamespaceTreePanel.prototype.setupTree = function () {
	this.setupBacktoTop();

	this.$treeCnt = $( '<div>' ).addClass(
		'oojsplus-panel-nav-tree-cnt' );
	this.$element.append( this.$treeCnt );

	const rootNS = mw.config.get( 'wgCanonicalNamespace' );
	const pageName = mw.config.get( 'wgPageName' );
	const pageRoot = pageName.split( '/' );
	let root = pageRoot[ 0 ];

	if ( rootNS === '' ) {
		root = ':' + root;
	}

	const activePath = [];
	if ( pageRoot.length > 1 ) {
		let subpageName = root;
		activePath.push( subpageName );
		for ( let i = 1; i < pageRoot.length - 1; i++ ) {
			subpageName += '/' + pageRoot[ i ];
			activePath.push( subpageName );
		}
	}

	const session = require( 'mediawiki.storage' ).session;

	// Detect branch changes to prune stale expanded-nodes entries.
	// Only the root segment is stored — not the full path.
	const currentBranchRoot = activePath.length > 0 ? activePath[ 0 ] : null;
	const prevBranchRoot = session.get( this.sessionKey + '-branch-root' );
	if ( currentBranchRoot ) {
		session.set( this.sessionKey + '-branch-root', currentBranchRoot );
	}
	this.pruneStaleActivePath( prevBranchRoot, currentBranchRoot, session );

	const rootCacheKey = this.sessionKey + '-root';
	const cachedRoot = session.getObject( rootCacheKey );
	if ( cachedRoot ) {
		this.expandActivePathIntoTree( cachedRoot, activePath, session ).done( ( pages ) => {
			this.restoreExpandedNodes( pages, session ).done( () => {
				this.pages = pages;
				this.clearSkeleton();
				this.updatePages();
				setTimeout( () => this.scrollToActivePath( activePath, root ), 100 );
			} );
		} );
		return;
	}

	// No root cache yet — load everything from the server with the active path
	// pre-expanded so the user sees the right state immediately.
	this.store.loadNS( this.namespaceId, activePath ).done( ( data ) => {
		session.setObject( rootCacheKey, this.stripChildren( data ) );
		this.cacheNodeChildren( data, session );
		this.pages = data;
		this.clearSkeleton();
		this.updatePages();
		setTimeout( () => this.scrollToActivePath( activePath, root ), 100 );
	} );
};

bs.skin.ui.NamespaceTreePanel.prototype.setupBacktoTop = function () {
	this.$backToTop = $( '<button>' )
		.addClass( 'bs-discovery-tree-back-to-top bi-bs-back-to-top' )
		.text( mw.message( 'bs-discovery-back-to-top-text' ).text() )
		.hide()
		.on( 'click', () => {
			$( '#sb-pri' ).scrollTop( 0 );
		} );
	this.$element.prepend( this.$backToTop );

	$( '#sb-pri' ).off( 'scroll.ns-tree-backtotop' ).on( 'scroll.ns-tree-backtotop', () => {
		if ( $( '#sb-pri' ).scrollTop() > 300 ) {
			this.$backToTop.show();
		} else {
			this.$backToTop.hide();
		}
	} );
};

bs.skin.ui.NamespaceTreePanel.prototype.stripChildren = function ( pages ) {
	return Object.values( pages ).map( ( page ) => Object.assign( {}, page, { children: [] } ) );
};

bs.skin.ui.NamespaceTreePanel.prototype.pruneStaleActivePath = function ( prevBranchRoot, currentBranchRoot, session ) {
	if ( !prevBranchRoot || prevBranchRoot === currentBranchRoot ) {
		return;
	}

	const expandedKey = this.sessionKey + '-expanded-nodes';
	const expandedList = session.getObject( expandedKey ) || [];
	if ( expandedList.length === 0 ) {
		return;
	}

	const filtered = expandedList.filter( ( id ) => {
		const inOldBranch = id === prevBranchRoot || id.startsWith( prevBranchRoot + '/' );
		if ( !inOldBranch ) {
			return true;
		}
		const inNewBranch = currentBranchRoot && ( id === currentBranchRoot || id.startsWith( currentBranchRoot + '/' ) );
		return inNewBranch;
	} );
	session.setObject( expandedKey, filtered );
};

bs.skin.ui.NamespaceTreePanel.prototype.cacheNodeChildren = function ( pages, session ) {
	for ( const page of Object.values( pages ) ) {
		if ( page.children && page.children.length > 0 ) {
			this.store.setNodeCache( session, page.id, this.stripChildren( page.children ) );
			this.cacheNodeChildren( page.children, session );
		}
	}
};

bs.skin.ui.NamespaceTreePanel.prototype.expandActivePathIntoTree = function ( rootData, activePath, session ) {
	const dfd = $.Deferred();
	const pages = Object.values( rootData );
	this.expandNextPathNode( pages, activePath, 0, session ).done( () => {
		dfd.resolve( pages );
	} );
	return dfd.promise();
};

bs.skin.ui.NamespaceTreePanel.prototype.expandNextPathNode = function ( treeData, activePath, index, session ) {
	const dfd = $.Deferred();

	if ( index >= activePath.length ) {
		dfd.resolve();
		return dfd.promise();
	}

	const nodeId = activePath[ index ];
	const node = this.findNodeById( treeData, nodeId );
	if ( !node ) {
		// Node not present in this namespace tree — stop silently.
		dfd.resolve();
		return dfd.promise();
	}

	// getSubElements is wrapped → checks session cache before hitting the server.
	this.store.getSubElements( nodeId ).done( ( children ) => {
		// Ensure the node is not treated as a leaf so prepareData renders children.
		node.leaf = false;
		node.children = children;
		this.expandNextPathNode( children, activePath, index + 1, session ).done( () => {
			dfd.resolve();
		} );
	} ).fail( () => {
		// Don't block rendering if a single node fails to load.
		dfd.resolve();
	} );

	return dfd.promise();
};

bs.skin.ui.NamespaceTreePanel.prototype.findNodeById = function ( pages, id ) {
	for ( const page of Object.values( pages ) ) {
		if ( page.id === id ) {
			return page;
		}
	}
	return null;
};

bs.skin.ui.NamespaceTreePanel.prototype.restoreExpandedNodes = function ( pages, session ) {
	const expandedKey = this.sessionKey + '-expanded-nodes';
	const expandedNodeIds = session.getObject( expandedKey ) || [];

	if ( expandedNodeIds.length === 0 ) {
		return $.Deferred().resolve().promise();
	}

	const sorted = expandedNodeIds.slice().sort( ( a, b ) => a.split( '/' ).length - b.split( '/' ).length );

	return this.restoreNextExpandedNode( pages, sorted, 0 );
};

bs.skin.ui.NamespaceTreePanel.prototype.restoreNextExpandedNode = function ( rootPages, nodeIds, index ) {
	const dfd = $.Deferred();

	if ( index >= nodeIds.length ) {
		dfd.resolve();
		return dfd.promise();
	}

	this.restoreNodeByPath( rootPages, nodeIds[ index ] ).done( () => {
		this.restoreNextExpandedNode( rootPages, nodeIds, index + 1 ).done( () => {
			dfd.resolve();
		} );
	} );

	return dfd.promise();
};

bs.skin.ui.NamespaceTreePanel.prototype.restoreNodeByPath = function ( rootPages, nodeId ) {
	const parts = nodeId.split( '/' );
	const segments = [];
	let current = '';
	for ( const part of parts ) {
		current = current ? current + '/' + part : part;
		segments.push( current );
	}
	return this.restoreAlongPath( rootPages, segments, 0 );
};

bs.skin.ui.NamespaceTreePanel.prototype.restoreAlongPath = function ( pages, segments, index ) {
	const dfd = $.Deferred();

	if ( index >= segments.length ) {
		dfd.resolve();
		return dfd.promise();
	}

	const segmentId = segments[ index ];
	const node = this.findNodeById( pages, segmentId );

	if ( !node ) {
		// Node not in this namespace tree — stop.
		dfd.resolve();
		return dfd.promise();
	}

	const isLastSegment = index === segments.length - 1;

	if ( node.children && node.children.length > 0 ) {
		// Children already populated — continue down or finish.
		if ( isLastSegment ) {
			dfd.resolve();
		} else {
			this.restoreAlongPath( node.children, segments, index + 1 ).done( () => dfd.resolve() );
		}
		return dfd.promise();
	}

	// Load children from cache (store reads session cache first, falls back to server).
	this.store.getSubElements( segmentId ).done( ( children ) => {
		node.leaf = false;
		node.children = children;
		if ( isLastSegment ) {
			dfd.resolve();
		} else {
			this.restoreAlongPath( children, segments, index + 1 ).done( () => dfd.resolve() );
		}
	} ).fail( () => {
		dfd.resolve();
	} );

	return dfd.promise();
};

bs.skin.ui.NamespaceTreePanel.prototype.clearSkeleton = function () {
	if ( $( document ).find( '#' + this.skeletonID ) ) {
		$( '#' + this.skeletonID ).empty();
	}
};

bs.skin.ui.NamespaceTreePanel.prototype.updatePages = function () {
	this.$treeCnt.children().remove();

	const pageTree = new OOJSPlus.ui.data.NavigationTree( {
		style: {
			IconExpand: 'next',
			IconCollapse: 'expand'
		},
		data: this.pages,
		allowDeletions: false,
		allowAdditions: false,
		store: this.store,
		includeRedirect: false
	} );
	this.$treeCnt.append( pageTree.$element );

	// Track expand/collapse by patching expandNode/collapseNode on the tree.
	const session = require( 'mediawiki.storage' ).session;
	const expandedKey = this.sessionKey + '-expanded-nodes';

	const originalExpand = pageTree.expandNode.bind( pageTree );
	pageTree.expandNode = ( name ) => {
		const expandedList = session.getObject( expandedKey ) || [];
		if ( !expandedList.includes( name ) ) {
			expandedList.push( name );
			session.setObject( expandedKey, expandedList );
		}
		originalExpand( name );
	};

	const originalCollapse = pageTree.collapseNode.bind( pageTree );
	pageTree.collapseNode = ( name ) => {
		const filtered = ( session.getObject( expandedKey ) || [] ).filter(
			( id ) => id !== name && !id.startsWith( name + '/' )
		);
		session.setObject( expandedKey, filtered );
		originalCollapse( name );
	};
};

bs.skin.ui.NamespaceTreePanel.prototype.scrollToActivePath = function ( activePath, root ) {
	// For top-level pages (empty activePath), scroll to the root page
	const targetElement = activePath && activePath.length > 0 ? activePath[ activePath.length - 1 ] : root;

	if ( !targetElement ) {
		return;
	}

	const self = this;
	const treeCnt = document.querySelector( '#sb-pri' );
	if ( !treeCnt ) {
		return;
	}

	// Multiple requestAnimationFrame to ensure rendering is complete
	requestAnimationFrame( () => {
		requestAnimationFrame( () => {
			requestAnimationFrame( () => {
				const $element = self.$treeCnt.find( 'li.oojs-ui-data-tree-item[data-name="' + targetElement.replace( /"/g, '&quot;' ) + '"]' );

				if ( $element.length > 0 && $element[ 0 ].offsetParent !== null ) {
					const rect = $element[ 0 ].getBoundingClientRect();
					const containerRect = treeCnt.getBoundingClientRect();

					const relativeTop = rect.top - containerRect.top + treeCnt.scrollTop;
					const itemHeight = rect.height;
					const containerHeight = treeCnt.clientHeight;

					// Only scroll if item is outside visible area
					if ( relativeTop < treeCnt.scrollTop || relativeTop + itemHeight > treeCnt.scrollTop + containerHeight ) {
						treeCnt.scrollTop = relativeTop - ( containerHeight / 2 ) + ( itemHeight / 2 );
					}
				}
			} );
		} );
	} );
};

bs.skin.ui.NamespaceTreePanel.prototype.onSearchInput = function () {
	this.$treeCnt.children().remove();
	this.searchInput.$input.addClass( 'oo-ui-pendingElement-pending' );
	const searchString = this.searchInput.getValue();
	if ( searchString === '' ) {
		this.searchInput.$input.removeClass( 'oo-ui-pendingElement-pending' );
		this.setupTree();
		return;
	}
	this.store.queryPagesInNS( this.namespaceId, searchString ).done( ( data ) => {
		this.pages = data;
		this.updatePages();
		this.searchInput.$input.removeClass( 'oo-ui-pendingElement-pending' );
	} );
};
