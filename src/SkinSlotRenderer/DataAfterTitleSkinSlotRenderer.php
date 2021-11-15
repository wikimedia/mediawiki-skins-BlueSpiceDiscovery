<?php

namespace BlueSpice\Discovery\SkinSlotRenderer;

class DataAfterTitleSkinSlotRenderer extends ExtendedSkinSlotRendererBase {

	public const REG_KEY = 'DataAfterTitle';

	/**
	 *
	 * @param array &$items
	 * @return void
	 */
	protected function sortItems( &$items ) : void {
	}

	/**
	 *
	 * @return string
	 */
	protected function getContainerWrapperTag() : string {
		return 'div';
	}

	/**
	 *
	 * @return string
	 */
	protected function getContainerWrapperId() : string {
		return 'data-after-title';
	}

	/**
	 *
	 * @return string
	 */
	protected function getItemWrapperTag() : string {
		return 'div';
	}

	/**
	 *
	 * @param string $id
	 * @return string
	 */
	protected function getItemWrapperId( $id ) : string {
		return $id . '-cnt';
	}
}
