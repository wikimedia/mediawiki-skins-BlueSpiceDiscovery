<?php

namespace BlueSpice\Discovery\Hook;

interface BlueSpiceDiscoveryAfterGetFooterPlaces {

	/**
	 *
	 * @param array &$footerlinks
	 * @return array
	 */
	public function onBlueSpiceDiscoveryAfterGetFooterPlaces( &$footerlinks ): void;
}
