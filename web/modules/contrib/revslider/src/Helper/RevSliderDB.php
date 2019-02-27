<?php
/**
 * Created by FsFlex.
 * User: VH
 * Date: 6/26/2017
 * Time: 5:00 PM
 */

namespace Drupal\revslider\Helper;


use Drupal\Core\Database\Database;
use Drupal\Core\Database\Query\Condition;

class RevSliderDB
{
    const DRUPAL_TABLE_FILE_MANAGER = 'file_managed';
    const TABLE_CSS = 'revslider_css';
    const TABLE_OPTIONS = 'revslider_options';
    const TABLE_LAYER_ANIMATIONS = 'revslider_layer_animations';
    const TABLE_NAVIGATIONS = 'revslider_navigations';
    const TABLE_SLIDERS = 'revslider_sliders';
    const TABLE_SLIDES = 'revslider_slides';
    const TABLE_STATIC_SLIDERS = 'revslider_static_slides';

    public static $lastRowID = '';
    protected static $cache_data = array();

    protected $_table;
    protected $_select;
    protected $_where;
    protected $_limit;
    protected $_orderBy;
    protected $_groupBy;
    protected $_expression;
    //protected $_having;
    //protected $drupal_db_object;
    protected $_data;
    protected $test_data;
    public function __construct($args = array())
    {
        $this->test_data = $args;
        $this->_table = '';
        $this->_select = array();
        $this->_where = array(
            'condition' => 'AND',
            'params'    => array()
        );
        $this->_limit = array();
        $this->_orderBy = array();
        $this->_groupBy = array();
        $this->_expression = array();
        if (is_array($args)) {
            extract($args);
            if (!empty($table))
                $this->from($table);
            if (!empty($select))
                $this->select($select);
            if (!empty($where))
                $this->where($where);
            if (!empty($limit))
                $this->limit($limit);
            if (!empty($order_by))
                $this->orderBy($order_by);
            if (!empty($group_by))
                $this->groupBy($group_by);
            if (!empty($expression))
                $this->expression($expression);
        }
    }

    public static function query($query, $args = array(), $options = array())
    {
        return Database::getConnection()->query($query, $args, $options);
    }

    public static function instance($args)
    {
        return new RevSliderDB($args);
    }

    public function expression(array $expressions)
    {

        if (count($expressions) < 1)
            return $this;
        if (count($expressions) > 0 && is_string($expressions[0]) )
            $this->setExpression($expressions[0], $expressions[1]);
        foreach ($expressions as $expression)
        {
            if(is_array($expression))
                $this->expression($expression);
        }
        return $this;
    }

    protected function setExpression($expression, $alias)
    {
        if (!is_string($expression) || !is_string($alias) || empty($expression) || empty($alias))
            return false;
        $this->_expression[] = array($expression, $alias);
        return true;
    }

    public function from($table)
    {
        $this->_table = $table;
        return $this;
    }

    /**
     * @param array ...$fields , Drupal Use ONLY_FULL_GROUP_BY for MySQL
     * @return $this
     */
    public function groupBy(...$fields)
    {
        foreach ($fields as $field) {
            if (is_string($field) && !empty($field))
                $this->_groupBy[] = $field;
        }
        return $this;
    }

    public function orderBy(...$orders)
    {
        if (count($orders) < 1)
            return $this;
        if (is_string($orders[0])) {
            $this->setOrderBy($orders[0], (isset($orders[1])) ? $orders[1] : '');
        }
        if (is_array($orders[0])) {
            if (is_string($orders[0][0])) {
                $this->setOrderBy($orders[0][0], (isset($orders[0][1])) ? $orders[0][1] : '');
            }
            foreach ($orders[0] as $order) {
                if (is_array($order) && count($order) > 0)
                    $this->orderBy($order[0], isset($order[1]) ? $order[1] : '');
            }
        }
        return $this;
    }

    private function setOrderBy($col, $type = 'ASC')
    {
        if (!is_string($col) || empty($col))
            return false;
        $order_type = (is_string($type) && strtoupper($type) === 'DESC') ? 'DESC' : 'ASC';
        $this->_orderBy[] = array($col, $order_type);
        return true;
    }

    public function select(...$selects)
    {
        if (count($selects) === 1)
            $select = $selects[0];
        else
            $select = $selects;
        if (is_string($select))
            $this->_select[] = $select;
        if (is_array($select)) {
            foreach ($select as $item)
                if (is_string($item))
                    $this->_select[] = $item;
        }
        return $this;
    }

    public function where($col, ...$params)
    {
        $error_msg = 'Error where clause';
        if (is_array($col)) {
            $result = $this->readWhereGroup($col);
            if (empty($result))
            {
                RevSliderFunctions::throwError($error_msg . ' 1');
            }
            $this->_where['params'][] = $result;
        } elseif (is_string($col)) {
            $add = array($col);
            if (isset($params[0]))
                $add[] = $params[0];
            if (isset($params[1]))
                $add[] = $params[1];
            $result = $this->readWhereGroup($add);
            if (empty($result))
            {
                RevSliderFunctions::throwError($error_msg . ' 2');
            }
            $this->_where['params'][] = $result;
        } else
            RevSliderFunctions::throwError($error_msg . ' 3');
        return $this;
    }

