<?php

namespace Tests;

use Illuminate\Foundation\Testing\TestCase as BaseTestCase;
use App\Traits\TestApi;

abstract class TestCase extends BaseTestCase
{
    use CreatesApplication, TestApi;
    /**
     * @test
     */
}
