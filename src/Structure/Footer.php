<?php

namespace BlueSpice\Discovery\Structure;

use BaseTemplate;
use BlueSpice\Discovery\IBaseTemplateAware;

class Footer extends SkinStructureBase implements IBaseTemplateAware {

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
		$footerlinks = $this->template->get( 'footerlinks' );
		$footerplaces = $footerlinks['places'];
		$items = [];
		foreach ( $footerplaces as $footerplace ) {
			$items[] = [
				'id' => $footerplace . '-cnt',
				'body' => $this->template->get( $footerplace, '' )
			];
		}
		return $items;
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
