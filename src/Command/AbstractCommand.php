<?php

namespace App\Command;

use Symfony\Component\Console\Command\Command;

class AbstractCommand extends Command
{
    protected $biz;

    public function __construct($biz)
    {
        parent::__construct();
        $this->biz = $biz;
    }
}
