<?php

namespace BlueSpice\Discovery\Structure;

use BlueSpice\Discovery\CookieHandler;
use IContextSource;
use SpecialPage;

class SidebarPrimary extends StackedTabPanelContainerBase {
	/**
	 *
	 * @var string
	 */
	private $registryKey = 'SidebarPrimaryTabPanels';

	/**
	 * @return string
	 */
	public function getName() : string {
		return 'sidebar-primary';
	}

	/**
	 *
	 * @return string
	 */
	public function getId() : string {
		return 'sb-pri';
	}

	/**
	 *
	 * @return string
	 */
	public function getTabPanelRegistryKey() : string {
		return $this->registryKey;
	}

	/**
	 *
	 * @return array
	 */
	public function getClasses(): array {
		$cookieHandler = new CookieHandler( $this->template->getSkin()->getRequest() );
		$expanded = $cookieHandler->getCookie( $this->getId() . '-cnt' );
		$classes = [ 'col', 'col-west', 'px-0', 'collapse' ];
		if ( $expanded === 'true' ) {
			array_push( $classes, 'show' );
		}

		return $classes;
	}

	/**
	 *
	 * @param IContextSource $context
	 * @return bool
	 */
	public function shouldRender( $context ): bool {
		if ( !parent::shouldRender( $context ) ) {
			return false;
		}
		$specialUserLogin = SpecialPage::getSafeTitleFor( 'UserLogin' );
		$title = $context->getTitle();
		if ( $specialUserLogin->equals( $title ) ) {
			return false;
		}
		$user = $context->getUser();
		$permissionManager = $this->services->getPermissionManager();
		if ( !$permissionManager->userCan( 'read', $user, $title ) ) {
			return false;
		}
		return true;
	}
}
