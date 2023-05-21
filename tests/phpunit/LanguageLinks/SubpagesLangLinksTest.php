<?php

namespace BlueSpice\Discovery\Tests\LanguageLinks;

use BlueSpice\Discovery\LangLinksProvider\Subpages;
use MediaWiki\MediaWikiServices;
use MediaWikiIntegrationTestCase;
use Title;

/**
 * @group Database
 * @covers BlueSpice\Discovery\LangLinksProvider\Subpages
 */
class SubpagesLangLinksTest extends MediaWikiIntegrationTestCase {

	/**
	 * @return void
	 */
	public function addDBDataOnce() {
		$this->setMwGlobals( 'wgNamespacesWithSubpages', [ NS_MAIN => true ] );
		$this->setMwGlobals( 'wgLanguageCode', 'en' );

		$this->insertPage( 'Test Page' );
		$this->insertPage( 'Test Page/de' );
		$this->insertPage( 'Test Page/fr' );

		$this->insertPage( 'Test Page/notsubpage' );
	}

	/**
	 * @covers BlueSpice\Discovery\LangLinksProvider\Subpages::getLangLinks
	 * @dataProvider provideData
	 */
	public function testGetLangLinks( $title, $expected ) {
		$services = MediaWikiServices::getInstance();
		$languageNameUtils = $services->getLanguageNameUtils();

		$subpages = new Subpages( $languageNameUtils, $services->getPageProps() );
		$result = $subpages->getLangLinks( [], $title );

		$this->assertIsArray( $result );
		$this->assertEquals( $expected, $result );
	}

	/**
	 * @return array[]
	 */
	public static function provideData() {
		$localUrl = Title::newFromText( 'Test Page' )->getLocalURL();
		return [
			'on-base-page' => [
				'title' => Title::newFromText( 'Test Page' ),
				'expected' => [
					[
						'href' => $localUrl . '/de',
						'text' => 'Deutsch',
						'title' => 'Test Page/de',
						'class' => 'interlanguage-link interwiki-de',
						'link-class' => 'interlanguage-link-target',
						'lang' => 'de',
						'hreflang' => 'de'
					],
					[
						'href' => $localUrl . '/fr',
						'text' => 'français',
						'title' => 'Test Page/fr',
						'class' => 'interlanguage-link interwiki-fr',
						'link-class' => 'interlanguage-link-target',
						'lang' => 'fr',
						'hreflang' => 'fr'
					]
				]
			],
			'on-non-lang-subpage' => [
				'title' => Title::newFromText( 'Test Page/notsubpage' ),
				'expected' => []
			]
		];
	}
}
