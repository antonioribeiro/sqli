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

namespace PragmaRX\Sqli;

use PragmaRX\Sqli\Support\DatabaseConnection;
use PragmaRX\Sqli\Support\Statement;
use PragmaRX\Sqli\Support\Sqlinteractive;

class Sqli
{
	/**
	 * DatabaseConnection object.
	 *
	 * @var Support\DatabaseConnection
	 */
	private $database;

	/**
	 * The statement.
	 *
	 * @var Support\Statement
	 */
	private $statement;

	/**
	 * The SQL RePL (read execute print loop) interface.
	 *
	 * @var Support\Sqli
	 */
	private $sqlI;

	/**
	 * Class instantiator.
	 *
	 * @param DatabaseConnection $database
	 * @param Statement $statement
	 * @param \PragmaRX\Sqli\Support\Sqlinteractive $sqlI
	 */
	public function __construct(DatabaseConnection $database, Statement $statement, Sqlinteractive $sqlI)
	{
		$this->statement = $statement;
		
		$this->database = $database;

		$this->sqlI = $sqlI;
	}

	/**
	 * Public method for executing statements.
	 *
	 * @param $verb
	 * @param $arguments
	 * @param mixed $database
	 * @return mixed
	 */
	public function execute($verb, $arguments, $database = null)
	{
		$this->statement->setVerb($verb);

		$this->statement->setArguments($arguments);

		$this->database->setConnection($database);

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

	/**
	 * Run the sql interactive interface.
	 *
	 */
	public function sqlI()
	{
		return $this->sqlI->run();
	}

	/**
	 * @param $database
	 */
	public function setConnection($database)
	{
		$this->database->setConnection($database);
	}

}
