<?php

namespace BlueSpice\Discovery\SkinSlotRenderer;

/**
 * Items rendered next to the primary sidebar toggle ("hamburger") in the primary navbar
 */
class NavbarPrimaryEastItemsSkinSlotRenderer extends ExtendedSkinSlotRendererBase {

	public const REG_KEY = 'NavbarPrimaryEastItems';

	/**
	 * @return string
	 */
	protected function getContainerWrapperTag(): string {
		return 'ul';
	}

	/**
	 * @return array
	 */
	protected function getContainerWrapperClasses(): array {
		return [ 'navbar-nav', 'align-items-center' ];
	}

	/**
	 * @return string
	 */
	protected function getItemWrapperTag(): string {
		return 'li';
	}

	/**
	 * @return array
	 */
	protected function getItemWrapperClasses(): array {
		return [ 'nav-item' ];
	}
}
