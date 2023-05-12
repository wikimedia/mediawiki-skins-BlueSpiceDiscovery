( function( mw, $, d ){

	$( window ).scroll( function() {
		if( !isSpecialPage() && window.innerWidth >= 767  ) {
			setSickyTitleforVerticalPosition();
			handleVEToolbar();
		}
	} );

	$( window ).on( 'resize', function( e ) {
		var $titleLine = getTitleLine()

		if ( ( window.innerWidth < 766 ) && $titleLine.hasClass( 'title-fixed' ) ) {
			disableStickyTitle();
		} else if ( window.innerWidth >= 767 ) {
			setSickyTitleforVerticalPosition();
			handleVEToolbar();
		}
	} );

	function setSickyTitleforVerticalPosition() {
		var windowTop = $( this ).scrollTop();
		var top = getTitleSection().offset().top;

		if ( windowTop > top ) {
			enableSitckyTitle();
		} else {
			disableStickyTitle();
		}
	}

	function handleVEToolbar() {
		if ( inEditMode() ) {
			if ( isStickyTitle() ) {
				setVEToolbarPosition();
			} else {
				resetVEToolbarPosition();
			}
		}
	}

	function isStickyTitle() {
		var $titleLine = getTitleLine();
		return $titleLine.hasClass( 'title-fixed' );
	}

	function isSpecialPage() {
		return $( 'body' ).hasClass( 'ns-special' );
	}

	function getTitleSection() {
		return $( '#title-section' );
	}

	function getTitleLine() {
		return $( '#title-line' );
	}

	function getTitleLineContent() {
		return $( '#title-line > div' );
	}

	function setTitleLineContentWidth( width ) {
		var $titleLineContent = getTitleLineContent();
		$titleLineContent.innerWidth( width );
	}

	function getWrapperTopPosition() {
		return $( '#wrapper' ).offset().top;
	}

	function getMainWidth() {
		return $( '#main' ).outerWidth();
	}

	function getContentWidth() {
		return $( 'main' ).outerWidth();
	}

	function resizeTitleLineContent() {
		var mainWidth = getMainWidth();
		// MMV overlay triggers this resize method but with
		// var mainWidth = 0
		if ( mainWidth > 0 ) {
			setTitleLineContentWidth( mainWidth );
		}
	}

	function enableSitckyTitle() {
		var $titleLine = getTitleLine();
		$titleLine.addClass( 'title-fixed' );

		var $titleSection = getTitleSection();
		$titleSection.css( 'padding-top', $titleLine.height() );

		resizeTitleLineContent();
	}

	function disableStickyTitle() {
		var $titleLine = getTitleLine();
		$titleLine.removeClass( 'title-fixed' );

		var $titleSection = getTitleSection();
		$titleSection.removeAttr( 'style' );

		var $titleLineContent = getTitleLineContent();
		$titleLineContent.removeAttr( 'style' );
	}

	function getVEToolbar() {
		return  $( '#content .ve-init-target >.ve-ui-toolbar > .oo-ui-toolbar-bar' );
	}

	function setVEToolbarPosition() {
		var top = getWrapperTopPosition();
		var mainWidth = getContentWidth();
		var $titleLine = getTitleLine();
		var topPosition = $titleLine.height() + top;

		var $toolbar = getVEToolbar();
		$toolbar.css( 'top', topPosition );
		$toolbar.css( 'position', 'fixed' );
		$toolbar.width( mainWidth );

		var $titleSection = getTitleSection();
		$titleSection.css( 'padding-bottom', $toolbar.height() );
	}

	function resetVEToolbarPosition() {
		var $toolbar = getVEToolbar();
		$toolbar.removeAttr( "style" );
	}

	function inEditMode() {
		var targetVisual = $( '.ve-init-target-visual' );
		var targetSource = $( '.ve-init-target-source' );

		if ( targetVisual.length || targetSource.length ) {
			return true;
		}

		return false;
	}

} )( mediaWiki, jQuery, document );