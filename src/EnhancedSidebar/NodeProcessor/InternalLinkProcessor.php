<?php

namespace BlueSpice\Discovery\EnhancedSidebar\NodeProcessor;

use BlueSpice\Discovery\EnhancedSidebar\Node\InternalLinkNode;
use MediaWiki\Permissions\PermissionManager;
use MWStake\MediaWiki\Lib\Nodes\INode;
use MWStake\MediaWiki\Lib\Nodes\INodeSource;
use ParserFactory;
use Title;
use TitleFactory;

class InternalLinkProcessor extends EnhancedSidebarNodeProcessor {

	/** @var TitleFactory */
	private $titleFactory;

	/** @var PermissionManager */
	private $permissionManager;

	/**
	 * @param PermissionManager $permissionManager
	 * @param ParserFactory $parserFactory
	 * @param TitleFactory $titleFactory
	 */
	public function __construct(
		PermissionManager $permissionManager, ParserFactory $parserFactory,
		TitleFactory $titleFactory
	) {
		parent::__construct( $parserFactory );
		$this->titleFactory = $titleFactory;
		$this->permissionManager = $permissionManager;
	}

	/**
	 * @param string $type
	 * @return bool
	 */
	public function supportsNodeType( $type ): bool {
		return $type === 'internal';
	}

	/**
	 * @param array $data
	 * @return INode
	 */
	public function getNodeFromData( array $data ): INode {
		return new InternalLinkNode(
			$this->getTitleFromParam( $data['page'] ),
			$data['text'],
			$this->isHidden( $data )
		);
	}

	/**
	 * @param string $pagename
	 * @return Title
	 */
	protected function getTitleFromParam( $pagename ): Title {
		return $this->titleFactory->newFromText( $pagename ) ?? $this->titleFactory->newMainPage();
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
	 * @param Title $title
	 *
	 * @return bool
	 */
	protected function userCanReadTitle( Title $title ) {
		if ( $this->user === null ) {
			return false;
		}
		return $this->permissionManager->userCan( 'read', $this->user, $title );
	}

	/**
	 * @inheritDoc
	 */
	protected function getKeysToPreprocess( array $data ): array {
		return [ 'text', 'hidden', 'page' ];
	}
}
