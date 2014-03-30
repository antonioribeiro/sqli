<?php namespace PragmaRX\SqlI\Vendor\Laravel\Artisan;

class Insert extends Base {

	/**
	 * Command name.
	 *
	 * @var string
	 */
	protected $name = 'insert';

	/**
	 * Command description.
	 *
	 * @var string
	 */
	protected $description = 'Execute a SQL INSERT statement';

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
