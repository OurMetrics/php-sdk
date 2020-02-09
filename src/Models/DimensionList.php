<?php namespace OurMetrics\SDK\Models;

class DimensionList
{
	/** @var Dimension[] */
	protected $dimensions = [];

	/**
	 * Supports a key-value array or array of Dimension
	 *
	 * @param array|Dimension[] $dimensions
	 */
	public function __construct( $dimensions = [] ) {
		foreach ( $dimensions as $name => $value ) {
			if ( $value instanceof Dimension ) {
				$this->add( $value );
				continue;
			}

			$this->add( new Dimension( $name, $value ) );
		}
	}

	public function add( Dimension $dimension ) {
		$this->dimensions[] = $dimension;
	}

	/**
	 * @return Dimension[]
	 */
	public function all(): array {
		return $this->dimensions;
	}

	/**
	 * @return array
	 */
	public function toArray(): array {
		return array_map( function ( Dimension $dimension ) {
			return $dimension->toArray();
		}, $this->all() );
	}
}