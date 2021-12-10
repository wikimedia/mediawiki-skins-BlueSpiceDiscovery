<?php

use BlueSpice\Discovery\AttentionIndicatorFactory;
use BlueSpice\Discovery\BreadcrumbDataProviderFactory;
use BlueSpice\Discovery\CookieHandler;
use BlueSpice\Discovery\TemplateDataProvider;
use MediaWiki\MediaWikiServices;

return [
	'BlueSpiceDiscoveryTemplateDataProvider' => function ( MediaWikiServices $services ) {
		$panelManager = new TemplateDataProvider(
			$services->getHookContainer()
		);
		return $panelManager;
	},
	'BlueSpiceDiscoveryCookieHandler' => function ( MediaWikiServices $services ) {
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
	'BlueSpiceDiscoveryBreadcrumbDataProviderFactory' => function ( MediaWikiServices $services ) {
		$context = RequestContext::getMain();
		$messageLocalizer = $context;
		return new BreadcrumbDataProviderFactory(
			$messageLocalizer,
			$context->getRequest()->getValues(),
			$services->getTitleFactory(),
			$services->getNamespaceInfo(),
			$services->getObjectFactory()
		);
	}
];
