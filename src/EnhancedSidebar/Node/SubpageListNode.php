<?php

namespace BlueSpice\Discovery\EnhancedSidebar\Node;

use Title;

class SubpageListNode extends InternalLinkNode {

	/** @var int */
	private $depth;

	/**
	 * @param Title $target
	 * @param string $label
	 * @param bool $hidden
	 * @param int $depth
	 */
	public function __construct( Title $target, $label, bool $hidden, int $depth ) {
		parent::__construct( $target, $label, $hidden );
		$this->depth = $depth;
	}

	/**
	 * @return string
	 */
	public function getType(): string {
		return 'enhanced-sidebar-subpage-tree';
	}

	/**
	 * @return void
	 */
	public function jsonSerialize() {
		return array_merge( parent::jsonSerialize(), [
			'depth' => $this->depth
		] );
	}

	/**
	 * @return array
	 */
	public function treeSerialize(): array {
		return array_merge( parent::treeSerialize(), [
			'items' => $this->getSubpages(),
		] );
	}

	/**
	 * @return bool
	 */
	public function supportsChildren(): bool {
		// It adds its own children, no children from definition supported
		return false;
	}

	/**
	 * @return array
	 */
	private function getSubpages(): array {
		if ( !$this->target->hasSubpages() ) {
			return [];
		}

		return $this->getSubpagesInternally( $this->target, 1 );
	}

	/**
	 * @param Title $target
	 * @param int $depth
	 * @return void
	 */
	private function getSubpagesInternally( Title $target, int $depth ) {
		if ( $depth > $this->depth ) {
			return [];
		}
		if ( !$target->hasSubpages() ) {
			return [];
		}
		$children = [];
		$subpages = $target->getSubpages();
		/** @var Title $subpage */
		foreach ( $subpages as $subpage ) {
			if ( !$subpage->getBaseTitle()->equals( $target ) ) {
				continue;
			}
			$children[] = [
				'name' => $this->generateId(),
				'href' => $subpage->getLocalUrl(),
				'label' => $subpage->getText(),
				'items' => $this->getSubpagesInternally( $subpage, $depth + 1 )
			];
		}
		return $children;
	}
}
