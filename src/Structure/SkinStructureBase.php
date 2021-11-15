<?php

namespace BlueSpice\Discovery\Structure;

use BaseTemplate;
use BlueSpice\Discovery\ISkinLayout;
use BlueSpice\Discovery\ISkinStructure;
use BlueSpice\Discovery\SkinSlotRenderer\ExtendedSkinSlotRendererBase;
use IContextSource;
use MediaWiki\MediaWikiServices;
use MWStake\MediaWiki\Component\CommonUserInterface\ComponentManager;
use MWStake\MediaWiki\Component\CommonUserInterface\RendererDataTreeBuilder;
use MWStake\MediaWiki\Component\CommonUserInterface\RendererDataTreeRenderer;
use RequestContext;

abstract class SkinStructureBase implements ISkinStructure {

	/**
	 *
	 * @var RequestContext
	 */
	protected $context;

	/**
	 *
	 * @var LayoutBase
	 */
	protected $layout;

	/**
	 *
	 * @var OutputPage
	 */
	protected $out;

	/**
	 *
	 * @var BaseTemplate
	 */
	protected $template;

	/**
	 *
	 * @var MediaWikiServices
	 */
	protected $services = null;

	/**
	 *
	 * @var array
	 */
	protected $componentProcessData = [];

	/**
	 *
	 * @param IContextSource $layout
	 */
	public function __construct( $layout ) {
		$this->layout = $layout;
		$this->context = $layout->context;
		$this->out = $layout->context->getOutput();
		$this->template = $layout->template;
		$this->services = MediaWikiServices::getInstance();

		$templateDataProvider = $this->services->getService( 'BlueSpiceDiscoveryTemplateDataProvider' );
		$this->componentProcessData = $templateDataProvider->getAll();
	}

	/**
	 *
	 * @param IContextSource $layout
	 * @return ISkinLayout
	 */
	public static function factory( $layout ) {
		return new static( $layout );
	}

	/**
	 * Parse templates recursive
	 *
	 * @return bool
	 */
	public function enableRecursivePartials() : bool {
		return false;
	}

	/**
	 *
	 * @param IContextSource $context
	 * @return bool
	 */
	public function shouldRender( IContextSource $context ) : bool {
		return true;
	}

	/**
	 *
	 * @return array
	 */
	public function getParams() : array {
		return [];
	}

	/**
	 *
	 * @param string $skinSlotRegKey
	 * @return string
	 */
	protected function getSkinSlotHtml( $skinSlotRegKey ) : string {
		/** @var MediaWikiServices */
		$services = MediaWikiServices::getInstance();

		/** @var SkinSlotRendererFactory */
		$skinSlotRendererFactory = $services->get( 'MWStakeCommonUISkinSlotRendererFactory' );

		/** @var ExtendedSkinSlotRendererBase */
		$skinSlotRenderer = $skinSlotRendererFactory->create( $skinSlotRegKey );
		$html = $skinSlotRenderer->getHtml( $this->componentProcessData );

		return $html;
	}

	/**
	 *
	 * @param IComponent $component
	 * @return string
	 */
	protected function getComponentHtml( $component ) : string {
		/** @var MediaWikiServices */
		$services = MediaWikiServices::getInstance();

		/** @var ComponentManager */
		$componentManager = $services->getService( 'MWStakeCommonUIComponentManager' );

		/** @var RendererDataTreeBuilder */
		$rendererDataTreeBuilder = $services->getService( 'MWStakeCommonUIRendererDataTreeBuilder' );

		/** @var RendererDataTreeRenderer */
		$rendererDataTreeRenderer = $services->getService( 'MWStakeCommonUIRendererDataTreeRenderer' );

		$componentTree = $componentManager->getCustomComponentTree(
			$component,
			$this->componentProcessData
		);
		if ( empty( $componentTree ) ) {
			return '';
		}

		$rendererDataTree = $rendererDataTreeBuilder->getRendererDataTree( [ array_pop( $componentTree ) ] );

		$html = $rendererDataTreeRenderer->getHtml( $rendererDataTree );

		return $html;
	}
}
