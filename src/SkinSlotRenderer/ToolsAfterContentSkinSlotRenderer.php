<?php

namespace BlueSpice\Discovery\SkinSlotRenderer;

class ToolsAfterContentSkinSlotRenderer extends ExtendedSkinSlotRendererBase {

	public const REG_KEY = 'ToolsAfterContent';

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
		return 'tools-after-content';
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
