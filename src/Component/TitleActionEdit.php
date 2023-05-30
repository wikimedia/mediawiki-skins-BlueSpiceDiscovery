<?php

namespace BlueSpice\Discovery\Component;

use Exception;
use IContextSource;
use MediaWiki\MediaWikiServices;
use MediaWiki\Permissions\PermissionManager;
use Message;
use MWStake\MediaWiki\Component\CommonUserInterface\Component\SimpleDropdownIconSplitButton;
use MWStake\MediaWiki\Component\CommonUserInterface\Component\SimpleDropdownItemlistFromArray;
use MWStake\MediaWiki\Component\CommonUserInterface\LinkFormatter;

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
	 * @param array $componentProcessData
	 */
	public function __construct( $permissionManager, $componentProcessData ) {
		parent::__construct( [] );

		$this->permissionManager = $permissionManager;

		if ( isset( $componentProcessData['panel'] )
			&& isset( $componentProcessData['panel']['edit'] ) ) {
				$this->editActions = $componentProcessData['panel'][ 'edit' ];
		}
	}

	/**
	 * @inheritDoc
	 */
	public function getId(): string {
		$actionIds = array_keys( $this->editActions );

		if ( empty( $actionIds ) ) {
			return 'ta-edit-btn';
		}

		if ( isset( $this->editActions['ca-formedit'] ) ) {
			return 'ca-formedit';
		}

		if ( isset( $this->editActions['ca-ve-edit'] ) ) {
			return 'ca-ve-edit';
		}

		if ( isset( $this->editActions['ca-edit'] ) ) {
			return 'ca-edit';
		}

		return $actionIds[0];
	}

	/**
	 * @inheritDoc
	 */
	public function getHref(): string {
		$actionKey = $this->getId();

		if ( isset( $this->editActions[$actionKey]['href'] ) ) {
			return $this->editActions[$actionKey]['href'];
		} else {
			throw new Exception( 'No edit action found' );
		}
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
		if ( empty( $this->editActions ) ) {
			return false;
		}

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
		if ( empty( $this->editActions ) ) {
			return [];
		}

		$actionId = $this->getId();
		$actions = $this->editActions;
		if ( isset( $actions[$actionId] ) ) {
			unset( $actions[$actionId] );
		}

		$services = MediaWikiServices::getInstance();
		/** @var LinkFormatter */
		$linkFormatter = $services->getService( 'MWStakeLinkFormatter' );
		return $linkFormatter->formatLinks( $actions );
	}
}
