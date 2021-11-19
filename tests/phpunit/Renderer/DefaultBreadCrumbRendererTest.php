<?php

namespace BlueSpice\Discovery\Tests\Renderer;

use BlueSpice\Discovery\Renderer\DefaultBreadCrumbRenderer;
use MediaWiki\MediaWikiServices;
use MediaWikiIntegrationTestCase;
use Message;
use MessageLocalizer;
use Title;
use User;

/**
 * @group Database
 */
class DefaultBreadCrumbRendererTest extends MediaWikiIntegrationTestCase {

	/**
	 *
	 * @return void
	 */
	public function addDBDataOnce() {
		$this->insertPage( 'Dummy' );
		$this->insertPage( 'Dummy/ABC' );
		$this->insertPage( 'Dummy/DEF' );
		$this->insertPage( 'Dummy/DEF/Some_Subpage' );
		$this->insertPage( 'Dummy/Dumm/eee/f/gh' );
	}

	/**
	 *
	 * @param Title $title
	 * @param array $webRequestValues
	 * @param array $expectedRootNode
	 * @param array $expectedLeafNode
	 * @param array $expectedLabels
	 * @return void
	 * @dataProvider provideGetParamsTestData
	 * @covers BlueSpice\Discovery\Renderer\DefaultBreadCrumbRenderer::getParams
	 */
	public function testGetParams( $title, $webRequestValues, $expectedRootNodeUrl,
		$expectedLeafNodeUrl, $expectedLabels ) {
		$this->setMwGlobals( [
			'wgArticlePath' => '/wiki/$1'
		] );

		$mockUser = $this->createMock( User::class );
		$mockMessageLocalizer = $this->createMock( MessageLocalizer::class );
		$mockMessageLocalizer->method( 'msg' )->willReturnCallback( static function ( $messageKey ) {
			return Message::newFromKey( $messageKey );
		} );

		$titleFactory = MediaWikiServices::getInstance()->getTitleFactory();
		$specialPageFactory = MediaWikiServices::getInstance()->getSpecialPageFactory();
		$namespaceInfo = MediaWikiServices::getInstance()->getNamespaceInfo();

		$renderer = new DefaultBreadCrumbRenderer( $title, $mockUser, $webRequestValues,
			$mockMessageLocalizer, $titleFactory, $specialPageFactory, $namespaceInfo );

		$params = $renderer->getParams();

		$actualRootNodeUrl = $params['rootNode']['href'];
		$leafNode = array_pop( $params['nodes'] );
		$actualLeafNodeUrl = $leafNode['button-href'];

		$actualLabels = [];
		foreach ( $params['labels'] as $label ) {
			$actualLabels[] = $label['text'];
		}

		$actualLabels = array_map( static function ( $actualLabelMsg ) {
			/** @var Message $actualLabelMsg */
			return $actualLabelMsg->getKey();
		}, $actualLabels );

		$this->assertEquals( $expectedRootNodeUrl, $actualRootNodeUrl );
		$this->assertEquals( $expectedLeafNodeUrl, $actualLeafNodeUrl );
		$this->assertEquals( $expectedLabels, $actualLabels );
	}

	public function provideGetParamsTestData() {
		return [
			'main-namespace-view-mode' => [
				Title::newFromText( 'Dummy/ABC' ),
				[],
				'/wiki/Main_Page',
				'/wiki/Dummy/ABC',
				[]
			],
			'talk-namespace-history-mode' => [
				Title::newFromText( 'Talk:Dummy/ABC' ),
				[ 'action' => 'history' ],
				'/wiki/Main_Page',
				'/wiki/Dummy/ABC',
				[ 'bs-discovery-breadcrumb-label-talk', 'bs-discovery-breadcrumb-label-action-history' ]
			],
			'specialpage-with-title-in-path' => [
				Title::newFromText( 'Special:Move/Dummy/ABC' ),
				[ 'action' => 'history' ],
				'/wiki/Main_Page',
				'/wiki/Dummy/ABC',
				[ 'move', 'bs-discovery-breadcrumb-label-action-history' ]
			],
			// We don't have SMW enabled on WMF CI
			// TODO: Implement proper integration
			// 'specialpage-with-smw-browse' => [
			// 	Title::newFromText( 'Special:Browse/:Dummy/ABC' ),
			// 	[],
			// 	'/wiki/Main_Page',
			// 	'/wiki/Dummy/ABC',
			// 	// Be aware that the `bs-discovery-breadcrumb-label-special-` is only used because
			// 	// we operate on `RawMessage` objects in this test. In the real world this would
			// 	// rather be `Browse`
			// 	[ 'bs-discovery-breadcrumb-label-special-browse' ]
			// ],
			'specialpage' => [
				Title::newFromText( 'Special:Allpages' ),
				[],
				'/wiki/Special:SpecialPages',
				'/wiki/Special:Allpages',
				[]
			]
		];
	}

}