    protected function readWhereGroup(array $group)
    {
        $condition = 'AND';
        if (isset($group['condition'])) {
            $con = $group['condition'];
            if (is_string($con) && strtolower($con) === 'or')
                $condition = 'OR';
            unset($group['condition']);
        } elseif(is_string($group[0])) {
            if (count($group) == 2)
                if (strtolower($group[1]) === 'is null')
                    return array($group[0], 'is null');
                elseif (strtolower($group[1]) === 'is not null')
                    return array($group[0], 'is not null');
                else
                    return array($group[0], '=', $group[1]);
            elseif (count($group) > 2)
                return array($group[0], $group[1], $group[2]);
            else
                return null;
        }
        $where_group = array(
            'condition' => $condition,
            'params'    => array()
        );
        foreach ($group as $item) {
            if (is_array($item) && isset($item[0])) {
                if (is_array($item[0]))
                    $result_read = $this->readWhereGroup($where_group);
                else
                    $result_read = $this->readWhereGroup($item);
                if (!empty($result_read))
                    $where_group['params'][] = $result_read;
            }
        }
        if (empty($where_group['params']))
            $where_group = null;
        return $where_group;
    }

    public function limit($start, $length = '')
    {
        if (empty($length)) {
            if (is_array($start)) {
                $this->_limit = array($start[0], $start[1]);
            } else
                $this->_limit = array('0', $start);
        } else
            $this->_limit = array($start, $length);
        return $this;
    }

    public function get($return_element_type = 'array')
    {
        $hash = $this->getHash();
        if (key_exists($hash, self::$cache_data))
            $data = self::$cache_data[$hash];
        else
            $data = $this->executeSelectQuery();
        switch ($return_element_type) {
            case 'array': {
                $data = RevSliderFunctions::toArray($data);
            }
        }
        return $data;
    }

    public function is_queried()
    {
        $hash = $this->getHash();
        if (key_exists($hash, self::$cache_data))
            return true;
        return false;
    }

    public function first()
    {
        if (!$this->is_queried())
            $this->limit(1);
        $data = $this->get();
        if (empty($data))
            return null;
        else
            return $data[0];
    }

    protected function executeSelectQuery()
    {
        $table = $this->_table;
        $select = $this->_select;
        $where = $this->_where;
        $orderBy = $this->_orderBy;
        $groupby = $this->_groupBy;
        $expression = $this->_expression;
        $limit = $this->_limit;
        $table_alias = 'tb1';
        //from | table
        $query = Database::getConnection()->select($table, $table_alias);
        //select
        if (empty($select)) {
            if (empty($expression))
                $query->fields($table_alias);
        } else
            $query->fields($table_alias, $select);
        //expression
        foreach ($expression as $exp) {
            $query->addExpression($exp[0], $exp[1]);
        }
        //where
        if(!empty($where['params']))
            $query->condition($this->buildWhereCondition($where));
        //orderBy
        foreach ($orderBy as $order) {
            $query->orderBy($table_alias . '.' . $order[0], $order[1]);
        }
        //groupBy
        foreach ($groupby as $group_field) {
            $query->groupBy($table_alias . '.' . $group_field);
        }
        //limit
        if (!empty($limit))
            $query->range($limit[0], $limit[1]);
        $data = $query->execute()->fetchAll();
        $this->saveQueryData($data);
        return $data;
    }

    protected function buildWhereCondition(array $where)
    {
        $con_key = !isset($where['condition']) ? 'AND' : $where['condition'];
        switch ($con_key) {
            case 'OR' : {
                $con = new Condition('OR');
                break;
            }
            default : {
                $con = new Condition('AND');
            }
        }
        foreach ($where['params'] as $item) {
            if (key_exists('params', $item))
                $con->condition($this->buildWhereCondition($item));
            elseif ($item[1] === 'is null')
                $con->isNull($item[0]);
            elseif ($item[1] === 'is not null')
                $con->isNotNull($item[0]);
            else
                $con->condition($item[0], $item[2], $item[1]);

        }
        return $con;
    }

    protected function saveQueryData($data)
    {
        $hash = $this->getHash();
        self::$cache_data[$hash] = $data;
    }
    public static function clearCache($table)
    {
        $keys = array_keys(self::$cache_data);
        if($table = '*')
        {
            self::$cache_data = array();
        }
        else
        {
            foreach ($keys  as $key)
            {
                if(explode('-',$key)[1] === $table)
                    unset(self::$cache_data[$key]);
            }
        }
        return true;
    }
    public function getHash()
    {
        $table = $this->_table;
        $select = $this->_select;
        $where = $this->_where;
        $limit = $this->_limit;
        $orderBy = $this->_orderBy;
        $expression = $this->_expression;
        $groupBy = $this->_groupBy;
        $table = trim($table);
        sort($select);
        $this->sortWhere($where);
        sort($limit);
        sort($expression);
        $key_raw = array(
            'table'      => $table,
            'select'     => $select,
            'where'      => $where,
            'limit'      => $limit,
            'orderBy'    => $orderBy,
            'groupBy'    => $groupBy,
            'expression' => $expression
        );
        return md5(serialize($key_raw)).'-'.$table;
    }
    public static function getCache()
    {
        return self::$cache_data;
    }
    protected function sortWhere(&$where)
    {
        $keys = array_keys($where['params']);
        foreach ($keys as $key)
            if (key_exists('params', $where['params'][$key]))
                $this->sortWhere($where['params'][$key]);
        sort($where);
    }

