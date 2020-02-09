<?php namespace OurMetrics\SDK\Models;

class Dimension
{
	public $key;
	public $value;

	public function __construct( $key, $value ) {
		$this->key   = (string) $key;
		$this->value = $value;
	}

	public function toArray(): array {
		return [ 'key' => (string) $this->key, 'value' => (string) ( is_bool( $this->value ) ? ( $this->value ? 1 : 0 ) : $this->value ) ];
	}
}