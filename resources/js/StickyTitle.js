( function( mw, $, d, undefined ){

	if( !$( 'body' ).hasClass('ns-special') ) {
		$( window ).scroll( function() {
			var top = $( '#title-section' ).offset().top;
			var windowTop = $(this).scrollTop();

			if ( windowTop > top && window.innerWidth >= 767 ) {
				var titleWidth = $( '#main' ).innerWidth();
				$( '#title-line' ).addClass( 'title-fixed' );
				$( '#title-line' ).width( titleWidth );
				if ( $( '.ve-init-target-visual > .ve-ui-toolbar-floating > .oo-ui-toolbar-bar' ) ||
					$( '.ve-init-target-source > .ve-ui-toolbar-floating > .oo-ui-toolbar-bar ') ) {
						var topHeight = $( '#title-line' ).height() + $( '#nb-pri' ).height();
						$( '.ve-init-target-visual > .ve-ui-toolbar-floating > .oo-ui-toolbar-bar' ).css( 'top', topHeight );
						$( '.ve-init-target-source > .ve-ui-toolbar-floating > .oo-ui-toolbar-bar ').css( 'top', topHeight );
					}
			} else {
				$( '#title-line' ).removeClass( 'title-fixed' );
				$( '#title-line' ).width( '100%' );
				$( '.ve-init-target-visual .oo-ui-toolbar-bar' ).css( 'top', '' );
				$( '.ve-init-target-source .oo-ui-toolbar-bar ').css( 'top', '' );
			}
		});
	}

} )( mediaWiki, jQuery, document );