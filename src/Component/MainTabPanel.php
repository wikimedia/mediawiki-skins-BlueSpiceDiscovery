<?php

namespace BlueSpice\Discovery\Component;

use IContextSource;
use Message;
use MWStake\MediaWiki\Component\CommonUserInterface\Component\ComponentBase;
use MWStake\MediaWiki\Component\CommonUserInterface\IComponent;
use MWStake\MediaWiki\Component\CommonUserInterface\IRestrictedComponent;
use MWStake\MediaWiki\Component\CommonUserInterface\ITabPanel;

class MainTabPanel extends ComponentBase implements ITabPanel, IRestrictedComponent {

	/**
	 *
	 */
	public function __construct() {
	}

	/**
	 *
	 * @return string
	 */
	public function getId() : string {
		return 'tp-main';
	}

	/**
	 *
	 * @return Message
	 */
	public function getText() : Message {
		return Message::newFromKey( 'bs-discovery-tabpanel-main-text' );
	}

	/**
	 *
	 * @return Message
	 */
	public function getTitle() : Message {
		return Message::newFromKey( 'bs-discovery-tabpanel-main-title' );
	}

	/**
	 *
	 * @return Message
	 */
	public function getAriaLabel() : Message {
		return Message::newFromKey( 'bs-discovery-tabpanel-main-aria-label' );
	}

	/**
	 *
	 * @return Message
	 */
	public function getAriaDesc() : Message {
		return Message::newFromKey( 'bs-discovery-tabpanel-main-desc' );
	}

	/**
	 *
	 * @return IComponent[]
	 */
	public function getSubComponents() : array {
		return [
			new MainLinksPanel(),
			new MediaWikiLinksPanel(),
		];
	}

	/**
	 *
	 * @param IContextSource $context
	 * @return bool
	 */
	public function isActive( $context ) : bool {
		return true;
	}

	/**
	 *
	 * @return array
	 */
	public function getPermissions(): array {
		return [ 'read' ];
	}
}
