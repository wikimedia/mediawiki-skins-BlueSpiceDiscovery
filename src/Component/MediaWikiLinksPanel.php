<?php

namespace BlueSpice\Discovery\Component;

use IContextSource;
use MediaWiki\MediaWikiServices;
use Message;
use MWStake\MediaWiki\Component\CommonUserInterface\Component\Literal;
use MWStake\MediaWiki\Component\CommonUserInterface\Component\RestrictedTextLink;
use MWStake\MediaWiki\Component\CommonUserInterface\Component\SimpleCard;
use MWStake\MediaWiki\Component\CommonUserInterface\Component\SimpleCardHeader;
use MWStake\MediaWiki\Component\CommonUserInterface\Component\SimpleLinklistGroupFromArray;
use MWStake\MediaWiki\Component\CommonUserInterface\IComponent;
use MWStake\MediaWiki\Component\CommonUserInterface\LinkFormatter;
use Title;

class MediaWikiLinksPanel extends SimpleCard {

	/**
	 *
	 */
	public function __construct() {
		parent::__construct( [] );
	}

	/**
	 * @inheritDoc
	 */
	public function getId(): string {
		return 'mw-links';
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
		if ( !isset( $this->componentProcessData['panel'] )
			|| !isset( $this->componentProcessData['panel']['sidebar'] ) ) {
			return [];
		}
		$sidebar = $this->componentProcessData['panel']['sidebar'];
		if ( empty( $sidebar ) ) {
			return [];
		}
		$sidebarEdit = Title::makeTitle( NS_MEDIAWIKI, 'Sidebar' );

		$services = MediaWikiServices::getInstance();
		/** @var LinkFormatter */
		$linkFormatter = $services->getService( 'MWStakeLinkFormatter' );

		$items = [];
		foreach ( $sidebar as $section => $links ) {
			$this->checkActiveState( $links );

			$items[] = $this->buildPanel( $section, $links, $linkFormatter );

		}

		$items[] = $this->buildEditLink();

		return $items;
	}

	/**
	 *
	 * @param string $section
	 * @param array $links
	 * @param LinkFormatter $linkFormatter
	 * @return IComponent
	 */
	private function buildPanel( $section, $links, $linkFormatter ): IComponent {
		$id = 'n-links-' . strtolower( $section );
		// Messages for navigation like 'bs-discovery-navigation-heading
		$headerTextMsg = Message::newFromKey( 'bs-discovery-' . $section . '-heading' );
		$headerText = $headerTextMsg->exists() ? $headerTextMsg->text() : $section;
		$item = new SimpleCard( [
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
				new SimpleLinklistGroupFromArray( [
					'id' => $id,
					'classes' => [],
					'aria' => [
						'labelledby' => $id . '-head'
					],
					'links' => $linkFormatter->formatLinks( $links )
				] )
			]
		] );

		return $item;
	}

	/**
	 *
	 * @return IComponent
	 */
	private function buildEditLink(): IComponent {
		/** @var Title */
		$title = Title::makeTitle( NS_MEDIAWIKI, 'Sidebar' );

		$item = new RestrictedTextLink( [
			'role' => 'link',
			'id' => 'edit-sidebar-link',
			'href' => $title->getEditURL(),
			'text' => Message::newFromKey( 'bs-discovery-edit-mediawiki-sidebar-link-text' ),
			'title' => Message::newFromKey( 'bs-discovery-edit-mediawiki-sidebar-link-title' ),
			'aria-label' => Message::newFromKey( 'bs-discovery-edit-mediawiki-sidebar-link-aria-label' ),
			'permissions' => [ 'editinterface' ],
		] );

		return $item;
	}

	/**
	 *
	 * @param arry &$links
	 * @return void
	 */
	private function checkActiveState( &$links ) {
		foreach ( $links as $key => $link ) {
			if ( isset( $link['active'] ) && $link['active'] === true ) {
				if ( isset( $link['class'] ) ) {
					$links[$key]['class'] .= ' active';
				} elseif ( $link['active'] === true ) {
					$links[$key]['class'] = ' active';
				}
			}
		}
	}
}
