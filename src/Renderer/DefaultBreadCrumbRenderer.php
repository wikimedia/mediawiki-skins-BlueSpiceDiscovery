<?php

namespace BlueSpice\Discovery\Renderer;

use BlueSpice\Discovery\BreadcrumbDataProvider;
use MediaWiki\Special\SpecialPageFactory;
use MessageLocalizer;
use Title;
use User;

class DefaultBreadCrumbRenderer extends TemplateRendererBase {

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
	 * @var MessageLocalizer
	 */
	private $messageLocalizer;

	/**
	 *
	 * @var SpecialPageFactory
	 */
	private $specialPageFactory;

	/**
	 *
	 * @var Title
	 */
	private $relevantTitle = null;

	/**
	 * @var BreadcrumbDataProvider
	 */
	private $breadcrumbProvider = null;

	/**
	 * @param Title $title
	 * @param User $user
	 * @param MessageLocalizer $messageLocalizer
	 * @param SpecialPageFactory $specialPageFactory
	 * @param NamespaceInfo $namespaceInfo
	 * @param BreadcrumbDataProviderFactory $breadcrumbProviderFactory
	 */
	public function __construct( $title, $user, $messageLocalizer, $specialPageFactory,
		$namespaceInfo, $breadcrumbProviderFactory ) {
		parent::__construct();

		$this->title = $title;
		$this->user = $user;
		$this->messageLocalizer = $messageLocalizer;
		$this->specialPageFactory = $specialPageFactory;
		$this->namespaceInfo = $namespaceInfo;

		$this->breadcrumbProvider = $breadcrumbProviderFactory->getProviderForTitle( $title, $user );
	}

	/**
	 *
	 * @return array
	 */
	public function getParams(): array {
		$this->relevantTitle = $this->breadcrumbProvider->getRelevantTitle( $this->title );
		$this->buildRootNode();

		$nodes = $this->breadcrumbProvider->getNodes( $this->relevantTitle );
		$this->buildNodes( $nodes );
		$labels = $this->breadcrumbProvider->getLabels( $this->title );

		$this->options['labels'] = $labels;

		return $this->options;
	}

	/**
	 *
	 * @return void
	 */
	private function buildRootNode() {
		$rootNodeText = $this->relevantTitle->getNsText();
		if ( $this->relevantTitle->getNamespace() === 0 ) {
			$rootNodeText = $this->messageLocalizer->msg(
				'bs-discovery-breadcrumb-nav-node-ns-main' )->plain();
		}

		if ( $this->relevantTitle->isTalkPage() ) {
			if ( $this->relevantTitle->getNamespace() === 1 ) {
				$rootNodeText = $this->messageLocalizer->msg(
					'bs-discovery-breadcrumb-nav-node-ns-main' )->plain();
			} else {
				$rootNodeText = $this->relevantTitle->getSubjectNsText();
			}
		}
		$titleMainPage = null;
		if ( $this->relevantTitle->isSpecialPage() ) {
			$titleMainPage = $this->specialPageFactory->getTitleForAlias( 'Specialpages' );

			$rootNodeText = $this->relevantTitle->getPageLanguage()->getNsText(
				$this->relevantTitle->getNamespace() );
			$rootNodeUrl = $titleMainPage->getLocalURL();
		}
		if ( $titleMainPage === null ) {
			$titleMainPage = Title::makeTitleSafe( $this->relevantTitle->getNamespace(),
			Title::newMainPage()->getDBkey() );

			if ( $titleMainPage->isMainPage() ) {
				$rootNodeUrl = $titleMainPage->getLocalURL();
			} else {
				$titleMainPage = $this->specialPageFactory->getTitleForAlias( 'Allpages' );
				$rootNodeUrl = $titleMainPage->getLocalURL(
					'namespace=' . $this->relevantTitle->getNamespace() );
			}
		}

		$this->options['rootNode'] = [
			'text' => $rootNodeText,
			'href' => $rootNodeUrl,
			'role' => 'link',
			'title' => $rootNodeText,
			'aria-label' => $this->messageLocalizer->msg(
				'bs-discovery-breadcrumb-nav-node-ns-aria-label' )->text()
		];
	}

	/**
	 * @param array $nodesData
	 * @return void
	 */
	private function buildNodes( $nodesData ) {
		$nodes = [];

		foreach ( $nodesData as $node ) {
			$nodeTextParts = explode( ':', $node['nodeText'] );
			$nodeText = array_pop( $nodeTextParts );
			$nodeHTML = [
				'id' => md5( 'breadcrumb-nav-subpages-' . $node['id'] ),
				'button-text' => new \RawMessage( $nodeText ),
				'button-classes' => $node['classes'],
				'button-title' => $this->messageLocalizer
					->msg( 'bs-discovery-breadcrumb-nav-node-title', $node['title'] ),
				'button-href' => $node['url'],
				'button-aria-label' => $this->messageLocalizer
					->msg( 'bs-discovery-breadcrumb-nav-node-aria-label', $node['title'] ),
				'split-button-title' => $this->messageLocalizer
					->msg( 'bs-discovery-breadcrumb-nav-node-split-button-title' ),
				'split-button-aria-label' => $this->messageLocalizer
					->msg( 'bs-discovery-breadcrumb-nav-node-split-button-aria-label' ),
				'nodes-class' => [],
				'split-button-classes' => [ 'breadcrumb-nav-subpages', $node['splitBtnClass'] ],
				'hasitems' => $node['subpages'],
				'path' => $node['path']
			];
			array_push( $nodes, $nodeHTML );
		}

		$this->options['nodes'] = $nodes;
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
