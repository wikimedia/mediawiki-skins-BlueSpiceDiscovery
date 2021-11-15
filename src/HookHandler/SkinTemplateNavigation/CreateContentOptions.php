<?php

namespace BlueSpice\Discovery\HookHandler\SkinTemplateNavigation;

use MediaWiki\Hook\SkinTemplateNavigation__UniversalHook;
use MediaWiki\Permissions\PermissionManager;
use SkinTemplate;
use SpecialPage;
use Title;

class CreateContentOptions implements SkinTemplateNavigation__UniversalHook {

	/**
	 *
	 * @var PermissionManager
	 */
	private $permissionManager = null;

	/**
	 *
	 * @param PermissionManager $permissionManager
	 */
	public function __construct( PermissionManager $permissionManager ) {
		$this->permissionManager = $permissionManager;
	}

	/**
	 * @param SkinTemplate $sktemplate
	 * @param array &$links
	 * @return void
	 */
	public function onSkinTemplateNavigation__Universal( $sktemplate, &$links ) :void {
		$user = $sktemplate->getSkin()->getUser();
		$title = $sktemplate->getSkin()->getTitle();
		if ( $title instanceof Title === false ) {
			return;
		}

		if ( $this->permissionManager->userHasRight( $user, 'createpage' ) ) {
			$links['actions']['new-file'] = [
				'text' => $sktemplate->msg( 'bs-discovery-create-button-new-file-text' ),
				'title' => $sktemplate->msg( 'bs-discovery-create-button-new-file-title' ),
				'href' => SpecialPage::getTitleFor( 'Upload' )->getLocalURL(),
				'class' => 'new-file'
			];
		}

		if ( $this->permissionManager->userCan( 'edit', $user, $title ) ) {
			$links['actions']['new-section'] = [
				'text' => $sktemplate->msg( 'bs-discovery-create-button-new-section-text' ),
				'title' => $sktemplate->msg( 'bs-discovery-create-button-new-section-title' ),
				'href' => $sktemplate->getTitle()->getLocalURL( [
					'action' => 'edit',
					'section' => 'new'
				] ),
				'class' => 'new-section'
			];
		}
	}
}
