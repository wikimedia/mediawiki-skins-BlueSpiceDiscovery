<?php

namespace BlueSpice\Discovery\HookHandler\BeforePageDisplay;

use MediaWiki\Output\Hook\BeforePageDisplayHook;
use MediaWiki\Session\SessionManager;

class InvalidateNamespaceTreeCache implements BeforePageDisplayHook {

	private const SESSION_KEY = 'discovery-ns-tree-invalidate';

	/**
	 * @inheritDoc
	 */
	public function onBeforePageDisplay( $out, $skin ): void {
		$session = SessionManager::getGlobalSession();
		$pending = $session->get( self::SESSION_KEY, [] );

		if ( empty( $pending ) ) {
			return;
		}

		$title = $out->getTitle();
		if ( $title === null || !$title->isContentPage() ) {
			return;
		}

		$out->addJsConfigVars( [
			'bsgDiscoveryNsTreeInvalidateCache' => $pending
		] );
		$session->remove( self::SESSION_KEY );
	}
}
