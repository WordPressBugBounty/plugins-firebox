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
use FireBox\Core\Analytics\QueryBuilders\Views\TopCampaignStrategy;
use FireBox\Core\Analytics\QueryBuilders\Views\PopularViewTimesStrategy;
use FireBox\Core\Analytics\QueryBuilders\Views\CountriesStrategy;
use FireBox\Core\Analytics\QueryBuilders\Views\ReferrersStrategy;
use FireBox\Core\Analytics\QueryBuilders\Views\DevicesStrategy;
use FireBox\Core\Analytics\QueryBuilders\Views\EventsStrategy;
use FireBox\Core\Analytics\QueryBuilders\Views\PagesStrategy;
use FireBox\Core\Analytics\QueryBuilders\Views\WeeklyStrategy;
use FireBox\Core\Analytics\QueryBuilders\Views\MonthlyStrategy;
use FireBox\Core\Analytics\QueryBuilders\Views\DayOfWeekStrategy;
use FireBox\Core\Analytics\QueryBuilders\Views\ListStrategy;
use FireBox\Core\Analytics\QueryBuilders\Views\CountStrategy;

if (!defined('ABSPATH'))
{
	exit; // Exit if accessed directly.
}

class ViewsQueryStrategyFactory
{
	private static $strategies = [
		'top_campaign' => TopCampaignStrategy::class,
		'popular_view_times' => PopularViewTimesStrategy::class,
		'countries' => CountriesStrategy::class,
		'referrers' => ReferrersStrategy::class,
		'devices' => DevicesStrategy::class,
		'events' => EventsStrategy::class,
		'pages' => PagesStrategy::class,
		'weekly' => WeeklyStrategy::class,
		'monthly' => MonthlyStrategy::class,
		'day_of_week' => DayOfWeekStrategy::class,
		'list' => ListStrategy::class,
		'count' => CountStrategy::class,
	];
	
	public static function create(string $type, $metric): QueryStrategyInterface
	{
		$strategyClass = self::$strategies[$type] ?? ListStrategy::class;
		return new $strategyClass($metric);
	}
	
	public static function registerStrategy(string $type, string $strategyClass): void
	{
		self::$strategies[$type] = $strategyClass;
	}
}
