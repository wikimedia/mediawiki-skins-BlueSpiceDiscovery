<?php

namespace BlueSpice\Discovery\Component;

use BlueSpice\Discovery\SkinSlotRenderer\GlobalActionsManagerSkinSlotRenderer;
use BlueSpice\Discovery\SkinSlotRenderer\GlobalActionsToolsSkinSlotRenderer;
use MediaWiki\MediaWikiServices;
use Message;
use MWStake\MediaWiki\Component\CommonUserInterface\Component\Literal;
use MWStake\MediaWiki\Component\CommonUserInterface\Component\SimpleCard;
use MWStake\MediaWiki\Component\CommonUserInterface\Component\SimpleCardHeader;
use MWStake\MediaWiki\Component\CommonUserInterface\Component\SimpleDropdownIcon;
use MWStake\MediaWiki\Component\CommonUserInterface\IRestrictedComponent;

class GlobalActionsButton extends SimpleDropdownIcon implements IRestrictedComponent {

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
		return 'ga-btn';
	}

	/**
	 * @return array
	 */
	public function getContainerClasses(): array {
		return [ 'has-megamenu' ];
	}

	/**
	 * @return array
	 */
	public function getButtonClasses(): array {
		return [ 'ico-btn' ];
	}

	/**
	 * @return array
	 */
	public function getMenuClasses(): array {
		return [ 'megamenu' ];
	}

	/**
	 * @return array
	 */
	public function getIconClasses(): array {
		return [ 'bi-gear-fill' ];
	}

	/**
	 * @return Message
	 */
	public function getTitle(): Message {
		return Message::newFromKey( 'bs-discovery-navbar-global-actions-button-title' );
	}

	/**
	 * @return Message
	 */
	public function getAriaLabel(): Message {
		return Message::newFromKey( 'bs-discovery-navbar-global-actions-button-aria-label' );
	}

	/**
	 * @inheritDoc
	 */
	public function getSubComponents(): array {
		return [
			new SimpleCard( [
				'id' => 'ga-mm',
				'classes' => [
					'mega-menu', 'async', 'd-flex', 'justify-content-center',
					'flex-md-row', 'flex-lg-row', 'flex-xl-row', 'flex-xxl-row'
				],
				'items' => [
					new SimpleCard( [
						'id' => 'ga-tools',
						'classes' => [ 'card-mn' ],
						'items' => [
							new SimpleCardHeader( [
								'id' => 'ga-menu-tools-head',
								'classes' => [ 'menu-title' ],
								'items' => [
									new Literal(
										'ga-menu-title',
										Message::newFromKey( 'bs-discovery-navbar-global-actions-tool-text' )
									)
								]
							] ),
							new Literal(
								'ga-menu-list-items',
								$this->getToolSkinSlotHtml()
							)
						]
					] ),
					new SimpleCard( [
						'id' => 'ga-manager',
						'classes' => [ 'card-mn' ],
						'items' => [
							new SimpleCardHeader( [
								'id' => 'ga-menu-manager-head',
								'classes' => [ 'menu-title' ],
								'items' => [
									new Literal(
										'ga-menu-title',
										Message::newFromKey( 'bs-discovery-navbar-global-actions-manager-text' )
									)
								]
							] ),
							new Literal(
								'ga-menu-list-items',
								$this->getManagerSkinSlotHtml()
							),
						]
					] )
				]
			] ),
			/* literal for transparent megamenu container */
			new Literal(
				'ga-mm-div',
				'<div id="ga-mm-div" class="mm-bg"></div>'
			)
		];
	}

	/**
	 *
	 * @return array
	 */
	public function getPermissions(): array {
		return [ 'read' ];
	}

	/**
	 *
	 * @return string
	 */
	private function getToolSkinSlotHtml(): string {
		/** @var MediaWikiServices */
		$services = MediaWikiServices::getInstance();

		/** @var SkinSlotRendererFactory */
		$skinSlotRendererFactory = $services->get( 'MWStakeCommonUISkinSlotRendererFactory' );

		/** @var GlobalActionsToolsSkinSlotRenderer */
		$skinSlotRenderer = $skinSlotRendererFactory->create( GlobalActionsToolsSkinSlotRenderer::REG_KEY );

		return $skinSlotRenderer->getHtml();
	}

	/**
	 *
	 * @return string
	 */
	private function getManagerSkinSlotHtml(): string {
		/** @var MediaWikiServices */
		$services = MediaWikiServices::getInstance();

		/** @var SkinSlotRendererFactory */
		$skinSlotRendererFactory = $services->get( 'MWStakeCommonUISkinSlotRendererFactory' );

		/** @var GlobalActionsManagerSkinSlotRenderer */
		$skinSlotRenderer = $skinSlotRendererFactory->create( GlobalActionsManagerSkinSlotRenderer::REG_KEY );

		return $skinSlotRenderer->getHtml();
	}
}
