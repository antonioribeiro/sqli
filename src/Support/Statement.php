<?php 

/**
 * Part of the Select package.
 *
 * NOTICE OF LICENSE
 *
 * Licensed under the 3-clause BSD License.
 *
 * This source file is subject to the 3-clause BSD License that is
 * bundled with this package in the LICENSE file.  It is also available at
 * the following URL: http://www.opensource.org/licenses/BSD-3-Clause
 *
 * @package    Select
 * @version    0.1.0
 * @author     Antonio Carlos Ribeiro @ PragmaRX
 * @license    BSD License (3-clause)
 * @copyright  (c) 2013, PragmaRX
 * @link       http://pragmarx.com
 */

namespace PragmaRX\Select\Support;

class Statement {

	private $verb;

	private $arguments;

	public function __construct($files)
	{
		$this->files = $files;
	}

	public function getStatement()
	{
		return $this->makeStatement($this->getVerb(), $this->getArguments());
	}

	public function setVerb($verb)
	{
		$this->verb = $verb;
	}

	public function setArguments($arguments)
	{
		$this->arguments = $arguments;
	}

	public function getVerb()
	{
		return $this->verb;
	}

	public function getArguments()
	{
		return $this->arguments;
	}

	/**
	 * 	Unix-like systems may convert 'select * from whatever' to
	 *	'select file1 file2 file3 file4 from whatever'
	 *	so we will try to transform this back to star (*).
	 *
	 * @param $arguments
	 * @return mixed
	 */
	private function assembleArguments($arguments)
	{
		if (is_string($arguments))
		{
			return $arguments;
		}

		$files = $this->files->getAllFromWorkingDirectory();

		$i = 0;

		asort($files);

		while ($i < count($arguments))
		{
			$slice = array_slice($arguments, $i, count($files));

			asort($slice);

			if (implode(',', $slice) == implode(',', $files)) {
				array_splice($arguments, $i, count($files), '*');

				$i = 0;

				continue;
			}

			$i++;
		}

		return trim(implode(' ', $arguments));
	}

	public function addVerbToStatement($verb, $command)
	{
		$verb = ($verb == 'sql' ? '' : $verb);

		$first = strtolower(explode(' ', $command)[0]);

		if ($first !== strtolower($verb))
		{
			$command = "$verb $command";
		}

		return trim($command);
	}

	public function makeStatement($verb, $command)
	{
		return $this->addVerbToStatement(
			$verb,
			$this->assembleArguments($command)
		);
	}

}