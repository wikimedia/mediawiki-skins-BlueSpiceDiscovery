<?php

namespace BlueSpice\Discovery\BreadcrumbRootProvider;

use BlueSpice\Discovery\IBreadcrumbRootProvider;
use MediaWiki\Message\Message;
use MediaWiki\SpecialPage\SpecialPageFactory;
use MediaWiki\Title\Title;

class BaseBreadcrumbRootProvider implements IBreadcrumbRootProvider {

	public function __construct( private readonly SpecialPageFactory $specialPageFactory ) {
	}

	/**
	 * @param Title $title
	 * @return array
	 */
	public function getNodes( Title $title ): array {
		$nsText = $title->getNsText();
		$nodes = [];

		if ( $title->getNamespace() === 0 ) {
			$nsText = Message::newFromKey(
				'bs-discovery-breadcrumb-nav-node-ns-main' )->plain();
		}

		if ( $title->isTalkPage() ) {
			if ( $title->getNamespace() === 1 ) {
				$nsText = Message::newFromKey(
					'bs-discovery-breadcrumb-nav-node-ns-main' )->plain();
			} else {
				$nsText = $title->getSubjectNsText();
			}
		}

		if ( $title->isSpecialPage() ) {
			$titleMainPage = $this->specialPageFactory->getTitleForAlias( 'Specialpages' );

			$nsText = $title->getPageLanguage()->getNsText(
				$titleMainPage->getNamespace() );
			$rootNodeUrl = $titleMainPage->getLocalURL();
		} else {
			$allPages = $this->specialPageFactory->getTitleForAlias( 'Allpages' );
			$rootNodeUrl = $allPages->getLocalURL(
				'namespace=' . $title->getNamespace() );
		}

		$nodes[] = [
			'text' => str_replace( '_', ' ', $nsText ),
			'href' => $rootNodeUrl,
			'title' => Message::newFromKey( 'bs-discovery-breadcrumb-nav-root-node-title', $nsText )->text()
		];
		return $nodes;
	}

	/**
	 * @param Title $title
	 * @return bool
	 */
	public function applies( Title $title ): bool {
		return true;
	}

}
