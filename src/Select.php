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

namespace PragmaRX\Select;

use PragmaRX\Select\Support\Database;
use PragmaRX\Select\Support\Statement;

class Select
{
	/**
	 * Database object.
	 *
	 * @var Support\Database
	 */
	private $database;

	/**
	 * Class instantiator.
	 *
	 * @param Database $database
	 * @param Statement $statement
	 */
	public function __construct(Database $database, Statement $statement)
	{
		$this->statement = $statement;
		
		$this->database = $database;
	}

	/**
	 * Public method for executing statements.
	 *
	 * @param $verb
	 * @param $arguments
	 * @return mixed
	 */
	public function execute($verb, $arguments)
	{
		$this->statement->setVerb($verb);

		$this->statement->setArguments($arguments);

		return $this->executeStatement($this->statement->getStatement());
	}

	/**
	 * Execute a statement in database.
	 *
	 * @param $command
	 * @return mixed
	 */
	public function executeStatement($command)
	{
		return $this->database->execute($command);
	}

	/**
	 * Get a list of database tables.
	 *
	 * @param $count
	 * @return mixed
	 */
	public function getTables($count)
	{
		return $this->database->getAllTables($count);
	}

}