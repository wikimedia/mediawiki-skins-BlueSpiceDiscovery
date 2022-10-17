( function( mw, $, d ){

	if( !$( 'body' ).hasClass('ns-special') && window.innerWidth >= 767  ) {
		$( window ).scroll( function() {
			var top = $( '#wrapper' ).offset().top;
			var windowTop = $(this).scrollTop();
			var $title = $( '#title-line' );
			var $titleContent = $( '#title-line > div' );

			if ( windowTop >= top ) {
				$( '#title-section' ).css( 'padding-top', $title.height() );
				$title.addClass( 'title-fixed' );

				var titleWidth = $( '#main' ).outerWidth();
				$titleContent.innerWidth( titleWidth );

			} else {
				$title.removeClass( 'title-fixed' );
				$titleContent.removeAttr( 'style' );
				$( '#title-section' ).removeAttr( 'style' );
			}

			if ( $( '.ve-init-target-visual' ).length || $( '.ve-init-target-source' ).length ) {
				var $toolbar =  $( '#content .ve-init-target >' +
					'.ve-ui-toolbar > .oo-ui-toolbar-bar' );
				var topHeight = $title.height() + top;

				if ( $title.hasClass( 'title-fixed' ) ) {
					$toolbar.css( 'top', topHeight );
					$toolbar.css( 'position', 'fixed' );
					$toolbar.width( titleWidth );
					$( '#title-section' ).css( 'padding-bottom', $toolbar.height() );
				} else {
					$toolbar.removeAttr( "style" )
				}
			}
		});
	}

} )( mediaWiki, jQuery, document );