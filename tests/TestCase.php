<?php
// phpcs:ignoreFile
abstract class TestCase extends Laravel\Lumen\Testing\TestCase
{
    /**
     * Creates the application.
     *
     * @return \Laravel\Lumen\Application
     */
    public function createApplication()
    {
    	// In Cases where using string functions (strtolower) on UTF-8 characters
    	// like strtolower('margão') gives invalid UTF-8 out put then using that 
    	// Output at UTF-8 comptabile function break it like json_encode will give 
    	// Error that its malformed utf-8 encoding charcter.This only happens in running
    	// test cases as locale is set to some other value.
    	// So either we should use mb_strtolower function to handle UTF-8 chars
    	// or  set locale properly so setting it back to 'C'
    	// This generally happens when system locale is set to invalid value.
    	setlocale(LC_ALL,'C');
        return require __DIR__.'/../bootstrap/app.php';

    }
}
