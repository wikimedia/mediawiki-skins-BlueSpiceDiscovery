<?php

namespace BlueSpice\Discovery\Structure;

use BlueSpice\Discovery\Component\BackToTopButton;
use IContextSource;

class BackToTop extends SkinStructureBase {

	/**
	 *
	 * @return string
	 */
	public function getName() : string {
		return 'back-to-top';
	}

	/**
	 * @return string
	 */
	public function getTemplatePath() : string {
		return $GLOBALS['wgStyleDirectory'] .
			'/BlueSpiceDiscovery/resources/templates/structure/back-to-top';
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
	 * @return array
	 */
	public function getParams(): array {
		$component = new BackToTopButton();
		return [
			'body' => $this->getComponentHtml( $component )
		];
	}
}
