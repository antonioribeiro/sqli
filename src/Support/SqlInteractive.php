<?php

namespace PragmaRX\Sqli\Support;

use Illuminate\Support\Arr;
use PragmaRX\Sqli\Support\DatabaseConnection;
use PragmaRX\Sqli\Vendor\Laravel\Artisan\Sqli as Command;

class Sqlinteractive
{
	/**
	 * The database connection object.
	 *
	 * @var DatabaseConnection
	 */
	protected $databaseConnection;

	/**
	 * The referer Artisan command.
	 *
	 * @var \PragmaRX\Sqli\Vendor\Laravel\Artisan\Sqli
	 */
	private $command;

	/**
	 * History file name.
	 *
	 * @var string
	 */
	private $defaultHistoryFileName = '.lsqli_history';

	/**
	 * Default prompt string.
	 *
	 * @var string
	 */
	private $prompt = 'sqli';

	/**
	 * The timer.
	 *
	 * @var
	 */
	private $timer;

	/**
	 * Quit the application?
	 *
	 * @var bool
	 */
	private $quit = false;

	/**
	 * @var Readline
	 */
	private $readline;

	/**
	 * @var Option
	 */
	private $option;

	/**
	 * @var Commands
	 */
	private $commands;

	/**
	 * @var Completer
	 */
	private $completer;

	/**
	 * Constructor
	 *
	 * @param \PragmaRX\Sqli\Support\DatabaseConnection $databaseConnection
	 * @param \PragmaRX\Sqli\Vendor\Laravel\Artisan\Sqli $command
	 * @param Readline $readline
	 * @param Options $option
	 * @param Commands $commands
	 */
	public function __construct(DatabaseConnection $databaseConnection,
	                            Command $command,
	                            Readline $readline,
								Options $option,
								Commands $commands,
								Completer $completer)
	{
		$this->databaseConnection = $databaseConnection;

		$this->command = $command;

		$this->readline = $readline;

		$this->option = $option;

		$this->commands = $commands;

		$this->completer = $completer;

		$this->configure();
	}

	/**
	 * Run the main loop.
	 *
	 * @return void
	 */
	public function run()
	{
		$this->configureErrorReporting();

		while ( ! $this->quit)
		{
			try
			{
				if (((boolean) $__code__ = $this->read()) === false)
				{
					continue;
				}

				$this->execute($__code__);
			}
			catch (\PDOException $e)
			{
				$this->outputError('Could not connect to '.$this->databaseConnection->getConnectionName().', please check if your connection is properly configured.');

				$this->quit();
			}
			catch (\Exception $e)
			{
				echo ($_ = $e) . "\n";
			}
		}
	}

	/**
	 * Get the history file name.
	 *
	 * @return string
	 */
	private function getHistoryFile()
	{
		$file = getcwd().'/'.$this->defaultHistoryFileName;

		if ( ! is_writable($file))
		{
			$file = env('HOME') . '/'.$this->defaultHistoryFileName;
		}

		return $file;
	}

	/**
	 * Set the PHP error reporting.
	 *
	 */
	private function configureErrorReporting()
	{
		error_reporting(E_ALL | E_STRICT);

		ini_set('html_errors', 'Off');

		ini_set('display_errors', 'On');
	}

	/**
	 * Execute a command.
	 *
	 * @param $command
	 * @return mixed
	 * @internal param $command
	 */
	private function execute($command)
	{
		if ($this->quit)
		{
			return $this->output($command);
		}

		$result = $this->executeInternalCommand($command);

		if ( ! $result)
		{
			try
			{
				$this->startTimer();

				$result = $this->databaseConnection->execute($command);
			}
			catch (\Exception $e)
			{
				$result = $e->getMessage();

				$error = true;
			}
		}

		return $this->output($result, isset($error));
	}

	/**
	 * Output a result or message to screen.
	 *
	 * @param $result
	 * @param bool $isError
	 * @return mixed
	 */
	private function output($result, $isError = false)
	{
		return $this->{$isError ? 'outputError' : 'outputResult'}($result);
	}

	/**
	 * Output a result to screen.
	 *
	 * @param $output
	 */
	private function outputResult($output)
	{
		$this->display($output, 'info');
	}

	/**
	 * Output an error message to screen.
	 *
	 * @param $output
	 */
	private function outputError($output)
	{
		$this->display($output, 'error');
	}

	/**
	 * Output a comment to screen.
	 *
	 * @param $output
	 */
	private function outputComment($output)
	{
		$this->display($output, 'comment');
	}

	/**
	 * Display a full result or a message.
	 *
	 * @param $output
	 * @param string $type
	 */
	private function display($output, $type = 'info')
	{
		$this->command->display($output, $type);

		if ($this->timerStarted())
		{
			$this->command->display($this->getElapsedTime(), 'comment');
		}
	}

