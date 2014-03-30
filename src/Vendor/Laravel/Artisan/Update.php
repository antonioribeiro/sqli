<?php namespace PragmaRX\SqlI\Vendor\Laravel\Artisan;

class Update extends Base {

	/**
	 * Command name.
	 *
	 * @var string
	 */
	protected $name = 'update';

	/**
	 * Command description.
	 *
	 * @var string
	 */
	protected $description = 'Execute a SQL UPDATE statement';

	/**
	 * Execute the command.
	 *
	 * @return void
	 */
	public function fire()
	{
		parent::fire();

		$this->display($this->laravel->select->execute($this->name, $this->input->getArgument('query')));
	}

}
