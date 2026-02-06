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

if (!defined('ABSPATH'))
{
	exit; // Exit if accessed directly.
}

class MetricQueryBuilder
{
	private $select = '';
	private $from = '';
	private $joins = '';
	private $where = [];
	private $groupBy = '';
	private $having = '';
	private $orderBy = '';
	private $limit = '';
	private $offset = '';
	private $placeholders = [];
	
	public function select(string $select): self
	{
		$this->select = $select;
		return $this;
	}
	
	public function from(string $from): self
	{
		$this->from = $from;
		return $this;
	}
	
	public function join(string $join): self
	{
		$this->joins .= ' ' . $join;
		return $this;
	}
	
	public function where(string $condition): self
	{
		$this->where[] = $condition;
		return $this;
	}
	
	public function groupBy(string $groupBy): self
	{
		$this->groupBy = $groupBy;
		return $this;
	}
	
	public function orderBy(string $orderBy): self
	{
		$this->orderBy = $orderBy;
		return $this;
	}
	
	public function limit(int $limit): self
	{
		$this->limit = "LIMIT {$limit}";
		return $this;
	}
	
	public function offset(int $offset): self
	{
		$this->offset = "OFFSET {$offset}";
		return $this;
	}
	
	public function addPlaceholder($value): self
	{
		$this->placeholders[] = $value;
		return $this;
	}
	
	public function toSQL(): string
	{
		$sql = "SELECT {$this->select}";
		$sql .= " FROM {$this->from}";
		$sql .= $this->joins;
		
		if (!empty($this->where)) {
			$sql .= " WHERE " . implode(' AND ', $this->where);
		}
		
		if ($this->groupBy) {
			$sql .= " {$this->groupBy}";
		}
		
		if ($this->having) {
			$sql .= " {$this->having}";
		}
		
		if ($this->orderBy) {
			$sql .= " {$this->orderBy}";
		}
		
		if ($this->limit) {
			$sql .= " {$this->limit}";
		}
		
		if ($this->offset) {
			$sql .= " {$this->offset}";
		}
		
		return $sql;
	}
	
	public function getPlaceholders(): array
	{
		return $this->placeholders;
	}
}
