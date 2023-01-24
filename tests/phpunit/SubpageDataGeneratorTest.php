<?php

namespace BlueSpice\Discovery\Tests;

use BlueSpice\Discovery\SubpageDataGenerator;
use MediaWikiIntegrationTestCase;
use Message;
use Title;

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
		$suppageDataGenerator = new SubpageDataGenerator();

		$title = Title::newFromText( 'Dummy2/DEF' );

		$actualSubpageData = $suppageDataGenerator->generate( $title );

		$this->assertEquals( $this->getExpected(), $actualSubpageData );
	}

	/**
	 * @return array
	 */
	private function getExpected(): array {
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

		// Change 'classes' order for $title passed to generate() because 'items' gets reset
		if ( $name == 'Dummy2/DEF' ) {
			if ( !empty( $classes ) ) {
				$item['classes'] = $classes;
			}
		}

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
