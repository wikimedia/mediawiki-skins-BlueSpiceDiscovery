( function( mw, $, d, undefined ){

	if( !$( 'body' ).hasClass('ns-special') ) {
		$( window ).scroll( function() {
			var top = $( '#title-section' ).offset().top;
			var windowTop = $(this).scrollTop();
			var $title = $( '#title-line' );

			if ( windowTop > top && window.innerWidth >= 767 ) {
				var titleWidth = $( '#main' ).innerWidth();
				$title.addClass( 'title-fixed' );
				$title.width( titleWidth );
			} else {
				$title.removeClass( 'title-fixed' );
				$title.width( '100%' );
			}

			if ( $( '.ve-init-target-visual' ).length || $( '.ve-init-target-source' ).length ) {
				var $floatingVisualVE = $( '.ve-init-target-visual > .ve-ui-toolbar-floating > .oo-ui-toolbar-bar' );
				var $floatingSourceVE = $( '.ve-init-target-source > .ve-ui-toolbar-floating > .oo-ui-toolbar-bar' );
				var $visualVE = $( '.ve-init-target-visual > .ve-ui-toolbar > .oo-ui-toolbar-bar' );
				var $sourceVE = $( '.ve-init-target-source > .ve-ui-toolbar > .oo-ui-toolbar-bar' );
				var topHeight = $( '#title-line' ).height() + $( '#nb-pri' ).height();

				if ( $title.hasClass( 'title-fixed' ) ) {
					if ( $( '.ve-init-target-visual > .ve-ui-toolbar-floating' ).length ) {
						$floatingVisualVE.css( 'top', topHeight );
						$floatingSourceVE.css( 'top', topHeight );
					} else {
						$visualVE.css( { 'top': topHeight, 'position': 'fixed' } );
						$sourceVE.css( { 'top': topHeight, 'position': 'fixed' } );
					}
				} else {
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

		$( d ).on( 'click', '.toc a', function ( e ) {
			var topHeight = $( '#title-line' ).height() + $( '#nb-pri' ).height();
			var target = $( this ).attr( 'href' );
			var id = $( target ).attr( 'id' );

			if ( !id ) {
				var parent = $( document.getElementById( target.substr(1) ) );
			} else {
				var parent = $( '#' + id );
			}

			$( 'body,html' ).animate( {
					scrollTop: parent.position().top - 1.75 * topHeight
			});
		});
	}

} )( mediaWiki, jQuery, document );