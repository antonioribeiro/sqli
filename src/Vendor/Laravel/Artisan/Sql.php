<?php namespace PragmaRX\Select\Vendor\Laravel\Artisan;

class Sql extends Base {

	/**
	 * Command name.
	 *
	 * @var string
	 */
	protected $name = 'sql';

	/**
	 * Command description.
	 *
	 * @var string
	 */
	protected $description = 'Execute a raw SQL statement';

	/**
	 * Execute the command.
	 *
	 * @return void
	 */
	public function fire()
	{
		$this->display($this->laravel->select->execute($this->name, $this->input->getArgument('query')));
	}

}
