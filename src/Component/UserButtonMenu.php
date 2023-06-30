<?php

namespace BlueSpice\Discovery\Component;

use BlueSpice\Discovery\SkinSlotRenderer\UserMenuCardsSkinSlotRenderer;
use BlueSpice\DynamicFileDispatcher\Params;
use BlueSpice\DynamicFileDispatcher\UrlBuilder;
use BlueSpice\DynamicFileDispatcher\UserProfileImage;
use BlueSpice\UtilityFactory;
use Html;
use IContextSource;
use MediaWiki\MediaWikiServices;
use Message;
use MWStake\MediaWiki\Component\CommonUserInterface\Component\Literal;
use MWStake\MediaWiki\Component\CommonUserInterface\Component\SimpleCard;
use MWStake\MediaWiki\Component\CommonUserInterface\Component\SimpleCardBody;
use MWStake\MediaWiki\Component\CommonUserInterface\Component\SimpleCardHeader;
use MWStake\MediaWiki\Component\CommonUserInterface\Component\SimpleDropdown;
use MWStake\MediaWiki\Component\CommonUserInterface\Component\SimpleLinklistGroupFromArray;
use MWStake\MediaWiki\Component\CommonUserInterface\LinkFormatter;
use RawMessage;
use RequestContext;
use User;

class UserButtonMenu extends SimpleDropdown {

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
		return 'usr-btn';
	}

	/**
	 * @inheritDoc
	 */
	public function getSubComponents(): array {
		$services = MediaWikiServices::getInstance();
		/** @var LinkFormatter */
		$linkFormatter = $services->getService( 'MWStakeLinkFormatter' );
		$links = [];
		foreach ( $this->componentProcessData['template']['personal_urls'] as $key => $link ) {
			if ( in_array( $key, $this->getSkipList() ) ) {
				continue;
			}
			$links[$key] = $link;
			if ( !isset( $this->getOverrides()[$key] ) ) {
				continue;
			}
			foreach ( $this->getOverrides()[$key] as $override => $value ) {
				$links[$key][$override] = $value;
			}
		}

		$id = 'p-tools';

		return [
			new SimpleCard( [
				'id' => 'p-tools-megamn',
				'classes' => [
					'mega-menu', 'd-flex', 'justify-content-center'
				],
				'items' => [
					new SimpleCardBody( [
						'id' => 'p-tools-megamn-body',
						'classes' => [ 'd-flex', 'mega-menu-wrapper' ],
						'items' => [
							new Literal(
								'user-menu-additional-cards-unset',
								$this->getSkinSlotHtml()
							),
							new SimpleCard( [
								'id' => $id . '-menu',
								'classes' => [ 'card-mn' ],
								'items' => [
									new SimpleCardHeader( [
										'id' => $id . '-head',
										'classes' => [ 'menu-title' ],
										'items' => [
											new Literal(
												$id . '-title',
												Message::newFromKey(
													'bs-discovery-navbar-user-button-personal-menu-text'
												)->text()
											)
										]
									] ),
									new SimpleLinklistGroupFromArray( [
										'id' => $id,
										'classes' => [ 'menu-card-body', 'menu-list', 'll-dft' ],
										'links' => $linkFormatter->formatLinks( $this->sortLinks( $links ) ),
										'aria' => [
											'labelledby' => $id . '-head'
										],
									] ),
								]
							] )
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
	 * @return Message
	 */
	public function getText(): Message {
		return new RawMessage( $this->getAvatar() );
	}

	/**
	 * @return Message
	 */
	public function getTitle(): Message {
		return Message::newFromKey( 'bs-discovery-navbar-user-button-title' );
	}

	/**
	 * @return Message
	 */
	public function getAriaLabel(): Message {
		return Message::newFromKey( 'bs-discovery-navbar-user-button-aria-label' );
	}

	/**
	 *
	 * @param IContextSource $context
	 * @return bool
	 */
	public function shouldRender( IContextSource $context ): bool {
		/** @var User */
		$user = $context->getUser();
		if ( !$user->isAnon() ) {
			return true;
		}

		return false;
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

		/** @var UserMenuCardsSkinSlotRenderer */
		$skinSlotRenderer = $skinSlotRendererFactory->create( UserMenuCardsSkinSlotRenderer::REG_KEY );

		return $skinSlotRenderer->getHtml();
	}

	/**
	 * @return array
	 */
	protected function getFavoritePositions(): array {
		return [
			'watchlist' => 30,
			'mycontris' => 60,
			'mytalk' => 90,
			'userpage' => 100,
			'preferences' => 140,
			'logout' => 150,
		];
	}

	/**
	 * @return array
	 */
	protected function getSkipList(): array {
		return [
			'mytalk',
		];
	}

	/**
	 * @return array
	 */
	protected function getOverrides(): array {
		return [
			'mycontris' => [
				'text' => Message::newFromKey(
					'bs-discovery-personalurl-mycontributions-text'
				)
			],
			'userpage' => [
				'text' => Message::newFromKey(
					'bs-discovery-personalurl-userpage-text'
				)
			]
		];
	}

	/**
	 * @param array $links
	 * @return array
	 */
	protected function sortLinks( $links ): array {
		foreach ( $links as $key => &$data ) {
			if ( isset( $data['position'] ) ) {
				continue;
			}
			$data['position'] = isset( $this->getFavoritePositions()[$key] )
				? $this->getFavoritePositions()[$key]
				: 0;
		}
		usort( $links, static function ( $e1, $e2 ) {
			if ( $e1['position'] == $e2['position'] ) {
				return 0;
			}
			return $e1['position'] > $e2['position'] ? 1 : 0;
		} );
		return $links;
	}

	/**
	 *
	 * @return string
	 */
	private function getAvatar(): string {
		$defaultIcon = '<i class="ico-usr"></i>';
		if ( !$this->avatarServicesAvailable() ) {
			return $defaultIcon;
		}

		/** @var IContextSource */
		$context = RequestContext::getMain();

		/** @var User */
		$user = $context->getUser();

		/** @var UrlBuilder */
		$imageUrlBuilder = MediaWikiServices::getInstance()->getService(
			'BSDynamicFileDispatcherUrlBuilder'
		);

		/** @var UtilityFactory */
		$utilityFactory = MediaWikiServices::getInstance()->getService(
			'BSUtilityFactory'
		);

		$imgBaseSize = 28;
		$realsize = (int)$imgBaseSize * 1.4;
		$userImageParams = [
			Params::MODULE => 'userprofileimage',
			UserProfileImage::USERNAME => $user->getName(),
			UserProfileImage::HEIGHT => $realsize,
			UserProfileImage::WIDTH => $realsize,
		];

		$src = $imageUrlBuilder->build( new Params( $userImageParams ) );
		if ( empty( $src ) ) {
			return $defaultIcon;
		}

		$image = Html::element(
			'img',
			[
				// title is not required becaus the anchor tag arround this image hase already a title
				'src' => $src,
				'alt' => $utilityFactory->getUserHelper( $user )->getDisplayName(),
				'width' => $imgBaseSize . 'px',
				'height' => $imgBaseSize . 'px'
			]
		);

		return $image;
	}

	/**
	 *
	 * @return bool
	 */
	private function avatarServicesAvailable(): bool {
		$hasImageUrlBuilder = MediaWikiServices::getInstance()->hasService(
			'BSDynamicFileDispatcherUrlBuilder'
		);
		$hasUtilityFactory = MediaWikiServices::getInstance()->hasService(
			'BSUtilityFactory'
		);
		if ( $hasImageUrlBuilder && $hasUtilityFactory ) {
			return true;
		}
		return false;
	}
}
