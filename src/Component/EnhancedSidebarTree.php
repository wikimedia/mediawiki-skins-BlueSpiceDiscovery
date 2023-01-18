<?php

namespace BlueSpice\Discovery\Component;

use BlueSpice\Discovery\CookieHandler;
use MWStake\MediaWiki\Component\CommonUserInterface\Component\SimpleTreeContainer;
use MWStake\MediaWiki\Component\CommonUserInterface\TreeDataGenerator;
use Psr\Log\LoggerAwareInterface;
use Psr\Log\LoggerInterface;

class EnhancedSidebarTree extends SimpleTreeContainer implements LoggerAwareInterface {

	/**
	 * @var LoggerInterface
	 */
	private $logger;

	/**
	 * @var array
	 */
	private $items;

	/**
	 * @var TreeDataGenerator
	 */
	private $treeDataGenerator;

	/**
	 * @var CookieHandler
	 */
	protected $cookieHandler = null;

	/**
	 * @param array $items
	 * @param TreeDataGenerator $treeDataGenerator
	 * @param CookieHandler $cookieHandler
	 * @param array $options
	 */
	public function __construct( array $items, TreeDataGenerator $treeDataGenerator,
			CookieHandler $cookieHandler, array $options = []
		) {
		parent::__construct( $options );

		$this->items = $items;
		$this->treeDataGenerator = $treeDataGenerator;
		$this->cookieHandler = $cookieHandler;
	}

	/**
	 * @param LoggerInterface $logger
	 * @return void
	 */
	public function setLogger( LoggerInterface $logger ) {
		$this->logger = $logger;
	}

	/**
	 * @inheritDoc
	 */
	public function getSubComponents(): array {
		if ( empty( $this->items ) ) {
			return [];
		}

		$nodes = $this->treeDataGenerator->generate(
			$this->items,
			$this->getTreeExpandPaths()
		);

		return $nodes;
	}

	/**
	 * @return array
	 */
	private function getTreeExpandPaths(): array {
		$paths = $this->cookieHandler->getCookie( $this->getId(), [] );
		return $paths;
	}

	/**
	 * @return string[]
	 */
	public function getRequiredRLStyles(): array {
		return [ 'skin.discovery.mws-tree-component.styles' ];
	}

	/**
	 * @inheritDoc
	 */
	public function getRequiredRLModules(): array {
		$parentScripts = parent::getRequiredRLModules();

		$scripts = array_merge(
			[ 'skin.discovery.mws-tree-component.scripts' ],
			$parentScripts
		);

		return $scripts;
	}
}
