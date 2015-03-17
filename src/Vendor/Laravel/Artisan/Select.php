<?php namespace PragmaRX\Sqli\Vendor\Laravel\Artisan;

class Select extends Base {

	/**
	 * Command name.
	 *
	 * @var string
	 */
	protected $name = 'select';

	/**
	 * Command description.
	 *
	 * @var string
	 */
	protected $description = 'Query the database using SQL language';

	/**
	 * Execute the command.
	 *
	 * @return void
	 */
	public function fire()
	{
		parent::fire();

		$this->display($this->laravel->select->execute($this->name, $this->input->getArgument('query'), $this->input->getOption('database')));
	}

}
