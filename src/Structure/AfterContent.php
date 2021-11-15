<?php

namespace BlueSpice\Discovery\Structure;

use BlueSpice\Discovery\SkinSlotRenderer\DataAfterContentSkinSlotRenderer;
use BlueSpice\Discovery\SkinSlotRenderer\ToolsAfterContentSkinSlotRenderer;

class AfterContent extends SkinStructureBase {

	/**
	 *
	 * @return string
	 */
	public function getName() : string {
		return 'aftercontent';
	}

	/**
	 * @return string
	 */
	public function getTemplatePath() : string {
		return $GLOBALS['wgStyleDirectory'] .
			'/BlueSpiceDiscovery/resources/templates/structure/aftercontent';
	}

	/**
	 * @return array
	 */
	public function getParams() : array {
		$this->fetchDataAfterContent();
		$this->fetchSkinSlotToolsAfterContent();
		$this->fetchSkinSlotDataAfterContent();

		return $this->skinComponents;
	}

	/**
	 * Give MediaWiki extensions a chance to add data after content
	 * https://www.mediawiki.org/wiki/Manual:Skinning_Part_2#dataAfterContent
	 *
	 * @return void
	 */
	private function fetchDataAfterContent() {
		$this->skinComponents['data-after-content'] = $this->template->get( 'dataAfterContent' );
	}

	/**
	 *
	 * @return void
	 */
	private function fetchSkinSlotDataAfterContent() {
		$this->skinComponents['skin-slot-data-after-content'] = $this->getSkinSlotHtml(
			DataAfterContentSkinSlotRenderer::REG_KEY
		);
	}

	/**
	 *
	 * @return void
	 */
	private function fetchSkinSlotToolsAfterContent() {
		$this->skinComponents['skin-slot-tools-after-content'] = $this->getSkinSlotHtml(
			ToolsAfterContentSkinSlotRenderer::REG_KEY
		);
	}
}
