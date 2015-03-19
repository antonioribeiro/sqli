<?php

namespace PragmaRX\Sqli\Support;

class Readline {

	/**
	 * Does it have readline support enabled?
	 *
	 * @var
	 */
	private $hasReadlineSupport;

	/**
	 * The input.
	 *
	 * @var
	 */
	private $input;

	/**
	 * History file name.
	 *
	 * @var string
	 */
	private $defaultHistoryFileName = '.sqli_history';

	/**
	 * @var Option
	 */
	private $option;

	/**
	 * @var Commands
	 */
	private $commands;

	/**
	 * Create a Readline instance.
	 *
	 */
	public function __construct(Options $option, Commands $commands)
	{
		$this->option = $option;

		$this->commands = $commands;

		$this->configure();
	}

	/**
	 * The destructor.
	 *
	 * @return void
	 */
	public function __destruct()
	{
		if ($this->input)
		{
			fclose($this->input);
		}

		if ($this->hasReadlineSupport)
		{
			readline_write_history($this->option->get('readline_hist'));
		}
	}

	private function getHistoryFile()
	{
		$file = getcwd() . DIRECTORY_SEPARATOR .$this->defaultHistoryFileName;

		if ( ! is_writable($file))
		{
			$file = env('HOME') . DIRECTORY_SEPARATOR . $this->defaultHistoryFileName;
		}

		return $file;
	}

	/**
	 * Configure everything.
	 *
	 */
	private function configure()
	{
		$this->input = fopen('php://stdin', 'r');

		$this->hasReadlineSupport = true;

		if (!function_exists('readline') || env('TERM') == 'dumb')
		{
			$this->hasReadlineSupport = false;
		}

		if ($this->hasReadlineSupport && is_readable($this->option->get('readline_hist')))
		{
			readline_read_history($this->option->get('readline_hist'));
		}
	}

	/**
	 * Read input
	 *
	 * @param string $prompt
	 * @return string
	 */
	public function read($prompt = null)
	{
		if ($this->hasReadlineSupport)
		{
			return readline($prompt);
		}

		echo $prompt;

		return fgets($this->input);
	}

	public function saveHistory($code)
	{
		if ($this->hasReadlineSupport)
		{
			readline_add_history($code);

			readline_write_history($this->option->get('readline_hist'));
		}
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

}
