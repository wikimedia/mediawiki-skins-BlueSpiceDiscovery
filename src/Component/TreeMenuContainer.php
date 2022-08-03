<?php

namespace BlueSpice\Discovery\Component;

use Html;
use MWStake\MediaWiki\Component\CommonUserInterface\Component\Literal;

class TreeMenuContainer extends Literal {

	/**
	 * @var string
	 */
	private $labelledby = '';

	/**
	 * @var array
	 */
	private $classes = [];

	/**
	 * @var array
	 */
	private $treeData = [];

	/**
	 * @param string $id
	 * @param string $labelledby
	 * @param array $classes
	 * @param array $treeData
	 */
	public function __construct(
		string $id, string $labelledby, array $classes = [], array $treeData = []
	) {
		parent::__construct( $id, '' );

		$this->labelledby = $labelledby;
		$this->classes = $classes;
		$this->treeData = $treeData;
	}

	/**
	 * Raw HTML string
	 *
	 * @return string
	 */
	public function getHtml(): string {
		$params = [
			'id' => $this->getId(),
			'aria-labelledby' => $this->labelledby,
		];

		if ( !empty( $this->classes ) ) {
			$params['class'] = implode( ' ', $this->classes );
			$params['class'] .= ' tree-menu-cnt';
		}

		if ( !empty( $this->treeData ) ) {
			$params['data-tree'] = $this->getTreeDataJson();
		}

		$menuContainer = Html::element( 'div', $params );

		return $menuContainer;
	}

	/**
	 * @return string
	 */
	private function getTreeDataJson(): string {
		$json = json_encode( $this->treeData );
		return $json;
	}

	/**
	 * @return string[]
	 */
	public function getRequiredRLModules(): array {
		return [ 'skin.discovery.tree-menu-container' ];
	}

	/**
	 * @return string[]
	 */
	public function getRequiredRLStyles(): array {
		return [ 'skin.discovery.tree-menu-container.styles' ];
	}
}
