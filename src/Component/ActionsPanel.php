<?php

namespace BlueSpice\Discovery\Component;

use BlueSpice\Discovery\LinkFormatter;
use Message;
use MWStake\MediaWiki\Component\CommonUserInterface\Component\Literal;
use MWStake\MediaWiki\Component\CommonUserInterface\Component\SimpleCard;
use MWStake\MediaWiki\Component\CommonUserInterface\Component\SimpleCardHeader;
use MWStake\MediaWiki\Component\CommonUserInterface\Component\SimpleLinklistGroupFromArray;
use MWStake\MediaWiki\Component\CommonUserInterface\IRestrictedComponent;

class ActionsPanel extends SimpleCard implements IRestrictedComponent {

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
		return 'actions-pnl';
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
		$actionsPrimary = [];
		$actionsSecondary = [];

		if ( isset( $this->componentProcessData['panel'] )
			&& isset( $this->componentProcessData['panel']['actions_primary'] ) ) {
				$actionsPrimary = $this->componentProcessData['panel']['actions_primary'];
		}

		if ( isset( $this->componentProcessData['panel'] )
			&& isset( $this->componentProcessData['panel']['actions_secondary'] ) ) {
				$actionsSecondary = $this->componentProcessData['panel']['actions_secondary'];
		}

		if ( empty( $actionsPrimary ) && empty( $actionsSecondary ) ) {
			return [];
		}

		/** @var LinkFormatter */
		$linkFormatter = new LinkFormatter();

		$list = [
			new SimpleCardHeader( [
				'id' => $this->getId() . '-head',
				'classes' => [ 'menu-title' ],
				'items' => [
					new Literal(
						$this->getId() . '-head',
						Message::newFromKey( 'bs-discovery-sidebar-actions-heading' )->text()
					)
				]
			] )
		];

		if ( !empty( $actionsPrimary ) ) {
			$pnl = new SimpleLinklistGroupFromArray( [
				'id' => $this->getId() . '-list-pri',
				'classes' => [],
				'aria' => [
					'labelledby' => $this->getId() . '-head'
				],
				'links' => $linkFormatter->formatLinks( $this->sortLinks( $actionsPrimary ) )
			] );

			array_push( $list, $pnl );
		}

		if ( !empty( $actionsSecondary ) ) {
			$pnl = new SimpleLinklistGroupFromArray( [
				'id' => $this->getId() . '-list-sec',
				'classes' => [],
				'aria' => [
					'labelledby' => $this->getId() . '-head'
				],
				'links' => $linkFormatter->formatLinks( $this->sortLinks( $actionsSecondary ) )
			] );

			array_push( $list, $pnl );
		}

		return $list;
	}

	/**
	 *
	 * @return string[]
	 */
	public function getPermissions() : array {
		return [ 'read' ];
	}

	/**
	 * @return array
	 */
	protected function getFavoritePositions(): array {
		return [
			'ca-move' => 10,
			'ca-delete' => 30,
			'ca-purge' => 40,
			'ca-protect' => 50,
		];
	}

	/**
	 * @param array $links
	 * @return array
	 */
	protected function sortLinks( $links ): array {
		foreach ( $links as $key => &$data ) {
			if ( isset( $data['position'] ) ) {
				continue;
			}
			$data['position'] = isset( $this->getFavoritePositions()[$key] )
				? $this->getFavoritePositions()[$key]
				: 0;
		}
		usort( $links, function ( $e1, $e2 ) {
			if ( $e1['position'] == $e2['position'] ) {
				return 0;
			}
			return $e1['position'] > $e2['position'] ? 1 : 0;
		} );
		return $links;
	}
}
