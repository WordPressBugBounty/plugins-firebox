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

namespace FireBox\Core\Analytics\QueryBuilders\Views;

if (!defined('ABSPATH'))
{
	exit; // Exit if accessed directly.
}

class TopCampaignStrategy extends BaseViewsQueryStrategy
{
	public function getSelect(): string
	{
		return 'l.box as id, (select p.post_title from ' . $this->metric->getWpdb()->prefix . 'posts as p WHERE p.ID = l.box) as label, COUNT(*) as total';
	}
	
	public function getGroupBy(): string
	{
		return 'GROUP BY l.box';
	}
	
	public function getOrderBy(): string
	{
		return $this->getTotalOrderBy();
	}
}
