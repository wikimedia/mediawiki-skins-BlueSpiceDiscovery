<?php

namespace BlueSpice\Discovery\Structure;

use BaseTemplate;
use BlueSpice\Discovery\HookRunner;
use BlueSpice\Discovery\IBaseTemplateAware;
use BlueSpice\Discovery\ITemplateDataProvider;
use BlueSpice\Discovery\Renderer\ComponentRenderer;
use BlueSpice\Discovery\Renderer\SkinSlotRenderer;
use ExtensionRegistry;
use MediaWiki\HookContainer\HookContainer;
use MediaWiki\MediaWikiServices;
use MediaWiki\Permissions\PermissionManager;
use Message;
use RawMessage;
use Title;

class Footer extends SkinStructureBase implements IBaseTemplateAware {

	/** @var BaseTemplate */
	private $template;

	/** @var HookContainer */
	private $hookContainer;

	/**
	 *
	 * @param ITemplateDataProvider $templateDataProvider
	 * @param ComponentRenderer $componentRenderer
	 * @param SkinSlotRenderer $skinSlotRenderer
	 * @param PermissionManager $permissionManager
	 * @param HookContainer $hookContainer
	 */
	public function __construct(
		ITemplateDataProvider $templateDataProvider,
		ComponentRenderer $componentRenderer,
		SkinSlotRenderer $skinSlotRenderer,
		PermissionManager $permissionManager,
		HookContainer $hookContainer ) {
		$this->hookContainer = $hookContainer;
		parent::__construct( $templateDataProvider, $componentRenderer, $skinSlotRenderer, $permissionManager );
	}

	/**
	 *
	 * @param ITemplateDataProvider $templateDataProvider
	 * @param ComponentRenderer $componentRenderer
	 * @param SkinSlotRenderer $skinSlotRenderer
	 * @param PermissionManager $permissionManager
	 * @return ISkinStructure
	 */
	public static function factory(
		ITemplateDataProvider $templateDataProvider,
		ComponentRenderer $componentRenderer,
		SkinSlotRenderer $skinSlotRenderer,
		PermissionManager $permissionManager
	) {
		$hookContainer = MediaWikiServices::getInstance()->getHookContainer();
		return new static(
			$templateDataProvider, $componentRenderer, $skinSlotRenderer, $permissionManager, $hookContainer
		);
	}

	/**
	 *
	 * @return string
	 */
	public function getName(): string {
		return 'footer';
	}

	/**
	 * @return array
	 */
	public function getParams(): array {
		return [
			'places' => $this->getFooterPlaces(),
			'icons' => $this->getFooterIcons()
		];
	}

	/**
	 *
	 * @return void
	 */
	private function getFooterPlaces(): array {
		$data = [];
		$data['places'] = $this->template->getSkin()->getSiteFooterLinks();
		if ( ExtensionRegistry::getInstance()->isLoaded( 'BlueSpicePrivacy' ) ) {
			$privacyPolicyUrl = Title::newFromText( 'PrivacyPages/PrivacyPolicy', NS_SPECIAL )->getPrefixedURL();
			$data['defaultfooterlinks']['privacy'] = $this->template->getSkin()->footerLink(
				Message::newFromKey( 'privacy' )->inContentLanguage(),
				new RawMessage( $privacyPolicyUrl )
			);
		}
		foreach ( $data as $key => $existingItems ) {
			$newItems = [];
			$this->getHookRunner()->onSkinAddFooterLinks( $this->template->getSkin(), $key, $newItems );
			$data[$key] = $existingItems + $newItems;
		}
		$footerlinks = $data['places'];
		$this->hookContainer->run( 'BlueSpiceDiscoveryAfterGetFooterPlaces', [ &$footerlinks ] );
		$items = [];
		foreach ( $footerlinks as $place => $footerlink ) {
			$items[] = [
				'id' => $place . '-cnt',
				'body' => $footerlink
			];
		}
		return $items;
	}

	/**
	 * @return HookRunner
	 */
	private function getHookRunner() {
		return new HookRunner( $this->hookContainer );
	}

	/**
	 * @return array
	 */
	private function getFooterIcons(): array {
		$items = [];
		$footericons = $this->template->get( 'footericons' );
		$items = $footericons['poweredby'];

		foreach ( $items as $key => &$item ) {
			$validHref = isset( $item['url'] )
				&& ( $item['url'] !== '' )
				&& ( strpos( $item['url'], '#' ) !== 0 );

			if ( $validHref ) {
				$parsedURL = wfParseUrl( $item['url'] );
				if ( $parsedURL ) {
					$item['target'] = '_blank';
				}
			}
		}
		return $items;
	}

	/**
	 * @param BaseTemplate $baseTemplate
	 * @return void
	 */
	public function setBaseTemplate( BaseTemplate $baseTemplate ): void {
		$this->template = $baseTemplate;
	}

	/**
	 * @return array
	 */
	public function getStyles(): array {
		return [ 'skin.discovery.footer.styles' ];
	}
}
