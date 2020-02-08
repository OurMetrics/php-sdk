<?php namespace OurMetrics\SDK\Models;

use OurMetrics\SDK\Exceptions\InvalidDimensionKeyException;

class Dimension
{
	public $key;
	public $value;

	public function __construct( $key, $value ) {
		if ( empty( $key ) || \mb_strlen( $key ) > 255 ) {
			throw new InvalidDimensionKeyException( "The key '{$key}' is not a valid dimension key. Must be a string between 1-255 characters." );
		}

		$this->key   = (string) $key;
		$this->value = $value;
	}

	public function toArray(): array {
		return [ 'key' => (string) $this->key, 'value' => (string) ( is_bool( $this->value ) ? ( $this->value ? 1 : 0 ) : $this->value ) ];
	}
}