<?php namespace PragmaRX\Select\Vendor\Laravel\Artisan;

use Illuminate\Console\Command;
use Symfony\Component\Console\Input\InputArgument;

class Base extends Command {

	/**
	 * The table helper set.
	 *
	 * @var \Symfony\Component\Console\Helper\TableHelper
	 */
	protected $table;

	public function displayMessages($type, $messages)
	{
		foreach($messages as $message)
		{
			$this->$type($message);
		}
	}

	/**
	 * Get the console command arguments.
	 *
	 * @return array
	 */
	protected function getArguments()
	{
		return array(
			array('query', InputArgument::IS_ARRAY, 'The SQL query to be executed'),
		);
	}

	/**
	 * Get the console command options.
	 *
	 * @return array
	 */
	protected function getOptions()
	{
		return isset($this->options) ? $this->options : array();
	}

	public function display($result)
	{
		if ($result)
		{
			if (is_array($result))
			{
				$this->displayTable($result);
			}
			else
			{
				$this->info($result);
			}
		}
	}

	public function displayTable($table)
	{
		$headers = $this->makeHeaders($table[0]);

		$rows = array();

		foreach ($table as $row)
		{
			$rows[] = (array) $row;
		}

		$this->table = $this->getHelperSet()->get('table');

		$this->table->setHeaders($headers)->setRows($rows);

		$this->table->render($this->getOutput());
	}

	private function makeHeaders($items)
	{
		return array_keys((array) $items);
	}	
}
