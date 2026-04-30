<?php

namespace BlueSpice\Discovery;

use MWStake\MediaWiki\Component\ManifestRegistry\ManifestObjectFactory;
use MWStake\MediaWiki\Component\ManifestRegistry\ManifestRegistryFactory;

class MetaItemsProviderFactory {

	private const REGISTRY_NAME = 'BlueSpiceDiscoveryMetaItemProviderRegistry';
	private const INSTANCEOF = '\BlueSpice\Discovery\IMetaItemProvider';

	/**
	 * @var ManifestObjectFactory
	 */
	private $objectFactory = null;

	/**
	 * @var ManifestRegistryFactory
	 */
	private $registryFactory = null;

	/**
	 * @var array|null
	 */
	private $menus = null;

	/**
	 * @param ManifestObjectFactory $objectFactory
	 * @param ManifestRegistryFactory $registryFactory
	 */
	public function __construct(
		ManifestObjectFactory $objectFactory,
		ManifestRegistryFactory $registryFactory
	) {
		$this->objectFactory = $objectFactory;
		$this->registryFactory = $registryFactory;
	}

	/**
	 * @return IMetaItemProvider[]
	 */
	public function getAllMetaItemsProvider(): array {
		if ( $this->menus !== null ) {
			return $this->menus;
		}
		$this->menus = $this->objectFactory->createAllObjects(
			self::REGISTRY_NAME,
			[],
			self::INSTANCEOF
		);

		return $this->menus;
	}

	/**
	 * @param string $name
	 * @return IMetaItemProvider|null
	 */
	public function getMetaItem( string $name ): ?IMetaItemProvider {
		return $this->objectFactory->createObject(
			self::REGISTRY_NAME,
			$name,
			[],
			self::INSTANCEOF
		);
	}

	/**
	 * @param string $name
	 * @return array|null List of supported skin class names, or null if unrestricted
	 */
	public function getSupportedSkins( string $name ): ?array {
		$registry = $this->registryFactory->get( self::REGISTRY_NAME );
		$spec = $registry->getObjectSpec( $name );
		return $spec['supportedSkins'] ?? null;
	}
}
