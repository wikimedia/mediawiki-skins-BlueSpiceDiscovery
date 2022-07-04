<?php

namespace BlueSpice\Discovery\Component;

use BlueSpice\Timestamp;
use HtmlArmor;
use MediaWiki\Linker\LinkRenderer;
use MediaWiki\MediaWikiServices;
use MediaWiki\Revision\RevisionRecord;
use MediaWiki\Revision\RevisionStore;
use Message;
use MWStake\MediaWiki\Component\CommonUserInterface\Component\Literal;
use RequestContext;
use Title;
use User;
use WikiPage;

class LastEditInfo extends Literal {

	/**
	 *
	 * @var RequestContext
	 */
	private $requestContext = null;

	/**
	 *
	 * @var LinkRenderer
	 */
	private $linkRenderer = null;

	/**
	 *
	 * @var RevisionStore
	 */
	private $revisionStore = null;

	/**
	 *
	 * @param RequestContext $requestContext
	 * @param LinkRenderer $linkRenderer
	 * @param RevisionStore $revisionStore
	 */
	public function __construct( $requestContext, $linkRenderer, $revisionStore ) {
		parent::__construct(
			'last-edit-info',
			''
		);

		$this->requestContext = $requestContext;
		$this->linkRenderer = $linkRenderer;
		$this->revisionStore = $revisionStore;

		$this->services = MediaWikiServices::getInstance();
	}

	/**
	 * Raw HTML string
	 *
	 * @return string
	 */
	public function getHtml(): string {
		return $this->buildHtml();
	}

	/**
	 *
	 * @return string
	 */
	private function buildHtml(): string {
		$html = '';
		$this->requestContext = RequestContext::getMain();
		$title = Title::newFromDbKey(
			$this->requestContext->getTitle()
		);
		if ( !$title || $title->isSpecialPage() ) {
			return $html;
		}

		$wikiPage = WikiPage::factory( $title );
		$revision = $this->revisionStore->getRevisionByTitle( $wikiPage->getTitle() );
		if ( !$revision ) {
			return $html;
		}

		$revisionDiffLink = $this->buildRevisionDiffLink( $wikiPage, $revision );
		$lastEditorLink = $this->buildLastEditorLink( $wikiPage, $revision );

		$lastEditInfo = Message::newFromKey(
			'bs-discovery-title-last-edit-info',
			$revisionDiffLink,
			$lastEditorLink
		);

		if ( $lastEditInfo ) {
			$html .= $lastEditInfo->text();
		}

		return $html;
	}

	/**
	 *
	 * @param WikiPage $wikiPage
	 * @param RevisionRecord|null $revision
	 * @return string
	 */
	private function buildRevisionDiffLink( $wikiPage, $revision ): string {
		$html = '';
		$rawTimestamp = $revision->getTimestamp();
		/** @var Timestamp */
		$revisionTimestamp = Timestamp::getInstance( $rawTimestamp );
		$timestamp = $revisionTimestamp->getAgeString( null, null, 1 );
		$ariaLabel = $lastEditInfo = Message::newFromKey(
			'bs-discovery-title-last-edit-info-timestamp-aria-label',
			$timestamp
		);

		$html = $this->linkRenderer->makeLink(
			$wikiPage->getTitle(),
			new HtmlArmor( $timestamp ),
			[
				'title' => $timestamp,
				'aria-label' => $ariaLabel,
				'role' => 'link'
			],
			[
				'oldid' => $revision->getId(),
				'diff' => 'prev'
			]
		);
		return $html;
	}

	/**
	 *
	 * @param WikiPage $wikiPage
	 * @param RevisionRecord|null $revision
	 * @return string
	 */
	private function buildLastEditorLink( $wikiPage, $revision ): string {
		$html = '';
		$userIdentity = $revision->getUser();
		$user = User::newFromId( $userIdentity->getId() );
		$username = $user->getName();
		if ( $user->getRealName() !== '' ) {
			$username = $user->getRealName();
		}

		/* Main_page is created with user id 0 */
		if ( $userIdentity->getId() === 0 ) {

			if ( $user->isSystemUser() ) {
				$html = Message::newFromKey( 'bs-discovery-last-edit-by-system-user' );
				return $html->text();
			}

			if ( $user->isAnon() ) {
				$html = Message::newFromKey( 'bs-discovery-last-edit-by-anon-user' );
				return $html->text();
			}

		}

		$html = $this->linkRenderer->makeLink(
			$user->getUserPage(),
			new HtmlArmor( $username ),
			[
				'aria-label' => Message::newFromKey( 'bs-discovery-title-last-edit-info-user-aria-label', $username ),
				'role' => 'link'
			],
			[]
		);
		return $html;
	}
}
