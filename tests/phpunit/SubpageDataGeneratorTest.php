<?php

namespace BlueSpice\Discovery\Tests;

use BlueSpice\Discovery\SubpageDataGenerator;
use MediaWiki\Message\Message;
use MediaWiki\Title\Title;
use MediaWikiIntegrationTestCase;

/**
 * @group Database
 */
class SubpageDataGeneratorTest extends MediaWikiIntegrationTestCase {

	/**
	 *
	 * @return void
	 */
	public function addDBDataOnce() {
		$this->setMwGlobals( 'wgNamespacesWithSubpages', [ NS_MAIN => true ] );

		$this->insertPage( 'Dummy2' );
		$this->insertPage( 'Dummy2/ABC' );
		$this->insertPage( 'Dummy2/DEF' );
		$this->insertPage( 'Dummy2/DEF/Some_Subpage' );
		$this->insertPage( 'Dummy2/GHI/AAA/BBB/CCC' );
	}

	/**
	 * @covers BlueSpice\Discovery\SubpageDataGenerator::generate
	 */
	public function testGenerate() {
		$subpageDataGenerator = new SubpageDataGenerator();

		// Tree from root title of $title
		$title = Title::newFromText( 'Dummy2/DEF' );
		$subpageDataGenerator->setActiveTitle( $title );
		$actualSubpageData = $subpageDataGenerator->generate( $title );
		$this->assertEquals( $this->getExpectedFromRoot(), $actualSubpageData );

		// Tree with specific root title
		$title = Title::NewFromText( 'Dummy2/GHI' );
		$subpageDataGenerator->setTreeRootTitle( $title );
		$actualSubpageData = $subpageDataGenerator->generate( $title );
		$this->assertEquals( $this->getExpectedFromSpecificTitle(), $actualSubpageData );
	}

	/**
	 * @return array
	 */
	private function getExpectedFromRoot(): array {
		return [
			$this->makeItem(
				'Dummy2/ABC',
				'ABC',
				[],
				[]
			),
			$this->makeItem(
				'Dummy2/DEF',
				'DEF',
				[ 'active' ],
				[
					$this->makeItem(
						'Dummy2/DEF/Some_Subpage',
						'Some Subpage',
						[],
						[]
					),
				]
			),
			$this->makeItem(
				'Dummy2/GHI',
				'GHI',
				[ 'new' ],
				[
					$this->makeItem(
						'Dummy2/GHI/AAA',
						'AAA',
						[ 'new' ],
						[
							$this->makeItem(
								'Dummy2/GHI/AAA/BBB',
								'BBB',
								[ 'new' ],
								[
									$this->makeItem(
										'Dummy2/GHI/AAA/BBB/CCC',
										'CCC',
										[],
										[]
									)
								]
							)
						]
					)
				]
			)
		];
	}

	/**
	 * @return array
	 */
	private function getExpectedFromSpecificTitle(): array {
		return [
			$this->makeItem(
				'Dummy2/GHI/AAA',
				'AAA',
				[ 'new' ],
				[
					$this->makeItem(
						'Dummy2/GHI/AAA/BBB',
						'BBB',
						[ 'new' ],
						[
							$this->makeItem(
								'Dummy2/GHI/AAA/BBB/CCC',
								'CCC',
								[],
								[]
							)
						]
					)
				]
			)
		];
	}

	/**
	 * @param string $name
	 * @param string $text
	 * @param array $classes
	 * @param array $items
	 * @return array
	 */
	private function makeItem( string $name, string $text, array $classes = [], array $items = [] ): array {
		$title = Title::newFromText( $name );

		$fullId = md5( $title->getFullText() );
		$id = substr( $fullId, 0, 6 );

		$item = [
			'id' => $id,
			'name' => $name,
			'text' => $text,
			'href' => $title->getLocalURL(),
		];

		if ( !empty( $items ) ) {
			$item['items'] = $items;
		}

		if ( !$title->exists() ) {
			$item['title'] = Message::newFromKey(
				'bs-discovery-page-does-not-exist-title',
				$title->getPrefixedText()
			)->text();
		}

		if ( !empty( $classes ) ) {
			$item['classes'] = $classes;
		}

		return $item;
	}
}
