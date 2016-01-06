<?php

namespace yii\queue\tests;

use yii\queue\controllers\ThreadedController;
use Yii;

/**
 *
 */
class ThreadedTest extends TestCase
{
    public function setUp()
    {
        $this->mockApplication([
            'components' => [
                'queue' => [
                    'class' => 'yii\queue\SqlQueue',
                ],
                'db' => [
                    'class' => 'yii\db\Connection',
                    'dsn' => 'sqlite::memory:',
                    'charset' => 'utf8',
                ],
            ],
        ]);
    }

    protected function pushJobToQueue($data = 'test', $delay = 0, $testJobClass = 'yii\queue\tests\TestJob')
    {
        $job = new $testJobClass([
            'data' => $data,
        ]);

        return $job->push($delay);
    }

    public function testThreadedController()
    {
        $this->pushJobToQueue();
        $controller = new ThreadedController('controller', Yii::$app, [
            'timeout' => time(),
            'sleep' => 1,
        ]);
        $controller->actionListen();
        $this->assertFalse(\Yii::$app->queue->pop());
    }

    public function testConcurrentThreads()
    {
        for ($i = 0; $i < 10; ++$i) {
            $this->pushJobToQueue('test', 0, 'yii\queue\tests\LongTestJob');
        }
        $controller = new ThreadedController('controller', Yii::$app, [
            // 'timeout' => time(),
            // 'sleep' => 1,
        ]);
        $controller->actionListen();
        $this->assertFalse(\Yii::$app->queue->pop());
    }
}
