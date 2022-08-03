<?php

namespace BlueSpice\Discovery\MenuProvider;

use BlueSpice\Discovery\Component\EnhancedMediawikiSidebar as ComponentEnhancedMediawikiSidebar;
use BlueSpice\Discovery\EnhancedSidebar\NodeProcessor\EnhancedSidebarNodeProcessor;
use BlueSpice\Discovery\EnhancedSidebar\Parser as EnhancedSidebarParser;
use BlueSpice\Discovery\IMenuProvider;
use MediaWiki\Logger\LoggerFactory;
use MediaWiki\Revision\RevisionStore;
use Message;
use MWStake\MediaWiki\Component\CommonUserInterface\IComponent;
use Psr\Log\LoggerInterface;
use RawMessage;
use Title;
use TitleFactory;
use Wikimedia\ObjectFactory\ObjectFactory;

class EnhancedMediawikiSidebar implements IMenuProvider {
	/** @var LoggerInterface */
	private $logger;
	/** @var EnhancedSidebarParser */
	private $parser = null;

	/**
	 * @param TitleFactory $titleFactory
	 * @param string $pagename
	 */
	public function __construct(
		RevisionStore $revisionStore, TitleFactory $titleFactory,
		ObjectFactory $objectFactory, string $pagename, string $processorRegistryAttribute
	) {
		$this->logger = LoggerFactory::getInstance( 'bluespicediscovery' );
		$title = $titleFactory->newFromText( $pagename );
		if ( $title instanceof Title ) {
			$revision = $revisionStore->getRevisionByTitle( $title );
			$nodeProcessors = $this->initNodeProcessors(
				$processorRegistryAttribute, $objectFactory
			);
			if ( $revision ) {
				$this->parser = new EnhancedSidebarParser(
					$revision, $nodeProcessors
				);
			}
		}
	}

	/**
	 * @return string
	 */
	public function getName(): string {
		return 'enhanced-mediawiki-sidebar';
	}

	/**
	 * @return Message
	 */
	public function getLabelMsg(): Message {
		return new Message( 'bs-discovery-menu-provider-enhanced-mediawiki-sidebar-tree-label' );
	}

	/**
	 * @return Message
	 */
	public function getDescriptionMsg(): Message {
		return new RawMessage( 'bs-discovery-menu-provider-enhanced-mediawiki-sidebar-tree-desc' );
	}

	/**
	 * @return IComponent
	 */
	public function getComponent(): IComponent {
		if ( !$this->parser ) {
			throw new \MWException( 'EnhancedSidebar cannot be parsed' );
		}
		$sidebarComponent = new ComponentEnhancedMediawikiSidebar( $this->parser );
		$sidebarComponent->setLogger( $this->logger );
		return $sidebarComponent;
	}

	/**
	 * @param string $processorRegistryAttribute
	 * @param ObjectFactory $objectFactory
	 * @return array
	 */
	private function initNodeProcessors(
		string $processorRegistryAttribute, ObjectFactory $objectFactory
	): array {
		$attribute = \ExtensionRegistry::getInstance()->getAttribute(
			"BlueSpiceDiscovery$processorRegistryAttribute"
		);

		$processors = [];
		foreach ( $attribute as $key => $spec ) {
			$object = $objectFactory->createObject( $spec );
			if ( !( $object instanceof EnhancedSidebarNodeProcessor ) ) {
				continue;
			}
			$processors[$key] = $object;
		}

		return $processors;
	}
}
