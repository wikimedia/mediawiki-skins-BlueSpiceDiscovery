<?php

namespace BlueSpice\Discovery\LangLinksProvider;

use BlueSpice\Discovery\ILangLinksProvider;
use MediaWiki\Languages\languageNameUtils;
use Title;

class Subpages implements ILangLinksProvider {

	/** @var languageNameUtils */
	private $languageNameUtils = null;

	/**
	 * @param languageNameUtils $languageNameUtils
	 */
	public function __construct( languageNameUtils $languageNameUtils ) {
		$this->languageNameUtils = $languageNameUtils;
	}

	/**
	 * @param string[] $wikitextLangLinks
	 * @param Title $title
	 * @return string[]
	 */
	public function getLangLinks( array $wikitextLangLinks, Title $title ): array {
		$titleObject = $title;
		$links = [];
		if ( $titleObject->hasSubpages() ) {
			$subpages = $titleObject->getSubpages();
			foreach ( $subpages as $subpage ) {
				if ( $this->languageNameUtils->isKnownLanguageTag( $subpage->getSubpageText() ) ) {
					$langName = $this->languageNameUtils->getlanguageName( $subpage->getSubpageText() );
					array_push( $links,
						[
							'href' => $subpage->getLocalURL(),
							'text' => $langName,
							'title' => $titleObject->getPrefixedText() . ' – ' . $langName,
							'class' => 'interlanguage-link interwiki-' . $subpage->getSubpageText(),
							'link-class' => 'interlanguage-link-target',
							'lang' => $subpage->getSubpageText(),
							'hreflang' => $subpage->getSubpageText()
						]
					);
				}
			}
		}
		return $links;
	}
}
