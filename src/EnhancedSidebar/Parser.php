<?php

namespace BlueSpice\Discovery\EnhancedSidebar;

use BlueSpice\Discovery\EnhancedSidebar\Node\EnhancedSidebarNode;
use BlueSpice\Discovery\EnhancedSidebar\NodeProcessor\EnhancedSidebarNodeProcessor;
use MediaWiki\Revision\RevisionRecord;
use MediaWiki\Revision\SlotRecord;
use MWStake\MediaWiki\Lib\Nodes\IMutableNode;
use MWStake\MediaWiki\Lib\Nodes\INode;
use MWStake\MediaWiki\Lib\Nodes\MutableParser;
use User;

class Parser extends MutableParser {

	/** @var EnhancedSidebarNodeProcessor[] */
	private $nodeProcessors;

	/** @var array */
	private $nodes = null;

	/**
	 * @param RevisionRecord $revision
	 * @param array $nodeProcessors
	 */
	public function __construct( RevisionRecord $revision, array $nodeProcessors ) {
		parent::__construct( $revision );
		$this->nodeProcessors = $nodeProcessors;
	}

	/**
	 * @return array
	 */
	public function parse(): array {
		$content = $this->pullContent();
		if ( is_array( $this->nodes ) ) {
			return $this->nodes;
		}
		$this->nodes = [];
		$this->processNodesInternally( $content, 1 );
		return $this->nodes;
	}

	/**
	 * @param User $user
	 * @return array
	 */
	public function parseForOutput( User $user ): array {
		$this->setUserOnProcessors( $user );
		$this->parse();

		$data = [];
		/** @var EnhancedSidebarNode $node */
		foreach ( $this->nodes as $node ) {
			// Convert usual flat list of nodes into a tree
			if ( $node->isHidden() ) {
				continue;
			}
			if ( $node->getLevel() !== 1 ) {
				continue;
			}
			$data[] = $node->treeSerialize() + $this->getChildren( $node );
		}

		return $data;
	}

	/**
	 * @param EnhancedSidebarNode $node
	 * @return array[]
	 */
	private function getChildren( EnhancedSidebarNode $node ) {
		$parentFound = false;
		$children = [];
		/** @var EnhancedSidebarNode $node */
		foreach ( $this->nodes as $childNode ) {
			if ( $childNode === $node ) {
				$parentFound = true;
				continue;
			}
			if ( $parentFound ) {
				if ( $childNode->getLevel() === $node->getLevel() + 1 ) {
					$children[] = $childNode->treeSerialize() + $this->getChildren( $childNode );
				} else {
					return [ 'items' => $children ];
				}
			}
		}

		return [ 'items' => $children ];
	}

	/**
	 * @param User $user
	 * @return void
	 */
	private function setUserOnProcessors( User $user ) {
		foreach ( $this->nodeProcessors as $nodeProcessor ) {
			$nodeProcessor->setUser( $user );
		}
	}

	/**
	 * @return array
	 */
	public function pullContent(): array {
		$content = $this->revision->getContent( SlotRecord::MAIN );
		if ( !$content ) {
			throw new \UnexpectedValueException( 'Content of the page not readable' );
		}
		if ( !( $content instanceof \JsonContent ) ) {
			throw new \UnexpectedValueException( 'Not a JSON content' );
		}
		$text = $content->getText();
		$data = json_decode( $text, true );
		if ( !$data ) {
			throw new \Exception( json_last_error_msg() );
		}
		return $data;
	}

	/**
	 * @param array $nodes
	 * @param int $level
	 * @return void
	 */
	private function processNodesInternally( array $nodes, int $level ) {
		foreach ( $nodes as $nodeData ) {
			// Get node processor that supports the node type
			try {
				$nodeProcessor = $this->getNodeProcessor( $nodeData['type'] ?? '' );
			} catch ( \Exception $e ) {
				continue;
			}

			$node = $nodeProcessor->getNode( new JsonNodeSource( $nodeData ) );
			if ( !( $node instanceof EnhancedSidebarNode ) ) {
				continue;
			}
			$node->setLevel( $level );
			$this->nodes[] = $node;
			if ( isset( $nodeData['children'] ) && is_array( $nodeData['children'] ) && $node->supportsChildren() ) {
				$this->processNodesInternally( $nodeData['children'], $level + 1 );
			}
		}
	}

	/**
	 * @param string $type
	 * @return EnhancedSidebarNodeProcessor
	 */
	private function getNodeProcessor( string $type ): EnhancedSidebarNodeProcessor {
		foreach ( $this->nodeProcessors as $nodeProcessor ) {
			if ( $nodeProcessor->supportsNodeType( $type ) ) {
				return $nodeProcessor;
			}
		}
		throw new \UnexpectedValueException( 'No node processor found for type ' . $type );
	}

	/**
	 * @return \Content
	 */
	protected function getContentObject(): \Content {
		return new \JsonContent( json_encode( $this->getMutatedData() ) );
	}

	/**
	 * @param INode $node
	 * @param string $mode
	 * @param bool $newline
	 * @return void
	 */
	public function addNode( INode $node, $mode = 'append', $newline = true ): void {
		// TODO: Implement addNode() method.
	}

	/**
	 * @param IMutableNode $node
	 * @return bool
	 */
	public function replaceNode( IMutableNode $node ): bool {
		return true;
	}

	/**
	 * @param INode $node
	 * @return bool
	 */
	public function removeNode( INode $node ): bool {
		return true;
	}
}
