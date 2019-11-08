<?php
namespace App\Worker;

use App\Biz\AppBiz;
use Codeages\Plumber\AbstractWorker;
use Codeages\Plumber\Queue\Job;
use Doctrine\DBAL\Connection;

class ExampleWorker extends AbstractWorker
{
    public function execute(Job $job)
    {
        $this->reconnectIfNecessary();

        // write your code.
        echo  "Hello, world!";

        return self::FINISH;
    }

    /**
     * @return AppBiz
     */
    private function getBiz()
    {
        return $this->container->get('biz');
    }

    protected function reconnectIfNecessary()
    {
        /** @var Connection $db */
        $db = $this->getBiz()['db'];
        if (false === $db->ping()) {
            $db->close();
            $db->connect();
            $this->logger->info("MySql connection is reconnected.");
        }
    }
}
