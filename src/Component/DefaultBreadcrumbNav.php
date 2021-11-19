<?php

namespace BlueSpice\Discovery\Component;

use BlueSpice\Discovery\Renderer\DefaultBreadCrumbRenderer;
use MessageLocalizer;
use MWStake\MediaWiki\Component\CommonUserInterface\Component\Literal;
use Title;

class DefaultBreadcrumbNav extends Literal {

	/**
	 *
	 * @var Title
	 */
	private $title = null;

	/**
	 *
	 * @var User
	 */
	private $user = null;

	/**
	 *
	 * @var array
	 */
	private $webRequestValues = null;

	/**
	 *
	 * @var MessageLocalizer
	 */
	private $messageLocalizer = null;

	/**
	 *
	 * @var TitleFactory
	 */
	private $titleFactory = null;

	/**
	 *
	 * @var SpecialPageFactory
	 */
	private $specialPageFactory = null;

	/**
	 *
	 * @var NamespaceInfo
	 */
	private $namespaceInfo = null;

	/**
	 *
	 * @param Title $title
	 * @param User $user
	 * @param array $webRequestValues
	 * @param MessageLocalizer $messageLocalizer
	 * @param TitleFactory $titleFactory
	 * @param SpecialPageFactory $specialPageFactory
	 * @param NamespaceInfo $namespaceInfo
	 */
	public function __construct( $title, $user, $webRequestValues, $messageLocalizer,
		$titleFactory, $specialPageFactory, $namespaceInfo ) {
		$this->title = $title;
		$this->user = $user;
		$this->webRequestValues = $webRequestValues;
		$this->messageLocalizer = $messageLocalizer;
		$this->titleFactory = $titleFactory;
		$this->specialPageFactory = $specialPageFactory;
		$this->namespaceInfo = $namespaceInfo;
	}

	/**
	 * Raw HTML string
	 *
	 * @return string
	 */
	public function getHtml(): string {
		return $this->buildHtml();
	}

	private function buildHtml(): string {
		$renderer = new DefaultBreadCrumbRenderer( $this->title, $this->user, $this->webRequestValues,
			$this->messageLocalizer, $this->titleFactory, $this->specialPageFactory, $this->namespaceInfo );

		return $renderer->getHtml();
	}

}
