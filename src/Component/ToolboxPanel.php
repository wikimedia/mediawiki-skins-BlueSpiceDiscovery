<?php

namespace BlueSpice\Discovery\Component;

use BlueSpice\Discovery\LinkFormatter;
use Message;
use MWStake\MediaWiki\Component\CommonUserInterface\Component\Literal;
use MWStake\MediaWiki\Component\CommonUserInterface\Component\SimpleCard;
use MWStake\MediaWiki\Component\CommonUserInterface\Component\SimpleCardHeader;
use MWStake\MediaWiki\Component\CommonUserInterface\Component\SimpleLinklistGroupFromArray;
use MWStake\MediaWiki\Component\CommonUserInterface\IRestrictedComponent;

class ToolboxPanel extends SimpleCard implements IRestrictedComponent {

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
		return 't-links';
	}

	/**
	 * @inheritDoc
	 */
	public function getContainerClasses() : array {
		return [ 'w-100', 'bg-transp' ];
	}

	/**
	 * @inheritDoc
	 */
	public function getSubComponents() : array {
		if ( !isset( $this->componentProcessData['panel'] )
			|| !isset( $this->componentProcessData['panel']['toolbox'] ) ) {
			return [];
		}
		$toolbox = $this->componentProcessData['panel']['toolbox'];
		if ( empty( $toolbox ) ) {
			return [];
		}

		/** @var LinkFormatter */
		$linkFormatter = new LinkFormatter();

		return [
			new SimpleCardHeader( [
				'id' => $this->getId() . '-head',
				'classes' => [ 'menu-title' ],
				'items' => [
					new Literal(
						$this->getId() . '-title',
						Message::newFromKey( 'bs-discovery-toolbox-heading' )->text()
					)
				]
			] ),
			new SimpleLinklistGroupFromArray( [
				'id' => $this->getId() . 'list',
				'classes' => [],
				'aria' => [
					'labelledby' => $this->getId() . '-head'
				],
				'links' => $linkFormatter->formatLinks( $toolbox )
			] )
		];
	}

	/**
	 *
	 * @return string[]
	 */
	public function getPermissions() : array {
		return [ 'read' ];
	}
}
