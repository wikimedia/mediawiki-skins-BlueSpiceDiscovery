<?php

namespace BlueSpice\Discovery\Structure;

use BlueSpice\Discovery\Component\CreateContentSplitButton;
use BlueSpice\Discovery\Component\GlobalActionsButton;
use BlueSpice\Discovery\Component\LanguageButton;
use BlueSpice\Discovery\Component\SidebarPrimaryToggleButton;
use BlueSpice\Discovery\Component\SidebarSecondaryToggleButton;
use BlueSpice\Discovery\Component\UserButtonLogin;
use BlueSpice\Discovery\Component\UserButtonMenu;
use BlueSpice\Discovery\CookieHandler;
use BlueSpice\Discovery\SkinSlotRenderer\NavbarPrimaryItemsSkinSlotRenderer;
use BlueSpice\Discovery\SkinSlotRenderer\NavbarPrimarySearchFormSkinSlotRenderer;
use IContextSource;
use Message;
use Title;

class NavbarPrimary extends NavbarBase {

	/**
	 * @return string
	 */
	public function getName(): string {
		return 'navbar-primary';
	}

	/**
	 * @return string
	 */
	public function getTemplatePath(): string {
		return $GLOBALS['wgStyleDirectory'] .
			'/BlueSpiceDiscovery/resources/templates/structure/navbar-primary';
	}

	/**
	 *
	 * @return void
	 */
	private function fetchSkinSlotNavbarPrimarySearchFormHtml(): void {
		$this->skinComponents['search-form'] = $this->getSkinSlotHtml(
			NavbarPrimarySearchFormSkinSlotRenderer::REG_KEY
		);
	}

	/**
	 *
	 * @return void
	 */
	private function fetchSkinSlotNavbarPrimaryItemsHtml(): void {
		$this->skinComponents['navbar-items'] = $this->getSkinSlotHtml(
			NavbarPrimaryItemsSkinSlotRenderer::REG_KEY
		);
	}

	/**
	 *
	 * @return void
	 */
	private function fetchNewContentButtonHtml() {
		$permissionManager = $this->services->getPermissionManager();
		$user = $this->template->getSkin()->getUser();

		$component = new CreateContentSplitButton( $user, $permissionManager );
		$html = $this->getComponentHtml( $component );

		$this->skinComponents['new-content-button'] = $html;
	}

	/**
	 *
	 * @return void
	 */
	private function fetchLanguageButtonHtml() {
		$langCode = $this->template->getSkin()->getLanguage()->getCode();

		$component = new LanguageButton( $langCode );
		$html = $this->getComponentHtml( $component );

		$this->skinComponents['language-button'] = $html;
	}

	/**
	 *
	 * @return void
	 */
	private function fetchSidebarPrimaryToggleButtonHtml() {
		$cookieHandler = $this->services->getService( 'BlueSpiceDiscoveryCookieHandler' );
		$sidebar = $this->layout->skinStructureElements['sidebar-primary'];

		if ( $sidebar->shouldRender( $this->context ) ) {
			$component = new SidebarPrimaryToggleButton( $cookieHandler );
			$html = $this->getComponentHtml( $component );

			$this->skinComponents['sidebar-primary-toggle'] = $html;
		}
	}

	/**
	 *
	 * @return void
	 */
	private function fetchSidebarSecondaryToggleButtonHtml() {
		$cookieHandler = $this->services->getService( 'BlueSpiceDiscoveryCookieHandler' );
		$sidebar = $this->layout->skinStructureElements['sidebar-secondary'];

		if ( $sidebar->shouldRender( $this->context ) ) {
			$component = new SidebarSecondaryToggleButton( $cookieHandler );
			$html = $this->getComponentHtml( $component );

			$this->skinComponents['sidebar-secondary-toggle'] = $html;
		}
	}

	/**
	 *
	 * @return void
	 */
	private function fetchGlobalActionsButtonHtml() {
		$component = new GlobalActionsButton();
		$html = $this->getComponentHtml( $component );

		$this->skinComponents['global-actions-button'] = $html;
	}

	/**
	 *
	 * @return void
	 */
	private function fetchLoginButtonHtml() {
		$component = new UserButtonLogin( $this->context );
		$html = $this->getComponentHtml( $component );

		$this->skinComponents['login-button'] = $html;
	}

	/**
	 *
	 * @return void
	 */
	private function fetchUserMenuButtonHtml() {
		$component = new UserButtonMenu();
		$html = $this->getComponentHtml( $component );

		$this->skinComponents['user-menu-button'] = $html;
	}

	/**
	 *
	 * @return array
	 */
	public function getParams(): array {
		$cookieHandler = new CookieHandler( $this->template->getSkin()->getRequest() );
		$expanded = $cookieHandler->getCookie( 'sb-pri-cnt' );
		$mainpage = Title::newMainPage();

		$this->fetchSkinSlotNavbarPrimarySearchFormHtml();
		$this->fetchNewContentButtonHtml();
		$this->fetchSkinSlotNavbarPrimaryItemsHtml();
		$this->fetchGlobalActionsButtonHtml();
		$this->fetchLanguageButtonHtml();
		$this->fetchLoginButtonHtml();
		$this->fetchUserMenuButtonHtml();
		$this->fetchSidebarSecondaryToggleButtonHtml();
		$this->fetchSidebarPrimaryToggleButtonHtml();

		return array_merge(
			$this->skinComponents,
			[
				'logo-href' => $mainpage->getLocalURL(),
				'logo-title' => $mainpage->getText(),
				'logo-aria-label' => Message::newFromKey(
					'bs-discovery-logo-return-to-aria-label',
					$mainpage->getText()
				),
				'logo-src' => $GLOBALS['wgLogo'],
				'navbar-menu-aria-label' => Message::newFromKey( 'bs-discovery-navbar-aria-label' ),
			]
		);
	}

	/**
	 * @param IContextSource $context
	 * @return string
	 */
	public function shouldRender( IContextSource $context ): bool {
		return true;
	}

}
