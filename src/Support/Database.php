<?php 

/**
 * Part of the Select package.
 *
 * NOTICE OF LICENSE
 *
 * Licensed under the 3-clause BSD License.
 *
 * This source file is subject to the 3-clause BSD License that is
 * bundled with this package in the LICENSE file.  It is also available at
 * the following URL: http://www.opensource.org/licenses/BSD-3-Clause
 *
 * @package    Select
 * @version    0.1.0
 * @author     Antonio Carlos Ribeiro @ PragmaRX
 * @license    BSD License (3-clause)
 * @copyright  (c) 2013, PragmaRX
 * @link       http://pragmarx.com
 */

namespace PragmaRX\Select\Support;

use Illuminate\Database\DatabaseManager;

class Database {

	private $db;

	public function __construct(DatabaseManager $db)
	{
		$this->db = $db;
	}

	public function execute($statement)
	{
		return $this->db->select(
			$this->db->raw($statement)
		);
	}

	public function getAllTables($count)
	{
		$tables = $this->execute($this->getSelectTablesStatement());

		if ($count)
		{
			$this->addRowCount($tables);
		}

		return $this->sortTableList($tables);
	}

	public function getSelectTablesStatement()
	{
		switch ($this->db->connection()->getConfig('driver')) {
			case 'pgsql':
				return "SELECT table_schema, table_name FROM information_schema.tables WHERE table_type = 'BASE TABLE' AND table_schema NOT IN ('pg_catalog', 'information_schema');";
				break;
			
			case 'mysql':
				return "SHOW TABLES;";
				break;

			case 'sqlsrv':
				return "SHOW TABLES;";
				break;

			default: 
				$error = 'Database driver not supported: '.DB::connection()->getConfig('driver');
				throw new Exception($error);
				break;
		}
	}

	private function addRowCount(&$tables)
	{
		foreach($tables as $key => $row)
		{
			$count = $this->execute("select count(*) as count from ".$row->table_name);

			$tables[$key]->row_count = $count[0]->count;
		}
	}

	private function sortTableList($tables)
	{
		uasort($tables, array($this, 'sortTableListCompare'));

		return $tables;
	}

	private function sortTableListCompare($a, $b) 
	{
		if ($a->table_name == $b->table_name) 
		{
			return 0;
		}

		return ($a->table_name < $b->table_name) ? -1 : 1;
	}
}