<?php

namespace BlueSpice\Discovery\Component;

use MediaWiki\MediaWikiServices;
use Message;
use MWStake\MediaWiki\Component\CommonUserInterface\Component\RestrictedTextLink;
use SpecialPage;

class SpecialUpload extends RestrictedTextLink {

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
		return 'ga-special-upload';
	}

	/**
	 *
	 * @return string
	 */
	public function getHref(): string {
		/** @var Title */
		$specialpage = SpecialPage::getTitleFor( 'Upload' );
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
	 * @inheritDoc
	 */
	public function getRole(): string {
		return 'menuitem';
	}

	/**
	 *
	 * @return Message
	 */
	public function getText(): Message {
		$specialpage = MediaWikiServices::getInstance()->getSpecialPageFactory()
		->getPage( 'Upload' );
		return $this->ensureMessageObject( $specialpage->getDescription() );
	}

	/**
	 *
	 * @return Message
	 */
	public function getTitle(): Message {
		$specialpage = MediaWikiServices::getInstance()->getSpecialPageFactory()
		->getPage( 'Upload' );
		return $this->ensureMessageObject( $specialpage->getDescription() );
	}

	/**
	 *
	 * @return Message
	 */
	public function getAriaLabel(): Message {
		$specialpage = MediaWikiServices::getInstance()->getSpecialPageFactory()
		->getPage( 'Upload' );
		return $this->ensureMessageObject( $specialpage->getDescription() );
	}
}
