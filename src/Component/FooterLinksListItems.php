<?php

namespace BlueSpice\Discovery\Component;

use Html;
use IContextSource;
use MediaWiki\Extension\MenuEditor\Node\TwoFoldLinkSpec;
use MediaWiki\Extension\MenuEditor\Parser\WikitextMenuParser;
use MediaWiki\MediaWikiServices;
use MediaWiki\Permissions\Authority;
use MediaWiki\Revision\RevisionStore;
use MediaWiki\Utils\UrlUtils;
use Message;
use MWStake\MediaWiki\Component\CommonUserInterface\Component\Literal;
use MWStake\MediaWiki\Component\CommonUserInterface\LinkFormatter;
use MWStake\MediaWiki\Component\Wikitext\ParserFactory;
use MWStake\MediaWiki\Lib\Nodes\INode;
use Skin;
use Title;
use TitleFactory;

class FooterLinksListItems extends Literal {

	/** @var Skin */
	private $skin;

	/** @var Title */
	private $footerLinksSourceTitle;

	/** @var TitleFactory */
	private $titleFactory;

	/** @var UrlUtils */
	private $urlUtils;

	/** @var LinkFormatter */
	private $linkFormatter;

	/** @var ParserFactory */
	private $parserFactory;

	/** @var RevisionStore */
	private $revisionStore;

	/**
	 * @param MediaWikiServices $services
	 * @param Skin $skin
	 */
	public function __construct(
		Skin $skin, TitleFactory $titleFactory, RevisionStore $revisionStore,
		UrlUtils $urlUtils, LinkFormatter $linkFormatter, ParserFactory $parserFactory
	) {
		$this->skin = $skin;
		parent::__construct( '', '' );

		/** @var TitleFactory */
		$this->titleFactory = $titleFactory;
		$this->urlUtils = $urlUtils;
		$this->linkFormatter = $linkFormatter;
		$this->parserFactory = $parserFactory;
		$this->revisionStore = $revisionStore;
		$this->footerLinksSourceTitle = $this->titleFactory->makeTitle( NS_MEDIAWIKI, 'FooterLinks' );
	}

	/** @inheritDoc */
	public function getId(): string {
		return 'footerlinks';
	}

	/** @inheritDoc */
	public function shouldRender( IContextSource $context ): bool {
		return $context->getAuthority()->isAllowed( 'read' );
	}

	/** @inheritDoc */
	public function getHtml(): string {
		$customFooterLinks = $this->getCustomFooterLinks();

		$items = [];
		foreach ( $customFooterLinks as $link ) {
			$item = Html::openElement( 'li' );
			$item .= $link;
			$item .= Html::closeElement( 'li' );

			$items[] = $item;
		}

		/** @var Authority */
		$authority = $this->skin->getContext()->getAuthority();
		if ( $authority->isAllowed( 'editinterface' ) ) {
			$item = Html::openElement(
				'li',
				[
					'class' => 'edit-footerlinks-link',
				]
			);
			$item .= $this->getEditLink();
			$item .= Html::closeElement( 'li' );

			$items[] = $item;
		}

		return implode( '', $items );
	}

	/**
	 * @return array
	 */
	private function getCustomFooterLinks(): array {
		$parserData = $this->getParserData();
		$customFooterLinksData = $this->buildCustomFooterLinksData( $parserData );

		$links = [];
		foreach ( $customFooterLinksData as $data ) {
			$text = $data['text'];
			unset( $data['text'] );

			$links[] = Html::element(
				'a',
				$data,
				htmlspecialchars( $text )
			);
		}

		return $links;
	}

	/**
	 * @param INode[] $parserData
	 * @return array
	 */
	private function buildCustomFooterLinksData( array $parserData ): array {
		$links = [];
		foreach ( $parserData as $dataItem ) {
			if ( !is_a( $dataItem, 'MWStake\MediaWiki\Lib\Nodes\INode', true ) ) {
				continue;
			}

			/** @var TwoFoldLinkSpec $dataItem */
			$url = $this->skin->makeInternalOrExternalUrl( $dataItem->getTarget() );
			$data = [
				'text' => $dataItem->getLabel(),
				'href' => $url,
				'role' => 'link'
			];

			$parsedURL = $this->urlUtils->parse( $dataItem->getTarget() );
			if ( !$parsedURL ) {
				$title = $this->titleFactory->newFromText( $dataItem->getTarget() );
				if ( !$title->exists() ) {
					$data['class'] = 'new';
				}
			}

			$links[] = $data;
		}

		if ( empty( $links ) ) {
			return [];
		}

		$formattedLinks = $this->linkFormatter->formatLinks( $links );

		return $formattedLinks;
	}

	/**
	 * @return string
	 */
	private function getEditLink(): string {
		$params = [
			'role' => 'link',
			'id' => 'edit-footerlinks-link',
			'href' => $this->footerLinksSourceTitle->getEditURL()
		];

		if ( !$this->footerLinksSourceTitle->exists() ) {
			$params['class'] = 'new';
		}

		return Html::element(
			'a',
			$params,
			Message::newFromKey( 'bs-discovery-edit-footerlinks-link-text' )
		);
	}

	/**
	 * @return INode[]
	 */
	private function getParserData(): array {
		if ( $this->footerLinksSourceTitle instanceof Title === false
			|| !$this->footerLinksSourceTitle->exists()
		) {
			return [];
		}

		$parser = new WikitextMenuParser(
			$this->revisionStore->getRevisionByTitle( $this->footerLinksSourceTitle ),
			$this->parserFactory->getNodeProcessors()
		);

		if ( !$parser ) {
			return [];
		}

		try {
			return $parser->parse();
		} catch ( \Exception $ex ) {
			return [];
		}
	}
}
