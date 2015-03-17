<?php 

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