	/**
	 * Start a timer.
	 *
	 */
	private function startTimer()
	{
		$this->timer = microtime(true);
	}

	/**
	 * Check if timer is started.
	 *
	 * @return bool
	 */
	private function timerStarted()
	{
		return ! is_null($this->timer);
	}

	/**
	 * Get the elapsed time string.
	 *
	 * @return string
	 */
	private function getElapsedTime()
	{
		$message = sprintf("Executed in %.4f seconds.", abs(microtime(true) - $this->timer));

		$this->stopTimer();

		return $message;
	}

	/**
	 * Stop the timer.
	 *
	 */
	private function stopTimer()
	{
		$this->timer = null;
	}

	/**
	 * Quit the application.
	 *
	 */
	private function quit()
	{
		$this->quit = true;

		return 'I quit.';
	}

	/**
	 * Execute an internal command.
	 *
	 * @param $command
	 * @return mixed
	 */
	private function executeInternalCommand($command)
	{
		$internalCommand = $this->commands->all($command);

		return  isset($internalCommand)
				? $this->{$internalCommand['method']}($command)
				: null;
	}

	/**
	 * Get the table list.
	 *
	 * @param $command
	 * @return mixed
	 */
	private function tables($command)
	{
		$this->output(
			$this->databaseConnection->getAllTables(
				strpos(strtolower($command), 'count') !== false
			)
		);

		return true;
	}

	/**
	 * Help for all commands.
	 *
	 * @return array
	 */
	private function help()
	{
		$result = array();

		foreach ($this->internalCommands as $key => $command)
		{
			if ($command['description'])
			{
				$result[] = array('command' => $key, 'description' => $command['description']);
			}
		}

		uasort($result, make_comparer('command'));

		$this->output($result);

		return true;
	}

	/**
	 * Change the current database connection.
	 *
	 */
	private function changeDatabase($command)
	{
		$parts = explode(' ', $command);

		if (count($parts) < 2)
		{
			$this->outputError('Connection name is needed.');
		}
		else
		if ( ! $this->databaseConnection->databaseExists($parts[1]))
		{
			$this->outputError("Connection $parts[1] does not exists.");
		}
		else
		{
			$this->setConnection($parts[1]);
		}

		// Send new table names and columns to completer
		$this->configureCompleter();

		return true;
	}

	private function connections()
	{
		$result = array();

		foreach ($this->databaseConnection->getConnections() as $key => $connection)
		{
			$result[] = array('Connection Name' => $key, 'Driver' => $connection['driver'], 'Database' => $connection['database'], 'Host' => $connection['host']);
		}

		$this->output($result);

		return true;
	}

	private function configure()
	{
		$this->option->setPrompt($this->prompt);

		$this->option->setReadlineHistory($this->getHistoryFile());

		$this->option->setConnectionName($this->databaseConnection->getConnectionName());

		$this->option->setDatabaseName($this->databaseConnection->getDatabaseName());

		$this->configureCompleter();
	}

	private function setConnection($connection)
	{
		$this->databaseConnection->setConnection($connection);

		$this->option->setConnectionName($connection);
	}

	/**
	 * Read input
	 *
	 * @throws \Exception
	 * @internal param $
	 *
	 * @return string Input
	 */
	public function read()
	{
		$code  = '';
		$done  = true;
		$lines = 0;

		do
		{
			$prompt = $lines > 0 ? '> ' : $this->makePrompt();

			$line = $this->readline->read($prompt);

			// If the input was empty, return false; this breaks the loop.
			if ($line === false)
			{
				echo "\n";

				return $this->quit();
			}

			$line = trim($line);

			// If the last char is not a semicolon and this is not an internal command accumulate more lines.
			if (substr($line, -1) != ';')
			{
				$done = ($lines !== 0 || ! $this->commands->all($code));
			}

			$code .= $line;
			$lines++;
		}
		while ( ! $done);

		$this->readline->saveHistory($code);

		return $code;
	}

	/**
	 * @return string
	 */
	private function makePrompt()
	{
		return ($this->option->get('showtime') ? date('G:i:s ') : '') .
			$this->option->get('connectionName').
			':'.
			$this->option->get('databaseName').'> ';
	}

	private function configureCompleter()
	{
		$this->completer->addCommands($this->commands->getCommandNames());

		$this->completer->addCommands(array_keys($this->databaseConnection->getConnections()));

		$this->completer->addCommands($this->databaseConnection->getTablesNames());

		foreach ($this->databaseConnection->getTablesNames() as $table)
		{
			$this->completer->addCommands($this->databaseConnection->getColumnsNames($table));
		}
	}

}
