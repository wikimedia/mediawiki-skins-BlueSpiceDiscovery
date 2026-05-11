<?php

namespace BlueSpice\Discovery\Component;

use Html;
use MediaWiki\Context\IContextSource;
use Message;
use MWStake\MediaWiki\Component\CommonUserInterface\Component\Literal;
use MWStake\MediaWiki\Component\CommonUserInterface\Component\SimpleButton;
use MWStake\MediaWiki\Component\CommonUserInterface\Component\SimpleCard;
use MWStake\MediaWiki\Component\CommonUserInterface\Component\SimpleCardHeader;
use RawMessage;

class AsyncNamespaceTreePanel extends SimpleCard {

	/**
	 *
	 */
	public function __construct() {
		parent::__construct( [] );
	}

	/**
	 * @inheritDoc
	 */
	public function getId(): string {
		return 'namespace-tree-pnl';
	}

	/**
	 * @inheritDoc
	 */
	public function getContainerClasses(): array {
		return [ 'w-100', 'bg-transp' ];
	}

	/**
	 * @inheritDoc
	 */
	public function getSubComponents(): array {
		$id = $this->getId();
		$headerText = Message::newFromKey( 'bs-discovery-namespace-tree-pnl-header-text' )->text();
		$headerHtml = Html::element( 'div', [], $headerText );

		return [
			new SimpleCardHeader( [
				'id' => $id . '-head',
				'classes' => [ 'menu-title', 'navigation-tree-header' ],
				'items' => [
					new Literal(
						$id . '-head-label',
						$headerHtml
					),
					new SimpleButton( [
						'id' => 'navigation-tree-search',
						'aria-label' => Message::newFromKey( 'bs-discovery-namespace-tree-search-btn-label' ),
						'aria' => [
							'pressed' => false
						],
						'text' => new RawMessage( '' ),
						'title' => Message::newFromKey( 'bs-discovery-namespace-tree-search-btn-label' ),
						'classes' => [ 'bi-bs-search' ]
					] ),
					new SimpleButton( [
						'id' => 'navigation-tree-create',
						'aria-label' => Message::newFromKey( 'bs-discovery-namespace-tree-create-btn-label' ),
						'text' => new RawMessage( '' ),
						'title' => Message::newFromKey( 'bs-discovery-namespace-tree-create-btn-label' ),
						'classes' => [ 'bi-bs-create-page', 'ca-new-page' ]
					] )
				]
			] ),
			new AsyncNamespaceTree( 'async-namespace-tree', '' )
		];
	}

	/**
	 * @inheritDoc
	 */
	public function shouldRender( IContextSource $context ): bool {
		$title = $context->getTitle();

		if ( !$title ) {
			return false;
		}

		if ( !$title->isContentPage() ) {
			return false;
		}

		return true;
	}
}
