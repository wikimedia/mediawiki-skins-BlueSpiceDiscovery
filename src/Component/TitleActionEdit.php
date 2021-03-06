<?php

namespace BlueSpice\Discovery\Component;

use IContextSource;
use MediaWiki\MediaWikiServices;
use MediaWiki\Permissions\PermissionManager;
use Message;
use MWStake\MediaWiki\Component\CommonUserInterface\Component\SimpleDropdownIconSplitButton;
use MWStake\MediaWiki\Component\CommonUserInterface\Component\SimpleDropdownItemlistFromArray;
use MWStake\MediaWiki\Component\CommonUserInterface\LinkFormatter;
use RequestContext;

class TitleActionEdit extends SimpleDropdownIconSplitButton {

	/**
	 *
	 * @var PermissionManager
	 */
	private $permissionManager = null;

	/**
	 *
	 * @var array
	 */
	private $editActions = [];

	/**
	 *
	 * @param PermissionManager $permissionManager
	 */
	public function __construct( $permissionManager ) {
		parent::__construct( [] );

		$this->permissionManager = $permissionManager;
	}

	/**
	 * @inheritDoc
	 */
	public function getId(): string {
		return 'ta-edit-btn';
	}

	/**
	 * @inheritDoc
	 */
	public function getHref(): string {
		$context = RequestContext::getMain();
		$title = $context->getTitle();

		$veNamespace = $GLOBALS['wgVisualEditorAvailableNamespaces'] ?? [];
		$ns = $title->getNamespace();

		if ( isset( $veNamespace[$ns] ) && $veNamespace[$ns] ) {
			if ( isset( $this->editActions['ca-ve-edit'] ) ) {
				return $this->editActions['ca-ve-edit']['href'];
			}
		}
		return $this->editActions['ca-edit']['href'];
	}

	/**
	 * @return array
	 */
	public function getContainerClasses(): array {
		return $this->options['container-classes'];
	}

	/**
	 * @return array
	 */
	public function getButtonClasses(): array {
		$classes = [ 'ico-btn' ];
		$actions = $this->getEditActions();
		if ( empty( $actions ) ) {
			array_push( $classes, 'disabled' );
		}
		return $classes;
	}

	/**
	 * @return array
	 */
	public function getSplitButtonClasses(): array {
		return [ 'dropdown-icon' ];
	}

	/**
	 * @return array
	 */
	public function getMenuClasses(): array {
		return [ 'mws-dropdown-secondary', 'dropdown-menu-end' ];
	}

	/**
	 * @return array
	 */
	public function getIconClasses(): array {
		return [ 'bi-pencil-fill' ];
	}

	/**
	 * @return Message
	 */
	public function getButtonTitle(): Message {
		return Message::newFromKey( 'bs-discovery-title-action-edit-button-title' );
	}

	/**
	 * @return Message
	 */
	public function getSplitButtonTitle(): Message {
		return Message::newFromKey( 'bs-discovery-title-action-edit-button-title' );
	}

	/**
	 * @return Message
	 */
	public function getButtonAriaLabel(): Message {
		return Message::newFromKey( 'bs-discovery-title-action-edit-button-aria-label' );
	}

	/**
	 * @return Message
	 */
	public function getSplitButtonAriaLabel(): Message {
		return Message::newFromKey( 'bs-discovery-title-action-edit-button-aria-label' );
	}

	/**
	 * @inheritDoc
	 */
	public function getSubComponents(): array {
		return [
			new SimpleDropdownItemlistFromArray( [
				'id' => 'new-content-itms',
				'classes' => [],
				'links' => $this->getEditActions()
			] )
		];
	}

	/**
	 *
	 * @param IContextSource $context
	 * @return bool
	 */
	public function shouldRender( IContextSource $context ): bool {
		$user = $context->getUser();
		$title = $context->getTitle();
		$userCan = $this->permissionManager->quickUserCan( 'edit', $user, $title );
		if ( $userCan ) {
			return true;
		}
		return false;
	}

	/**
	 *
	 * @return array
	 */
	private function getEditActions(): array {
		if ( isset( $this->componentProcessData['panel'] )
			&& isset( $this->componentProcessData['panel']['edit'] ) ) {
				$this->editActions = $this->componentProcessData['panel'][ 'edit' ];
		}
		if ( empty( $this->editActions ) ) {
			return [];
		}
		$services = MediaWikiServices::getInstance();
		/** @var LinkFormatter */
		$linkFormatter = $services->getService( 'MWStakeLinkFormatter' );
		return $linkFormatter->formatLinks( $this->editActions );
	}
}
