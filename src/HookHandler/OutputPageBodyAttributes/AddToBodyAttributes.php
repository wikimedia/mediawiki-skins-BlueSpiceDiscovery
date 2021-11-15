<?php

namespace BlueSpice\Discovery\HookHandler\OutputPageBodyAttributes;

use BlueSpice\Discovery\CookieHandler;
use OutputPage;
use Skin;

class AddToBodyAttributes {

	/**
	 *
	 * @param OutputPage $out
	 * @param Skin $skin
	 * @param array &$bodyAttrs
	 * @return void
	 */
	public static function onOutputPageBodyAttributes( OutputPage $out, Skin $skin, &$bodyAttrs ) {
		$cookieHandler = new CookieHandler( $skin->getRequest() );

		$cookie = $cookieHandler->getCookie( 'bodyClasses' );
		if ( $cookie ) {
			$cookieData = json_decode( $cookie, true );
			foreach ( $cookieData as $key => $value ) {
				$bodyAttrs[ 'class' ] .= $value;
			}

			$bodyClassName = $out->getProperty( 'bodyClassName' );
			$bodyAttrs[ 'class' ] .= $bodyClassName;
		}
	}
}
