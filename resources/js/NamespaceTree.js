$( () => {
	require( './ui/NamespaceTreePanel.js' );
	const $namespaceTreeCnt = $( '#namespace-tree' );

	const namespaceId = mw.config.get( 'wgNamespaceNumber' );
	const wikiId = mw.config.get( 'wgWikiID' );
	const invalidatedNs = mw.config.get( 'bsgDiscoveryNsTreeInvalidateCache' );
	if ( Array.isArray( invalidatedNs ) && invalidatedNs.length > 0 ) {
		const session = require( 'mediawiki.storage' ).session;
		for ( const nsId of invalidatedNs ) {
			const prefix = wikiId + '-namespace-tree-ns-' + nsId;
			session.remove( prefix + '-root' );
			session.remove( prefix + '-nodes' );
			session.remove( prefix + '-expanded-nodes' );
			session.remove( prefix + '-branch-root' );
		}
	}

	const namespaceTreePanel = new bs.skin.ui.NamespaceTreePanel( {
		path: 'mws/v1/title-tree-store',
		skeletonID: 'bs-skin-tree-skeleton',
		namespaceId: namespaceId,
		wikiId: wikiId
	} );
	$namespaceTreeCnt.append( namespaceTreePanel.$element );
} );
