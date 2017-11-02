<?php
namespace Codeages\PhalconBiz;

class Pagination
{
    public $total;

    public $offset;

    public $limit;

    public function __construct($total, $offset, $limit)
    {
        $this->total = $total;
        $this->offset = $offset;
        $this->limit = $limit;
    }
}