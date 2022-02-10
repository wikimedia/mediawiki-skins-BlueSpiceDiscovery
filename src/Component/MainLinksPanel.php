<?php

namespace BlueSpice\Discovery\Component;

use BlueSpice\Discovery\SkinSlotRenderer\MainPanelSkinSlotRenderer;
use IContextSource;
use MediaWiki\MediaWikiServices;
use Message;
use MWStake\MediaWiki\Component\CommonUserInterface\Component\CallbackLiteral;
use MWStake\MediaWiki\Component\CommonUserInterface\Component\Literal;
use MWStake\MediaWiki\Component\CommonUserInterface\Component\SimpleCard;
use MWStake\MediaWiki\Component\CommonUserInterface\Component\SimpleCardHeader;

class MainLinksPanel extends SimpleCard {

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
		return 'main-pnl';
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
		$item = new SimpleCard( [
			'id' => 'main-links-panel',
			'classes' => [ 'w-100', 'bg-transp' ],
			'items' => [
				new SimpleCardHeader( [
					'id' => 'main-links-panel-head',
					'classes' => [ 'menu-title' ],
					'items' => [
						new Literal(
							'main-links-panel-head',
							Message::newFromKey( 'bs-discovery-main-links-heading' )->text()
						)
					]
				] ),
				new CallbackLiteral(
					$this->getId() . '-list',
					function ( $componetProcessData ) {
						return $this->getSkinSlotHtml();
					}
				)
			]
		] );
		return [ $item ];
	}

	/**
	 *
	 * @param IContextSource $context
	 * @return bool
	 */
	public function shouldRender( IContextSource $context ): bool {
		return true;
	}

	/**
	 *
	 * @return string
	 */
	private function getSkinSlotHtml(): string {
		/** @var MediaWikiServices */
		$services = MediaWikiServices::getInstance();

		/** @var SkinSlotRendererFactory */
		$skinSlotRendererFactory = $services->get( 'MWStakeCommonUISkinSlotRendererFactory' );

		/** @var MainPanelSkinSlotRenderer */
		$skinSlotRenderer = $skinSlotRendererFactory->create( MainPanelSkinSlotRenderer::REG_KEY );

		return $skinSlotRenderer->getHtml();
	}
}
