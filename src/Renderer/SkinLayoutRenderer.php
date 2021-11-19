<?php

namespace BlueSpice\Discovery\Renderer;

use BlueSpice\Discovery\ISkinLayoutRenderer;
use ExtensionRegistry;
use TemplateParser;

class SkinLayoutRenderer implements ISkinLayoutRenderer {

	/** @var skinLayout */
	private $skinLayout = null;

	/**
	 *
	 * @param SkinLayout $skinLayout
	 */
	public function __construct( $skinLayout ) {
		$this->skinLayout = $skinLayout;
	}

	/**
	 *
	 * @param SkinLayout $skinLayout
	 * @return SkinLayoutRenderer
	 */
	public static function factory( $skinLayout ): ISkinLayoutRenderer {
		return new SkinLayoutRenderer( $skinLayout );
	}

	/**
	 *
	 * @return string
	 */
	public function getHtml(): string {
		$templateParser = new TemplateParser(
			$this->getTemplatePath()
		);
		$templateParser->enableRecursivePartials(
			$this->skinLayout->enableRecursivePartials()
		);
		$html = $templateParser->processTemplate(
			$this->skinLayout->getName(),
			$this->getAllStructureElementsHtml()
		);
		return $html;
	}

	/**
	 *
	 * Use this if comonent is called with {{{myStructure}}} in the layout template
	 *
	 * @return array
	 */
	private function getAllStructureElementsHtml(): array {
		$params = [];
		$skinStructureElements = $this->skinLayout->getSkinStructureElements();
		foreach ( $skinStructureElements as $skinStructureElement ) {
			$skinStructureRenderer = new SkinStructureRenderer( $skinStructureElement );
			$name = $skinStructureElement->getName();
			$params[$name] = $skinStructureRenderer->getHtml( $this->skinLayout->context );
		}
		return $params;
	}

	/**
	 *
	 * @return string
	 */
	public function getTemplatePath(): string {
		$layoutRegistry = ExtensionRegistry::getInstance()->getAttribute(
			'BlueSpiceDiscoveryLayoutRegistry'
		);
		$name = $this->skinLayout->getName();
		return $layoutRegistry[$name]['template'];
	}
}
