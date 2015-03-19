<?php

namespace PragmaRX\Sqli\Support;

use Psy\Readline\Libedit;
use Psy\Readline\GNUReadline;
	
class Readline {

	/**
	 * Does it have readline support enabled?
	 *
	 * @var
	 */
	private $readlineSupport;

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
	public function __construct(Option $option, Commands $commands)
	{
	    $this->readline = $this->getReadLine();

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
		fclose($this->input);

		if ($this->readlineSupport)
		{
			readline_write_history($this->option->get('readline_hist'));
		}
	}

    public function getReadline()
    {
        if ( ! isset($this->readline))
        {
            $className = $this->getReadlineClass();
            $this->readline = new $className(
                $this->getHistoryFile(),
                $this->getHistorySize(),
                $this->getEraseDuplicates()
            );
        }

        return $this->readline;
    }

    private function getReadlineClass()
    {
        if (GNUReadline::isSupported())
        {
            return 'Psy\Readline\GNUReadline';
        }
        elseif (Libedit::isSupported())
        {
            return 'Psy\Readline\Libedit';
        }

        return 'Psy\Readline\Transient';
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

	private function getHistorySize()
	{
		return 5000;
	}

	private function getEraseDuplicates()
	{
		return true;
	}

	/**
	 * Configure everything.
	 *
	 */
	private function configure()
	{
		$this->input = fopen('php://stdin', 'r');

		$this->readlineSupport = true;

		if (!function_exists('readline') || env('TERM') == 'dumb')
		{
			$this->readlineSupport = false;
		}

		if ($this->readlineSupport && is_readable($this->option->get('readline_hist')))
		{
			readline_read_history($this->option->get('readline_hist'));
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
	public function read()
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
				$done = ($lines !== 0 || ! $this->commands->all($code));
			}

			$code .= $line;
			$lines++;
		}
		while ( ! $done);

		// Add the whole block to the readline history.
		if ($this->readlineSupport)
		{
			readline_add_history($code);

			readline_write_history($this->option->get('readline_hist'));
		}

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

}
