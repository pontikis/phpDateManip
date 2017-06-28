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
 * @version    0.3.0 (XX XXX 2017)
 *
 */
class phpDateManip {

	private $dt;
	private $a_valid_date_ranges;
	private $datetime_range_start;
	private $datetime_range_end;
	private $datetime_string_range_start;
	private $datetime_string_range_end;
	private $a_valid_date_modifications;
	private $a_valid_date_modification_units;
	private $datetime_modified;
	private $datetime_string_modified;
	private $strings;
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

		$this->a_valid_date_modifications = array(
			'increase',
			'decrease',
		);

		$this->a_valid_date_modification_units = array(
			'Year',
			'Month',
			'Week',
			'Day',
			'Hour',
			'Min',
			'Sec'
		);

		$this->strings = array(
			'invalid_date_range' => 'Invalid date range',
			'invalid_timezone' => 'Invalid timezone',
			'invalid_date_modification' => 'Invalid date modification',
			'invalid_date_modification_quantity' => 'Invalid quantity for date modification',
			'invalid_date_modification_unit' => 'Invalid unit for date modification',
			'invalid_date_to_modify' => 'Invalid date to modify'
		);

		$this->datetime_modified = null;
		$this->datetime_string_modified = null;

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

	public function getDatetimeModified() {
		return $this->datetime_modified;
	}

	public function getDatetimeStringModified() {
		return $this->datetime_string_modified;
	}

	// setters -----------------------------------------------------------------

	/**
	 * @param array $strings
	 */
	public function setStrings($strings) {
		$this->strings = $strings;
	}

	// methods -----------------------------------------------------------------

	/**
	 * Creates date ranges
	 *
	 * Example: $res = createDateRange('current_year', 'Europe/Athens', 'j/n/Y H:i');
	 *
	 * @param $str_date_range
	 * @param $str_timezone
	 * @param $str_dateformat_not_intl
	 * @param array $a_intl_settings
	 * @return bool
	 */
	public function createDateRange($str_date_range, $str_timezone, $str_dateformat_not_intl, array $a_intl_settings = array()) {

		if(!in_array($str_date_range, $this->a_valid_date_ranges)) {
			$this->last_error = $this->strings['invalid_date_range'];
			return false;
		}

		if(!$this->isValidTimezone($str_timezone)) {
			$this->last_error = $this->strings['invalid_timezone'];
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
		$this->datetime_string_range_start = $this->formatDateTime($d, $str_dateformat_not_intl, $a_intl_settings);

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
		$this->datetime_string_range_end = $this->formatDateTime($d, $str_dateformat_not_intl, $a_intl_settings);
		return true;
	}


	/**
	 * Modifies (increase or decrease) a datetime string
	 *
	 * Example: $res = $phpDateManip->modifyDateString('increase', 3, 'Month', '1/1/2017 00:00', 'Europe/Athens', 'j/n/Y H:i');
	 *
	 * @param string $str_action Valid options: "increase decrease"
	 * @param int $int_quantity
	 * @param string $str_unit Valid options: "Year Month Week Day Hour Min Sec"
	 * @param string $str_date
	 * @param string $str_timezone
	 * @param string $str_dateformat_not_intl
	 * @param array $a_intl_settings
	 * @return bool
	 */
	public function modifyDateString($str_action,
									 $int_quantity,
									 $str_unit,
									 $str_date,
									 $str_timezone,
									 $str_dateformat_not_intl,
									 array $a_intl_settings = array()) {

		if(!in_array($str_action, $this->a_valid_date_modifications)) {
			$this->last_error = $this->strings['invalid_date_modification'];
			return false;
		}

		if(!$this->_is_positive_integer($int_quantity)) {
			$this->last_error = $this->strings['invalid_date_modification_quantity'];
			return false;
		}

		if(!in_array($str_unit, $this->a_valid_date_modification_units)) {
			$this->last_error = $this->strings['invalid_date_modification_unit'];
			return false;
		}

		if(!$this->isValidTimezone($str_timezone)) {
			$this->last_error = $this->strings['invalid_timezone'];
			return false;
		}

		$resDate = $this->isValidDateTimeString($str_date, $str_dateformat_not_intl, $str_timezone, $a_intl_settings);
		if(!$resDate) {
			$this->last_error = $this->strings['invalid_date_to_modify'];
			return false;
		} else {
			if($a_intl_settings) {
				$d = new DateTime('now', new DateTimeZone($str_timezone));
				$d->setTimestamp($resDate); // integer timestamp
			} else {
				$d = DateTime::createFromFormat($str_dateformat_not_intl, $str_date, new DateTimeZone($str_timezone));
			}

			$str_interval = ($str_action == 'increase' ? '+' : '-');
			$str_interval .= $int_quantity;
			$str_interval .= ' ';
			$str_interval .= $str_unit;
			$d->add(DateInterval::createFromDateString($str_interval));

			$this->datetime_modified = $d;
			$this->datetime_string_modified = $this->formatDateTime($d, $str_dateformat_not_intl, $a_intl_settings);
			return true;

		}

	}

	/**
	 * Format datetime object
	 *
	 * Example: $res = $phpDateManip->formatDateTime($d, 'j/n/Y H:i');
	 *
	 * @param DateTime $d
	 * @param $str_dateformat_not_intl
	 * @param array $a_intl_settings
	 * @return string
	 */
	public function formatDateTime(DateTime $d, $str_dateformat_not_intl, array $a_intl_settings = array()) {

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


	/**
	 * Check if a string (of any locale) is a valid date(time)
	 *
	 * DateTime::createFromFormat requires PHP >= 5.3
	 *
	 * @param string $str_dt
	 * @param string $str_dateformat
	 * @param string $str_timezone (If timezone is invalid, php will throw an exception)
	 * @param array $intl international options
	 * @return bool|int
	 */
	public function isValidDateTimeString($str_dt, $str_dateformat, $str_timezone = null, $intl = array()) {
		if($intl) {
			$formatter = new IntlDateFormatter($intl['locale'], $intl['datetype'], $intl['timetype'], $intl['timezone'], $intl['calendar'], $intl['pattern']);
			return $formatter->parse($str_dt);
		} else {
			if($str_timezone) {
				$date = DateTime::createFromFormat($str_dateformat, $str_dt, new DateTimeZone($str_timezone));
			} else {
				$date = DateTime::createFromFormat($str_dateformat, $str_dt);
			}
			$a_err = DateTime::getLastErrors(); // compatibility with php 5.3
			return $date && $a_err['warning_count'] == 0 && $a_err['error_count'] == 0;
		}
	}

	// private functions -------------------------------------------------------

	/**
	 * Check if expression is positive integer
	 *
	 * @param $str
	 * @return bool
	 */
	function _is_positive_integer($str) {
		return (is_numeric($str) && $str > 0 && $str == round($str));
	}

}