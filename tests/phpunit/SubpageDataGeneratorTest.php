<?php

namespace BlueSpice\Discovery\Tests;

use BlueSpice\Discovery\SubpageDataGenerator;
use MediaWikiIntegrationTestCase;
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
		$this->insertPage( 'Dummy' );
		$this->insertPage( 'Dummy/ABC' );
		$this->insertPage( 'Dummy/DEF' );
		$this->insertPage( 'Dummy/DEF/Some_Subpage' );
		$this->insertPage( 'Dummy/GHI/AAA/BBB/CCC' );
	}

	/**
	 * @covers BlueSpice\Discovery\SubpageDataGenerator::generate
	 */
	public function testGenerate() {
		$suppageDataGenerator = new SubpageDataGenerator();

		$title = Title::newFromText( 'Dummy/DEF' );

		$actualSubpageData = $suppageDataGenerator->generate( $title );

		$this->assertEquals( $this->getExpected(), $actualSubpageData );
	}

	/**
	 * @return array
	 */
	private function getExpected(): array {
		return [
			$this->makeItem(
				'Dummy/ABC',
				'ABC',
				[],
				[]
			),
			$this->makeItem(
				'Dummy/DEF',
				'DEF',
				[ 'active' ],
				[
					$this->makeItem(
						'Dummy/DEF/Some_Subpage',
						'Some Subpage',
						[],
						[]
					),
				]
			),
			$this->makeItem(
				'Dummy/GHI',
				'GHI',
				[ 'new' ],
				[
					$this->makeItem(
						'Dummy/GHI/AAA',
						'AAA',
						[ 'new' ],
						[
							$this->makeItem(
								'Dummy/GHI/AAA/BBB',
								'BBB',
								[ 'new' ],
								[
									$this->makeItem(
										'Dummy/GHI/AAA/BBB/CCC',
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

		if ( !empty( $classes ) ) {
			$item['classes'] = $classes;
		}

		if ( !empty( $items ) ) {
			$item['items'] = $items;
		}

		return $item;
	}
}
