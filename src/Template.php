<?php

namespace BlueSpice\Discovery;

use BaseTemplate;
use Exception;
use ExtensionRegistry;
use MediaWiki\MediaWikiServices;
use RequestContext;

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
		$services = MediaWikiServices::getInstance();
		$TemplateDataProvider = $services->getService( 'BlueSpiceDiscoveryTemplateDataProvider' );
		$TemplateDataProvider->init( $this );

		$context = new RequestContext();
		$config = MediaWikiServices::getInstance()->getConfigFactory()->makeConfig( 'bsg' );
		$layoutEnabled = $config->get( 'LayoutEnabled' );
		$layoutRegistry = ExtensionRegistry::getInstance()->getAttribute(
			'BlueSpiceDiscoveryLayoutRegistry'
		);

		$queryValues = $context->getRequest()->getQueryValues();
		if ( array_key_exists( 'skintemplate', $queryValues )
			&& array_key_exists( $queryValues['skintemplate'], $layoutRegistry ) ) {
				$layoutEnabled = $queryValues['skintemplate'];
		}

		if ( array_key_exists( $layoutEnabled, $layoutRegistry ) ) {
			$callback = $layoutRegistry[$layoutEnabled]['callback'];

			$skinLayout = call_user_func_array( $callback, [ $this, $context ] );

			if ( $skinLayout instanceof ISkinLayout === false ) {
				throw new Exception(
					'Factory callback for ' . $layoutEnabled
						. ' did not return a ISkinLayout object'
				);
			}

			$skinLayoutRendererCallback = $config->get( 'LayoutRenderer' );
			$this->skinLayoutRenderer = call_user_func_array( $skinLayoutRendererCallback, [ $skinLayout ] );
		} else {
			throw new Exception(
				'No template ' . $layoutEnabled . ' registered'
			);
		}
		return true;
	}
}
