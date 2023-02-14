<?php

namespace BlueSpice\Discovery\SkinSlotRenderer;

class ToolsAfterContentSkinSlotRenderer extends DataAfterTitleSkinSlotRenderer {

	public const REG_KEY = 'ToolsAfterContent';

	/**
	 *
	 * @return string
	 */
	protected function getContainerWrapperId(): string {
		return 'tools-after-content';
	}

	/**
	 *
	 * @param MetaItemsManager $metaItemsManager
	 * @return array
	 */
	protected function getMetaItems( $metaItemsManager ): array {
		return $metaItemsManager->getMetaItemsFromConfigVar( 'DiscoveryMetaItemsFooter' );
	}
}
