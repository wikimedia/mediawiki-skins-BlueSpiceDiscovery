<?php

namespace BlueSpice\Discovery;

use MediaWiki\Context\RequestContext;

class MetaItemOptions {

	/**
	 * @var MetaItemsProviderFactory
	 */
	private $metaItemFactory = null;

	/**
	 * @param MetaItemsProviderFactory $metaItemFactory
	 */
	public function __construct( $metaItemFactory ) {
		$this->metaItemFactory = $metaItemFactory;
	}

	/**
	 * @return array
	 */
	public function getOptions() {
		$options = [];
		$skin = RequestContext::getMain()->getSkin();

		$metaItemsProviders = $this->metaItemFactory->getAllMetaItemsProvider();
		foreach ( $metaItemsProviders as $metaItemProvider ) {
			$name = $metaItemProvider->getName();
			$supportedSkins = $this->metaItemFactory->getSupportedSkins( $name );
			if ( $supportedSkins !== null ) {
				$isSupported = false;
				foreach ( $supportedSkins as $skinClass ) {
					if ( is_a( $skin, $skinClass, true ) ) {
						$isSupported = true;
						break;
					}
				}
				if ( !$isSupported ) {
					continue;
				}
			}
			$options[] = $name;
		}

		return $options;
	}

}
