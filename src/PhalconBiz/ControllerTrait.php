<?php
namespace Codeages\PhalconBiz;

trait ControllerTrait
{
    /**
     * 获取查询条件
     *
     * @param array $default 默认条件
     * @return array
     */
    public function conditions($default = [])
    {
        $conditions = array_merge($default, $this->request->getQuery());

        unset($conditions['offset']);
        unset($conditions['limit']);
        unset($conditions['sorts']);

        return $conditions;
    }

    /**
     * 获取查询排序方式
     *
     * @param array $default 默认排序方式
     * @return array
     */
    public function sorts($default = [])
    {
        $querySorts = $this->request->getQuery('sorts', 'string', '');
        if (empty($querySorts)) {
            return $default;
        }

        $querySorts = explode(',', $querySorts);

        $sorts = [];
        foreach($querySorts as $sort) {
            if (strpos($sort, '-') === 0) {
                $sorts[substr($sort, 1)] = 'DESC';
            } else {
                $sorts[$sort] = 'ASC';
            }
        }

        return $sorts;
    }

    /**
     * 获取查询分页对象
     *
     * @param int $total 条目总数
     * @return void
     */
    public function pagination($total)
    {
        $offset = $this->request->getQuery('offset', 'int', 0);
        $limit = $this->request->getQuery('limit', 'int', 30);

        return new Pagination($total, $offset, $limit);
    }

    /**
     * 转换单个实体对象
     *
     * @param array $item 实体对象
     * @param string $transformer 转换器名称
     * @return array
     */
    public function item($item, $transformer)
    {
        return $this->createTransformType($transformer)->transformItem($item);
    }

    /**
     * 转换实体对象集合
     *
     * @param array[] $items 实体对象集合
     * @param string $transformer 转换器名称
     * @param Pagination|null $pagination 分页对象
     * @return array
     */
    public function items($items, $transformer, $pagination = null)
    {
        $items = $this->createTransformType($transformer)->transformItems($items);
        if ($pagination) {
            return [
                'data' => $items,
                'paging' => [
                    'total' => $pagination->total,
                    'offset' => $pagination->offset,
                    'limit' => $pagination->limit,
                ]
            ];
        }

        return $items;
    }

    /**
     * 创建转换器对象
     *
     * @param string $name 转换器名称
     * @return TransformType
     */
    protected function createTransformType($name)
    {
        $id = 'response.transform_type.'.$name;
        if ($this->di->has($id)) {
            return $this->di->get($id);
        };

        if (\strpos(static::class, "\\") === false) {
            $namespace = "TransformType\\";
        } else {
            $namespace = \substr(static::class, 0, \strrpos(static::class, "\\")) . "\\TransformType\\";
        }

        $class = "{$namespace}{$name}Type";

        if (!class_exists($class)) {
            throw new \InvalidArgumentException("Response transform type class `{$class}` is not exist.");
        }

        $type = new $class($this->di);

        $this->di->set($id, $type);

        return $type;
    }

    protected function success()
    {
        return ['success' => true];
    }

    protected function throwNotFoundException($message)
    {
        throw new NotFoundException($message);
    }
}
