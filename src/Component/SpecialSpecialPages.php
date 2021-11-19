<?php

namespace BlueSpice\Discovery\Component;

use MediaWiki\MediaWikiServices;
use Message;
use MWStake\MediaWiki\Component\CommonUserInterface\Component\RestrictedTextLink;
use RawMessage;
use SpecialPage;

class SpecialSpecialPages extends RestrictedTextLink {

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
		return 'ga-special-specialpages';
	}

	/**
	 *
	 * @return string
	 */
	public function getHref(): string {
		/** @var Title */
		$specialpage = SpecialPage::getTitleFor( 'Specialpages' );
		return $specialpage->getLocalURL();
	}

	/**
	 *
	 * @return array
	 */
	public function getPermissions(): array {
		return [ 'edit' ];
	}

	/**
	 *
	 * @return Message
	 */
	public function getText(): Message {
		$name = MediaWikiServices::getInstance()->getSpecialPageFactory()->
		getLocalNameFor( 'Specialpages', false );
		return new RawMessage( $name );
	}

	/**
	 *
	 * @return Message
	 */
	public function getTitle(): Message {
		$name = MediaWikiServices::getInstance()->getSpecialPageFactory()->
		getLocalNameFor( 'Specialpages', false );
		return new RawMessage( $name );
	}

	/**
	 *
	 * @return Message
	 */
	public function getAriaLabel(): Message {
		$name = MediaWikiServices::getInstance()->getSpecialPageFactory()->
		getLocalNameFor( 'Specialpages', false );
		return new RawMessage( $name );
	}
}
