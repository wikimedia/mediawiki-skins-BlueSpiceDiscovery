<?php

namespace BlueSpice\Discovery\Component;

use IContextSource;
use MediaWiki\MediaWikiServices;
use Message;
use MWStake\MediaWiki\Component\CommonUserInterface\Component\Literal;
use MWStake\MediaWiki\Component\CommonUserInterface\Component\SimpleCard;
use MWStake\MediaWiki\Component\CommonUserInterface\Component\SimpleCardHeader;
use MWStake\MediaWiki\Component\CommonUserInterface\Component\SimpleDropdownIcon;
use MWStake\MediaWiki\Component\CommonUserInterface\Component\SimpleLinklistGroupFromArray;
use MWStake\MediaWiki\Component\CommonUserInterface\LinkFormatter;

class LanguageButton extends SimpleDropdownIcon {

	/**
	 *
	 * @var string
	 */
	private $langCode = '';

	/**
	 *
	 * @param string $langCode
	 */
	public function __construct( $langCode ) {
		parent::__construct( [] );
		$this->langCode = $langCode;
	}

	/**
	 * @inheritDoc
	 */
	public function getId(): string {
		return 'lang-btn';
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
		return [ 'ico-lang' ];
	}

	/**
	 * @return Message
	 */
	public function getTitle(): Message {
		return Message::newFromKey( 'bs-discovery-navbar-language-button-title' );
	}

	/**
	 * @return Message
	 */
	public function getAriaLabel(): Message {
		return Message::newFromKey( 'bs-discovery-navbar-language-button-aria-label' );
	}

	/**
	 * @inheritDoc
	 */
	public function getSubComponents(): array {
		$currentLanguageParts = explode( '-', $this->langCode );
		$currentLanguageClass = 'wiki-' . $currentLanguageParts[0];

		/** @var array|bool $languages */
		$languages = $this->componentProcessData['panel']['languages'];

		$services = MediaWikiServices::getInstance();
		/** @var LinkFormatter */
		$linkFormatter = $services->getService( 'MWStakeLinkFormatter' );

		return [
			new SimpleCard( [
				'id' => 'lang-mm',
				'classes' => [ 'mega-menu', 'async', 'd-flex', 'justify-content-center', 'flex-row' ],
				'items' => [
					new SimpleCard( [
						'id' => 'lang-menu',
						'classes' => [ 'card-mn', $currentLanguageClass ],
						'items' => [
							new SimpleCardHeader( [
								'id' => 'lang-menu-head',
								'classes' => [ 'menu-title' ],
								'items' => [
									new Literal(
										'lang-menu-title',
										Message::newFromKey( 'bs-discovery-navbar-languages-button-text' )
									)
								]
							] ),
							new SimpleLinklistGroupFromArray( [
								'id' => 'langs',
								'classes' => [ 'menu-card-body', 'menu-list', 'll-dft' ],
								'aria' => [
									'labelledby' => "lang-menu-head"
								],
								'links' => $linkFormatter->formatLinks( $languages )
							] ),
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
	 * @param IContextSource $context
	 * @return bool
	 */
	public function shouldRender( IContextSource $context ): bool {
		$languages = $context->getSkin()->getLanguages();
		if ( !empty( $languages ) ) {
			return true;
		}
		return false;
	}
}
