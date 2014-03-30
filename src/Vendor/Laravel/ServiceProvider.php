<?php 

/**
 * Part of the SqlI package.
 *
 * NOTICE OF LICENSE
 *
 * Licensed under the 3-clause BSD License.
 *
 * This source file is subject to the 3-clause BSD License that is
 * bundled with this package in the LICENSE file.  It is also available at
 * the following URL: http://www.opensource.org/licenses/BSD-3-Clause
 *
 * @package    SqlI
 * @version    0.1.0
 * @author     Antonio Carlos Ribeiro @ PragmaRX
 * @license    BSD License (3-clause)
 * @copyright  (c) 2013, PragmaRX
 * @link       http://pragmarx.com
 */

namespace PragmaRX\SqlI\Vendor\Laravel;

use PragmaRX\SqlI\Select;
use PragmaRX\SqlI\Support\DatabaseConnection;
use PragmaRX\SqlI\Support\Statement;
use PragmaRX\SqlI\Support\WorkingDirectory;
use PragmaRX\SqlI\Support\SqlInteractive;

use PragmaRX\Support\Config;
use PragmaRX\Support\Filesystem;

use Symfony\Component\Finder\Finder;

use PragmaRX\SqlI\Vendor\Laravel\Artisan\Select as SelectCommand;
use PragmaRX\SqlI\Vendor\Laravel\Artisan\Delete as DeleteCommand;
use PragmaRX\SqlI\Vendor\Laravel\Artisan\Insert as InsertCommand;
use PragmaRX\SqlI\Vendor\Laravel\Artisan\Update as UpdateCommand;
use PragmaRX\SqlI\Vendor\Laravel\Artisan\Sql    as SqlCommand;
use PragmaRX\SqlI\Vendor\Laravel\Artisan\SqlI   as SqlICommand;
use PragmaRX\SqlI\Vendor\Laravel\Artisan\Tables as TablesCommand;

use PragmaRX\Support\ServiceProvider as PragmaRXServiceProvider;

class ServiceProvider extends PragmaRXServiceProvider {

	protected $packageVendor = 'pragmarx';
	protected $packageVendorCapitalized = 'PragmaRX';

	protected $packageName = 'select';
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
		$this->preRegister();

		$this->registerSelectCommand();
		$this->registerDeleteCommand();
		$this->registerInsertCommand();
		$this->registerUpdateCommand();
		$this->registerSqlCommand();
		$this->registerSqlICommand();
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

			$database = new DatabaseConnection($app['db']);

			return new Select(
				$database,
				new Statement(new WorkingDirectory),
				new SqlInteractive($database, $app['select.sqli.command'])
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
	private function registerSqlICommand()
	{
		$this->app['select.sqli.command'] = $this->app->share(function($app)
		{
			return new SqlICommand();
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
