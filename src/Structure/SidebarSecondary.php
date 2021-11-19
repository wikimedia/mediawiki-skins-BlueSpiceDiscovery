<?php

namespace BlueSpice\Discovery\Structure;

use BlueSpice\Discovery\CookieHandler;
use Message;

class SidebarSecondary extends StackedTabPanelContainerBase {
	/**
	 *
	 * @var string
	 */
	private $registryKey = 'SidebarSecondaryTabPanels';

	/**
	 * @return string
	 */
	public function getName(): string {
		return 'sidebar-secondary';
	}

	/**
	 *
	 * @return string
	 */
	public function getId(): string {
		return 'sb-sec';
	}

	/**
	 *
	 * @return string
	 */
	public function getTabPanelRegistryKey(): string {
		return $this->registryKey;
	}

	/**
	 *
	 * @return array
	 */
	public function getClasses(): array {
		$cookieHandler = new CookieHandler( $this->template->getSkin()->getRequest() );
		$expanded = $cookieHandler->getCookie( $this->getId() . '-cnt' );
		$classes = [ 'col', 'col-east', 'px-0', 'collapse' ];
		if ( $expanded !== 'false' ) {
			array_push( $classes, 'show' );
		}

		return $classes;
	}

	/**
	 *
	 * @return array
	 */
	public function getParams(): array {
		$params = parent::getParams();
		$params = array_merge(
			$params,
			[
				'id' => $this->getId(),
				'title' => Message::newFromKey( 'bs-discovery-sidebar-secondary-toggle-hide-title' ),
				'aria-label' => Message::newFromKey( 'bs-discovery-sidebar-secondary-toggle-hide-aria-label' ),
				'expanded' => 'true'
			]
		);

		return $params;
	}

	/**
	 * @return string
	 */
	public function getTemplatePath(): string {
		return $GLOBALS['wgStyleDirectory'] .
			'/BlueSpiceDiscovery/resources/templates/structure/stacked-tab-panel-container-with-close-btn';
	}
}
