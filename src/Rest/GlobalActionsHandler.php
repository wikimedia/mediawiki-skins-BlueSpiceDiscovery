<?php

namespace BlueSpice\Discovery\Rest;

use BlueSpice\Discovery\Renderer\ComponentRenderer;
use BlueSpice\Discovery\SkinSlotRenderer\GlobalActionsAdministrationSkinSlotRenderer;
use BlueSpice\Discovery\SkinSlotRenderer\GlobalActionsEditingSkinSlotRenderer;
use BlueSpice\Discovery\SkinSlotRenderer\GlobalActionsOverviewSkinSlotRenderer;
use MediaWiki\Message\Message;
use MediaWiki\Rest\SimpleHandler;
use MWStake\MediaWiki\Component\CommonUserInterface\Component\Literal;
use MWStake\MediaWiki\Component\CommonUserInterface\Component\SimpleCard;
use MWStake\MediaWiki\Component\CommonUserInterface\Component\SimpleCardBody;
use MWStake\MediaWiki\Component\CommonUserInterface\Component\SimpleCardHeader;
use MWStake\MediaWiki\Component\CommonUserInterface\SkinSlotRendererFactory;

class GlobalActionsHandler extends SimpleHandler {

	public function __construct(
		private readonly SkinSlotRendererFactory $skinSlotRendererFactory,
		private readonly ComponentRenderer $componentRenderer
	) {
	}

	/**
	 * @return \MediaWiki\Rest\Response
	 */
	public function execute() {
		$overviewHtml = $this->getOverviewSkinSlotHtml();
		$editingHtml = $this->getEditingSkinSlotHtml();
		$administrationHtml = $this->getAdministrationSkinSlotHtml();

		$cardBodyItems = [];

		$overviewCard = new SimpleCard( [
			'id' => 'ga-overview',
			'classes' => [ 'card-mn' ],
			'items' => [
				new SimpleCardHeader( [
					'id' => 'ga-menu-overview-head',
					'classes' => [ 'menu-title' ],
					'items' => [
						new Literal(
							'ga-menu-title',
							Message::newFromKey( 'bs-discovery-navbar-global-actions-overview-text' )
						)
					]
				] ),
				new Literal( 'ga-menu-list-items', $overviewHtml )
			]
		] );
		$cardBodyItems[] = $overviewCard;

		if ( $editingHtml ) {
			$editingCard = new SimpleCard( [
				'id' => 'ga-editing',
				'classes' => [ 'card-mn' ],
				'items' => [
					new SimpleCardHeader( [
						'id' => 'ga-menu-editing-head',
						'classes' => [ 'menu-title' ],
						'items' => [
							new Literal(
								'ga-menu-title',
								Message::newFromKey( 'bs-discovery-navbar-global-actions-editing-text' )
							)
						]
					] ),
					new Literal( 'ga-menu-list-items', $editingHtml )
				]
			] );
			$cardBodyItems[] = $editingCard;
		}

		if ( $administrationHtml ) {
			$administrationCard = new SimpleCard( [
				'id' => 'ga-administration',
				'classes' => [ 'card-mn' ],
				'items' => [
					new SimpleCardHeader( [
						'id' => 'ga-menu-administration-head',
						'classes' => [ 'menu-title' ],
						'items' => [
							new Literal(
								'ga-menu-title',
								Message::newFromKey( 'bs-discovery-navbar-global-actions-administration-text' )
							)
						]
					] ),
					new Literal( 'ga-menu-list-items', $administrationHtml )
				]
			] );
			$cardBodyItems[] = $administrationCard;
		}

		$mainCard = new SimpleCard( [
			'id' => 'ga-mm',
			'classes' => [ 'mega-menu', 'd-flex', 'justify-content-center' ],
			'items' => [
				new SimpleCardBody( [
					'id' => 'ga-tools-megamn-body',
					'classes' => [ 'd-flex', 'mega-menu-wrapper' ],
					'items' => $cardBodyItems
				] )
			]
		] );

		$html = $this->componentRenderer->getComponentHtml( $mainCard );
		$html .= '<div id="ga-mm-div" class="mm-bg"></div>';

		return $this->getResponseFactory()->createJson( [ 'html' => $html ] );
	}

	/**
	 * @return bool
	 */
	public function needsReadAccess() {
		return true;
	}

	/**
	 * @return string
	 */
	private function getOverviewSkinSlotHtml(): string {
		$skinSlotRenderer = $this->skinSlotRendererFactory->create(
			GlobalActionsOverviewSkinSlotRenderer::REG_KEY
		);
		return $skinSlotRenderer->getHtml();
	}

	/**
	 * @return string
	 */
	private function getEditingSkinSlotHtml(): string {
		$skinSlotRenderer = $this->skinSlotRendererFactory->create(
			GlobalActionsEditingSkinSlotRenderer::REG_KEY
		);
		return $skinSlotRenderer->getHtml();
	}

	/**
	 * @return string
	 */
	private function getAdministrationSkinSlotHtml(): string {
		$skinSlotRenderer = $this->skinSlotRendererFactory->create(
			GlobalActionsAdministrationSkinSlotRenderer::REG_KEY
		);
		return $skinSlotRenderer->getHtml();
	}
}
