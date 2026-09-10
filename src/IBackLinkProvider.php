<?php

namespace BlueSpice\Discovery;

use MediaWiki\Context\IContextSource;
use MediaWiki\Message\Message;
use MWStake\MediaWiki\Component\CommonUserInterface\IComponent;

interface IBackLinkProvider {

	/**
	 * @param IContextSource $context
	 * @return bool
	 */
	public function applies( IContextSource $context ): bool;

	/**
	 * @return string
	 */
	public function getHref(): string;

	/**
	 * @return Message
	 */
	public function getLabel(): Message;

	/**
	 * @return Message
	 */
	public function getTitle(): Message;

	/**
	 * @return Message
	 */
	public function getAriaLabel(): Message;

	/**
	 * @return IComponent[]
	 */
	public function getPreComponents(): array;

}
