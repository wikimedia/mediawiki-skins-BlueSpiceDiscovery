<?php

namespace BlueSpice\Discovery\EnhancedSidebar\Node;

use Title;

class ExternalLinkNode extends EnhancedSidebarNode {

	/** @var Title */
	private $target;

	/** @var string */
	private $label;

	/**
	 * @param Title $target
	 * @param string $label
	 * @param bool $hidden
	 */
	public function __construct( $target, $label, bool $hidden ) {
		parent::__construct( $hidden );
		$this->target = $target;
		$this->label = $label;
	}

	/**
	 * @return string
	 */
	public function getType(): string {
		return 'enhanced-sidebar-external-link';
	}

	/**
	 * @return void
	 */
	public function jsonSerialize() {
		return [
			'type' => $this->getType(),
			'level' => $this->getLevel(),
			'target' => $this->target,
			'label' => $this->label
		];
	}

	/**
	 * @return array
	 */
	public function treeSerialize(): array {
		return [
			'name' => $this->generateId(),
			'href' => $this->target,
			'label' => $this->label,
		];
	}
}
