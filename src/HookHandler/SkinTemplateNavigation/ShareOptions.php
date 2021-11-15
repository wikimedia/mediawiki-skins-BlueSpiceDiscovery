<?php

namespace BlueSpice\Discovery\HookHandler\SkinTemplateNavigation;

use MediaWiki\Hook\SkinTemplateNavigation__UniversalHook;
use SkinTemplate;
use Title;

class ShareOptions implements SkinTemplateNavigation__UniversalHook {

	/**
	 * @param SkinTemplate $sktemplate
	 * @param array &$links
	 * @return void
	 */
	public function onSkinTemplateNavigation__Universal( $sktemplate, &$links ) : void {
		$title = $sktemplate->getSkin()->getTitle();
		if ( $title instanceof Title === false ) {
			return;
		}

		$subject = $sktemplate->msg( 'bs-discovery-sidebar-secondary-share-sharebymail-subject' );
		$subject->params(
			$sktemplate->getConfig()->get( 'Sitename' ),
			$title->getFullText()
		);
		$body = rawurlencode( $subject->text() );
		$body = $sktemplate->msg( 'bs-discovery-sidebar-secondary-share-sharebymail-body' );
		$body->params(
			$sktemplate->getConfig()->get( 'Sitename' ),
			$title->getFullText(),
			$sktemplate->getConfig()->get( 'Server' ) . $sktemplate->getRequest()->getRequestURL()
		);

		$body = rawurlencode( $body->text() );
		$links['actions']['sharebymail'] = [
			'text' => $sktemplate->msg( 'bs-discovery-sidebar-secondary-share-sharebymail-text' ),
			'title' => $sktemplate->msg( 'bs-discovery-sidebar-secondary-share-sharebymail-title' ),
			'href' => "mailto:?subject=$subject&body=$body",
			'class' => 'sharebymail'
		];
	}
}
