<?php

namespace BlueSpice\Discovery\MenuProvider;

use BlueSpice\Discovery\Component\NamespaceMainPages;
use BlueSpice\Discovery\IMenuProvider;
use BlueSpice\UtilityFactory;
use Message;
use MWStake\MediaWiki\Component\CommonUserInterface\IComponent;
use RawMessage;

class NamespaceMainPageList implements IMenuProvider {

	/**
	 * @var UtilityFactory
	 */
	private $utilityFactory = null;

	/**
	 *
	 */
	public function __construct( UtilityFactory $uilityFactory ) {
		$this->utilityFactory = $uilityFactory;
	}

	/**
	 * @return string
	 */
	public function getName(): string {
		return 'namespace-mainpage-list';
	}

	/**
	 * @return Message
	 */
	public function getLabelMsg(): Message {
		return new Message( 'bs-discovery-menu-provider-namespace-mainpage-list-label' );
	}

	/**
	 * @return Message
	 */
	public function getDescriptionMsg(): Message {
		return new RawMessage( 'bs-discovery-menu-provider-namespace-mainpage-list-desc' );
	}

	/**
	 * @return IComponent
	 */
	public function getComponent(): IComponent {
		return new NamespaceMainPages( $this->utilityFactory );
	}
}
