<?php
/**
 * @link http://www.yiiframework.com/
 * @copyright Copyright (c) 2008 Yii Software LLC
 * @license http://www.yiiframework.com/license/
 */

namespace yii\queue;

use yii\base\Component;

/**
 * NillQueue
 * Whatever you push here, gets thrown into garbage bin
 * Good for tests
 *
 * @author Mikk Tendermann <mikk150@gmail.com>
 */
class NullQueue extends Component implements QueueInterface
{
    /**
     * Pushs payload to the queue.
     *
     * @param mixed $payload
     * @param integer $delay
     * @param string $queue
     * @return string
     */
    public function push($payload, $queue, $delay = 0) {
        return 'test';
    }

    /**
     * Pops message from the queue.
     *
     * @param string $queue
     * @return array|false
     */
    public function pop($queue) {
        return false;
    }

    /**
     * Purges the queue.
     *
     * @param string $queue
     */
    public function purge($queue) {

    }

    /**
     * Releases the message.
     *
     * @param array $message
     * @param integer $delay
     */
    public function release(array $message, $delay = 0) {

    }

    /**
     * Deletes the message.
     *
     * @param array $message
     */
    public function delete(array $message) {

    }
}
