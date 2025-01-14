<?php

namespace BlueSpice\Discovery\BackLinkProvider;

use BlueSpice\Discovery\IBackLinkProvider;
use MediaWiki\Message\Message;
use MediaWiki\Title\TitleFactory;

class PagesBackLinkProvider implements IBackLinkProvider {

	/** @var TitleFactory */
	private $titleFactory;

	/** @var Title */
	private $backToTitle = null;

	public function __construct( TitleFactory $titleFactory ) {
		$this->titleFactory = $titleFactory;
		$this->backToTitle = null;
	}

	/**
	 *
	 * @inheritDoc
	 */
	public function applies( $context ): bool {
		$request = $context->getRequest();
		$backToValue = $request->getVal( 'backTo' );

		if ( !$backToValue ) {
			return false;
		}
		$this->backToTitle = $this->titleFactory->newFromText( $backToValue );
		return true;
	}

	/**
	 *
	 * @inheritDoc
	 */
	public function getHref(): string {
		return $this->backToTitle->getLocalURL();
	}

	/**
	 *
	 * @inheritDoc
	 */
	public function getLabel(): Message {
		return Message::newFromKey( 'bs-discovery-back-to-page', $this->backToTitle->getText() );
	}

	/**
	 *
	 * @inheritDoc
	 */
	public function getTitle(): Message {
		return Message::newFromKey( 'bs-discovery-back-to-page-title', $this->backToTitle->getText() );
	}

	/**
	 *
	 * @inheritDoc
	 */
	public function getAriaLabel(): Message {
		return Message::newFromKey( 'bs-discovery-back-to-page', $this->backToTitle->getText() );
	}
}
