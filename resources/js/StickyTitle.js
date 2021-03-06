( function( mw, $, d ){

	if( !$( 'body' ).hasClass('ns-special') && window.innerWidth >= 767  ) {
		$( window ).scroll( function() {
			var top = $( '#title-section' ).offset().top;
			var windowTop = $(this).scrollTop();
			var $title = $( '#title-line' );
			var $titleContent = $( '#title-line > div' );

			if ( windowTop >= top ) {
				$( '#title-section' ).css( 'padding-bottom', $( '#title-line' ).height() );
				$title.addClass( 'title-fixed' );

				var titleWidth = $( '#content' ).innerWidth();
				$titleContent.width( titleWidth );

			} else {
				$title.removeClass( 'title-fixed' );
				$titleContent.removeAttr( 'style' );
				$( '#title-section' ).removeAttr( 'style' );
			}

			if ( $( '.ve-init-target-visual' ).length || $( '.ve-init-target-source' ).length ) {
				var $floatingVisualVE = $( '#content .ve-init-target-visual >' +
					'.ve-ui-toolbar-floating > .oo-ui-toolbar-bar' );
				var $floatingSourceVE = $( '#content .ve-init-target-source >' +
					'.ve-ui-toolbar-floating > .oo-ui-toolbar-bar' );
				var $visualVE = $( '#content .ve-init-target-visual > .ve-ui-toolbar > .oo-ui-toolbar-bar' );
				var $sourceVE = $( '#content .ve-init-target-source > .ve-ui-toolbar > .oo-ui-toolbar-bar' );
				var topHeight = $( '#title-line' ).height() + $( '#nb-pri' ).height();

				if ( $title.hasClass( 'title-fixed' ) ) {
					$( '#content' ).css( 'padding-bottom', $( '#title-line' ).height() );
					if ( $( '.ve-init-target-visual > .ve-ui-toolbar-floating' ).length ) {
						$floatingVisualVE.css( 'top', topHeight );
						$floatingSourceVE.css( 'top', topHeight );
					} else {
						$visualVE.css( { 'top': topHeight, 'position': 'fixed' } );
						$sourceVE.css( { 'top': topHeight, 'position': 'fixed' } );
					}
				} else {
					$( '#content' ).removeAttr( 'style' );
					if ( $( '.ve-init-target-visual > .ve-ui-toolbar-floating' ).length ) {
						$floatingVisualVE.css( 'top', topHeight );
						$floatingSourceVE.css( 'top', topHeight );
					} else {
						$visualVE.removeAttr( "style" );
						$sourceVE.removeAttr( "style" );
					}
				}
			}
		});
	}

} )( mediaWiki, jQuery, document );