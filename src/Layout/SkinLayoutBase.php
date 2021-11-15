<?php

namespace BlueSpice\Discovery\Layout;

use BlueSpice\Discovery\ISkinLayout;
use BlueSpice\Discovery\ISkinStructure;
use Exception;
use ExtensionRegistry;
use IContextSource;
use RequestContext;

abstract class SkinLayoutBase implements ISkinLayout {

	/**
	 *
	 * @var RequestContext
	 */
	public $context;

	/**
	 *
	 * @var BaseTemplate
	 */
	public $template;

	/**
	 *
	 * @var array
	 */
	public $skinStructureElements;

	/**
	 *
	 * @param BaseTemplate $template
	 * @param IContextSource $context
	 */
	public function __construct( $template, $context ) {
		$this->context = $context;
		$this->template = $template;
		$this->skinStructureElements = $this->findUsedStructureElements();
	}

	/**
	 *
	 * @param BaseTemplate $template
	 * @param IContextSource $context
	 * @return ISkinLayout
	 */
	public static function factory( $template, $context ) : ISkinLayout {
		return new static( $template, $context );
	}

	/**
	 *
	 * @return array
	 */
	private function findUsedStructureElements() : array {
		$skinStructureElements = [];
		$layoutName = $this->getName();
		$skinStructureRegistry = ExtensionRegistry::getInstance()->getAttribute(
			'BlueSpiceDiscoveryStructureRegistry'
		);
		if ( !array_key_exists( $layoutName, $skinStructureRegistry ) ) {
			return $skinStructureElements;
		}
		foreach ( $skinStructureRegistry[$layoutName] as $name => $spec ) {
			$skinStructureElement = $this->getStructureElement(
				$spec,
				$this
			);
			if ( ( $skinStructureElement instanceof ISkinStructure ) ) {
				$skinStructureElements[$name] = $skinStructureElement;
			} else {
				throw new Exception( "Can not extract data from " . $spec['callback'] );
			}
		}
		return $skinStructureElements;
	}

	/**
	 *
	 * @param array $spec
	 * @param Layout $layout
	 * @return ISkinStructure|null
	 */
	private function getStructureElement( $spec, $layout ) : ?ISkinStructure {
		if ( array_key_exists( 'callback', $spec ) ) {
			$skinStructureElement = call_user_func_array( $spec['callback'], [ $layout ] );
		}
		if ( !( $skinStructureElement instanceof ISkinStructure ) ) {
			throw new Exception( get_class( $skinStructureElement ) . " is not instance of ISkinLayout" );
		}
		return $skinStructureElement;
	}

	/**
	 * Parse templates recursive
	 *
	 * @return bool
	 */
	public function enableRecursivePartials() : bool {
		return false;
	}

	/**
	 *
	 * @return array
	 */
	public function getSkinStructureElements() : array {
		return $this->skinStructureElements;
	}
}
