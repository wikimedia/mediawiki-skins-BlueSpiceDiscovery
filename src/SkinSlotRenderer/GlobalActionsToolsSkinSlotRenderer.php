<?php

namespace BlueSpice\Discovery\SkinSlotRenderer;

use MWStake\MediaWiki\Component\CommonUserInterface\IComponent;

class GlobalActionsToolsSkinSlotRenderer extends ExtendedSkinSlotRendererBase {

	public const REG_KEY = 'GlobalActionsTools';

	/**
	 *
	 * @param array $data
	 * @return string
	 */
	public function getHtml( $data = [] ) : string {
		$skinSlots = $this->skinSlotRegistry->getSkinSlots();
		$items = $skinSlots[static::REG_KEY];

		if ( empty( $items ) ) {
			return '';
		}

		$this->sortItems( $items );

		$html = '<ul id="ga-menu-tools" aria-label-by="ga-menu-tools-head"';
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
