<?php

namespace BlueSpice\Discovery\Renderer;

use MediaWiki\Special\SpecialPageFactory;
use MessageLocalizer;
use NamespaceInfo;
use RawMessage;
use Title;
use TitleFactory;
use User;

class DefaultBreadCrumbRenderer extends TemplateRendererBase {
	/**
	 * SpecialPages where "subpage" params will not be evaluated
	 *
	 * Could be a config or a registry but that's too much overhead
	 * since i can think of any other SP that needs to be blacklisted
	 * @var string[]
	 */
	private $specialPageLabelBlacklist = [
		'Ask'
	];

	/**
	 * @var User
	 */
	private $user;

	/**
	 * @var Title
	 */
	private $title;

	/**
	 *
	 * @var array
	 */
	private $webRequestValues;

	/**
	 *
	 * @var MessageLocalizer
	 */
	private $messageLocalizer;

	/**
	 *
	 * @var TitleFactory
	 */
	private $titleFactory;

	/**
	 *
	 * @var SpecialPageFactory
	 */
	private $specialPageFactory;

	/**
	 *
	 * @var NamespaceInfo
	 */
	private $namespaceInfo;

	/**
	 *
	 * @var Title
	 */
	private $relevantTitle = null;

	/**
	 *
	 * @var bool
	 */
	private $talkName = false;

	/**
	 *
	 * @var bool
	 */
	private $specialName = false;
	/** @var array */
	private $specialPageParams = [];

	/**
	 * @param Title $title
	 * @param User $user
	 * @param array $webRequestValues
	 * @param MessageLocalizer $messageLocalizer
	 * @param TitleFactory $titleFactory
	 * @param SpecialPageFactory $specialPageFactory
	 * @param NamespaceInfo $namespaceInfo
	 */
	public function __construct( $title, $user, $webRequestValues, $messageLocalizer,
		$titleFactory, $specialPageFactory, $namespaceInfo ) {
		parent::__construct();

		$this->title = $title;
		$this->user = $user;
		$this->webRequestValues = $webRequestValues;
		$this->messageLocalizer = $messageLocalizer;
		$this->titleFactory = $titleFactory;
		$this->specialPageFactory = $specialPageFactory;
		$this->namespaceInfo = $namespaceInfo;
	}

	/**
	 *
	 * @return array
	 */
	public function getParams(): array {
		$this->extractRelevantTitle();

		$this->buildRootNode();
		$this->buildNodes();
		$this->buildLabels();

		return $this->options;
	}

	/**
	 *
	 * @return void
	 */
	private function extractRelevantTitle() {
		$this->relevantTitle = $this->title;

		// `Special:Move/Talk:Some_Page/with/Subpage`
		if ( $this->relevantTitle->isSpecialPage() ) {
			$this->specialName = $this->relevantTitle->getDBkey();
			$fullPageTitle = $this->relevantTitle->getPrefixedDBkey();
			if ( class_exists( 'SMW\Encoder' ) ) {
				$fullPageTitle = \SMW\Encoder::decode( $fullPageTitle );
			}

			// `[ "Special:Move/MyPage", "Talk:Some_Page/with/Subpage" ]`
			$titleParts = explode( '/', $fullPageTitle );
			$this->relevantTitle = $this->titleFactory->newFromText( array_shift( $titleParts ) );
			if ( !empty( $titleParts ) ) {
				$this->specialPageParams = $titleParts;
			}
		}

		if ( $this->relevantTitle->isTalkPage() ) {
			$this->talkName = true;
			$titleNamespace = $this->namespaceInfo->getSubjectPage( $this->relevantTitle );

			$this->relevantTitle = $this->titleFactory->makeTitle(
				$titleNamespace->getNamespace(),
				$titleNamespace->getDBkey()
			);
		}
	}

	/**
	 *
	 * @return void
	 */
	private function buildRootNode() {
		$rootNodeText = $this->title->getNsText();
		if ( $this->title->getNamespace() === 0 ) {
			$rootNodeText = $this->messageLocalizer->msg(
				'bs-discovery-breadcrumb-nav-node-ns-main' )->plain();
		}

		if ( $this->title->isTalkPage() ) {
			if ( $this->title->getNamespace() === 1 ) {
				$rootNodeText = $this->messageLocalizer->msg(
					'bs-discovery-breadcrumb-nav-node-ns-main' )->plain();
			} else {
				$rootNodeText = $this->title->getSubjectNsText();
			}
		}

		$titleMainPage = null;
		if ( $this->title->isSpecialPage() ) {
			if ( strpos( $this->title->getBaseText(), '/' ) ) {
				$fullPageTitle = $this->relevantTitle->getPrefixedDBkey();

				if ( strpos( $fullPageTitle, ':' ) ) {
					$titleParts = explode( ':', $fullPageTitle );
					$rootNodeText = $titleParts[0];
				} else {
					$rootNodeText = $this->messageLocalizer->msg(
						'bs-discovery-breadcrumb-nav-node-ns-main' )->plain();
				}
			} else {
				$this->specialName = false;
				$this->relevantTitle = $this->title;
			}
			$titleMainPage = $this->specialPageFactory->getTitleForAlias( 'Specialpages' );
			if ( strpos( $titleMainPage, ':' ) ) {
				$titleParts = explode( ':', $titleMainPage );
				$rootNodeText = $titleParts[0];
			}
			$rootNodeUrl = $titleMainPage->getLocalURL();
		}
		if ( $titleMainPage === null ) {
			$titleMainPage = Title::makeTitleSafe( $this->relevantTitle->getNamespace(),
			Title::newMainPage()->getDBkey() );
		}

		$this->options['rootNode'] = [
			'text' => $rootNodeText,
			'href' => $titleMainPage->getLocalURL(),
			'role' => 'link',
			'title' => $rootNodeText,
			'aria-label' => $this->messageLocalizer->msg(
				'bs-discovery-breadcrumb-nav-node-ns-aria-label' )->text()
		];
	}

