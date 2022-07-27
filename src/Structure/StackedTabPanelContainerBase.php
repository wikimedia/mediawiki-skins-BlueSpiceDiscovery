<?php

namespace BlueSpice\Discovery\Structure;

use BlueSpice\Discovery\IContextSourceAware;
use BlueSpice\Discovery\IResourceProvider;
use BlueSpice\Discovery\ITabPanelContainer;
use IContextSource;
use MediaWiki\MediaWikiServices;
use MWStake\MediaWiki\Component\CommonUserInterface\IComponent;
use MWStake\MediaWiki\Component\CommonUserInterface\ITabPanel;
use MWStake\MediaWiki\Component\CommonUserInterface\SkinSlotRegistry;

abstract class StackedTabPanelContainerBase extends SkinStructureBase
	implements ITabPanelContainer, IContextSourceAware, IResourceProvider {

	/**
	 * @var array
	 */
	private $skinComponents = [];

	/**
	 * @var string
	 */
	private $activeId = '';

	/**
	 * @var IContextSource
	 */
	private $context = null;

	/**
	 *
	 * @return array
	 */
	private function getTabPanels(): array {
		/** @var MediaWikiServices */
		$services = MediaWikiServices::getInstance();

		/** @var SkinSlotRegistry */
		$skinSlotRegistry = $services->getService( 'MWStakeSkinSlotRegistry' );

		$skinSlots = $skinSlotRegistry->getSkinSlots();
		$skinSlot = $skinSlots[$this->getTabPanelRegistryKey()];

		$tabPanels = [];
		foreach ( $skinSlot as $key => $item ) {
			if ( !is_callable( $item['factory'] ) ) {
				continue;
			}
			$component = call_user_func_array( $item['factory'], [] );
			if ( ( $component instanceof IComponent ) ) {
				$tabPanels[] = $component;
			}
		}
		return $tabPanels;
	}

	/**
	 * Params for tab panels
	 *  '0' => [
	 *          'body' => 'value1',
	 *          ],
	 *  '1' => ...
	 */
	private function buildTabPanels() {
		$tabPanels = $this->getTabPanels();

		$this->findActiveId( $tabPanels );

		$first = true;
		$mainPanel = [];
		$otherPanels = [];
		foreach ( $tabPanels as $id => $tabPanel ) {
			if ( !( $tabPanel instanceof ITabPanel )
			 || !$tabPanel->shouldRender( $this->context ) ) {
				continue;
			}

			$body = '';
			$subComponents = $tabPanel->getSubComponents();
			foreach ( $subComponents as $subComponent ) {
				if ( !empty( $subComponent ) ) {
					$body .= $this->componentRenderer->getComponentHtml(
						$subComponent,
						$this->componentProcessData
					);
				}
			}
			if ( $body === '' ) {
				continue;
			}

			$class = '';
			if ( $tabPanel->getId() === $this->activeId ) {
				$class = ' show active';
			}

			if ( $first ) {
				$first = false;
				$mainPanel['list'][] = [
					'id' => $tabPanel->getId(),
					'title' => $tabPanel->getTitle()->text(),
					'text' => $tabPanel->getText()->text(),
					'aria-label' => $tabPanel->getAriaLabel()->text()
				];
				$mainPanel['panel'][] = [
					'body' => $body,
					'id' => $tabPanel->getId(),
					'desc' => $tabPanel->getAriaDesc()->text(),
					'class' => $class
				];
			} else {
				$otherPanels['list'][] = [
					'id' => $tabPanel->getId(),
					'title' => $tabPanel->getTitle()->text(),
					'text' => $tabPanel->getText()->text(),
					'aria-label' => $tabPanel->getAriaLabel()->text()
				];
				$otherPanels['panel'][] = [
					'body' => $body,
					'id' => $tabPanel->getId(),
					'desc' => $tabPanel->getAriaDesc()->text(),
					'class' => $class
				];
			}
		}

		$main = [];
		if ( array_key_exists( 'panel', $mainPanel ) ) {
			foreach ( $mainPanel['panel'] as $panel ) {
				if ( array_key_exists( 'list', $otherPanels ) ) {
					$panel = array_merge( $panel, [ 'list' => $otherPanels['list'] ] );
				}
			}

			$main[] = $panel;
		}

		$other = [];
		if ( array_key_exists( 'panel', $otherPanels ) ) {
			foreach ( $otherPanels['panel'] as $panel ) {
				if ( array_key_exists( 'list', $mainPanel ) ) {
					$panel = array_merge( $panel, [ 'list' => $mainPanel['list'] ] );
				}

				$other[] = $panel;
			}
		}

		if ( !empty( $main ) ) {
			$this->skinComponents['sidebar']['main'] = $main;
		}

		if ( !empty( $other ) ) {
			$this->skinComponents['sidebar']['other'] = $other;
		}
	}

	/**
	 *
	 * @param ITabPanel[] $tabPanels
	 * @return void
	 */
	private function findActiveId( $tabPanels ): void {
		$first = true;
		foreach ( $tabPanels as $id => $tabPanel ) {
			if ( !( $tabPanel instanceof ITabPanel ) ) {
				continue;
			}
			if ( $first ) {
				$first = false;
				$this->activeId = $tabPanel->getId();
			}
			if ( $tabPanel->isActive( $this->context ) ) {
				$this->activeId = $tabPanel->getId();
				break;
			}
		}
	}

	/**
	 *
	 * @return array
	 */
	public function getParams(): array {
		$this->buildTabPanels();
		$params = [
			'id' => $this->getId()
		];
		if ( !empty( $this->getClasses() ) ) {
			$params = array_merge(
				$params,
				$this->skinComponents,
				[
					'container-class' => implode( ' ', $this->getClasses() )
				]
			);
		}

		return $params;
	}

	/**
	 * @return string
	 */
	public function getTemplateName(): string {
		return 'stacked-tab-panel-container';
	}

	/**
	 *
	 * @return array
	 */
	public function getClasses(): array {
		return [];
	}

	/**
	 *
	 * @param IContextSource $context
	 * @return bool
	 */
	public function shouldRender( IContextSource $context ): bool {
		$shouldRender = false;
		$tabPanels = $this->getTabPanels();
		foreach ( $tabPanels as $id => $tabPanel ) {
			if ( !( $tabPanel instanceof ITabPanel ) ) {
				continue;
			}
			$subComponents = $tabPanel->getSubComponents();
			foreach ( $subComponents as $subComponent ) {
				if ( $subComponent->shouldRender( $context ) ) {
					$shouldRender = true;
				}
			}
		}
		return $shouldRender;
	}

	/**
	 * @param IContextSource $context
	 * @return void
	 */
	public function setContextSource( IContextSource $context ): void {
		$this->context = $context;
	}

	/**
	 * @return array
	 */
	public function getStyles(): array {
		return [];
	}

	/**
	 * @return array
	 */
	public function getScripts(): array {
		return [];
	}
}
