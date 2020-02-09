<?php namespace OurMetrics\SDK\Models;


class Metric
{
	/** @var string */
	public $name;

	/** @var string */
	public $timestamp;

	/** @var double|float|int */
	public $value;

	/** @var string */
	public $unit;

	/** @var null|DimensionList */
	public $dimensions;

	/** @var int */
	public $resolution = 60; // 1-60

	/**
	 * @param string                        $name
	 * @param int|float|double              $value
	 * @param string                        $unit
	 * @param null|\DateTime|\Carbon\Carbon $timestamp
	 * @param array|null|DimensionList      $dimensions
	 * @param int                           $resolution
	 */
	public function __construct( $name, $value, $unit = Unit::NONE, $dimensions = [], $timestamp = null, $resolution = 60 ) {
		if ( ! $dimensions instanceof DimensionList ) {
			$dimensions = new DimensionList( $dimensions ?? [] );
		}

		$this->name       = (string) $name;
		$this->value      = (double) $value;
		$this->unit       = $unit ?? Unit::NONE;
		$this->dimensions = $dimensions;
		$this->timestamp  = $this->formatTimestamp( $timestamp );
		$this->resolution = (int) $resolution;
	}

	public function toArray(): array {
		return [
			'name'       => $this->name,
			'unit'       => $this->unit,
			'value'      => $this->value,
			'timestamp'  => $this->timestamp,
			'resolution' => $this->resolution,
			'dimensions' => $this->dimensions->toArray(),
		];
	}

	protected function formatTimestamp( $timestamp = null ): string {
		if ( $timestamp === null || empty( $timestamp ) ) {
			$timestamp = new \DateTime();
		}

		if ( \is_string( $timestamp ) || ( \is_numeric( $timestamp ) && \mb_strlen( $timestamp ) === 10 ) || ( \is_string( $timestamp ) && preg_match( "/\d{10}\.\d{4}/", $timestamp ) ) ) {
			$timestamp = new \DateTime( $timestamp );
		}

		return $timestamp->format( 'Y-m-d\TH:i:sP' );
	}
}