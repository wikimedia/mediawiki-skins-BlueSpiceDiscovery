<?php

namespace BlueSpice\Discovery;

use Linker;
use Message;
use RequestContext;

class LinkFormatter {

	/**
	 * @return StdClass
	 */
	public static function factory() {
		return new static();
	}

	/**
	 * @param array $links
	 * @return array
	 */
	public function formatLinks( $links ): array {
		$params = [];

		foreach ( $links as $key => $link ) {
			if ( is_string( $key ) ) {
				$strpos = strpos( $key, '-' );
				$subKey = substr( $key, $strpos + 1 );
			}

			if ( isset( $link['text'] ) && $link['text'] !== '' ) {
				$msg = Message::newFromKey( $link['text'] );
				if ( $msg->exists() ) {
					$link['text'] = $msg->text();
				}
			} elseif ( isset( $link['msg'] ) && $link['msg'] === '' ) {
				$msg = Message::newFromKey( $link['msg'] );
				if ( $msg->exists() ) {
					$link['text'] = $msg->text();
				}
			} elseif ( is_string( $key ) && Message::newFromKey( $key )->exists() ) {
				$msg = Message::newFromKey( $key );
				$link['text'] = $msg->text();
			} elseif ( is_string( $key ) && Message::newFromKey( $subKey )->exists() ) {
				$msg = Message::newFromKey( $subKey );
				$link['text'] = $msg->text();
			} else {
				continue;
			}

			if ( isset( $link['title'] ) && $link['title'] !== '' ) {
				$msg = Message::newFromKey( $link['title'] );
				if ( $msg->exists() ) {
					$link['title'] = $msg->text();
				}
			} elseif ( is_string( $key ) && Message::newFromKey( $key )->exists() ) {
				$msg = Message::newFromKey( $key );
				if ( $msg->exists() ) {
					$link['title'] = $msg->text();
				}
			} elseif ( isset( $link['id'] ) && $link['id'] !== '' ) {
				$tooltip = Linker::titleAttrib( $link['id'] );
				if ( $tooltip ) {
					$link['title'] = $tooltip;
				}
			}

			if ( isset( $link['data-mw'] ) && isset( $link['data'] ) ) {
				$link['data']['mw'] = $link['data-mw'];
			} elseif ( isset( $link['data-mw'] ) ) {
				$link['data'] = [
					'mw' => $link['data-mw']
				];
			}

			// Is target external?
			if ( isset( $link['href'] ) && ( $link['href'] !== '' ) && ( strpos( $link['href'], '#' ) !== 0 ) ) {
				$parsedUrl = wfParseUrl( $link['href'] );
				// MediaWiki global $wgExternalLinkTarget
				$context = RequestContext::getMain();
				$externalLinkTarget = $context->getConfig()->get( 'ExternalLinkTarget' );
				if ( $parsedUrl && $externalLinkTarget ) {
					if ( !isset( $link['target'] ) ) {
						$link['target'] = $externalLinkTarget;
					}
					if ( isset( $link['target'] ) && !isset( $link['rel'] ) ) {
						// See https://www.mediawiki.org/wiki/Manual:$wgExternalLinkTarget
						$rel = [ 'external', 'noreferrer', 'noopener' ];
						$link['rel'] = implode( ' ', $rel );
					}
				}
			}

			$params[] = $link;
		}

		return $params;
	}
}
