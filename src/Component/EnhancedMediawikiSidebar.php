<?php

namespace BlueSpice\Discovery\Component;

use BlueSpice\Discovery\EnhancedSidebar\Parser as EnchancedSidebarParser;
use IContextSource;
use MWStake\MediaWiki\Component\CommonUserInterface\Component\Literal;
use MWStake\MediaWiki\Component\CommonUserInterface\Component\SimpleCard;
use MWStake\MediaWiki\Component\CommonUserInterface\Component\SimpleCardHeader;
use Psr\Log\LoggerAwareInterface;
use Psr\Log\LoggerInterface;
use RequestContext;

class EnhancedMediawikiSidebar extends SimpleCard implements LoggerAwareInterface {

	/** @var EnchancedSidebarParser|null */
	private $parser;

	/** @var LoggerInterface */
	private $logger;

	/**
	 * @param EnchancedSidebarParser $parser
	 */
	public function __construct( EnchancedSidebarParser $parser ) {
		parent::__construct( [] );
		$this->parser = $parser;
	}

	/**
	 * @inheritDoc
	 */
	public function getId(): string {
		return 'enhanced-mediawiki-sidebar';
	}

	/**
	 * @inheritDoc
	 */
	public function getContainerClasses(): array {
		return [ 'w-100', 'bg-transp' ];
	}

	/**
	 * @inheritDoc
	 */
	public function getSubComponents(): array {
		return $this->buildPanels();
	}

	/**
	 *
	 * @param IContextSource $context
	 * @return bool
	 */
	public function shouldRender( IContextSource $context ): bool {
		return true;
	}

	/**
	 *
	 * @return array
	 */
	private function buildPanels(): array {
		$id = 'enhanced-mediawiki-sidebar-pnl';
		$headerText = wfMessage( 'bs-discovery-enhanced-mediawiki-sidebar-pnl-header-text' )->text();

		return [
			new SimpleCard( [
				'id' => $id,
				'classes' => [ 'w-100', 'bg-transp' ],
				'items' => [
					new SimpleCardHeader( [
						'id' => $id . '-head',
						'classes' => [ 'menu-title' ],
						'items' => [
							new Literal(
								$id . '-head',
								$headerText
							)
						]
					] ),
					new TreeMenuContainer(
						$id . '-menu',
						$id . '-head',
						[
							'w-100',
							'bg-transp'
						],
						$this->getTreeData()
					)
				]
			] )
		];
	}

	/**
	 * @return array
	 */
	private function getTreeData(): array {
		try {
			return $this->parser->parseForOutput( RequestContext::getMain()->getUser() );
		} catch ( \Exception $ex ) {
			$this->logger->error(
				'EnhancedSidebarParser failed to parse sidebar',
				[
					'exception' => $ex
				]
			);
			return [];
		}
	}

	/**
	 * @param LoggerInterface $logger
	 * @return void
	 */
	public function setLogger( LoggerInterface $logger ) {
		$this->logger = $logger;
	}
}
