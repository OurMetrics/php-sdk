<?php namespace OurMetrics\SDK;

use OurMetrics\SDK\Exceptions\CannotQueueMetricsException;
use OurMetrics\SDK\Exceptions\ProjectKeyMissingException;
use OurMetrics\SDK\Models\Metric;
use OurMetrics\SDK\Models\MetricList;

class Client
{
	protected $projectKey;

	protected $config = [];

	protected $silenced = false;

	/** @var MetricList */
	protected $queued;

	public function __construct( $projectKey, $config = [], $silence = false ) {
		if ( ! $silence && empty( $projectKey ) ) {
			throw new ProjectKeyMissingException( "The project key '{$projectKey}' is invalid." );
		}

		$this->projectKey = $projectKey;

		$this->silenced = $silence;

		$this->config = array_merge( [
			'endpoint'             => 'https://api.ourmetrics.com/metrics',
			'timeout'              => 2.0,
			'headers'              => [
				'user_agent' => 'OurMetrics SDK v0.1.0',
				'connection' => 'close',
			],
			'dispatch_on_destruct' => true,
		], $config );

		$this->queued = new MetricList();

		// todo validate config
	}

	/**
	 * @param Metric[]|Metric|MetricList $metrics
	 *
	 * @throws \OurMetrics\SDK\Exceptions\InvalidUnitException
	 * @throws \OurMetrics\SDK\Exceptions\CannotQueueMetricsException
	 */
	public function queue( $metrics ) {
		if ( is_array( $metrics ) ) {
			$this->queued->addList( new MetricList( $metrics ) );
		} elseif ( $metrics instanceof Metric ) {
			$this->queued->add( $metrics );
		} elseif ( $metrics instanceof MetricList ) {
			$this->queued->addList( $metrics );
		} else {
			throw new CannotQueueMetricsException( 'The passed metrics are not a valid format. Must be either an array of Metric, a single Metric or a MetricList.' );
		}
	}

	public function __destruct() {
		if ( $this->getConfig( 'dispatch_on_destruct', true ) ) {
			$this->dispatchQueued();
		}
	}

	public function registerShutdownFunction() {
		register_shutdown_function( function () {
			$this->dispatchQueued();
		} );
	}

	public function dispatchQueued() {
		foreach ( array_chunk( $this->queued->all(), 10, false ) as $metrics ) {
			$this->dispatch( new MetricList( $metrics ) );
		}

		$this->queued->clear();
	}

	public function dispatch( MetricList $metricList ) {
		if ( ! $this->canDispatch() ) {
			return;
		}

		$postData = http_build_query( [ 'metrics' => $metricList->toArray() ] );

		$endpointParts         = parse_url( $this->getConfig( 'endpoint' ) );
		$endpointParts['path'] = $endpointParts['path'] ?? '/';
		$endpointParts['port'] = $endpointParts['scheme'] === 'https' ? 443 : 80;

		$payload = [
			"POST {$endpointParts['path']} HTTP/1.1",
			'Host: ' . $endpointParts['host'],
			'User-Agent: ' . $this->getConfig( 'headers.user_agent', 'OurMetrics SDK' ),
			'Project-Key: ' . $this->projectKey,
			'Content-Length: ' . \mb_strlen( $postData ),
			'Connection: ' . $this->getConfig( 'headers.connection', 'close' ),
			'Content-Type: application/x-www-form-urlencoded',
		];

		$payload = implode( "\r\n", $payload ) . "\r\n\r\n";
		$payload .= $postData;

		$prefix = $endpointParts['scheme'] === 'https' ? 'tls://' : '';

		$socket = fsockopen( $prefix . $endpointParts['host'], $endpointParts['port'], $errno, $errst, 2.0 );
		fwrite( $socket, $payload );
		fclose( $socket );
	}

	protected function getConfig( $key, $default = null ) {
		$key   = explode( '.', \mb_strtolower( $key ) );
		$value = $this->config;

		foreach ( $key as $keyPart ) {
			$value = $value[ $keyPart ] ?? $default;
		}

		return $value;
	}

	protected function canDispatch() {
		return ! empty( $this->projectKey );
	}
}