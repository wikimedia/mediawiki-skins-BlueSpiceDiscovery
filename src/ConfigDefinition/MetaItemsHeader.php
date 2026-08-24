<?php

namespace BlueSpice\Discovery\ConfigDefinition;

use BlueSpice\ConfigDefinition\ArraySetting;
use BlueSpice\Discovery\MetaItemOptions;
use MediaWiki\MediaWikiServices;

class MetaItemsHeader extends ArraySetting {

	/**
	 * @return array
	 */
	public function getPaths() {
		return [
			static::MAIN_PATH_FEATURE . '/' . static::FEATURE_SKINNING . '/BlueSpiceDiscovery',
			static::MAIN_PATH_EXTENSION . '/BlueSpiceDiscovery/' . static::FEATURE_SKINNING,
			static::MAIN_PATH_PACKAGE . '/' . static::PACKAGE_FREE . '/BlueSpiceDiscovery',
		];
	}

	/**
	 * @return string
	 */
	public function getLabelMessageKey() {
		return 'bs-discovery-config-meta-items-header-label';
	}

	/**
	 * @return string
	 */
	public function getHelpMessageKey() {
		return 'bs-discovery-config-meta-items-header-help';
	}

	/**
	 * @return string
	 */
	public function getVariableName() {
		return 'bsg' . $this->getName();
	}

	/**
	 * Meta items provided by extensions that are not available in this installation
	 * must be filtered out, otherwise the setting can not be saved anymore
	 *
	 * @return array
	 */
	public function getValue() {
		return array_values( array_intersect( (array)parent::getValue(), $this->getOptions() ) );
	}

	/**
	 * @return array
	 */
	protected function getOptions() {
		$services = MediaWikiServices::getInstance();
		$metaItemFactory = $services->getService( 'BlueSpiceDiscoveryMetaItemFactory' );
		$metaOptions = new MetaItemOptions( $metaItemFactory );
		$options = $metaOptions->getOptions();
		return $options;
	}

}
