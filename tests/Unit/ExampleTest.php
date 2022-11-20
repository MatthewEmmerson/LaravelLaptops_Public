<?php

namespace Tests\Unit;

use Tests\TestCase;

class ExampleTest extends TestCase
{
    /**
     * A basic test example.
     *
     * If there are no tests in the 'Tests/Unit/' directory, the command 'php artisan test'
     * will not run- it will refuse if there are no tests in this file. This one test has
     * been left included to ensure that this issue is not encountered.
     *
     * @return void
     */
    public function test_need_one_test_in_the_unit_folder_to_run_php_artisan_test_command()
    {
        $this->assertTrue(true);
    }
}
