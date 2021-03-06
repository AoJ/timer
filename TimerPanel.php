<?php

/**
 * Debug panel for timing
 * use \Panel\TimerPanel\start('sql') ... \Panel\TimerPanel\stop('sql') for measuring time
 * use \Panel\TimerPanel\timeLeft('starting') for capture running time
 * @author AoJ
 */

namespace Panel;

/**
 * slouží k profilování aplikace
 * buď použití TimerPanel::trackTime() k zachycení času v místě volání
 * nebo k měření doby přes metody ::start('x') ::stop('x')
 * pro měření souhrnné času volání stačí místo stop volat ::endGroup('x')
 */

$mySuperChooperTimeStart = microtime(true);
class TimerPanel
{

  /** @var array */
	private static $times = array();
	private static $duration = array();


	public static function getTraceTimes() {
		return self::$times;
	}

	public static function getDuration() {
		return self::$duration;
	}

	/**
	 * @static
	 * @param string $name
	 */
	public static function start($name)
	{
		self::timer($name);
	}

	/**
	 * @static
	 * @param string $name
	 * @return float
	 */
	public static function stop($name)
	{
		$last = self::$duration[] = array('name' => $name, 'time' => self::timer($name));
		return $last['time'];
	}
	/**
	 * @static
	 * @param string $name
	 * @return float
	 */
	public static function endGroup($name)
	{
		if(!isset(self::$duration[$name])) self::$duration[$name] = array('name' => $name, 'time' => 0, 'times' => 0);

		self::$duration[$name]['time'] += self::timer($name);
		self::$duration[$name]['name'] = $name . ' ('.(++self::$duration[$name]['times']).'×)';
		return self::$duration[$name]['time'];
	}

	/**
	 * @static
	 * @param string $name
	 */
	public static function timeLeft($name)
	{
		self::$times[] = array('name' => $name, 'time' => (microtime(TRUE) - $mySuperChooperTimeStart);
	}


	/**
	 * @static
	 * @param string $name
	 */
	public static function traceTime($description = null)
	{
		if(!function_exists('debug_backtrace')) return;

		$bt = array_slice(debug_backtrace(false), 0, 5);
        foreach($bt as $trace) {
            if ($trace['function'] !== __FUNCTION__) {
                $caller = $trace;
                break;
            }
        }
		if(!$caller) {
			return;
		}

		$fields = array('class', 'function', 'line', 'file');
		foreach($fields as &$field) {
			if(!isset($caller[$field])) $caller[$field] = '';
		}
		$name = $caller['class'] ? "{$caller['class']}->{$caller['function']}" : '';
		if($description) $name .= ($name ? ' - ' : '') . $description;
		self::$times[] = array('name' => $name, 'time' => (microtime(TRUE) - $mySuperChooperTimeStart);
	}

	/**
	 * @return string
	 */
	public function getTab()
	{
		$count = count(self::$times) + count(self::$duration);
		return '<span><strong>Timers</strong> (' . $count . ')</span>';
	}


	/**
	 * @return string
	 */
	public function getPanel()
	{
		if(!self::$times && !self::$duration) return '';
		$render = function(&$times) {
			$return = '';
			foreach ($times as $value) {
				$name = $value['name'];
				$return .= "<tr><th>$name</th><td style='text-align: right;'>" . number_format(round($value['time'] * 1000, 1), 1) . " ms</td></tr>";
			}
			return $return;
		};
		$return = '<h1>Run Forest Run</h1>';
		$return .= '<div class="nette-inner" style="width: 500px;"><table width="100%">';
		if(self::$times) {
			$return .= '<thead><tr><td colspan=2>Time Left trace</td></tr></thead>';
			$return .= '<tbody>'.$render(self::$times).'</tbody>';
		}
		if(self::$duration) {
			$return .= '<thead><tr><td colspan=2>Durations</td></tr></thead>';
			$return .= '<tbody>'.$render(self::$duration).'</tbody>';
		}
		$return .= '</table></div>';
		return $return;
	}

		/**
	 * Starts/stops stopwatch.
	 * @param  string  name
	 * @return float   elapsed seconds
	 */
	protected static function timer($name = NULL)
	{
		static $time = array();
		$now = microtime(TRUE);
		$delta = isset($time[$name]) ? $now - $time[$name] : 0;
		$time[$name] = $now;
		return $delta;
	}
}
