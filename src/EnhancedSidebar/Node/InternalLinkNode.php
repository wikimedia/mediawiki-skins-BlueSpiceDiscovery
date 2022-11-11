<?php

namespace BlueSpice\Discovery\EnhancedSidebar\Node;

use InvalidArgumentException;
use MediaWiki\Permissions\PermissionManager;
use Title;
use UnexpectedValueException;
use User;

class InternalLinkNode extends EnhancedSidebarNode {

	/** @var Title|string */
	protected $target;

	/**
	 * @var PermissionManager
	 */
	protected $permissionManager;

	/**
	 * @param PermissionManager $permissionManager
	 * @param array $data
	 */
	public function __construct(
		PermissionManager $permissionManager, $data
	) {
		parent::__construct( $data );
		$this->permissionManager = $permissionManager;
		if ( !isset( $data['page'] ) ) {
			throw new InvalidArgumentException(
				$this->getType() . ' requires "target" parameter'
			);
		}
		$this->target = $data['page'];
	}

	/**
	 * @return string
	 */
	public function getType(): string {
		return 'enhanced-sidebar-internal-link';
	}

	/**
	 * @param User $user
	 * @return bool
	 */
	public function isHidden( User $user ): bool {
		$parentRes = parent::isHidden( $user );
		if ( $parentRes || !( $this->target instanceof Title ) ) {
			return $parentRes;
		}
		return !$this->permissionManager->userCan(
			'read',
			$user,
			$this->target
		);
	}

	/**
	 * @return array
	 */
	public function jsonSerialize() {
		return parent::jsonSerialize() + [
			'page' => $this->target instanceof Title ?
				$this->target->getPrefixedText() : $this->target
		];
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
		return parent::treeSerialize() + [
			'href' => $this->target->getLocalURL(),
		];
	}

	/**
	 * @return string
	 */
	protected function getDisplayText(): string {
		if ( !$this->target->exists() ) {
			return parent::getDisplayText();
		}
		// Try to find displaytitle of the target page
		$pageProps = \PageProps::getInstance();
		$raw = $pageProps->getProperties( $this->target, 'displaytitle' );
		if ( !isset( $raw[$this->target->getArticleID()] ) ) {
			return parent::getDisplayText();
		}
		return $raw[$this->target->getArticleID()];
	}

	/**
	 * @return string[]
	 */
	protected function getOutputCssClasses(): array {
		if ( $this->target instanceof Title && !$this->target->exists() ) {
			return [ 'internal', 'new' ] + parent::getOutputCssClasses();
		}
		return [ 'internal' ] + parent::getOutputCssClasses();
	}
}
