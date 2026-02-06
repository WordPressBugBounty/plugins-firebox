<?php
/**
 * @package         FireBox
 * @version         3.1.4 Free
 * 
 * @author          FirePlugins <info@fireplugins.com>
 * @link            https://www.fireplugins.com
 * @copyright       Copyright © 2025 FirePlugins All Rights Reserved
 * @license         GNU GPLv3 <http://www.gnu.org/licenses/gpl.html> or later
*/

namespace FireBox\Core\Analytics\QueryBuilders;

use FireBox\Core\Analytics\QueryBuilders\Interfaces\QueryStrategyInterface;
use FireBox\Core\Analytics\QueryBuilders\Clicks\TopCampaignStrategy;
use FireBox\Core\Analytics\QueryBuilders\Clicks\CountriesStrategy;
use FireBox\Core\Analytics\QueryBuilders\Clicks\ReferrersStrategy;
use FireBox\Core\Analytics\QueryBuilders\Clicks\DevicesStrategy;
use FireBox\Core\Analytics\QueryBuilders\Clicks\PagesStrategy;
use FireBox\Core\Analytics\QueryBuilders\Clicks\WeeklyStrategy;
use FireBox\Core\Analytics\QueryBuilders\Clicks\MonthlyStrategy;
use FireBox\Core\Analytics\QueryBuilders\Clicks\DayOfWeekStrategy;
use FireBox\Core\Analytics\QueryBuilders\Clicks\ListStrategy;
use FireBox\Core\Analytics\QueryBuilders\Clicks\CountStrategy;

if (!defined('ABSPATH'))
{
	exit; // Exit if accessed directly.
}

class ClicksQueryStrategyFactory
{
	private static $strategies = [
		'top_campaign' => TopCampaignStrategy::class,
		'countries' => CountriesStrategy::class,
		'referrers' => ReferrersStrategy::class,
		'devices' => DevicesStrategy::class,
		'pages' => PagesStrategy::class,
		'weekly' => WeeklyStrategy::class,
		'monthly' => MonthlyStrategy::class,
		'day_of_week' => DayOfWeekStrategy::class,
		'list' => ListStrategy::class,
		'count' => CountStrategy::class,
	];

	public static function create($type, $metric): QueryStrategyInterface
	{
		$strategyClass = self::$strategies[$type] ?? self::$strategies['list'];
		return new $strategyClass($metric);
	}

	public static function registerStrategy($type, $strategyClass)
	{
		self::$strategies[$type] = $strategyClass;
	}
}
