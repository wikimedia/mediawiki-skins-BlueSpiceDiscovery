<?php

namespace BlueSpice\Discovery\EnhancedSidebar\NodeProcessor;

use BlueSpice\Discovery\EnhancedSidebar\Node\SubpageListNode;
use MWStake\MediaWiki\Lib\Nodes\INode;
use MWStake\MediaWiki\Lib\Nodes\INodeSource;

class SubpageListProcessor extends InternalLinkProcessor {

	/**
	 * @param string $type
	 * @return bool
	 */
	public function supportsNodeType( $type ): bool {
		return $type === 'subpage-tree';
	}

	/**
	 * @param array $data
	 * @return INode
	 */
	public function getNodeFromData( array $data ): INode {
		$depth = is_numeric( $data['depth'] ) ? (int)$data['depth'] : 1;
		return new SubpageListNode(
			$this->getTitleFromParam( $data['base'] ),
			$data['text'],
			$this->isHidden( $data ),
			$depth
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
		return [ 'text', 'hidden', 'depth' ];
	}
}
