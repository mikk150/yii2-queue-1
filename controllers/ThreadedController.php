<?php

namespace yii\queue\controllers;

use yii\queue\WorkerThread;
use Yii;

/**
 *
 */
class ThreadedController extends QueueController
{
    /**
     * Maximum allowed concurrent threads.
     *
     * @var int
     */
    protected $maxThreads = 10;

    /**
     * array of threads.
     *
     * @var \Threads[]
     */
    private $_threads = [];

    /**
     * {@inheritdoc}
     */
    protected function process($queueName, $queueObjectName)
    {
        $queue = Yii::$app->{$queueObjectName};
        $job = $queue->pop($queueName);

        if ($job) {
            try {
                $jobObject = call_user_func($job['body']['serializer'][1], $job['body']['object']);
                $thread = new WorkerThread($job['body']['object']);
                $this->_threads[] = $thread;
                $thread->start();
                $queue->delete($job);

                return true;
            } catch (\Exception $e) {
                Yii::$app->getErrorHandler()->logException($e);
                
                return true;
            }
        }

        return false;
    }
    /**
     * {@inheritdoc}
     */
    public function actionListen($queueName = null, $queueObjectName = 'queue')
    {
        while (true) {
            echo 'Iteration'.PHP_EOL;
            $this->cleanThreads();
            if ($this->timeout !== null) {
                if ($this->timeout < time()) {
                    return true;
                }
            }
            if (!$this->process($queueName, $queueObjectName)) {
                sleep($this->sleep);
            }
        }
    }

    /**
     * cleans threads that has finished running, so new threads can be spawned.
     */
    public function cleanThreads()
    {
        $this->_threads = array_filter($this->_threads, function ($thread) {
            return $thread->isRunning;
        });
    }

    /**
     * {@inheritdoc}
     */
    public function beforeAction($action)
    {
        if (!parent::beforeAction($action)) {
            return false;
        }

        if (getenv('QUEUE_THREADS')) {
            $this->maxThreads = (int) getenv('QUEUE_MAX_THREADS');
        }

        return true;
    }
}
