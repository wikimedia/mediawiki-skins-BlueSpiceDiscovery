<?php

namespace BlueSpice\Discovery\EnhancedSidebar\Node;

use Title;

class InternalLinkNode extends EnhancedSidebarNode {

	/** @var Title */
	protected $target;

	/** @var string */
	protected $label;

	/**
	 * @param Title $target
	 * @param string $label
	 * @param bool $hidden
	 */
	public function __construct( Title $target, $label, bool $hidden ) {
		parent::__construct( $hidden );
		$this->target = $target;
		$this->label = $label;
	}

	/**
	 * @return string
	 */
	public function getType(): string {
		return 'enhanced-sidebar-internal-link';
	}

	/**
	 * @return void
	 */
	public function jsonSerialize() {
		return [
			'type' => $this->getType(),
			'level' => $this->getLevel(),
			'target' => $this->target->getPrefixedText(),
			'label' => $this->label
		];
	}

	/**
	 * @return array
	 */
	public function treeSerialize(): array {
		return [
			'name' => $this->generateId(),
			'href' => $this->target->getLocalURL(),
			'label' => $this->label,
		];
	}
}
