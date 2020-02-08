<?php namespace OurMetrics\SDK\Models;

class MetricList
{
	/** @var Metric[] */
	protected $metrics = [];

	/**
	 * Supports a key-value array or array of Metric
	 *
	 * @param array|Metric[]
	 *
	 * @throws \OurMetrics\SDK\Exceptions\InvalidUnitException
	 */
	public function __construct( $metrics = [] ) {
		foreach ( $metrics as $name => $value ) {
			if ( $value instanceof Metric ) {
				$this->add( $value );
				continue;
			}

			$this->add( new Metric( $name, $value ) );
		}
	}

	public function clear() {
		$this->metrics = [];
	}

	public function add( Metric $metric ) {
		$this->metrics[] = $metric;
	}

	public function addList( MetricList $metricList ) {
		foreach ( $metricList->all() as $metric ) {
			$this->add( $metric );
		}
	}

	/**
	 * @return Metric[]
	 */
	public function all(): array {
		return $this->metrics;
	}

	/**
	 * @return array
	 */
	public function toArray(): array {
		return array_map( function ( Metric $metric ) {
			return $metric->toArray();
		}, $this->all() );
	}
}