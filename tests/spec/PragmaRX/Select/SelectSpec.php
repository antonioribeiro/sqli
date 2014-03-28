<?php

namespace spec\PragmaRX\Select;

use PhpSpec\ObjectBehavior;
use Prophecy\Argument;

class SelectSpec extends ObjectBehavior
{
    function it_is_initializable()
    {
        $this->shouldHaveType('PragmaRX\Select\Select');
    }

	function it_select_users_table()
	{
		$this->execute('select', '* from users');
	}
}
