<?php

namespace BlueSpice\Discovery\Component;

use IContextSource;
use MWStake\MediaWiki\Component\CommonUserInterface\Component\Literal;
use MWStake\MediaWiki\Component\CommonUserInterface\Component\SimpleCard;
use MWStake\MediaWiki\Component\CommonUserInterface\Component\SimpleCardHeader;
use RequestContext;
use Title;

class SubpageTree extends SimpleCard {

	private const MAX_DEPTH = 5;

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
		return 'subpage-tree';
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
		$title = $context->getTitle();

		if ( !$title->exists() ) {
			return false;
		}

		if ( !$title->isContentPage() ) {
			return false;
		}

		if ( !$title->hasSubpages() ) {
			return false;
		}

		$data = $this->buildTreeSubData( $title, self::MAX_DEPTH );
		if ( empty( $data ) ) {
			return false;
		}

		return true;
	}

	/**
	 *
	 * @return array
	 */
	private function buildPanels(): array {
		$id = 'subpage-tree-pnl';
		$headerText = wfMessage( 'bs-discovery-subpage-tree-pnl-header-text' )->text();

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
	 * @param Title $title
	 * @param int $maxDepth
	 * @return array
	 */
	private function buildTreeSubData( Title $title, int $maxDepth ): array {
		$activeTitle = $title;

		/** $title->isSubpage() delivers false even if it is a subpage */
		if ( !$title->equals( $title->getRootTitle() ) ) {
			$title = $title->getRootTitle();
		}

		$subpageTitles = $title->getSubpages();

		$subpages = [];
		foreach ( $subpageTitles as $subpageTitle ) {
			$subpagePath = $this->makeSupbageData( $subpageTitle, $activeTitle, $maxDepth );

			$cur = &$subpages;
			$subPageDepth = 0;
			foreach ( $subpagePath as $key => $value ) {
				if ( !isset( $cur[$key] ) ) {
					$cur[$key] = $value;
				}

				$subPageDepth++;

				if ( $subPageDepth >= count( $subpagePath ) ) {
					unset( $cur[$key]['items'] );
					break;
				}

				if ( $subPageDepth >= $maxDepth ) {
					unset( $cur[$key]['items'] );
					break;
				}

				$cur = &$cur[$key]['items'];

			}
		}

		return $this->clearIndex( $subpages );
	}

	/**
	 * @param Title $title
	 * @param Title $activeTitle
	 * @param int $maxDepth
	 * @return array
	 */
	private function makeSupbageData( Title $title, Title $activeTitle, int $maxDepth ): array {
		$list = [];

		$namespace = $title->getNamespace();
		$titleText = $title->getText();
		$titleParts = explode( '/', $titleText );

		$curTitleText = '';
		for ( $index = 0; $index < count( $titleParts ); $index++ ) {
			if ( $index > $maxDepth ) {
				break;
			}

			$titlePart = $titleParts[$index];
			$curTitleText .= $titlePart;

			if ( $index === 0 ) {
				$curTitleText .= '/';
				continue;
			}

			$curTitle = Title::makeTitle( $namespace, $curTitleText );

			$key = $curTitle->getDBkey();
			$list[$key] = [
				'name' => $curTitle->getPrefixedDBkey(),
				'label' => $titleParts[$index],
				'href' => $curTitle->getLocalURL(),
				'items' => []
			];

			if ( $curTitle->equals( $activeTitle ) ) {
				$list[$key]['class'] = 'active';
			}

			$curTitleText .= '/';
		}

		return $list;
	}

	/**
	 * @param array $data
	 * @return array
	 */
	private function clearIndex( array $data ): array {
		$indexedData = [];
		foreach ( $data as $key => $value ) {
			if ( isset( $value['items'] ) ) {
				$value['items'] = $this->clearIndex( $value['items'] );
			}
			$indexedData[] = $value;
		}

		return $indexedData;
	}

	/**
	 * @return array
	 */
	private function getTreeData(): array {
		$context = RequestContext::getMain();
		/** @var Title */
		$title = $context->getTitle();

		$data = $this->buildTreeSubData( $title, self::MAX_DEPTH );

		return $data;
	}
}
