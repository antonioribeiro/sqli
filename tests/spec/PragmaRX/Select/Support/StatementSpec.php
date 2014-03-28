<?php

namespace spec\PragmaRX\Select\Support;

use PhpSpec\ObjectBehavior;
use Prophecy\Argument;
use PragmaRX\Select\Support\File;

class StatementSpec extends ObjectBehavior
{
	function let(File $file)
	{
		$this->beConstructedWith($file);
	}

    function it_is_initializable()
    {
        $this->shouldHaveType('PragmaRX\Select\Support\Statement');
    }

	function it_selects_all_rows_from_users_table()
	{
		$this->setVerb('select');
		$this->setArguments('* from users');

		$this->getStatement()->shouldReturn('select * from users');
	}

	function it_ignores_verbs_sent_twice(File $file)
	{
		$this->setVerb('select');
		$this->setArguments('select * from users');

		$this->getStatement()->shouldReturn('select * from users');
	}

	function it_ignores_sql_verb(File $file)
	{
		$this->setVerb('sql');
		$this->setArguments('select * from users');

		$this->getStatement()->shouldReturn('select * from users');
	}

	function it_understands_globbed_star_arguments(File $file)
	{
		$files1 = array('file3', 'file1', 'file2', 'file4');
		$files2 = array('file1', 'file3', 'file4', 'file2');

		$this->setVerb('select');
		$this->setArguments(array_merge($files1, array('from', 'users')));

		$file->getAllFromWorkingDirectory()->willReturn($files2);

		$this->getStatement()->shouldReturn('select * from users');
	}

	function it_fails_on_directory_error(File $file)
	{
		$files1 = array('file1', 'file2', 'file4', 'file2');
		$files2 = array('file3', 'file1', 'file2', 'file4');

		$this->setVerb('select');
		$this->setArguments(array_merge($files1, array('from', 'users')));

		$file->getAllFromWorkingDirectory()->willReturn($files2);

		$this->getStatement()->shouldReturn('select file1 file2 file4 file2 from users');
	}

}
