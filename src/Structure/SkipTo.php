<?php

namespace BlueSpice\Discovery\Structure;

use ExtensionRegistry;
use IContextSource;
use Message;

class SkipTo extends SkinStructureBase {

	/**
	 *
	 * @var array
	 */
	private $skinComponents = [];

	/**
	 *
	 * @return string
	 */
	public function getName() : string {
		return 'skip-to';
	}

	/**
	 * @return string
	 */
	public function getTemplatePath() : string {
		return $GLOBALS['wgStyleDirectory'] .
			'/BlueSpiceDiscovery/resources/templates/structure/skip-to';
	}

	/**
	 *
	 * @param IContextSource $context
	 * @return bool
	 */
	public function shouldRender( IContextSource $context ) : bool {
		return true;
	}

	/**
	 * Parse templates recursive
	 *
	 * @return bool
	 */
	public function enableRecursivePartials() : bool {
		return false;
	}

	/**
	 *
	 * @return array
	 */
	public function getParams() : array {
		return [
			'aria-label' => Message::newFromKey( 'bs-discovery-skip-links-navigation-aria-label' )->text(),
			'body' => $this->buildList()
		];
	}

	/**
	 *
	 * @return string
	 */
	private function buildList() : string {
		$html = '<ul>';
		$attribute = ExtensionRegistry::getInstance()->getAttribute(
			'BlueSpiceDiscoverySkipToRegistry'
		);
		$skipToRegistry = $attribute[$this->layout->getName()];
		foreach ( $skipToRegistry as $name => $skipToItem ) {
			$html .= '<li><a class="mw-jump-link" href="#' . $skipToItem['id'] . '">';
			$html .= Message::newFromKey( $skipToItem['msg'] )->text();
			$html .= '</a></li>';
		}
		$html .= '</ul>';
		return $html;
	}
}
