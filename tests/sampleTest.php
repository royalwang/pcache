<?php

namespace Tests\pcache;

use pcache\Sample;
use PHPUnit_Framework_TestCase;

class SampleTest extends PHPUnit_Framework_TestCase
{
    public function testA()
    {
        $this->assertEquals(0, (new Sample())->getA());
    }
}
