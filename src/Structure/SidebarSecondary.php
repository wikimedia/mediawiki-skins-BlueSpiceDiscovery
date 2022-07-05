<?php

namespace BlueSpice\Discovery\Structure;

use BlueSpice\Discovery\CookieHandler;
use IContextSource;
use SpecialPage;

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
		$specialUserLogin = SpecialPage::getSafeTitleFor( 'Userlogin' );
		$title = $context->getTitle();
		if ( $specialUserLogin->equals( $title ) ) {
			return false;
		}

		/*
		 * whitelist check necessary for InviteSignup
		 * permission manager messes up with sessions
		 * so that with submit login on invite link user's session
		 * get changed and cannot use invite link to register
		 */
		$config = $context->getConfig();
		$whitelist = $config->get( 'WhitelistRead' );
		if ( $whitelist && in_array( $title->getPrefixedText(), $whitelist ) ) {
			return true;
		}

		$user = $context->getUser();
		$permissionManager = $this->services->getPermissionManager();
		if ( !$permissionManager->userCan( 'read', $user, $title ) ) {
			return false;
		}
		return true;
	}
}
