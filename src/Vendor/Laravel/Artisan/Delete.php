<?php namespace PragmaRX\Select\Vendor\Laravel\Artisan;

class Delete extends Base {

	/**
	 * Command name.
	 *
	 * @var string
	 */
	protected $name = 'delete';

	/**
	 * Command description.
	 *
	 * @var string
	 */
	protected $description = 'Execute a SQL DELETE statement';

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
