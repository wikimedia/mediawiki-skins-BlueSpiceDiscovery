<?php

namespace BlueSpice\Discovery\Renderer;

use BlueSpice\Discovery\BreadcrumbDataProvider;
use MediaWiki\Context\RequestContext;
use MediaWiki\Language\RawMessage;
use MediaWiki\SpecialPage\SpecialPageFactory;
use MediaWiki\Title\NamespaceInfo;
use MediaWiki\Title\Title;
use MediaWiki\User\User;
use MessageLocalizer;
use Wikimedia\CSS\Sanitizer\StyleAttributeSanitizer;

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

	/** @var NamespaceInfo */
	private $namespaceInfo = null;

	/** @var IBreadcrumbRootProvider */
	private $rootProvider = null;

	/** @var StyleAttributeSanitizer */
	private $styleSanitizer = null;

	/**
	 * @param Title $title
	 * @param User $user
	 * @param MessageLocalizer $messageLocalizer
	 * @param SpecialPageFactory $specialPageFactory
	 * @param NamespaceInfo $namespaceInfo
	 * @param BreadcrumbDataProviderFactory $breadcrumbProviderFactory
	 * @param StyleAttributeSanitizer $styleSanitizer
	 */
	public function __construct( $title, $user, $messageLocalizer, $specialPageFactory,
		$namespaceInfo, $breadcrumbProviderFactory, StyleAttributeSanitizer $styleSanitizer ) {
		parent::__construct();

		$this->title = $title;
		$this->user = $user;
		$this->messageLocalizer = $messageLocalizer;
		$this->specialPageFactory = $specialPageFactory;
		$this->namespaceInfo = $namespaceInfo;

		$this->breadcrumbProvider = $breadcrumbProviderFactory->getProviderForTitle( $title, $user );
		$this->rootProvider = $breadcrumbProviderFactory->getRootProviderForTitle( $title );
		$this->styleSanitizer = $styleSanitizer;
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
		$rootNodes = $this->rootProvider->getNodes( $this->relevantTitle );
		foreach ( $rootNodes as $node ) {
			if ( !isset( $node['style'] ) ) {
				continue;
			}
			$node['style'] = $this->styleSanitizer->sanitizeString( $node['style'] );
		}
		$this->options['rootNode'] = $rootNodes;
	}

	/**
	 * @param array $nodesData
	 * @return void
	 */
	private function buildNodes( $nodesData ) {
		$nodes = [];
		$firstNode = false;

		foreach ( $nodesData as $node ) {
			// Remove namespace prefix from nodeText
			if ( !$firstNode ) {
				$nodeTextParts = explode( ':', $node['nodeText'] );
				$firstNode = true;
			} else {
				$nodeTextParts = explode( ':', $node['nodeText'], 1 );
			}

			$nodeText = array_pop( $nodeTextParts );

			$nodeHTML = [
				'id' => md5( 'breadcrumb-nav-subpages-' . $node['id'] ),
				'button-text' => new RawMessage( trim( $nodeText ) ),
				'button-classes' => $node['classes'],
				'split-button-title' => $this->messageLocalizer
					->msg( 'bs-discovery-breadcrumb-nav-node-split-button-title' ),
				'split-button-aria-label' => $this->messageLocalizer
					->msg( 'bs-discovery-breadcrumb-nav-node-split-button-aria-label' ),
				'nodes-class' => [],
				'split-button-classes' => [ 'breadcrumb-nav-subpages', $node['splitBtnClass'] ],
				'path' => $node['path']
			];

			$isSelfLink = $this->breadcrumbProvider->isSelfLink( $node );

			if ( $isSelfLink ) {
				$nodeHTML = array_merge(
					$nodeHTML,
					[
						'tag' => 'span',
					]
				);
			} else {
				$nodeHTML = array_merge(
					$nodeHTML,
					[
						'tag' => 'a',
						'button-title' => $this->messageLocalizer
							->msg( 'bs-discovery-breadcrumb-nav-node-title', $node['title'] )->text(),
						'button-href' => $node['url'],
						'button-aria-label' => $this->messageLocalizer
							->msg( 'bs-discovery-breadcrumb-nav-node-aria-label', $node['title'] )->text(),
					]
				);
			}

			// append subpage menu
			$requestContext = RequestContext::getMain();
			$action = $requestContext->getRequest()->getVal( 'action', 'view' );
			if ( !isset( $node['current'] ) || $node['current'] !== true || $action === 'view' ) {
				$nodeHTML = array_merge(
					$nodeHTML,
					[
						'hasItems' => $node['subpages'],
					]
				);
			}

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
