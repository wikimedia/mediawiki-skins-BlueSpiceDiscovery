<?php

namespace BlueSpice\Discovery\EnhancedSidebar\Node;

use MediaWiki\Permissions\PermissionManager;
use Title;
use UnexpectedValueException;
use User;

class SubpageListNode extends InternalLinkNode {

	/** @var int */
	private $depth;
	/** @var User */
	private $user;

	/**
	 * @param PermissionManager $permissionManager
	 * @param array $data
	 */
	public function __construct(
		PermissionManager $permissionManager, $data
	) {
		parent::__construct( $permissionManager, $data );
		$this->depth = $data['depth'] ?? 1;
	}

	/**
	 * @return string
	 */
	public function getType(): string {
		return 'enhanced-sidebar-subpage-tree';
	}

	/**
	 * @return array
	 */
	public function jsonSerialize() {
		return parent::jsonSerialize() + [
			'depth' => $this->depth
		];
	}

	/**
	 * @param User $user
	 *
	 * @return bool
	 */
	public function isHidden( User $user ): bool {
		$this->user = $user;
		return parent::isHidden( $user );
	}

	/**
	 * Serialize in format to be consumed by a tree
	 *
	 * @return array
	 * @throws \Exception
	 */
	public function treeSerialize(): array {
		if ( !( $this->target instanceof Title ) ) {
			throw new UnexpectedValueException(
				'Target is not a Title! Calling from an invalid context?'
			);
		}
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
	 *
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
	 *
	 * @return array
	 * @throws \Exception
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
			$subpageNode = new InternalLinkNode( $this->permissionManager, [
				'page' => $subpage,
				'text' => $subpage->getSubpageText()
			] );
			if ( $subpageNode->isHidden( $this->user ) ) {
				continue;
			}
			$children[] = $subpageNode->treeSerialize() + $this->getSubpagesInternally( $subpage, $depth + 1 );
		}
		return $children;
	}

	/**
	 * @return array|string[]
	 */
	protected function getOutputCssClasses(): array {
		// Remove parent-specific classes
		return array_diff( parent::getOutputCssClasses(), [ 'internal', 'new' ] );
	}
}
