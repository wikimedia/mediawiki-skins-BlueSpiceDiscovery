<?php

namespace BlueSpice\Discovery\EnhancedSidebar\NodeProcessor;

use MWStake\MediaWiki\Lib\Nodes\INodeProcessor;
use MWStake\MediaWiki\Lib\Nodes\INodeSource;
use ParserFactory;
use ParserOptions;
use Title;
use User;

abstract class EnhancedSidebarNodeProcessor implements INodeProcessor {
	/** @var User|null */
	protected $user = null;
	/** @var ParserFactory */
	private $parserFactory;

	/**
	 * @param ParserFactory $parserFactory
	 */
	public function __construct( ParserFactory $parserFactory ) {
		$this->parserFactory = $parserFactory;
	}

	/**
	 * @param User $user
	 *
	 * @return void
	 */
	public function setUser( User $user ) {
		$this->user = $user;
	}

	/**
	 * @param INodeSource $nodeSource
	 *
	 * @return array
	 */
	protected function getProcessedData( INodeSource $nodeSource ): array {
		$data = $nodeSource->getData();
		$preprocessable = $this->getKeysToPreprocess( $data );
		unset( $data['type'] );
		// Parse each of the data values
		foreach ( $preprocessable as $key ) {
			if ( !isset( $data[$key] ) ) {
				continue;
			}
			$data[$key] = $this->parseWikitextValue( $data[$key] );
		}

		return $data;
	}

	/**
	 * Get array if keys that should be preprocessed
	 *
	 * @param array $data Key value pairs
	 *
	 * @return array
	 */
	abstract protected function getKeysToPreprocess( array $data ): array;

	/**
	 * Use wiki parser to parse value of properties
	 * @param string $text
	 *
	 * @return mixed
	 */
	protected function parseWikitextValue( $text, ?Title $title = null ) {
		$requestContext = new \RequestContext();
		if ( $this->user ) {
			$requestContext->setUser( $this->user );
		}
		if ( $title ) {
			$requestContext->setTitle( $title );
		}

		$parser = $this->parserFactory->create();
		$parserOptions = ParserOptions::newFromUser( $this->user );
		$parser->setOptions( $parserOptions );

		$res = $parser->preprocess( $text, $title, $parserOptions );
		return $res;
	}

	/**
	 * @param array $data
	 *
	 * @return bool
	 */
	protected function isHidden( array $data ) {
		if ( !isset( $data['hidden'] ) ) {
			return false;
		}
		if ( is_bool( $data['hidden'] ) ) {
			return $data['hidden'];
		}
		return $data['hidden'] === 'true';
	}
}