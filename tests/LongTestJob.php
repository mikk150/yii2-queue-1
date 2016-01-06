<?php

namespace yii\queue\tests;

class LongTestJob extends TestJob
{
    public function run()
    {
        while (true) {
            echo 'Job running';
            sleep(1);
        }

        return $this->data;
    }
}
