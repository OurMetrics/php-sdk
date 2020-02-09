<?php namespace OurMetrics\SDK\Models;

class MetricList
{
	/** @var Metric[] */
	protected $metrics = [];

	/**
	 * Supports an array of Metric
	 *
	 * @param Metric[]
	 */
	public function __construct( $metrics = [] ) {
		foreach ( $metrics as $name => $value ) {
			if ( ! $value instanceof Metric ) {
				$value = new Metric($name, $value);
			}

			$this->add( $value );
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