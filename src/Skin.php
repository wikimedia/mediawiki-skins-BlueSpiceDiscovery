<?php

namespace BlueSpice\Discovery;

use ExtensionRegistry;
use MediaWiki\MediaWikiServices;
use OutputPage;
use SkinTemplate;

class Skin extends SkinTemplate {
	/**
	 *
	 * @var string
	 */
	public $skinname = 'discovery';

	/**
	 *
	 * @var string
	 */
	public $template = Template::class;

	/**
	 *
	 * @param OutputPage $out
	 */
	private function addStaticResourceModuleStyles( $out ) {
		$config = MediaWikiServices::getInstance()->getConfigFactory()->makeConfig( 'bsg' );
		$layoutEnabled = $config->get( 'LayoutEnabled' );

		// Layout resource modules
		$staticLayoutStylesRegistry = ExtensionRegistry::getInstance()->getAttribute(
			'BlueSpiceDiscoveryLayoutRegistry'
		);
		if ( array_key_exists( $layoutEnabled, $staticLayoutStylesRegistry ) ) {
			if ( array_key_exists( 'styles', $staticLayoutStylesRegistry[$layoutEnabled] ) ) {
				$out->addModuleStyles( $staticLayoutStylesRegistry[$layoutEnabled]['styles'] );
			}
			if ( array_key_exists( 'scripts', $staticLayoutStylesRegistry[$layoutEnabled] ) ) {
				$out->addModules( $staticLayoutStylesRegistry[$layoutEnabled]['scripts'] );
			}
		}

		// Structure resource modules
		$staticStructureStylesRegistry = ExtensionRegistry::getInstance()->getAttribute(
			'BlueSpiceDiscoveryStructureRegistry'
		);
		if ( array_key_exists( $layoutEnabled, $staticStructureStylesRegistry ) ) {
			foreach ( $staticStructureStylesRegistry[$layoutEnabled] as $name => $spec ) {
				if ( array_key_exists( 'styles', $spec ) ) {
					$out->addModuleStyles( $spec['styles'] );
				}
				if ( array_key_exists( 'scripts', $spec ) ) {
					$out->addModules( $spec['scripts'] );
				}
			}
		}
	}

	/**
	 *
	 * @param OutputPage $out
	 */
	public function initPage( OutputPage $out ) {
		parent::initPage( $out );

		// Enable responsive behaviour on mobile browsers
		$out->addMeta( 'viewport', 'width=device-width, initial-scale=1' );

		// Use mediawiki interface
		$out->addModuleStyles( 'mediawiki.skinning.interface' );

		// Use bootstrap framework
		$out->addModuleStyles( "skin.discovery.bootstrap.styles" );
		$out->addModules( "skin.discovery.bootstrap.scripts" );

		// Add only ResourceModules for active layout and used structures
		$this->addStaticResourceModuleStyles( $out );
	}

	/**
	 *
	 * @return bool
	 */
	public function isViewMode() {
		if (
			$this->getTitle()->isMainPage() &&
			$this->getRequest()->getRawVal( 'action', 'view' ) === 'view'
		) {
			return true;
		}
		return false;
	}

	/**
	 * Make sure ParserFunctions within `MediaWiki:Sidebar` are evaluated
	 * @param array &$bar
	 * @param string $message
	 */
	public function addToSidebar( &$bar, $message ) {
		if ( $message === 'sidebar' ) {
			$this->addToSidebarPlain( $bar, wfMessage( $message )->inContentLanguage()->text() );
			return;
		}
		parent::addToSidebar( $bar, $message );
	}
}
