<?php

namespace BlueSpice\Discovery\MenuProvider;

use BlueSpice\Discovery\Component\EnhancedMediawikiSidebar as ComponentEnhancedMediawikiSidebar;
use BlueSpice\Discovery\EnhancedSidebar\Parser as EnhancedSidebarParser;
use BlueSpice\Discovery\IMenuProvider;
use MediaWiki\Logger\LoggerFactory;
use MediaWiki\Revision\RevisionStore;
use Message;
use MWStake\MediaWiki\Component\CommonUserInterface\IComponent;
use MWStake\MediaWiki\Component\Wikitext\ParserFactory;
use Psr\Log\LoggerInterface;
use RawMessage;
use Title;
use TitleFactory;

class EnhancedMediawikiSidebar implements IMenuProvider {
	/** @var LoggerInterface */
	private $logger;
	/** @var EnhancedSidebarParser */
	private $parser = null;
	/**
	 * @var RevisionStore
	 */
	private $revisionStore;
	/**
	 * @var Title
	 */
	private $title;
	/** @var ParserFactory */
	private $parserFactory;

	/**
	 * @param RevisionStore $revisionStore
	 * @param TitleFactory $titleFactory
	 * @param ParserFactory $parserFactory
	 * @param string $pagename
	 */
	public function __construct(
		RevisionStore $revisionStore, TitleFactory $titleFactory,
		ParserFactory $parserFactory, string $pagename
	) {
		$this->logger = LoggerFactory::getInstance( 'bluespicediscovery' );
		$this->title = $titleFactory->newFromText( $pagename );
		$this->revisionStore = $revisionStore;
		$this->parserFactory = $parserFactory;
	}

	/**
	 * @return string
	 */
	public function getName(): string {
		return 'enhanced-mediawiki-sidebar';
	}

	/**
	 * @return Message
	 */
	public function getLabelMsg(): Message {
		return new Message( 'bs-discovery-menu-provider-enhanced-mediawiki-sidebar-tree-label' );
	}

	/**
	 * @return Message
	 */
	public function getDescriptionMsg(): Message {
		return new RawMessage( 'bs-discovery-menu-provider-enhanced-mediawiki-sidebar-tree-desc' );
	}

	/**
	 * @return IComponent
	 */
	public function getComponent(): IComponent {
		if ( $this->title instanceof Title ) {
			$revision = $this->revisionStore->getRevisionByTitle( $this->title );
			$this->parser = new EnhancedSidebarParser(
				$revision,
				$this->parserFactory->getNodeProcessors()
			);
		}
		if ( !$this->parser ) {
			throw new \MWException( 'EnhancedSidebar cannot be parsed' );
		}
		$sidebarComponent = new ComponentEnhancedMediawikiSidebar( $this->parser );
		$sidebarComponent->setLogger( $this->logger );
		return $sidebarComponent;
	}
}
