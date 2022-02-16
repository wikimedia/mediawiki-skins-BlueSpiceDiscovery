<?php

namespace BlueSpice\Discovery\SkinSlotRenderer;

class MainPanelSkinSlotRenderer extends ExtendedSkinSlotRendererBase {

	public const REG_KEY = 'MainLinksPanel';

	/**
	 *
	 * @param array &$items
	 * @return void
	 */
	protected function sortItems( &$items ): void {
		ksort( $items );
		usort( $items, static function ( $itemOne, $itemTwo ) {
			$item1SortKey = isset( $itemOne['position'] ) ? $itemOne['position'] : 100;
			$item2SortKey = isset( $itemTwo['position'] ) ? $itemTwo['position'] : 100;

			return $item1SortKey > $item2SortKey ? 1 : 0;
		} );
	}

	/**
	 *
	 * @return string
	 */
	protected function getContainerWrapperTag(): string {
		return 'ul';
	}

	/**
	 *
	 * @return array
	 */
	protected function getContainerWrapperClasses(): array {
		return [ 'list-group' ];
	}

	/**
	 *
	 * @return array
	 */
	protected function getContainerWrapperAriaAttributes(): array {
		return [
			'labelledby' => 'main-links-panel-head'
		];
	}

	/**
	 *
	 * @return string
	 */
	protected function getItemWrapperTag(): string {
		return 'li';
	}

	/**
	 *
	 * @return array
	 */
	protected function getItemWrapperClasses(): array {
		return [ 'list-group-item' ];
	}

	/**
	 *
	 * @param array &$rendererDataTree
	 * @return void
	 */
	private function checkActiveState( &$rendererDataTree ) {
		// TODO: Fix active state

		$class = '';
		if ( isset( $rendererDataTree[0]['data']['class'] ) ) {
			$class = $rendererDataTree[0]['data']['class'];
		}
		$localUrl = $this->template->getSkin()->getTitle()->getLocalURL();
		$fullUrl = $this->template->getSkin()->getTitle()->getFullUrl();
		if ( $rendererDataTree[0]['data']['href'] === $localUrl || $rendererDataTree[0]['data']['href'] === $fullUrl ) {
			$class .= ' active';
		}
		if ( $class !== '' ) {
			$rendererDataTree[0]['data']['class'] = $class;
		}
	}
}
