<?php

namespace BlueSpice\Discovery\Component;

use BlueSpice\Discovery\CookieHandler;
use BlueSpice\Discovery\EnhancedSidebar\Parser as EnhancedSidebarParser;
use MediaWiki\Context\RequestContext;
use MediaWiki\Message\Message;
use MediaWiki\Revision\RevisionStore;
use MediaWiki\Title\Title;
use MWStake\MediaWiki\Component\CommonUserInterface\Component\Container;
use MWStake\MediaWiki\Component\CommonUserInterface\Component\RestrictedTextLink;
use MWStake\MediaWiki\Component\CommonUserInterface\IComponent;
use MWStake\MediaWiki\Component\CommonUserInterface\TreeDataGenerator;
use MWStake\MediaWiki\Component\Wikitext\ParserFactory;
use Psr\Log\LoggerAwareInterface;
use Psr\Log\LoggerInterface;

class EnhancedSidebarContainer extends Container implements LoggerAwareInterface {

	/**
	 * @var LoggerInterface
	 */
	private $logger;

	/**
	 * @var string
	 */
	private $id = '';

	/**
	 * @var Title
	 */
	private $title;

	/**
	 * @var RevisionStore
	 */
	private $revisionStore;

	/**
	 * @var ParserFactory
	 */
	private $parserFactory;

	/**
	 * @var TreeDataGenerator
	 */
	private $treeDataGenerator;

	/**
	 * @var CookieHandler
	 */
	protected $cookieHandler = null;

	/**
	 * @param string $id
	 * @param Title $title
	 * @param RevisionStore $revisionStore
	 * @param ParserFactory $parserFactory
	 * @param TreeDataGenerator $treeDataGenerator
	 * @param CookieHandler $cookieHandler
	 */
	public function __construct( string $id, Title $title, RevisionStore $revisionStore,
		ParserFactory $parserFactory, TreeDataGenerator $treeDataGenerator, CookieHandler $cookieHandler
	) {
		parent::__construct( [] );

		$this->id = $id;
		$this->title = $title;
		$this->revisionStore = $revisionStore;
		$this->parserFactory = $parserFactory;
		$this->treeDataGenerator = $treeDataGenerator;
		$this->cookieHandler = $cookieHandler;
	}

	/**
	 * @param LoggerInterface $logger
	 * @return void
	 */
	public function setLogger( LoggerInterface $logger ) {
		$this->logger = $logger;
	}

	/**
	 * @inheritDoc
	 */
	public function getId(): string {
		return $this->id . '-pnl';
	}

	/**
	 * @inheritDoc
	 */
	public function getTagName(): string {
		return 'div';
	}

	/**
	 * @inheritDoc
	 */
	public function getClasses(): array {
		return [ 'enhanced-sidebar-cnt' ];
	}

	/**
	 * @inheritDoc
	 */
	public function getSubComponents(): array {
		$subComponents = $this->buildPanels();

		$editLink = $this->buildEditLink();
		$subComponents[] = $editLink;

		return $subComponents;
	}

	/**
	 * @return array
	 */
	private function buildPanels(): array {
		$parserData = $this->getParserData();

		if ( empty( $parserData ) ) {
			return [];
		}

		$panels = [];
		foreach ( $parserData as $panelData ) {
			$panels[] = $this->buildPanel( $panelData );
		}

		return $panels;
	}

	/**
	 * @return IComponent
	 */
	private function buildPanel( array $panelData ): IComponent {
		$panelId = $panelData['id'];
		$panelHeading = htmlspecialchars( $panelData['text'] );
		$panelItems = $panelData['items'];
		$classes = [];
		if ( isset( $panelData['classes'] ) ) {
			$classes = $panelData['classes'];
		}

		// Custom messages as heading for sections
		$customHeaderTextMsg = Message::newFromKey( $panelHeading );
		if ( $customHeaderTextMsg->exists() ) {
			$panelHeading = $customHeaderTextMsg->escaped();
		}

		$panel = new EnhancedSidebarPanel(
			$panelId,
			$panelHeading,
			$classes,
			$panelItems,
			$this->treeDataGenerator,
			$this->cookieHandler
		);

		$panel->setLogger( $this->logger );

		return $panel;
	}

	/**
	 * @return array
	 */
	private function getParserData(): array {
		if ( $this->title instanceof Title === false ) {
			return [];
		}

		if ( !$this->title->exists() ) {
			return [];
		}

		$revision = $this->revisionStore->getRevisionByTitle( $this->title );
		$parser = new EnhancedSidebarParser(
			$revision,
			$this->parserFactory->getNodeProcessors()
		);

		if ( !$parser ) {
			return [];
		}

		try {
			return $parser->parseForOutput( RequestContext::getMain()->getUser() );
		} catch ( \Exception $ex ) {
			$this->logger->error(
				'EnhancedSidebarParser failed to parse sidebar',
				[
					'exception' => $ex
				]
			);
			return [];
		}
	}

	/**
	 *
	 * @return IComponent
	 */
	private function buildEditLink(): IComponent {
		$item = new RestrictedTextLink( [
			'role' => 'link',
			'id' => $this->id . '-edit-sidebar-link',
			'href' => $this->title->getEditURL(),
			'text' => Message::newFromKey( 'bs-discovery-edit-mediawiki-sidebar-link-text' ),
			'title' => Message::newFromKey( 'bs-discovery-enhanced-sidebar-edit-button-title' ),
			'aria-label' => Message::newFromKey( 'bs-discovery-edit-mediawiki-sidebar-link-text' ),
			'permissions' => [ 'editinterface' ],
		] );

		return $item;
	}
}
