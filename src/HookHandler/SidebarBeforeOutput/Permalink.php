<?php

namespace BlueSpice\Discovery\HookHandler\SidebarBeforeOutput;

use MediaWiki\Hook\SidebarBeforeOutputHook;
use Skin;

class Permalink implements SidebarBeforeOutputHook {

	/**
	 *
	 * @param Skin $skin
	 * @param array &$sidebar
	 * @return void
	 */
	public function onSidebarBeforeOutput( $skin, &$sidebar ): void {
		if ( empty( $sidebar['TOOLBOX']['permalink'] ) ) {
			return;
		}
		$sidebar['TOOLBOX']['permalink']['text'] = $skin->msg( 'bs-discovery-permalink-copy-text' );
	}
}
