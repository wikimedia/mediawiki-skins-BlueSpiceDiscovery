<?php

namespace BlueSpice\Discovery\EnhancedSidebar;

use BlueSpice\Discovery\EnhancedSidebar\Parser as EnhancedSidebarParser;
use MediaWiki\Extension\MenuEditor\ParsableMenu;
use MediaWiki\Extension\MenuEditor\Parser\IMenuParser;
use MediaWiki\Revision\MutableRevisionRecord;
use MediaWiki\Revision\SlotRecord;
use MediaWiki\Storage\RevisionRecord;
use MWException;
use MWStake\MediaWiki\Component\Wikitext\ParserFactory;
use Title;

class Menu implements ParsableMenu {
	/** @var string */
	private $pagename;
	/** @var ParserFactory */
	private $parserFactory;

	/**
	 * @param string $pagename
	 */
	public function __construct( ParserFactory $parserFactory, string $pagename ) {
		$this->pagename = $pagename;
		$this->parserFactory = $parserFactory;
	}

	/**
	 * @inheritDoc
	 */
	public function getRLModule(): string {
		return "skin.discovery.enhancedSidebar.tree";
	}

	/**
	 * @inheritDoc
	 */
	public function getJSClassname(): string {
		return "bs.skin.enhancedSidebar.tree.EnhancedSidebarTree";
	}

	/**
	 * @inheritDoc
	 */
	public function appliesToTitle( Title $title ): bool {
		return $title->getNamespace() === NS_MEDIAWIKI &&
			$title->getPrefixedDBkey() === $this->pagename &&
			$title->getContentModel() === 'json';
	}

	/**
	 * @inheritDoc
	 */
	public function getKey(): string {
		return 'enhanced-sidebar';
	}

	/**
	 * @inheritDoc
	 */
	public function getEmptyContent(): array {
		return [];
	}

	/**
	 * @param Title $title
	 * @param RevisionRecord|null $revisionRecord
	 *
	 * @return IMenuParser
	 * @throws MWException
	 */
	public function getParser( Title $title, ?RevisionRecord $revisionRecord = null ): IMenuParser {
		if ( !$revisionRecord ) {
			$content = new \JsonContent( '[]' );
			$revisionRecord = new MutableRevisionRecord( $title );
			$revisionRecord->setSlot(
				SlotRecord::newUnsaved(
					SlotRecord::MAIN,
					$content
				)
			);
		}
		return new EnhancedSidebarParser(
			$revisionRecord,
			$this->parserFactory->getNodeProcessors()
		);
	}
}
