<?php

namespace  BlueSpice\Discovery\HookHandler\MWStakeCommonUIRegisterSkinSlotComponents;

use BlueSpice\Discovery\Component\ExportOptions;
use BlueSpice\Discovery\Component\ShareOptions;
use BlueSpice\Discovery\Component\Watch;
use MWStake\MediaWiki\Component\CommonUserInterface\Hook\MWStakeCommonUIRegisterSkinSlotComponents;

class SidebarSecondaryToolbar implements MWStakeCommonUIRegisterSkinSlotComponents {

	/**
	 * @inheritDoc
	 */
	public function onMWStakeCommonUIRegisterSkinSlotComponents( $registry ) : void {
		$registry->register(
			'ToolbarPanel',
			[
				'watch' => [
					'factory' => function () {
						return new Watch();
					}
				],
				'share' => [
					'factory' => function () {
						return new ShareOptions();
					}
				],
				'export' => [
					'factory' => function () {
						return new ExportOptions();
					}
				],
			]
		);
	}
}
