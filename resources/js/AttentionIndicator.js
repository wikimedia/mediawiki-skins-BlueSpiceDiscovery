( function( mw, $, d ){
	var indicators = {};
	var keys = [];
	$( "[data-attentionindicator]" ).each( function() {
		indicators[$(this).data( 'attentionindicator' )] = $( this );
		keys.push( $(this).data( 'attentionindicator' ) );
	} );
	if ( keys.length < 1 ) {
		return;
	}
	mw.loader.using( 'ext.bluespice' ).done( function() {
		var callback = function( result, Listener ) {
			var indication = false;
			if( result.success !== true ) {
				return;
			}
			BSPing.registerListener(
				'AttentionIndicator',
				1000,
				{ indicators: keys },
				callback.bind( this )
			);
			for ( var i in result.indicators ) {
				if ( !indicators[i] ) {
					continue;
				}
				if( result.indicators[i] ) {
					indicators[i].addClass( 'attention-indicator' );
					indication = true;
					continue;
				}
				indicators[i].removeClass( 'attention-indicator' );
			}
			if ( indication ) {
				$( '#usr-btn' ).addClass( 'attention-indicator' );
				$( '#usr-btn > i' ).parent().addClass( 'attention-indicator-icon' );
				return;
			}
			$( '#usr-btn' ).removeClass( 'attention-indicator' );
			$( '#usr-btn > i' ).parent().removeClass( 'attention-indicator-icon' );

		};
		BSPing.registerListener(
			'AttentionIndicator',
			1000,
			{ indicators: keys },
			callback
		);

	} );
} )( mediaWiki, jQuery, document );
