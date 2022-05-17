<?php

namespace BlueSpice\Discovery;

use BlueSpice\HideTitle\ICssSelectorModule;
use ResourceLoaderContext;

class HideTitleCssModule implements ICssSelectorModule {

	/**
	 * @return array
	 */
	public function getSelectors(): array {
		return [
			'all' => [
				'#title-section'
			]
		];
	}

	/**
	 * @param ResourceLoaderContext $context
	 * @return bool
	 */
	public function skip( $context ): bool {
		return false;
	}
}
