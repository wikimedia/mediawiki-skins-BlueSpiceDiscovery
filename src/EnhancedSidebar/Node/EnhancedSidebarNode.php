<?php

namespace BlueSpice\Discovery\EnhancedSidebar\Node;

use MediaWiki\Extension\MenuEditor\Node\MenuNode;

abstract class EnhancedSidebarNode extends MenuNode {

	/** @var bool */
	protected $hidden;

	/**
	 * @param bool $hidden
	 */
	public function __construct( bool $hidden ) {
		parent::__construct( -1, '' );
		$this->hidden = $hidden;
	}

	/**
	 * @return bool
	 */
	public function isHidden(): bool {
		return $this->hidden;
	}

	/**
	 * Serialize in format to be consumed by a tree
	 *
	 * @return array
	 */
	abstract public function treeSerialize(): array;

	public function supportsChildren(): bool {
		return true;
	}

	/**
	 * All nodes in a tree must have a unique id
	 *
	 * @return string
	 * @throws \Exception
	 */
	protected function generateId(): string {
		return md5(
			$this->getType() . $this->getLevel() . $this->getOriginalData() . random_bytes( 8 )
		);
	}
}
