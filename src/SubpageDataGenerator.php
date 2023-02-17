<?php

namespace BlueSpice\Discovery;

use Message;
use Title;

class SubpageDataGenerator {

	/** @var Title|null */
	private $activeTitle = null;

	/** @var Title|null */
	private $treeRootTitle = null;

	/**
	 * @param Title $title
	 * @return void
	 */
	public function setActiveTitle( Title $title ): void {
		$this->activeTitle = $title;
	}

	/**
	 * Tree root title has to be the subpage root of the title or
	 * one of titles suppages.
	 *
	 * @param Title $title
	 * @return void
	 */
	public function setTreeRootTitle( Title $title ): void {
		$this->treeRootTitle = $title;
	}

	/**
	 * $title is the root title or a subpage of the title for that the
	 * subpage tree should be created.
	 *
	 * @param Title $title
	 * @return array
	 */
	public function generate( Title $title, int $maxDepth = 6 ): array {
		$rootTitle = $title;

		/** $title->isSubpage() delivers false even if it is a subpage */
		if ( !$rootTitle->equals( $title->getRootTitle() ) ) {
			$rootTitle = $title->getRootTitle();
		}

		if ( $this->treeRootTitle ) {
			$rootTitle = $this->treeRootTitle;
			$rootTitleParts = explode( '/', $rootTitle->getDBkey() );
			if ( count( $rootTitleParts ) > 1 ) {
				$maxDepth = $maxDepth + count( $rootTitleParts ) - 1;
			}
		}

		$subpageTitles = $rootTitle->getSubpages();

		$subpages = [];
		foreach ( $subpageTitles as $subpageTitle ) {
			$subpagePath = $this->makeSupbageData( $subpageTitle, $maxDepth );
			$this->unsetUnusedItems( $subpagePath, $subpages, $maxDepth );
		}

		if ( $this->treeRootTitle ) {
			$rootTitleKey = $rootTitle->getDBkey();
			if ( isset( $subpages[$rootTitleKey]['items'] ) ) {
				return $this->clearRootIndex( $subpages[$rootTitleKey]['items'] );
			} else {
				return [];
			}
		}

		return $this->clearRootIndex( $subpages );
	}

	/**
	 * @param array $subpagePath
	 * @param array &$subpages
	 * @param int $maxDepth
	 */
	private function unsetUnusedItems( array $subpagePath, array &$subpages, int $maxDepth ) {
		$subPageDepth = 0;
		foreach ( $subpagePath as $key => $value ) {
			if ( !isset( $subpages[$key] ) ) {
				$subpages[$key] = $value;
			}

			$subPageDepth++;

			if ( $subPageDepth >= count( $subpagePath ) ) {
				unset( $subpages[$key]['items'] );
				break;
			}

			if ( $subPageDepth >= $maxDepth ) {
				unset( $subpages[$key]['items'] );
				break;
			}

			$subpages = &$subpages[$key]['items'];
		}
	}

	/**
	 * @param Title $title
	 * @param int $maxDepth
	 * @return array
	 */
	private function makeSupbageData( Title $title, int $maxDepth ): array {
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

			$fullId = md5( $curTitle->getFullText() );
			$id = substr( $fullId, 0, 6 );

			$key = $curTitle->getDBkey();
			$list[$key] = [
				'id' => $id,
				'name' => $curTitle->getPrefixedDBkey(),
				'text' => $titleParts[$index],
				'href' => $curTitle->getLocalURL(),
				'items' => []
			];

			$classes = [];
			if ( !$curTitle->exists() ) {
				$classes[] = 'new';

				$titleText = Message::newFromKey(
					'bs-discovery-page-does-not-exist-title',
					$curTitle->getPrefixedText()
				);
				$list[$key]['title'] = $titleText->text();
			}

			if ( $this->activeTitle && $curTitle->equals( $this->activeTitle ) ) {
				$classes[] = 'active';
			}

			if ( !empty( $classes ) ) {
				$list[$key]['classes'] = $classes;
			}

			$curTitleText .= '/';
		}

		return $list;
	}

	/**
	 * @param array $data
	 * @return array
	 */
	private function clearRootIndex( array $data ): array {
		$indexedData = [];
		foreach ( $data as $key => $value ) {
			if ( isset( $value['items'] ) ) {
				$value['items'] = $this->clearRootIndex( $value['items'] );
			}
			$indexedData[] = $value;
		}

		return $indexedData;
	}
}
