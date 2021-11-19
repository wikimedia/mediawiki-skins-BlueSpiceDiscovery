<?php

use BlueSpice\Discovery\AttentionIndicatorFactory;
use BlueSpice\Discovery\CookieHandler;
use BlueSpice\Discovery\TemplateDataProvider;
use MediaWiki\MediaWikiServices;

return [
	'BlueSpiceDiscoveryTemplateDataProvider' => static function ( MediaWikiServices $services ) {
		$panelManager = new TemplateDataProvider(
			$services->getHookContainer()
		);
		return $panelManager;
	},
	'BlueSpiceDiscoveryCookieHandler' => static function ( MediaWikiServices $services ) {
		$panelManager = new CookieHandler( new WebRequest );
		return $panelManager;
	},

	'BSAttentionIndicatorFactory' => static function ( MediaWikiServices $services ) {
		$registry = $services->getService( 'MWStakeManifestRegistryFactory' )
			->get( 'BlueSpiceDiscoveryAttentionIndicatorRegistry' );
		return new AttentionIndicatorFactory(
			$registry,
			$services->getMainConfig(),
			$services
		);
	},
];
