<?php

namespace BlueSpice\Discovery\Structure;

use BlueSpice\Discovery\Component\FullscreenButton;
use BlueSpice\Discovery\Component\LastEditInfo;
use BlueSpice\Discovery\Component\TitleActionEdit;
use BlueSpice\Discovery\SkinSlotRenderer\BreadcrumbSkinSlotRenderer;
use BlueSpice\Discovery\SkinSlotRenderer\DataAfterTitleSkinSlotRenderer;
use BlueSpice\Discovery\SkinSlotRenderer\DataBeforeContentSkinSlotRenderer;
use BlueSpice\Discovery\SkinSlotRenderer\TitleActionsSkinSlotRenderer;
use Html;
use Message;
use MWStake\MediaWiki\Component\CommonUserInterface\HtmlIdRegistry;

class Main extends SkinStructureBase {

	/**
	 *
	 * @return string
	 */
	public function getName(): string {
		return 'main';
	}

	/**
	 * @return string
	 */
	public function getTemplatePath(): string {
		return $GLOBALS['wgStyleDirectory'] .
			'/BlueSpiceDiscovery/resources/templates/structure/main';
	}

	/**
	 * @return array
	 */
	public function getParams(): array {
		$this->fetchSiteNotice();
		$this->fetchSkinSlotDataBeforeContent();
		$this->fetchBreadcrumb();
		$this->fetchLastEdit();
		$this->fetchTitle();
		$this->fetchSkinSlotTitleActions();
		$this->fetchTitleActionEdit();
		$this->fetchTitleActionFullscreenButton();
		$this->fetchSubTitles();
		$this->fetchSkinSlotDataAfterTitle();
		$this->fetchUndelete();
		$this->fetchIndicators();
		$this->fetchBodyText();

		return $this->skinComponents;
	}

	/**
	 * https://www.mediawiki.org/wiki/Manual:Skinning_Part_2#Site_notice
	 *
	 * @return void
	 */
	private function fetchSiteNotice() {
		$this->skinComponents['sitenotice'] = $this->template->get( 'sitenotice' );
	}

	/**
	 *
	 * @return void
	 */
	private function fetchBreadcrumb() {
		$this->skinComponents['breadcrumb-nav'] = $this->getSkinSlotHtml( BreadcrumbSkinSlotRenderer::REG_KEY );
	}

	/**
	 *
	 * @return void
	 */
	private function fetchLastEdit() {
		$linkRenderer = $this->services->getLinkRenderer();
		$revisionStore = $this->services->getRevisionStore();

		$component = new LastEditInfo( $this->context, $linkRenderer, $revisionStore );
		$html = $this->getComponentHtml( $component );

		$this->skinComponents['last-edit'] = $html;
	}

	/**
	 *
	 * @return void
	 */
	private function fetchTitleActionEdit() {
		$permissionManager = $this->services->getPermissionManager();

		$component = new TitleActionEdit( $permissionManager );
		$html = $this->getComponentHtml( $component );

		$this->skinComponents['title-action-edit'] = $html;
	}

	/**
	 *
	 * @return void
	 */
	private function fetchTitleActionFullscreenButton() {
		$cookieHandler = $this->services->getService( 'BlueSpiceDiscoveryCookieHandler' );

		$component = new FullscreenButton( $cookieHandler );
		$html = $this->getComponentHtml( $component );

		$this->skinComponents['fullscreen-button'] = $html;
	}

	/**
	 * https://www.mediawiki.org/wiki/Manual:Skinning_Part_2#Titel
	 *
	 * @return void
	 */
	private function fetchTitle() {
		$regularTitle = $this->template->getSkin()->getTitle()->getPrefixedText();
		$displayTitle = $this->template->data['title'];
		if ( $displayTitle === $regularTitle ) {
			$displayTitle = $this->template->getSkin()->getTitle()->getSubpageText();
		}
		$this->skinComponents['title'] = $displayTitle;
		$this->skinComponents['title-aria-label'] = Message::newFromKey(
			'bs-discovery-first-heading-aria-label'
			)->text();
	}

	/**
	 * Besides the tagline MediaWiki has two subtitles below the title to take into account.
	 * This one is used for various things like the subpage hierarchy and redirected from line.
	 * https://www.mediawiki.org/wiki/Manual:Skinning_Part_2#Subtitles
	 *
	 * @return void
	 */
	private function fetchSubTitles() {
		$this->skinComponents['subtitles'] = $this->template->get( 'subtitle' );
	}

	/**
	 * Besides the tagline MediaWiki has two subtitles below the title to take into account.
	 * This one is specifically for the undelete message.
	 * https://www.mediawiki.org/wiki/Manual:Skinning_Part_2#Subtitles
	 *
	 * @return void
	 */
	private function fetchUndelete() {
		$this->skinComponents['undelete'] = $this->template->get( 'undelete' );
	}

	/**
	 * https://www.mediawiki.org/wiki/Manual:Skinning_Part_2#Page_status_indicators
	 *
	 * @return void
	 */
	private function fetchIndicators() {
		$indicators = $this->template->get( 'indicators' );
		if ( !empty( $indicators ) ) {
			$htmlIdRegistry = new HtmlIdRegistry();
			$indicatorHtml = '';
			foreach ( $indicators as $id => $content ) {
				$indicatorHtml = Html::openElement(
					'div',
					[
						'id' => $htmlIdRegistry->makeHtmlIdSafe( "mw-indicator-$id" ),
						'class' => 'mw-indicator'
					]
				);
				$indicatorHtml .= $content;
				$indicatorHtml .= Html::closeElement( 'div' );
			}
			$this->skinComponents['indicators'] = $indicatorHtml;
		}
	}

	/**
	 * https://www.mediawiki.org/wiki/Manual:Skinning_Part_2#Body_text
	 *
	 * @return void
	 */
	private function fetchBodyText() {
		$this->skinComponents['bodytext'] = $this->template->get( 'bodytext' );
	}

	/**
	 *
	 * @return void
	 */
	private function fetchSkinSlotDataBeforeContent() {
		$this->skinComponents['skin-slot-data-before-content'] = $this->getSkinSlotHtml(
			DataBeforeContentSkinSlotRenderer::REG_KEY
		);
	}

	/**
	 *
	 * @return void
	 */
	private function fetchSkinSlotDataAfterTitle() {
		$this->skinComponents['skin-slot-data-after-title'] = $this->getSkinSlotHtml(
			DataAfterTitleSkinSlotRenderer::REG_KEY
		);
	}

	/**
	 *
	 * @return void
	 */
	private function fetchSkinSlotTitleActions() {
		$this->skinComponents['skin-slot-title-actions'] = $this->getSkinSlotHtml(
			TitleActionsSkinSlotRenderer::REG_KEY
		);
	}
}
