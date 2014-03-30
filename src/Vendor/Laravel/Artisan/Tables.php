<?php namespace PragmaRX\SqlI\Vendor\Laravel\Artisan;

use Symfony\Component\Console\Input\InputOption;

class Tables extends Base {

	/**
	 * Command name.
	 *
	 * @var string
	 */
	protected $name = 'tables';

	/**
	 * Command description.
	 *
	 * @var string
	 */
	protected $description = 'List all tables in database';

	protected $options = array(
			array('count', null, InputOption::VALUE_NONE, 'Display number of rows for each table.', null),
	);

	/**
	 * Execute the command.
	 *
	 * @return void
	 */
	public function fire()
	{
		parent::fire();

		$this->display($this->laravel->select->getTables($this->input->getOption('count')));
	}

}
