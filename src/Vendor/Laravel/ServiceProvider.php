<?php 

namespace PragmaRX\Sqli\Vendor\Laravel;

use PragmaRX\Sqli\Sqli;
use PragmaRX\Sqli\Support\DatabaseConnection;
use PragmaRX\Sqli\Support\Statement;
use PragmaRX\Sqli\Support\WorkingDirectory;
use PragmaRX\Sqli\Support\Sqlinteractive;

use PragmaRX\Support\Config;
use PragmaRX\Support\Filesystem;

use Symfony\Component\Finder\Finder;

use PragmaRX\Sqli\Vendor\Laravel\Artisan\Select as SelectCommand;
use PragmaRX\Sqli\Vendor\Laravel\Artisan\Delete as DeleteCommand;
use PragmaRX\Sqli\Vendor\Laravel\Artisan\Insert as InsertCommand;
use PragmaRX\Sqli\Vendor\Laravel\Artisan\Update as UpdateCommand;
use PragmaRX\Sqli\Vendor\Laravel\Artisan\Sql    as SqlCommand;
use PragmaRX\Sqli\Vendor\Laravel\Artisan\Sqli   as SqliCommand;
use PragmaRX\Sqli\Vendor\Laravel\Artisan\Tables as TablesCommand;

use PragmaRX\Support\ServiceProvider as PragmaRXServiceProvider;

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
		$this->app['select'] = $this->app->share(function($app)
		{
			$app['select.loaded'] = true;

			$database = new DatabaseConnection($app['db'], $app['config']);

			return new Sqli(
				$database,
				new Statement(new WorkingDirectory),
				new Sqlinteractive($database, $app['select.sqli.command'])
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
		$this->app['select.select.command'] = $this->app->share(function($app)
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
		$this->app['select.delete.command'] = $this->app->share(function($app)
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
		$this->app['select.insert.command'] = $this->app->share(function($app)
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
		$this->app['select.update.command'] = $this->app->share(function($app)
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
		$this->app['select.sql.command'] = $this->app->share(function($app)
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
		$this->app['select.sqli.command'] = $this->app->share(function($app)
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
		$this->app['select.tables.command'] = $this->app->share(function($app)
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
