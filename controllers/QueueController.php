<?php

namespace yii\queue\controllers;

use Yii;
use yii\console\Controller;

/**
 * Queue Process Command.
 *
 * Class QueueController
 */
class QueueController extends Controller
{
    protected $timeout;
    protected $sleep = 5;

    /**
     * Process a job.
     *
     * @param string $queueName
     * @param string $queueObjectName
     *
     * @throws \Exception
     */
    public function actionWork($queueName = null, $queueObjectName = 'queue')
    {
        $this->process($queueName, $queueObjectName);
    }

    /**
     * Continuously process jobs.
     *
     * @param string $queueName
     * @param string $queueObjectName
     *
     * @return bool
     *
     * @throws \Exception
     */
    public function actionListen($queueName = null, $queueObjectName = 'queue')
    {
        while (true) {
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
     * Process one unit of job in queue.
     *
     * @param $queueName
     * @param $queueObjectName
     *
     * @return bool
     */
    protected function process($queueName, $queueObjectName)
    {
        $queue = Yii::$app->{$queueObjectName};
        $job = $queue->pop($queueName);

        if ($job) {
            try {
                $jobObject = call_user_func($job['body']['serializer'][1], $job['body']['object']);
                $queue->delete($job);
                $jobObject->run();

                return true;
            } catch (\Exception $e) {
                Yii::$app->getErrorHandler()->logException($e);
            }
        }

        return false;
    }

    /*
     * {@inheritdoc}
     */
    public function beforeAction($action)
    {
        if (!parent::beforeAction($action)) {
            return false;
        }

        if (getenv('QUEUE_TIMEOUT')) {
            $this->timeout = (int) getenv('QUEUE_TIMEOUT') + time();
        }
        if (getenv('QUEUE_SLEEP')) {
            $this->sleep = (int) getenv('QUEUE_SLEEP');
        }

        return true;
    }
}
