<?php

/**
 * Class phpDateManip
 *
 * Date manipulation using php DateTime. International dates supported.
 * https://github.com/pontikis/phpDateManip
 *
 * @author     Christos Pontikis http://pontikis.net
 * @copyright  Christos Pontikis
 * @license    MIT http://opensource.org/licenses/MIT
 * @version    0.2.0 (27 Jun 2017)
 *
 */
class phpDateManip {

	private $dt;
	private $a_valid_date_ranges;
	private $datetime_range_start;
	private $datetime_range_end;
	private $datetime_string_range_start;
	private $datetime_string_range_end;
	private $last_error;


	public function __construct() {

		$this->a_valid_date_ranges = array(
			'current_year',
			'current_month',
			'current_week_from_monday',
			'current_week_from_sunday',
			'current_day'
		);
		$this->datetime_range_start = null;
		$this->datetime_range_end = null;
		$this->datetime_string_range_start = null;
		$this->datetime_string_range_end = null;
		$this->last_error = null;

	}

	// getters -----------------------------------------------------------------
	public function getError() {
		return $this->last_error;
	}

	public function getDatetimeRangeStart() {
		return $this->datetime_range_start;
	}

	public function getDatetimeRangeEnd() {
		return $this->datetime_range_end;
	}

	public function getDatetimeStringRangeStart() {
		return $this->datetime_string_range_start;
	}

	public function getDatetimeStringRangeEnd() {
		return $this->datetime_string_range_end;
	}

	// methods -----------------------------------------------------------------

	/**
	 * @param $str_date_range
	 * @param $str_timezone
	 * @param $str_dateformat_not_intl
	 * @param array $a_intl_settings
	 * @return bool
	 */
	public function createDateRange($str_date_range, $str_timezone, $str_dateformat_not_intl, array $a_intl_settings = array()) {

		if(!in_array($str_date_range, $this->a_valid_date_ranges)) {
			$this->last_error = 'Invalid date range';
			return false;
		}

		if(!$this->isValidTimezone($str_timezone)) {
			$this->last_error = 'Invalid timezone';
			return false;
		}

		$d = new DateTime('now', new DateTimeZone($str_timezone));
		switch($str_date_range) {
			case 'current_year':
				$d->modify('first day of january');
				break;
			case 'current_month':
				$d->modify('first day of this month');
				break;
			case 'current_week_from_monday':
				$d->modify('monday this week');
				break;
			case 'current_week_from_sunday':
				if($d->format('D') != 'Sun') {
					$d->modify('previous sunday');
				}
				break;
			case 'current_day':
				break;
		}
		$d->setTime(0, 0, 0);
		$this->datetime_range_start = $d;
		$this->datetime_string_range_start = $this->format_datetime($d, $str_dateformat_not_intl, $a_intl_settings);

		switch($str_date_range) {
			case 'current_year':
				$d->add(DateInterval::createFromDateString('+1 Year'));
				break;
			case 'current_month':
				$d->add(DateInterval::createFromDateString('+1 Month'));
				break;
			case 'current_week_from_monday':
				$d->add(DateInterval::createFromDateString('+1 Week'));
				break;
			case 'current_week_from_sunday':
				$d->add(DateInterval::createFromDateString('+1 Week'));
				break;
			case 'current_day':
				$d->add(DateInterval::createFromDateString('+1 Day'));
				break;
		}
		$this->datetime_range_end = $d;
		$this->datetime_string_range_end = $this->format_datetime($d, $str_dateformat_not_intl, $a_intl_settings);
		return true;
	}


	/**
	 * @param DateTime $d
	 * @param $str_dateformat_not_intl
	 * @param array $a_intl_settings
	 * @return string
	 */
	public function format_datetime(DateTime $d, $str_dateformat_not_intl, array $a_intl_settings = array()) {

		if($a_intl_settings) {
			$formatter = new IntlDateFormatter(
				$a_intl_settings['locale'],
				$a_intl_settings['datetype'],
				$a_intl_settings['timetype'],
				$a_intl_settings['timezone'],
				$a_intl_settings['calendar'],
				$a_intl_settings['pattern']
			);
			return $formatter->format($d);
		} else {
			return $d->format($str_dateformat_not_intl);
		}
	}

	// utility functions -------------------------------------------------------

	/**
	 * Check if a string is a valid timezone
	 *
	 * timezone_identifiers_list() requires PHP >= 5.2
	 *
	 * @param string $timezone
	 * @return bool
	 */
	public function isValidTimezone($timezone) {
		return in_array($timezone, timezone_identifiers_list());
	}
}