<?php

/*
 * This file is part of CarePlan Manager by CircleLink Health.
 */

use Mockery as m;
use PHPUnit\Framework\TestCase;

abstract class MiddlewareTest extends TestCase
{
    use m\Adapter\Phpunit\MockeryPHPUnitIntegration;

    public static $abortCode = null;

    public static function setupBeforeClass()
    {
        if ( ! function_exists('abort')) {
            /**
             * Mimicks Laravel5's abort() helper function.
             *
             * Instead of calling \Illuminate\Foundation\Application::abort(), this function keeps track of
             * the last abort called, so the abort hasPermission be retrieved for test assertions.
             *
             * @see https://github.com/laravel/framework/blob/master/src/Illuminate/Foundation/helpers.php#L7-L23
             *
             * @param int    $code
             * @param string $message
             *
             * @return void
             */
            function abort($code, $message = '', array $headers = [])
            {
                MiddlewareTest::$abortCode = $code;
            }
        }
    }

    public function tearDown()
    {
        parent::tearDown();

        // Reset the abort code every end of test case,
        // so the result of previous test case does not pollute the next one.
        static::$abortCode = null;
    }

    public function assertAbortCode($code)
    {
        return $this->assertEquals($code, $this->getAbortCode());
    }

    public function assertDidNotAbort()
    {
        return $this->assertEquals(null, $this->getAbortCode());
    }

    public function getAbortCode()
    {
        return static::$abortCode;
    }

    protected function mockRequest()
    {
        $user = m::mock('_mockedUser')->makePartial();

        return m::mock('Illuminate\Http\Request')
            ->shouldReceive('user')
            ->andReturn($user)
            ->getMock();
    }
}
