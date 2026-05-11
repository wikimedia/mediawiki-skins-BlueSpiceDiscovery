<?php

namespace BlueSpice\Discovery\Component;

use Html;
use MediaWiki\Context\IContextSource;
use MediaWiki\Html\TemplateParser;
use MWStake\MediaWiki\Component\CommonUserInterface\Component\Literal;

class AsyncNamespaceTree extends Literal {

	/**
	 * @inheritDoc
	 */
	public function getId(): string {
		return 'async-namespace-tree';
	}

	/**
	 * @inheritDoc
	 */
	public function getHtml(): string {
		$templateParser = new TemplateParser( dirname( __DIR__, 2 ) . '/resources/templates/skeleton' );

		$skeleton = $templateParser->processTemplate(
			'async-tree',
			[]
		);
		$html = Html::openElement( 'div', [
			'id' => 'namespace-tree',
			'class' => 'namespace-tree'
		] );
		$html .= Html::openElement( 'div', [
			'id' => 'namespace-tree-skeleton'
		] );
		$html .= $skeleton;
		$html .= Html::closeElement( 'div' );
		$html .= Html::closeElement( 'div' );
		return $html;
	}

	/**
	 * @inheritDoc
	 */
	public function shouldRender( IContextSource $context ): bool {
		$title = $context->getTitle();

		if ( !$title || !$title->isContentPage() ) {
			return false;
		}

		return true;
	}

	/**
	 * @inheritDoc
	 */
	public function getRequiredRLModules(): array {
		return [ 'skin.discovery.namespacetree-component' ];
	}
}
