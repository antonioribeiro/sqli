<?php

namespace PragmaRX\Sqli\Support;


class Commands {

	/**
	 * Array of internal commands.
	 *
	 * @var array
	 */
	private $internalCommands = array(
		'quit' => array('method' => 'quit', 'description' => 'Exit interface.'),
		'exit' => array('method' => 'quit', 'description' => ''),
		'die' => array('method' => 'quit', 'description' => ''),
		'tables' => array('method' => 'tables', 'description' => 'List all tables. Use "tables count" to list with row count.'),
		'help' => array('method' => 'help', 'description' => 'Show this help.'),
		'database' => array('method' => 'changeDatabase', 'description' => 'Change the current database connection. Usage: "database [connection name]".'),
		'databases' => array('method' => 'connections', 'description' => 'Show a list of database connections.'),
		'connections' => array('method' => 'connections', 'description' => 'Show a list of database connections.'),
	);

	/**
	 * Get the internal command method to be executed.
	 *
	 * @param $string
	 * @return null|string
	 */
	public function all($string)
	{
		preg_match('/\w+/', $string, $matches);

		$command = null;

		if ($matches)
		{
			if (isset($this->internalCommands[strtolower($matches[0])]))
			{
				$command = $this->internalCommands[strtolower($matches[0])];
			}
		}

		return $command;
	}

}
