<?php

namespace BlueSpice\Discovery\EnhancedSidebar\NodeProcessor;

use BlueSpice\Discovery\EnhancedSidebar\Node\ExternalLinkNode;
use MWStake\MediaWiki\Lib\Nodes\INode;
use MWStake\MediaWiki\Lib\Nodes\INodeSource;

class ExternalLinkProcessor extends EnhancedSidebarNodeProcessor {

	/**
	 * @param string $type
	 * @return bool
	 */
	public function supportsNodeType( $type ): bool {
		return $type === 'external';
	}

	/**
	 * @param array $data
	 * @return INode
	 */
	public function getNodeFromData( array $data ): INode {
		return new ExternalLinkNode(
			$data['href'],
			$data['text'],
			$this->isHidden( $data )
		);
	}

	/**
	 * @param INodeSource $nodeSource
	 * @return INode
	 */
	public function getNode( INodeSource $nodeSource ): INode {
		$data = $this->getProcessedData( $nodeSource );

		return $this->getNodeFromData( $data );
	}

	/**
	 * @inheritDoc
	 */
	protected function getKeysToPreprocess( array $data ): array {
		return [ 'text', 'hidden' ];
	}
}
