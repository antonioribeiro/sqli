<?php

/**
 * Part of the SqlI package.
 *
 * NOTICE OF LICENSE
 *
 * Licensed under the 3-clause BSD License.
 *
 * This source file is subject to the 3-clause BSD License that is
 * bundled with this package in the LICENSE file.  It is also available at
 * the following URL: http://www.opensource.org/licenses/BSD-3-Clause
 *
 * @package    SqlI
 * @version    0.1.0
 * @author     Antonio Carlos Ribeiro @ PragmaRX
 * @license    BSD License (3-clause)
 * @copyright  (c) 2013, PragmaRX
 * @copyright  (c) 2009, Ian Eure <ieure@php.net> *** This class is based on PHPRepl
 * @link       http://pragmarx.com
 */

namespace PragmaRX\SqlI\Support;

use PragmaRX\SqlI\Support\DatabaseConnection;
use PragmaRX\SqlI\Vendor\Laravel\Artisan\SqlI as Command;

class SqlInteractive
{
	/**
	 * The options for this instance.
	 *
	 * @var array
	 */
	protected $options = array();

	/**
	 * The database connection object.
	 *
	 * @var DatabaseConnection
	 */
	protected $databaseConnection;

	/**
	 * The referer Artisan command.
	 *
	 * @var \PragmaRX\SqlI\Vendor\Laravel\Artisan\SqlI
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
	);

	/**
	 * Quit the application?
	 *
	 * @var bool
	 */
	private $quit = false;

	/**
	 * The input.
	 *
	 * @var
	 */
	private $input;

	/**
	 * Does it have readline support enabled?
	 *
	 * @var
	 */
	private $readlineSupport;

	/**
	 * Constructor
	 *
	 * @param \PragmaRX\SqlI\Support\DatabaseConnection $databaseConnection
	 * @param \PragmaRX\SqlI\Vendor\Laravel\Artisan\SqlI $command
	 * @return \PragmaRX\SqlI\Support\SqlInteractive
	 */
	public function __construct(DatabaseConnection $databaseConnection, Command $command)
	{
		$this->databaseConnection = $databaseConnection;

		$this->command = $command;

		$this->configure();
	}

	/**
	 * Configure everything.
	 *
	 */
	private function configure()
	{
		$this->input = fopen('php://stdin', 'r');

		$this->options = $this->getDefaultOptions();

		$this->readlineSupport = true;

		if (!function_exists('readline') || getenv('TERM') == 'dumb')
		{
			$this->readlineSupport = false;
		}

		if ($this->readlineSupport && is_readable($this->getOption('readline_hist')))
		{
			readline_read_history($this->getOption('readline_hist'));
		}
	}

	/**
	 * Get default options
	 *
	 * @return array Defaults
	 */
	private function getDefaultOptions()
	{
		$defaults = array(
			'prompt'        => $this->prompt.'> ',
			'showtime'      => false,
			'readline_hist' => $this->getHistoryFile(),
		);

		return $defaults;
	}

	/**
	 * Option getter.
	 *
	 * @param $type
	 * @return null
	 */
	private function getOption($type)
	{
		if (!isset($this->options[$type]))
		{
			return null;
		}

		return $this->options[$type];
	}

	/**
	 * The destructor.
	 *
	 * @return void
	 */
	public function __destruct()
	{
		fclose($this->input);

		if ($this->readlineSupport)
		{
			readline_write_history($this->getOption('readline_hist'));
		}
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
	 * Read input
	 *
	 * @throws \Exception
	 * @internal param $
	 *
	 * @return string Input
	 */
	private function read()
	{
		$code  = '';
		$done  = true;
		$lines = 0;

		do
		{
			$prompt = $lines > 0 ? '> ' : $this->makePrompt();

			if ($this->readlineSupport)
			{
				$line = readline($prompt);
			}
			else
			{
				echo $prompt;
				$line = fgets($this->input);
			}

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
				$done = ($lines !== 0 || ! $this->getInternalCommand($code));
			}

			$code .= $line;
			$lines++;
		}
		while ( ! $done);

		// Add the whole block to the readline history.
		if ($this->readlineSupport)
		{
			readline_add_history($code);

			readline_write_history($this->getOption('readline_hist'));
		}

		return $code;
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
			$file = getenv('HOME') . '/'.$this->defaultHistoryFileName;
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
		$internalCommand = $this->getInternalCommand($command);

		return  isset($internalCommand)
				? $this->{$internalCommand['method']}($command)
				: null;
	}

	/**
	 * Get the internal command method to be executed.
	 *
	 * @param $string
	 * @return null|string
	 */
	private function getInternalCommand($string)
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
	 * @return string
	 */
	private function makePrompt()
	{
		return ($this->getOption('showtime') ? date('G:i:s ') : '') .
				$this->databaseConnection->getConnectionName().
				':'.
				$this->databaseConnection->getDatabaseName().'> ';
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
			$this->databaseConnection->setConnection($parts[1]);
		}

		return true;
	}
}