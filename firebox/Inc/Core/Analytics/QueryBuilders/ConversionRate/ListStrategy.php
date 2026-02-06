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

if (!defined('ABSPATH'))
{
	exit; // Exit if accessed directly.
}

class ListStrategy extends BaseConversionRateQueryStrategy
{
	public function getSelect(): string
	{
		$label = $this->getLabelExpression();

		return $label . ' AS label, CASE WHEN COUNT(DISTINCT l.id) = 0 THEN 0 ELSE (COUNT(DISTINCT bld.id) / COUNT(DISTINCT l.id)) * 100 END AS total';
	}

	public function getWhere(): string
	{
		return '';
	}

	public function getGroupBy(): string
	{
		return 'GROUP BY ' . $this->getLabelExpression();
	}

	public function getOrderBy(): string
	{
		$options = $this->metric->getOptions();

		if (isset($options['orderby']))
		{
			return 'ORDER BY ' . $options['orderby'];
		}

		return 'ORDER BY label DESC';
	}

	public function getHaving(): string
	{
		return 'HAVING total > 0';
	}

	private function getLabelExpression(): string
	{
		if ($this->metric->isSingleDay())
		{
			return 'CONCAT(' . $this->metric->getTimezoneDateSQL('DATE_FORMAT', 'l.date', '\'%H\'') . ", ':00')";
		}

		return $this->metric->getTimezoneDateSQL('DATE', 'l.date');
	}
}
