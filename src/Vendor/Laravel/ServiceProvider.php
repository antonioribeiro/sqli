<?php 

namespace PragmaRX\Sqli\Vendor\Laravel;

use PragmaRX\Sqli\Sqli;
use PragmaRX\Sqli\Support\Commands;
use PragmaRX\Sqli\Support\Completer;
use PragmaRX\Sqli\Support\Options;
use PragmaRX\Sqli\Support\Readline;
use PragmaRX\Sqli\Support\Statement;
use PragmaRX\Sqli\Support\Sqlinteractive;
use PragmaRX\Sqli\Support\WorkingDirectory;
use PragmaRX\Sqli\Support\DatabaseConnection;
use PragmaRX\Sqli\Vendor\Laravel\Artisan\Sql as SqlCommand;
use PragmaRX\Sqli\Vendor\Laravel\Artisan\Sqli as SqliCommand;
use PragmaRX\Support\ServiceProvider as PragmaRXServiceProvider;
use PragmaRX\Sqli\Vendor\Laravel\Artisan\Select as SelectCommand;
use PragmaRX\Sqli\Vendor\Laravel\Artisan\Delete as DeleteCommand;
use PragmaRX\Sqli\Vendor\Laravel\Artisan\Insert as InsertCommand;
use PragmaRX\Sqli\Vendor\Laravel\Artisan\Update as UpdateCommand;
use PragmaRX\Sqli\Vendor\Laravel\Artisan\Tables as TablesCommand;

class ServiceProvider extends PragmaRXServiceProvider {

	/**
	 * The package vendor name (lower case).
	 *
	 * @var string
	 */
	protected $packageVendor = 'pragmarx';

	/**
	 * The package vendor name in caps.
	 *
	 * @var string
	 */
	protected $packageVendorCapitalized = 'PragmaRX';

	/**
	 * The package name (lower case).
	 *
	 * @var string
	 */
	protected $packageName = 'select';

	/**
	 * The package name (capitalized).
	 *
	 * @var string
	 */
	protected $packageNameCapitalized = 'Select';

	/**
	 * Register the service provider.
	 *
	 * @return void
	 */
	public function register()
	{
		parent::register();

		$this->registerSelectCommand();
		$this->registerDeleteCommand();
		$this->registerInsertCommand();
		$this->registerUpdateCommand();
		$this->registerSqlCommand();
		$this->registerSqliCommand();
		$this->registerTablesCommand();

		$this->registerSelect();
	}

	/**
	 * Get the services provided by the provider.
	 *
	 * @return array
	 */
	public function provides()
	{
		return array('select');
	}

	/**
	 * Takes all the components of Select and glues them
	 * together to create Select.
	 *
	 * @return void
	 */
	private function registerSelect()
	{
		$this->app->singleton('select', function($app)
		{
			$app['select.loaded'] = true;

			$database = new DatabaseConnection($app['db'], $app['config']);

			$options = new Options();

			$commands = new Commands();

			return new Sqli(
				$database,
				new Statement(new WorkingDirectory),
				new Sqlinteractive(
						$database,
						$app['select.sqli.command'],
						new Readline($options, $commands),
						$options,
						$commands,
						new Completer($options)
				)
			);
		});
	}

	/**
	 * Register the Select Artisan command
	 *
	 * @return void
	 */ 
	private function registerSelectCommand()
	{
		$this->app->singleton('select.select.command', function($app)
		{
			return new SelectCommand();
		});

		$this->commands('select.select.command');
	}

	/**
	 * Register the Delete Artisan command
	 *
	 * @return void
	 */ 
	private function registerDeleteCommand()
	{
		$this->app->singleton('select.delete.command', function($app)
		{
			return new DeleteCommand();
		});

		$this->commands('select.delete.command');
	}

	/**
	 * Register the Insert Artisan command
	 *
	 * @return void
	 */ 
	private function registerInsertCommand()
	{
		$this->app->singleton('select.insert.command', function($app)
		{
			return new InsertCommand();
		});

		$this->commands('select.insert.command');
	}

	/**
	 * Register the Update Artisan command
	 *
	 * @return void
	 */
	private function registerUpdateCommand()
	{
		$this->app->singleton('select.update.command', function($app)
		{
			return new UpdateCommand();
		});

		$this->commands('select.update.command');
	}

	/**
	 * Register the Sql Artisan command
	 *
	 * @return void
	 */ 
	private function registerSqlCommand()
	{
		$this->app->singleton('select.sql.command', function($app)
		{
			return new SqlCommand();
		});

		$this->commands('select.sql.command');
	}

	/**
	 * Register the Sql Artisan command
	 *
	 * @return void
	 */
	private function registerSqliCommand()
	{
		$this->app->singleton('select.sqli.command', function($app)
		{
			return new SqliCommand();
		});

		$this->commands('select.sqli.command');
	}

	/**
	 * Register the Tables Artisan command
	 *
	 * @return void
	 */ 
	private function registerTablesCommand()
	{
		$this->app->singleton('select.tables.command', function($app)
		{
			return new TablesCommand();
		});

		$this->commands('select.tables.command');
	}

	/**
	 * Get the current package directory.
	 *
	 * @return string
	 */
	public function getPackageDir()
	{
		return __DIR__.DIRECTORY_SEPARATOR.'..'.DIRECTORY_SEPARATOR.'..';
	}

}
