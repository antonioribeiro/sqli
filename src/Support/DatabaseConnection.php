<?php 

/**
 * Part of the Sqli package.
 *
 * NOTICE OF LICENSE
 *
 * Licensed under the 3-clause BSD License.
 *
 * This source file is subject to the 3-clause BSD License that is
 * bundled with this package in the LICENSE file.  It is also available at
 * the following URL: http://www.opensource.org/licenses/BSD-3-Clause
 *
 * @package    Sqli
 * @version    0.1.0
 * @author     Antonio Carlos Ribeiro @ PragmaRX
 * @license    BSD License (3-clause)
 * @copyright  (c) 2013, PragmaRX
 * @link       http://pragmarx.com
 */

namespace PragmaRX\Sqli\Support;

use Illuminate\Database\DatabaseManager;
use Illuminate\Config\Repository as Config;
use Exception;

class DatabaseConnection {

	/**
	 * Database connection or manager.
	 *
	 * @var \Illuminate\Database\DatabaseManager
	 */
	private $databaseManager;

	/**
	 * Current database connection.
	 *
	 * @var string
	 */
	private $connectionName;

	/**
	 * Configuration object.
	 *
	 * @var \Illuminate\Config\Repository
	 */
	private $config;

	/**
	 * Class instantiator.
	 *
	 * @param DatabaseManager $databaseManager
	 * @param Config $config
	 */
	public function __construct(DatabaseManager $databaseManager, Config $config)
	{
		$this->databaseManager = $databaseManager;

		$this->config = $config;

		$this->connectionName = $this->databaseManager->getDefaultConnection();
	}

	/**
	 * Execute a statement using the connection.
	 *
	 * @param $statement
	 * @return mixed
	 */
	public function execute($statement)
	{
		$method = $this->isSelect($statement) ? 'select' : 'statement';

		return $this->connection()->{$method}(
			$this->connection()->raw($statement)
		);
	}

	/**
	 * Get a list of tables from the connection.
	 *
	 * @param $count
	 * @return mixed
	 */
	public function getAllTables($count = false)
	{
		$tables = $this->execute($this->getSelectTablesStatement());

		if ($count)
		{
			$this->addRowCount($tables);
		}

		return $this->sortTableList($tables);
	}

	/**
	 * Get the statement for selecting the list of table.
	 *
	 * @return string
	 * @throws Exception
	 */
	public function getSelectTablesStatement()
	{
		switch ($this->connection()->getConfig('driver'))
		{
			case 'pgsql':
				return "select table_schema, table_name from information_schema.tables where table_type = 'BASE TABLE' and table_schema not in ('pg_catalog', 'information_schema');";

			case 'mysql':
				return sprintf(
							'select table_schema, table_name from information_schema.tables where table_schema=\'%s\';',
							$this->connection()->getDatabaseName()
						);
				break;

// TODO
//			case 'sqlsrv':
//				return "SHOW TABLES;";
//				break;

			default: 
				$error = 'Database driver not supported: '.$this->connection()->getConfig('driver');
				throw new Exception($error);
				break;
		}
	}

	/**
	 * Add the row count to the list of tables.
	 *
	 * @param $tables
	 */
	private function addRowCount(&$tables)
	{
		foreach($tables as $key => $row)
		{
			$count = $this->execute("select count(*) as count from ".$row->table_name);

			$tables[$key]->row_count = $count[0]->count;
		}
	}

	/**
	 * Sort the list of tables.
	 *
	 * @param $tables
	 * @return mixed
	 */
	private function sortTableList($tables)
	{
		uasort($tables, array($this, 'sortTableListCompare'));

		return $tables;
	}

	/**
	 * Comparison helper for sortTableList.
	 *
	 * @param $a
	 * @param $b
	 * @return int
	 */
	private function sortTableListCompare($a, $b)
	{
		if ($a->table_name == $b->table_name) 
		{
			return 0;
		}

		return ($a->table_name < $b->table_name) ? -1 : 1;
	}

	/**
	 * Verify it's a select statement.
	 *
	 * @param $statement
	 * @return bool
	 */
	public function isSelect($statement)
	{
		return strtolower(explode(' ', $statement)[0]) === 'select';
	}

	/**
	 * @param $connectionName
	 */
	public function setConnection($connectionName)
	{
		$this->connectionName = ! is_null($connectionName)
								? $connectionName
								: $this->databaseManager->getDefaultConnection();
	}

	/**
	 * Get the current connection.
	 *
	 * @return \Illuminate\Database\Connection
	 */
	private function connection()
	{
		return $this->databaseManager->connection($this->connectionName);
	}

	/**
	 * Get the current connection name.
	 *
	 * @return string
	 */
	public function getConnectionName()
	{
		return $this->connectionName;
	}

	/**
	 * Get the current database name.
	 *
	 * @return string
	 */
	public function getDatabaseName()
	{
		return $this->connection()->getDatabaseName();
	}

	/**
	 * Check if a database connection name is configured.
	 *
	 * @param string $name
	 * @return string
	 */
	public function databaseExists($name)
	{
		$connections = $this->config->get('database.connections');

		return isset($connections[$name]);
	}

}
