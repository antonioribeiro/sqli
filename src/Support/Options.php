<?php

namespace PragmaRX\Sqli\Support;

class Options {

	/**
	 * The options for this instance.
	 *
	 * @var array
	 */
	protected $options = array();

	private $prompt;

	private $readlineHistory;

	private $connectionName;

	private $databaseName;

	public function __construct()
	{
		$this->configure();
	}

	/**
	 * Get default options
	 *
	 * @return array Defaults
	 */
	private function makeOptions()
	{
		$defaults = array(
			'prompt'        => $this->prompt.'> ',
			'showtime'      => false,
			'readline_hist' => $this->readlineHistory,
			'connectionName' => $this->connectionName,
			'databaseName' => $this->databaseName,
		);

		return $defaults;
	}

	/**
	 * Option getter.
	 *
	 * @param $type
	 * @return null
	 */
	public function get($type)
	{
		if (!isset($this->options[$type]))
		{
			return null;
		}

		return $this->options[$type];
	}

	private function configure()
	{
		$this->options = $this->makeOptions();
	}

	public function setPrompt($prompt)
	{
		$this->prompt = $prompt;

		$this->configure();
	}

	public function setReadlineHistory($history)
	{
		$this->readlineHistory = $history;

		$this->configure();
	}

	public function setConnectionName($connectionName)
	{
		$this->connectionName = $connectionName;

		$this->configure();
	}

	public function setDatabaseName($databaseName)
	{
		$this->databaseName = $databaseName;

		$this->configure();
	}

}
