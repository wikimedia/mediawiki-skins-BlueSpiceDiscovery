<?php

namespace BlueSpice\Discovery\Structure;

use IContextSource;

abstract class NavbarBase extends SkinStructureBase {

	/**
	 * @var array
	 */
	private $skinComponents = [];

	/**
	 *
	 * @param IContextSource $context
	 * @return bool
	 */
	public function shouldRender( IContextSource $context ) : bool {
		return true;
	}
}
