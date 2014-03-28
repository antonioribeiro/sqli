<?php namespace PragmaRX\Select\Vendor\Laravel\Artisan;

use File;
use Config;
use App;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Input\InputArgument;

class Select extends Base {

	/**
	 * The console command name.
	 *
	 * @var string
	 */
	protected $name = 'select';

	/**
	 * The console command description.
	 *
	 * @var string
	 */
	protected $description = 'Query the database using SQL language';

	/**
	 * Command options.
	 *
	 * @var array
	 */
	protected $arguments = array();

	/**
	 * The table helper set.
	 *
	 * @var \Symfony\Component\Console\Helper\TableHelper
	 */
	protected $table;

	/**
	 * Execute the console command.
	 *
	 * @return void
	 */
	public function fire()
	{
		$result = $this->laravel->select->execute('SELECT', $this->input->getArgument('query'));

		if ($result)
		{
			$this->display($result);
		}
	}

	public function getArguments()
	{
		return array(
			array('query', InputArgument::IS_ARRAY, 'The SQL query to be executed'),
		);
	}

	public function display($result)
	{
		$headers = $this->makeHeaders($result[0]);

		$rows = array();

		foreach ($result as $row)
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
