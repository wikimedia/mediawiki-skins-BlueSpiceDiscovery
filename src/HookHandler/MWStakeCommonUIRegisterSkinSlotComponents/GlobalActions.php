<?php

namespace BlueSpice\Discovery\HookHandler\MWStakeCommonUIRegisterSkinSlotComponents;

use BlueSpice\Discovery\Component\SpecialAllTemplates;
use BlueSpice\Discovery\Component\SpecialSpecialPages;
use BlueSpice\Discovery\Component\SpecialUpload;
use BlueSpice\Discovery\Component\SpecialWatchlist;
use MWStake\MediaWiki\Component\CommonUserInterface\Hook\MWStakeCommonUIRegisterSkinSlotComponents;

class GlobalActions implements MWStakeCommonUIRegisterSkinSlotComponents {

	/**
	 * @inheritDoc
	 */
	public function onMWStakeCommonUIRegisterSkinSlotComponents( $registry ) : void {
		$registry->register(
			'GlobalActionsTools',
			[
				'special-specialpages' => [
					'factory' => function () {
						return new SpecialSpecialPages();
					}
				],
				'special-alltemplates' => [
					'factory' => function () {
						return new SpecialAllTemplates();
					}
				],
				'special-upload' => [
					'factory' => function () {
						return new SpecialUpload();
					}
				],
				'special-watchlist' => [
					'factory' => function () {
						return new SpecialWatchlist();
					}
				]
			]
		);
	}
}
