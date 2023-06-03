<?php

namespace Tests;

use Illuminate\Foundation\Testing\TestCase as BaseTestCase;
use Illuminate\Foundation\Testing\DatabaseTruncation;

abstract class TestCase extends BaseTestCase
{
    use CreatesApplication;
    use DatabaseTruncation;

    /**
     * Indicates which connections should have their tables truncated.
     *
     * @var array
     */
    protected $connectionsToTruncate = ['mysql'];
}
