<?php namespace OurMetrics\SDK\Models;

/*
 * This class is a list of possible units. Use as so:
 *
 * 'unit' => OurMetrics\SDK\Models\Unit::SECONDS
 */

class Unit
{
	// Misc.
	public const NONE     = 'none';
	public const COUNT    = 'count';
	public const PERCENT  = 'percent';
	public const CLICKS   = 'clicks';
	public const VISITS   = 'visits';
	public const SESSIONS = 'sessions';

	// Time
	public const DAYS         = 'days';
	public const HOURS        = 'hours';
	public const MINUTES      = 'minutes';
	public const SECONDS      = 'seconds';
	public const MILISECONDS  = 'miliseconds';
	public const MICROSECONDS = 'microseconds';

	// Virtual size
	public const TERABYTES = 'terabytes';
	public const GIGABYTES = 'gigabytes';
	public const MEGABYTES = 'megabytes';
	public const KILOBYTES = 'kilobytes';
	public const BYTES     = 'bytes';

	// Misc. / Per second
	public const COUNT_SECOND    = 'count/second';
	public const CLICKS_SECOND   = 'clicks/second';
	public const VISITS_SECOND   = 'visits/second';
	public const SESSIONS_SECOND = 'sessions/second';

	// Virtual Size / Per second
	public const TERABYTES_SECOND = 'terabytes/second';
	public const GIGABYTES_SECOND = 'gigabytes/second';
	public const MEGABYTES_SECOND = 'megabytes/second';
	public const KILOBYTES_SECOND = 'kilobytes/second';
	public const BYTES_SECOND     = 'bytes/second';

	// Monetary values
	public const MONEY_USD = 'money:usd';
	public const MONEY_GBP = 'money:gbp';
	public const MONEY_DKK = 'money:dkk';
	public const MONEY_SEK = 'money:sek';
	public const MONEY_NOK = 'money:nok';
	public const MONEY_EUR = 'money:eur';
	public const MONEY_CNY = 'money:cny';
	public const MONEY_JPY = 'money:jpy';
	public const MONEY_AUD = 'money:aud';
	public const MONEY_CAD = 'money:cad';
	public const MONEY_CHF = 'money:chf';
	public const MONEY_NZD = 'money:nzd';
}