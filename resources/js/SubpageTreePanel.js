$( () => {
	const $subpageTreeCnt = $( '#subpage-tree' );

	const subPageTreePanel = new OOJSPlus.ui.panel.NavigationTreePanel( {
		path: 'mws/v1/title-tree-store',
		skeletonID: 'bs-skin-tree-skeleton'
	} );
	const originalUpdatePages = subPageTreePanel.updatePages.bind( subPageTreePanel );
	subPageTreePanel.updatePages = function () {
		originalUpdatePages();
		if ( this.$treeCnt && this.$treeCnt.children().length > 0 ) {
			this.pageTree.setItemActions( [ new StandardDialogs.ui.AddSubPageItemAction() ] );
		}
	};
	$subpageTreeCnt.append( subPageTreePanel.$element );
} );
