( function( mw, $, d, undefined ){
	var treeMenuContainer = $( '.tree-menu-cnt' );

	for ( var index = 0; index < treeMenuContainer.length; index++ ) {
		var data = $( treeMenuContainer[index] ).attr( 'data-tree' );

		var $panel = $( treeMenuContainer[index] ).parents( '.card' );
		var header = $panel.find( '.card-header' );
		var labelledby = $( header[0] ).attr( 'id' );

		var tree = new OOJSPlus.ui.data.Tree( {
			id: 'subpage-tee-menu',
			fixed: true,
			allowAdditions: false,
			allowDeletions: false,
			labelledby: labelledby,
			expanded: false,
			data: JSON.parse( data ),
			style: {
				IconCollapse: 'data-tree-collapse',
				IconExpand: 'data-tree-expand',
			}
		} );

		$( treeMenuContainer[index] ).append( tree.$element );
	}

} )( mediaWiki, jQuery, document );