    public static function table($table_name)
    {
        return new RevSliderDB(array('table' => $table_name));
    }

    public function insert($data, $rollback_if_any_fail = true)
    {
        if (empty($this->_table))
            return false;
        $trans = Database::getConnection()->startTransaction();
        $result = @$this->executeInsertQuery($data);
        if ($rollback_if_any_fail && $result === false)
            $trans->rollBack();
        self::clearCache($this->_table);
        return $result;
    }

    protected function executeInsertQuery($data)
    {
        $conn = Database::getConnection();
        $query = $conn->insert($this->_table);
        $values = $data;
        if (isset($data[0]) && is_array($data[0])) {
            $fields = array_keys($data[0]);
            $query->fields($fields);
            foreach ($values as $row) {
                $query->values($row);
            }
        } else {
            $query->fields($values);
        }
        $result = $query->execute();
        self::$lastRowID = $result;
        $this->clearCacheBlockList();
        return $result;
    }

    /**
     * @param $data
     * @param bool $rollback_if_any_fail
     * @return false if fail and count row effect if success
     */
    public function update($data, $rollback_if_any_fail = true)
    {
        if (empty($this->_table) || empty($this->_where))
            return false;

        $trans = Database::getConnection()->startTransaction();
        $result = @$this->executeUpdateQuery($data);
        if ($rollback_if_any_fail && $result === false)
            $trans->rollBack();
        return $result;
    }

    protected function executeUpdateQuery($data)
    {
        switch ($this->_table)
        {
            case self::TABLE_SLIDERS:
                $this->clearCacheBlockOnUpdateSlider();
                $this->checkEditBlockList($data);
                break;
            case self::TABLE_SLIDES:
                $this->clearCacheBlockOnUpdateSlide();
                break;
        }

        $where = $this->_where;
        $conn = Database::getConnection();
        $query = $conn->update($this->_table);
        $query->condition($this->buildWhereCondition($where));
        $query->fields($data);
        $result = $query->execute();
        self::clearCache($this->_table);
        return $result;
    }

    public function delete($rollback_if_any_fail = true)
    {
        if (empty($this->_table) || empty($this->_where))
            return false;
        $trans = Database::getConnection()->startTransaction();
        $result = @$this->executeDeleteQuery();
        if ($rollback_if_any_fail && $result === false)
            $trans->rollBack();
        return $result;
    }

    protected function executeDeleteQuery()
    {
        switch ($this->_table)
        {
            case self::TABLE_SLIDERS:
                $this->clearCacheBlockOnUpdateSlider();
                break;
            case self::TABLE_SLIDES:
                $this->clearCacheBlockOnUpdateSlide();
                break;
        }
        $where = $this->_where;
        $conn = Database::getConnection();
        $query = $conn->delete($this->_table);
        $query->condition($this->buildWhereCondition($where));
        $result = $query->execute();
        self::clearCache($this->_table);
        $this->clearCacheBlockList();
        return $result;
    }


    public function count()
    {
        if ($this->is_queried())
        {
            $data = $this->get();
            return count($data);
        }
        else
        {
            $this->_select = array();
            $this->expression(array('count(*)','count'));
            $result = $this->first();
            return $result['count'];
        }

    }
    //clear cache block list
    protected function clearCacheBlockList()
    {
        $cache = \Drupal::cache('discovery');
        $cache->delete('block_plugins');
    }
    //clear cache
    protected function checkEditBlockList($data)
    {
        $block_list_use_fields = array('title','alias');
        $check = false;
        foreach ($block_list_use_fields as $field)
        {
            if(array_key_exists($field,$data))
            {
                $check = true;
                break;
            }
        }
        if($check)
            $this->clearCacheBlockList();
    }
    protected function clearCacheBlockOnUpdateSlider()
    {
        $this->select('alias');
        $result = $this->executeSelectQuery();
        if(empty($result))
            return;
        $alias = $result[0]->alias;
        $tag = 'revslider_'.$alias;
        $this->clearCacheTag($tag);
    }
    protected function clearCacheBlockOnUpdateSlide()
    {
        $this->select('slider_id');
        $result = $this->executeSelectQuery();
        if(empty($result))
            return;
        $id = $result[0]->slider_id;
        $slider = RevSliderDB::instance(array(
            'table'=>self::TABLE_SLIDERS,
            'where'=>array('id',$id),
            'select'=>array('alias')
        ))->first();

        $alias = $slider['alias'];
        $tag = 'revslider_'.$alias;
        $this->clearCacheTag($tag);
    }
    protected function clearCacheTag($tag)
    {
        \Drupal::service('cache_tags.invalidator')->invalidateTags([$tag]);
    }
}