<?php 

/**
 * Part of the Sqli package.
 *
 * NOTICE OF LICENSE
 *
 * Licensed under the 3-clause BSD License.
 *
 * This source file is subject to the 3-clause BSD License that is
 * bundled with this package in the LICENSE file.  It is also available at
 * the following URL: http://www.opensource.org/licenses/BSD-3-Clause
 *
 * @package    Sqli
 * @version    0.1.0
 * @author     Antonio Carlos Ribeiro @ PragmaRX
 * @license    BSD License (3-clause)
 * @copyright  (c) 2013, PragmaRX
 * @link       http://pragmarx.com
 */

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
	 * This is the boot method for this ServiceProvider
	 *
	 * @return void
	 */
	public function wakeUp()
	{

	}

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

		$this->commands('select.select.command');
		$this->commands('select.delete.command');
		$this->commands('select.insert.command');
		$this->commands('select.update.command');
		$this->commands('select.sql.command');
		$this->commands('select.sqli.command');
		$this->commands('select.tables.command');
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
	}   

	/**
	 * Get the root directory for this ServiceProvider
	 * 
	 * @return string
	 */
	public function getRootDirectory()
	{
		return __DIR__.'/../..';
	}    
}
