<?php

namespace BlueSpice\Discovery\HookHandler\MWStakeCommonUIRegisterSkinSlotComponents;

use BlueSpice\Discovery\Component\DefaultBreadcrumbNav;
use MediaWiki\MediaWikiServices;
use MWStake\MediaWiki\Component\CommonUserInterface\Hook\MWStakeCommonUIRegisterSkinSlotComponents;
use RequestContext;

class Breadcrumb implements MWStakeCommonUIRegisterSkinSlotComponents {

	/**
	 * @inheritDoc
	 */
	public function onMWStakeCommonUIRegisterSkinSlotComponents( $registry ) : void {
		$registry->register(
			'Breadcrumb',
			[
				'default-breadcrumb-nav' => [
					'factory' => function () {
						$context = RequestContext::getMain();
						$title = $context->getTitle();
						$user = $context->getUser();
						$webRequestValues = $context->getRequest()->getValues();
						$messageLocalizer = $context;
						$titleFactory = MediaWikiServices::getInstance()->getTitleFactory();
						$specialPageFactory = MediaWikiServices::getInstance()->getSpecialPageFactory();
						$namespaceInfo = MediaWikiServices::getInstance()->getNamespaceInfo();
						return new DefaultBreadcrumbNav( $title, $user, $webRequestValues,
							$messageLocalizer, $titleFactory, $specialPageFactory, $namespaceInfo );
					}
				]
			]
		);
	}
}
