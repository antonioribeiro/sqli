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

namespace PragmaRX\Select;

use Symfony\Component\Finder\Finder;
use DB;

class Select
{
	public function execute($base, $dml)
	{
		$dml = $this->makeCommand($base, $dml);

		return $this->executeCommand($dml);
	}

	/**
	 * @param $dml
	 * @return mixed
	 */
	private function buildCommandString($dml)
	{
		if (is_string($dml))
		{
			return $dml;
		}

		// Unix-like systems may convert 'select * from whatever' to
		// 'select file1 file2 file3 file4 from whatever'
		// so we will try to transform this back to star (*).
		$files = array();

		foreach ((new Finder())->in(getcwd())->depth(0) as $file)
		{
			$files[] = $file->getFilename();
		}

		$i = 0;

		asort($files);

		while ($i < count($dml))
		{
			if ($dml[$i] == $files[0]) {
				$slice = array_slice($dml, $i, count($files));

				asort($slice);

				if (implode(',', $slice) == implode(',', $files)) {
					array_splice($dml, $i, count($files), '*');

					$i = 0;

					continue;
				}
			}

			$i++;
		}

		return trim(implode(' ', $dml));
	}

	public function addBaseToCommand($base, $command)
	{
		$first = strtolower(explode(' ', $command)[0]);

		if ($first !== strtolower($base))
		{
			$command = "$base $command";
		}

		return $command;
	}

	public function makeCommand($base, $command)
	{
		return $this->addBaseToCommand(
			$base,
			$this->buildCommandString($command)
		);
	}

	public function executeCommand($command)
	{
		return DB::select(DB::raw($command));
	}

}