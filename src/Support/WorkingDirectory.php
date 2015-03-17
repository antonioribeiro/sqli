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

namespace PragmaRX\Sqli\Support;

use Symfony\Component\Finder\Finder;

class WorkingDirectory {

	/**
	 * Class instantiator.
	 *
	 */
	public function __construct()
	{
		$this->finder = new Finder;
	}

	/**
	 * Get the list of files in the current working directory.
	 *
	 * @return array
	 */
	public function getFiles()
	{
		$files = array();

		foreach ($this->finder->in(getcwd())->depth(0) as $file)
		{
			$files[] = $file->getFilename();
		}

		return $files;
	}

}
