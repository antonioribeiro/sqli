<?php 

namespace PragmaRX\Sqli\Support;

use PragmaRX\Sqli\Support\WorkingDirectory;

class Statement {

	/**
	 * The verb.
	 * @var
	 */
	private $verb;

	/**
	 * The arguments.
	 * @var
	 */
	private $arguments;

	/**
	 * The working directory, we might get the files from it.
	 *
	 * @var
	 */
	private $workingDirectory;

	/**
	 * Create instance.
	 *
	 * @param WorkingDirectory workingDirectory
	 */
	public function __construct(WorkingDirectory $workingDirectory)
	{
		$this->workingDirectory = $workingDirectory;
	}

	/**
	 * Retrieve a full statement.
	 *
	 * @return string
	 */
	public function getStatement()
	{
		return $this->makeStatement($this->getVerb(), $this->getArguments());
	}

	/**
	 * Verb setter.
	 *
	 * @param $verb
	 */
	public function setVerb($verb)
	{
		$this->verb = $verb;
	}

	/**
	 * Arguments setter.
	 *
	 * @param $arguments
	 */
	public function setArguments($arguments)
	{
		$this->arguments = $arguments;
	}

	/**
	 * Verb getter.
	 *
	 * @return mixed
	 */
	public function getVerb()
	{
		return $this->verb;
	}

	/**
	 * Arguments getter.
	 *
	 * @return mixed
	 */
	public function getArguments()
	{
		return $this->arguments;
	}

	/**
	 * Routine for assembling the list of arguments to a string.
	 *
	 * 	Unix-like systems may convert (glob expansion) 'select * from whatever' to
	 *	'select file1 file2 file3 file4 from whatever' so we will try to transform
	 *  it back to star (*).
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

		$files = $this->workingDirectory->getFiles();

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

	/**
	 * Add the verb to the statement.
	 *
	 * @param $verb
	 * @param $command
	 * @return string
	 */
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

	/**
	 * Make a statement.
	 *
	 * @param $verb
	 * @param $command
	 * @return string
	 */
	public function makeStatement($verb, $command)
	{
		return $this->addVerbToStatement(
			$verb,
			$this->assembleArguments($command)
		);
	}

}