	/**
	 *
	 * @return void
	 */
	private function buildNodes() {
		$nodeTitleParts = explode( '/', $this->relevantTitle );
		$numberOfParts = count( $nodeTitleParts );
		$nodeTitleName = '';
		$nodes = [];

		for ( $index = 0; $index < $numberOfParts; $index++ ) {
			$nodeTitleName .= '/' . $nodeTitleParts[$index];

			$nodeTitle = Title::newFromText(
				trim( $nodeTitleName, '/' ),
				$this->relevantTitle->getNamespace()
			);

			$path = '';
			if ( $this->relevantTitle->getNamespace() === 0 ) {
				$path = ':';
			}
			$path .= $nodeTitle->getPrefixedDBkey();

			$classes = [];
			$current = false;
			$subpages = true;
			$btnClass = '';

			if ( !$nodeTitle->exists() && !$nodeTitle->isSpecialPage() ) {
				$classes[] = 'new';
			}

			if ( $nodeTitle->equals( $this->relevantTitle ) ) {
				// See https://getbootstrap.com/docs/5.0/components/breadcrumb/
				$current = true;
				$classes[] = 'active';

				// We assume that _all_ nodes have subpages, as also non existing ones will be listed
				// Only the leaf node must be checked explicitly
				if ( $nodeTitle->hasSubpages() ) {
					$subpages = true;
				} else {
					$subpages = false;
					$btnClass = 'd-none';
				}
			}

			if ( strpos( $nodeTitleParts[$index], ':' ) ) {
				$text = explode( ':', $nodeTitleParts[$index], 2 );
				$nodeText = $text[1];
			} else {
				$nodeText = $nodeTitleParts[$index];
			}

			$node = [
				'id' => md5( 'breadcrumb-nav-subpages-' . $nodeTitle->getArticleID() ),
				'button-text' => new \RawMessage( $nodeText ),
				'button-classes' => $classes,
				'button-title' => $this->messageLocalizer
					->msg( 'bs-discovery-breadcrumb-nav-node-title', $nodeTitle->getFullText() ),
				'button-href' => $nodeTitle->getLocalURL(),
				'button-aria-label' => $this->messageLocalizer
					->msg( 'bs-discovery-breadcrumb-nav-node-aria-label', $nodeTitle->getFullText() ),
				'split-button-title' => $this->messageLocalizer
					->msg( 'bs-discovery-breadcrumb-nav-node-split-button-title' ),
				'split-button-aria-label' => $this->messageLocalizer
					->msg( 'bs-discovery-breadcrumb-nav-node-split-button-aria-label' ),
				'nodes-class' => [],
				'split-button-classes' => [ 'breadcrumb-nav-subpages', $btnClass ],
				'hasitems' => $subpages,
				'path' => $path
			];
			array_push( $nodes, $node );
		}

		$this->options['nodes'] = $nodes;
	}

	/**
	 *
	 * @return void
	 */
	private function buildLabels() {
		$labels = [];
		if ( $this->talkName === true ) {
			$labels[] = [
				'text' => $this->messageLocalizer->msg( 'bs-discovery-breadcrumb-label-talk' )
			];
		}
		if ( $this->specialName ) {
			$special = $this->specialPageFactory->getPage( $this->specialName );
			if ( $special && !in_array( $special->getName(), $this->specialPageLabelBlacklist ) ) {
				foreach ( $this->specialPageParams as $param ) {
					$labels[] = [ 'text' => new RawMessage( $param ) ];
				}
			}
		}
		if ( isset( $this->webRequestValues['action'] ) ) {
			$msgKey = 'bs-discovery-breadcrumb-label-action-' . $this->webRequestValues['action'];
			$msgText = $this->messageLocalizer->msg( $msgKey );
			if ( !$msgText->exists() ) {
				$msgText = new RawMessage( $this->webRequestValues['action'] );
			}
			$labels[] = [ 'text' => $msgText ];
		}

		$this->options['labels'] = $labels;
	}

	/**
	 *
	 * @return string
	 */
	public function getTemplatePath(): string {
		return $GLOBALS['wgStyleDirectory'] .
		'/BlueSpiceDiscovery/resources/templates/renderer/default-breadcrumb';
	}
}
