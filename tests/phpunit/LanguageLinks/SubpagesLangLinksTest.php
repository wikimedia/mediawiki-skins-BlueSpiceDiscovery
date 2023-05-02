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

		$this->insertPage( 'Test Page' );
		$this->insertPage( 'Test Page/de' );
		$this->insertPage( 'Test Page/en' );
		$this->insertPage( 'Test Page/fr' );

		$this->insertPage( 'Test Page/notsubpage' );
	}

	/**
	 * @covers BlueSpice\Discovery\LangLinksProvider\Subpages::getLangLinks
	 */
	public function testGetLangLinks() {
		$services = MediaWikiServices::getInstance();
		$languageNameUtils = $services->getLanguageNameUtils();
		$configFactory = $services->getConfigFactory();

		$subpages = new Subpages( $languageNameUtils, $configFactory );

		$title = Title::newFromText( 'Test Page' );
		$localURL = $title->getLocalURL();

		$expected = [
			[
				'href' => $localURL . '/de',
				'text' => 'Deutsch',
				'title' => 'Test Page' . ' – ' . 'Deutsch',
				'class' => 'interlanguage-link interwiki-' . 'de',
				'link-class' => 'interlanguage-link-target',
				'lang' => 'de',
				'hreflang' => 'de'
			],
			[
				'href' => $localURL . '/en',
				'text' => 'English',
				'title' => 'Test Page' . ' – ' . 'English',
				'class' => 'interlanguage-link interwiki-' . 'en',
				'link-class' => 'interlanguage-link-target',
				'lang' => 'en',
				'hreflang' => 'en'
			],
			[
				'href' => $localURL . '/fr',
				'text' => 'français',
				'title' => 'Test Page' . ' – ' . 'français',
				'class' => 'interlanguage-link interwiki-' . 'fr',
				'link-class' => 'interlanguage-link-target',
				'lang' => 'fr',
				'hreflang' => 'fr'
			]
		];

		$result = $subpages->getLangLinks( [], $title );

		$this->assertIsArray( $result );
		$this->assertCount( 3, $result );
		$this->assertEquals( $expected, $result );
	}
}
