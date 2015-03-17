<?php namespace PragmaRX\Sqli\Vendor\Laravel\Artisan;

class Sqli extends Base {

	/**
	 * Command name.
	 *
	 * @var string
	 */
	protected $name = 'sqli';

	/**
	 * Command description.
	 *
	 * @var string
	 */
	protected $description = 'Interact with your database using SQL';

	/**
	 * Execute the command.
	 *
	 * @return void
	 */
	public function fire()
	{
		parent::fire();

		$this->display($this->laravel->select->sqlI($this->input->getOption('database')));
	}

}
