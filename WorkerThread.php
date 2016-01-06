<?php

namespace yii\queue;

use Thread;

/**
 * This is container for Threaded worker, it will run jobs in parallel if running in real pthreads not polyfill one.
 */
class WorkerThread extends Thread
{
    private $_job;

    public function __construct(ActiveJob $job)
    {
        $this->_job = $job;
    }

    public function run()
    {
        echo 'Running job'.PHP_EOL;
        var_dump($this->_job);
        $this->_job->run();
    }
}
