<?php

namespace BlueSpice\Discovery\Component;

use MediaWiki\MediaWikiServices;
use Message;
use MWStake\MediaWiki\Component\CommonUserInterface\Component\SimpleDropdownIcon;
use MWStake\MediaWiki\Component\CommonUserInterface\Component\SimpleDropdownItemlistFromArray;
use MWStake\MediaWiki\Component\CommonUserInterface\LinkFormatter;

class ExportOptions extends SimpleDropdownIcon {

	/**
	 *
	 */
	public function __construct() {
		parent::__construct( [] );
	}

	/**
	 * @inheritDoc
	 */
	public function getId() : string {
		return 'export-menu';
	}

	/**
	 * @return array
	 */
	public function getButtonClasses() : array {
		$classes = [ 'ico-btn' ];

		if ( !isset( $this->componentProcessData['panel'] )
			|| !isset( $this->componentProcessData['panel']['export'] )
			|| empty( $this->componentProcessData['panel']['export'] ) ) {
				array_push( $classes, 'disabled' );
		}

		return $classes;
	}

	/**
	 * @return array
	 */
	public function getMenuClasses() : array {
		return [ 'mws-dropdown-secondary' ];
	}

	/**
	 * @return array
	 */
	public function getIconClasses() : array {
		return [ 'bi-file-earmark' ];
	}

	/**
	 * @return Message
	 */
	public function getTitle() : Message {
		return Message::newFromKey( 'bs-discovery-sidebar-secondary-export-link-title' );
	}

	/**
	 * @return Message
	 */
	public function getAriaLabel() : Message {
		return Message::newFromKey( 'bs-discovery-sidebar-secondary-export-link-aria-label' );
	}

	/**
	 * @inheritDoc
	 */
	public function getSubComponents() : array {
		if ( !isset( $this->componentProcessData['panel'] )
			|| !isset( $this->componentProcessData['panel']['export'] ) ) {
			return [];
		}
		$export = $this->componentProcessData['panel']['export'];
		if ( empty( $export ) ) {
			return [];
		}

		$services = MediaWikiServices::getInstance();
		/** @var LinkFormatter */
		$linkFormatter = $services->getService( 'MWStakeLinkFormatter' );

		return [
			new SimpleDropdownItemlistFromArray( [
				'id' => $this->getId() . '-list',
				'links' => $linkFormatter->formatLinks( $export ),
				'classes' => []
			] )
		];
	}
}
