<?php

namespace BlueSpice\Discovery\HookHandler;

use MediaWiki\Hook\PageMoveCompleteHook;
use MediaWiki\Page\Hook\PageDeleteCompleteHook;
use MediaWiki\Session\SessionManager;
use MediaWiki\Storage\Hook\PageSaveCompleteHook;
use MediaWiki\Title\Title;

class PageActions implements PageSaveCompleteHook, PageMoveCompleteHook, PageDeleteCompleteHook {

	private const SESSION_KEY = 'discovery-ns-tree-invalidate';

	/**
	 * @inheritDoc
	 */
	public function onPageSaveComplete(
		$wikiPage, $user, $summary, $flags, $revisionRecord, $editResult
	): void {
		if ( !$editResult->isNew() ) {
			return;
		}
		$this->invalidateNamespace( $wikiPage->getTitle() );
	}

	/**
	 * @inheritDoc
	 */
	public function onPageMoveComplete(
		$old, $new, $user, $pageid, $redirid, $reason, $revision
	): void {
		// Both source and target namespace may be affected.
		$this->invalidateNamespace( Title::castFromLinkTarget( $old ) );
		$this->invalidateNamespace( Title::castFromLinkTarget( $new ) );
	}

	/**
	 * @inheritDoc
	 */
	public function onPageDeleteComplete(
		$page, $deleter, $reason, $pageID, $deletedRev, $logEntry, $archivedRevisionCount
	): void {
		$this->invalidateNamespace( $page->getTitle() );
	}

	/**
	 * Mark the namespace of the given title as requiring a cache invalidation
	 * on the next page load. Multiple namespaces can be queued simultaneously.
	 *
	 * @param Title|null $title
	 */
	private function invalidateNamespace( ?Title $title ): void {
		if ( $title === null ) {
			return;
		}
		if ( !$title->isContentPage() ) {
			return;
		}
		$nsId = $title->getNamespace();
		$session = SessionManager::getGlobalSession();
		$pending = $session->get( self::SESSION_KEY, [] );
		if ( !in_array( $nsId, $pending, true ) ) {
			$pending[] = $nsId;
			$session->set( self::SESSION_KEY, $pending );
		}
	}
}
