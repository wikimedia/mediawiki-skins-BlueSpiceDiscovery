<?php

namespace BlueSpice\Discovery;

use MediaWiki\Title\Title;

interface IBreadcrumbRootProvider {

	/**
	 * @param Title $title
	 * @return array
	 */
	public function getNodes( Title $title ): array;

	/**
	 *
	 * @param Title $title
	 * @return bool
	 */
	public function applies( Title $title ): bool;

}
