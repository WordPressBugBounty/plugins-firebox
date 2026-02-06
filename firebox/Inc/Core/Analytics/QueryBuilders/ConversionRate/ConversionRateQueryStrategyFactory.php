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

namespace FireBox\Core\Analytics\QueryBuilders\ConversionRate;

use FireBox\Core\Analytics\QueryBuilders\Interfaces\QueryStrategyInterface;

if (!defined('ABSPATH'))
{
	exit; // Exit if accessed directly.
}

class ConversionRateQueryStrategyFactory
{
	public static function create($queryType, $metric): QueryStrategyInterface
	{
		switch ($queryType)
		{
			case 'top_campaigns':
			case 'top_campaign':
				return new TopCampaignStrategy($metric);
			case 'countries':
				return new CountriesStrategy($metric);
			case 'referrers':
				return new ReferrersStrategy($metric);
			case 'devices':
				return new DevicesStrategy($metric);
			case 'pages':
				return new PagesStrategy($metric);
			case 'weekly':
				return new WeeklyStrategy($metric);
			case 'monthly':
				return new MonthlyStrategy($metric);
			case 'dayofweek':
			case 'day_of_week':
				return new DayOfWeekStrategy($metric);
			case 'list':
				return new ListStrategy($metric);
			case 'count':
			default:
				return new CountStrategy($metric);
		}
	}
}
