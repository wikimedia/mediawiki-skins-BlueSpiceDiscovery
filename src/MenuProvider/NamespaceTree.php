<?php

namespace BlueSpice\Discovery\MenuProvider;

use BlueSpice\Discovery\Component\AsyncNamespaceTreePanel;
use BlueSpice\Discovery\IMenuProvider;
use MediaWiki\Message\Message;
use MWStake\MediaWiki\Component\CommonUserInterface\IComponent;

class NamespaceTree implements IMenuProvider {

	/**
	 * @return string
	 */
	public function getName(): string {
		return 'namespace-tree';
	}

	/**
	 * @return Message
	 */
	public function getLabelMsg(): Message {
		return Message::newFromKey( 'bs-discovery-menu-provider-namespace-tree-label' );
	}

	/**
	 * @return Message
	 */
	public function getDescriptionMsg(): Message {
		return Message::newFromKey( 'bs-discovery-menu-provider-namespace-tree-desc' );
	}

	/**
	 * @return IComponent
	 */
	public function getComponent(): IComponent {
		return new AsyncNamespaceTreePanel();
	}
}
