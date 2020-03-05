<?php namespace OurMetrics\SDK;

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
	 */
	public function queue( $metrics ) {
		$this->queued->addList( $this->getMetricListFromAssortedMetrics( $metrics ) );
	}

	/**
	 * Useful for tracking events ("button clicked" etc.)
	 *
	 * @param string           $event
	 * @param int|double|float $value
	 */
	public function track( string $event, $value = 1.0 ) {
		$this->queued->add( new Metric( $event, $value ) );
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
			$this->dispatch( $metrics );
		}

		$this->clearQueue();
	}

	/**
	 * Will un-queue all pending metrics.
	 */
	public function clearQueue() {
		$this->queued->clear();
	}

	/**
	 * @param Metric[]|Metric|MetricList $metrics
	 */
	public function dispatch( $metrics ) {
		if ( ! $this->canDispatch() ) {
			return;
		}

		$postData = http_build_query( [ 'metrics' => $this->getMetricListFromAssortedMetrics( $metrics )->toArray() ] );

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

	public function isSilenced(): bool {
		return $this->silenced;
	}

	protected function getConfig( $key, $default = null ) {
		$key   = explode( '.', \mb_strtolower( $key ) );
		$value = $this->config;

		foreach ( $key as $keyPart ) {
			$value = $value[ $keyPart ] ?? $default;
		}

		return $value;
	}

	protected function canDispatch(): bool {
		return ! empty( $this->projectKey );
	}

	/**
	 * @param Metric[]|Metric|MetricList $metrics
	 *
	 * @return MetricList
	 */
	protected function getMetricListFromAssortedMetrics( $metrics ): MetricList {
		if ( $metrics instanceof MetricList ) {
			return $metrics;
		}

		if ( is_array( $metrics ) ) {
			return new MetricList( $metrics );
		}

		if ( $metrics instanceof Metric ) {
			return new MetricList( [ $metrics ] );
		}

		return new MetricList( [] );
	}
}