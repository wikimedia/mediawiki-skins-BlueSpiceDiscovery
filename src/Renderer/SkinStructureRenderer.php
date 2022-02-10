<?php

namespace BlueSpice\Discovery\Renderer;

use BlueSpice\Discovery\ISkinStructureRenderer;
use IContextSource;
use TemplateParser;

class SkinStructureRenderer implements ISkinStructureRenderer {

	/** @var SkinStructureElement */
	private $skinStructureElement = null;

	/**
	 *
	 * @param SkinStructureElement $skinStructureElement
	 */
	public function __construct( $skinStructureElement ) {
		$this->skinStructureElement = $skinStructureElement;
	}

	/**
	 *
	 * @param IContextSource $context
	 * @return string
	 */
	public function getHtml( $context ): string {
		if ( !$this->skinStructureElement->shouldRender( $context ) ) {
			return '';
		}
		$params = array_merge(
			$this->skinStructureElement->getParams()
		);
		$this->getTemplate();
		$templateParser = new TemplateParser(
			$this->structureTemplatePath
		);
		$templateParser->enableRecursivePartials(
			$this->skinStructureElement->enableRecursivePartials()
		);
		$html = $templateParser->processTemplate(
			$this->structureTemplateName,
			$params
		);
		return $html;
	}

	/**
	 * @inheritDoc
	 */
	private function getTemplate() {
		$structureTemplatePath = $this->skinStructureElement->getTemplatePath();
		$structureTemplateParts = explode( '/', $structureTemplatePath );
		$this->structureTemplateName = array_pop( $structureTemplateParts );
		$this->structureTemplatePath = implode( '/', $structureTemplateParts );
	}
}
