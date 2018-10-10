<?php namespace PragmaRX\Sqli\Vendor\Laravel\Artisan;

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
	public function handle()
	{
		parent::handle();

		$this->display($this->laravel->select->execute($this->name, $this->input->getArgument('query'), $this->input->getOption('database')));
	}

}
