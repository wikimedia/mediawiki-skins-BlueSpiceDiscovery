<?php

namespace BlueSpice\Discovery\SkinSlotRenderer;

use MWStake\MediaWiki\Component\CommonUserInterface\IComponent;

class GlobalActionsManagerSkinSlotRenderer extends ExtendedSkinSlotRendererBase {

	public const REG_KEY = 'GlobalActionsManager';

	/**
	 *
	 * @param array &$items
	 * @return void
	 */
	protected function sortItems( &$items ): void {
		$helper = [];
		foreach ( $items as $itemid => $item ) {
			if ( !isset( $item[ 'id' ] ) || empty( $item[ 'id' ] ) ) {
				$item[ 'id' ] = "bs-ga-link-$itemid";
			}
			$component = call_user_func_array( $item[ 'factory' ], [] );
			$text = $component->getText()->text();
			$helper[ $text ] = $item;
		}

		ksort( $helper );
		$items = array_values( $helper );
	}

	/**
	 *
	 * @param array $data
	 * @return string
	 */
	public function getHtml( $data = [] ): string {
		$skinSlots = $this->skinSlotRegistry->getSkinSlots();
		$items = $skinSlots[static::REG_KEY];

		if ( empty( $items ) ) {
			return '';
		}

		$this->sortItems( $items );

		$html = '<ul id="ga-menu-manager" aria-labelledby="ga-menu-manager-head"';
		$html .= ' class="list-group menu-card-body menu-list">';
		foreach ( $items as $id => $item ) {
			if ( !is_callable( $item['factory'] ) ) {
				continue;
			}
			$component = call_user_func_array( $item['factory'], [] );
			if ( !( $component instanceof IComponent ) ) {
				continue;
			}

			$componentTree = $this->componentManager->getCustomComponentTree(
				$component,
				$data
			);

			if ( !empty( $componentTree ) ) {
				$rendererDataTree = $this->rendererDataTreeBuilder->getRendererDataTree( [
					array_pop( $componentTree )
				] );
				$html .= '<li>';
				$html .= $this->rendererDataTreeRenderer->getHtml( $rendererDataTree );
				$html .= '</li>';
			}
		}

		$html .= '</ul>';
		return $html;
	}
}
