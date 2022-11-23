<?php

namespace BlueSpice\Discovery;

use Message;
use Title;

class SubpageDataGenerator {

	/**
	 * @param Title $title
	 * @return array
	 */
	public function generate( Title $title, int $maxDepth = 5 ): array {
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

			if ( $curTitle->equals( $activeTitle ) ) {
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
}
