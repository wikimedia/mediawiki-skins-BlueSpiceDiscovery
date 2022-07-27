<?php

namespace BlueSpice\Discovery;

use BaseTemplate;
use Exception;
use ExtensionRegistry;
use MediaWiki\MediaWikiServices;
use Wikimedia\ObjectFactory;

/**
 * @package MediaWiki\Skins\WikimediaApiPortal
 * @method Skin getSkin()
 */
class Template extends BaseTemplate {
	/**
	 *
	 * @var ISkinLayoutRenderer
	 */
	public $skinLayoutRenderer = null;

	/**
	 *
	 */
	public function execute() {
		if ( $this->processSkinLayout() ) {
			/* Output */
			$this->html( 'headelement' );
			echo $this->skinLayoutRenderer->getHtml();
			$this->printTrail();
		}
	}

	/**
	 *
	 * @return bool
	 */
	private function processSkinLayout(): bool {
		/** @var IContextSource */
		$context = $this->getSkin()->getContext();

		/** @var MediaWikiServices */
		$services = MediaWikiServices::getInstance();
		$TemplateDataProvider = $services->getService( 'BlueSpiceDiscoveryTemplateDataProvider' );
		$TemplateDataProvider->init( $this );

		$config = MediaWikiServices::getInstance()->getConfigFactory()->makeConfig( 'bsg' );
		$layoutEnabled = $config->get( 'LayoutEnabled' );

		$queryValues = $context->getRequest()->getQueryValues();
		if ( array_key_exists( 'skintemplate', $queryValues ) ) {
				$layoutEnabled = $queryValues['skintemplate'];
		}

		$layoutRegistry = ExtensionRegistry::getInstance()->getAttribute(
			'BlueSpiceDiscoveryLayoutRegistry'
		);

		$layoutSpecs = [];
		if ( isset( $layoutRegistry[$layoutEnabled] ) ) {
			$layoutSpecs = $layoutRegistry[$layoutEnabled];
			if ( is_array( $layoutSpecs['factory'] ) ) {
				$callback = end( $layoutSpecs['factory'] );
				$layoutSpecs['factory'] = $callback;
			}
			if ( is_array( $layoutSpecs['class'] ) ) {
				$callback = end( $layoutSpecs['class'] );
				$layoutSpecs['class'] = $callback;
			}
			if ( isset( $layoutSpecs['factory'] ) && isset( $layoutSpecs['class'] ) ) {
				unset( $layoutSpecs['factory'] );
			}
		} else {
			throw new Exception(
				'No layout ' . $layoutEnabled . ' registered'
			);
		}

		/** @var ObjectFactory */
		$objectFactory = $services->getService( 'ObjectFactory' );
		$skinLayout = $objectFactory->createObject( $layoutSpecs );

		if ( $skinLayout instanceof IBaseTemplateAware ) {
			$skinLayout->setBaseTemplate( $this );
		}

		if ( $skinLayout instanceof IContextSourceAware ) {
			$skinLayout->setContextSource( $context );
		}

		$skinLayoutRendererCallback = $config->get( 'LayoutRenderer' );
		$this->skinLayoutRenderer = call_user_func_array( $skinLayoutRendererCallback, [ $skinLayout ] );

		return true;
	}
}
