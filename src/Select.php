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
	private $database;

	public function __construct(Database $database, Statement $statement)
	{
		$this->statement = $statement;
		
		$this->database = $database;
	}

	public function execute($verb, $arguments)
	{
		$this->statement->setVerb($verb);

		$this->statement->setArguments($arguments);

		return $this->executeStatement($this->statement->getStatement());
	}

	public function executeStatement($command)
	{
		return $this->database->execute($command);
	}

	public function getTables($count)
	{
		return $this->database->getAllTables($count);
	}

}