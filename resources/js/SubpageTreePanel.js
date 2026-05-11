$( () => {
	const $subpageTreeCnt = $( '#subpage-tree' );

	const subPageTreePanel = new OOJSPlus.ui.panel.NavigationTreePanel( {
		path: 'mws/v1/title-tree-store',
		skeletonID: 'bs-skin-tree-skeleton'
	} );
	$subpageTreeCnt.append( subPageTreePanel.$element );
} );
