<?php

namespace BlueSpice\Discovery\Component;

use MediaWiki\MediaWikiServices;
use Message;
use MWStake\MediaWiki\Component\CommonUserInterface\Component\RestrictedTextLink;
use RawMessage;
use SpecialPage;

class SpecialWatchlist extends RestrictedTextLink {

	/**
	 *
	 */
	public function __construct() {
		parent::__construct( [] );
	}

	/**
	 *
	 * @return string
	 */
	public function getId(): string {
		return 'ga-special-watchlist';
	}

	/**
	 *
	 * @return string
	 */
	public function getHref(): string {
		/** @var Title */
		$specialpage = SpecialPage::getTitleFor( 'Watchlist' );
		return $specialpage->getLocalURL();
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
	 * @return Message
	 */
	public function getText(): Message {
		$specialpage = MediaWikiServices::getInstance()->getSpecialPageFactory()
		->getPage( 'Watchlist' );
		return new RawMessage( $specialpage->getDescription() );
	}

	/**
	 *
	 * @return Message
	 */
	public function getTitle(): Message {
		$specialpage = MediaWikiServices::getInstance()->getSpecialPageFactory()
		->getPage( 'Watchlist' );
		return new RawMessage( $specialpage->getDescription() );
	}

	/**
	 *
	 * @return Message
	 */
	public function getAriaLabel(): Message {
		$specialpage = MediaWikiServices::getInstance()->getSpecialPageFactory()
		->getPage( 'Watchlist' );
		return new RawMessage( $specialpage->getDescription() );
	}
}
