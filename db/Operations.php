<?php
// +----------------------------------------------------------------------
// | QHPHP [ 代码创造未来，思维改变世界。 ] db数控库操作类
// +----------------------------------------------------------------------
// | Copyright (c) 2006~2018 https://www.astrocms.cn/ All rights reserved.
// +----------------------------------------------------------------------
// | Licensed ( http://www.apache.org/licenses/LICENSE-2.0 )
// +----------------------------------------------------------------------
// | Author: ZHAOSONG <1716892803@qq.com>
// +----------------------------------------------------------------------
namespace qhphp\db;
use PDO;
use Exception;

class Operations {

    /**
     * 批量插入数据
     * @param array $list 数据列表，每个元素是一个关联数组
     * @return bool 插入操作是否成功
     * @author zhaosong
     */
    public function insertAll(array $list) {
        $fields = implode(',', array_diff(array_keys($list[0]), [$this->getPrimaryKey()]));
        $sqlTemplate = "INSERT INTO {$this->table} ($fields) VALUES ";
        $placeholders = [];
        $values = [];

        foreach ($list as $row) {
            unset($row[$this->getPrimaryKey()]);
            $placeholders[] = '('. implode(',', array_fill(0, count($row), '?')). ')';
            $values = array_merge($values, array_values($row));
        }

        $sql = $sqlTemplate . implode(',', $placeholders);
        $stmt = $this->pdo->prepare($sql);
        return $stmt->execute($values);
    }

    /**
     * 设置字段值
     * @param array $data 关联数组，键是字段名，值是要设置的值
     * @return bool 更新操作是否成功
     * @author zhaosong
     */
    public function setField(array $data) {
        $setClause = implode(', ', array_map(function ($key) {
            return sprintf("`%s` = :%s", $key, $key);
        }, array_keys($data)));

        // 构建 SQL 语句
        $sql = "UPDATE {$this->table} SET {$setClause} {$this->where}";
        $stmt = $this->pdo->prepare($sql);
        $combinedData = array_combine(array_map(function ($key) {
            return ":$key";
        }, array_keys($data)), $data);

        foreach ($combinedData as $placeholder => $value) {
            $stmt->bindValue($placeholder, $value);
        }

        return $stmt->execute();
    }

    /**
     * 根据主键删除记录
     * @param mixed $ids 主键值，可以是单个值或数组
     * @return bool 删除操作是否成功
     * @author zhaosong
     */
    public function deletekey(mixed $ids) {
        $ids = is_array($ids) ? $ids : [$ids];
        $placeholders = implode(',', array_fill(0, count($ids), '?'));
        // 构建 SQL 语句
        $sql = "DELETE FROM {$this->table} WHERE {$this->getPrimaryKey()} IN ($placeholders)";

        $stmt = $this->pdo->prepare($sql);
        $stmt->execute($ids);
    }

    /**
     * 根据主键获取所有记录
     * @param mixed $ids 主键值，可以是单个值或数组
     * @return array 查询到的数据列表
     * @author zhaosong
     */
    public function selectall(mixed $ids) {
        if (is_array($ids)) {
            $placeholders = implode(',', array_fill(0, count($ids), '?'));
            $sql = "SELECT * FROM {$this->table} WHERE {$this->getPrimaryKey()} IN ($placeholders)";
            $stmt = $this->pdo->prepare($sql);
            foreach ($ids as $index => $id) {
                $stmt->bindParam($index + 1, $ids[$index], PDO::PARAM_INT);
            }
        } else {
            $sql = "SELECT * FROM {$this->table} WHERE {$this->getPrimaryKey()} =?";
            $stmt->bindParam(1, $ids, PDO::PARAM_INT);
        }

        $stmt->execute();

        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    /**
     * 更新主键对应的记录
     * @param array $data 关联数组，包含要更新的字段和值
     * @return bool 更新操作是否成功
     * @author zhaosong
     */
    public function updateKey(array $data) {
        $primaryKeyField = $this->getPrimaryKey();

        $setClauses = [];
        $params = [];
        foreach ($data as $key => $value) {
            $setClauses[] = "$key=?";
            $params[] = $value;
        }
        $set = implode(',', $setClauses);

        $whereClause = "$primaryKeyField=?";
        $params[] = $data[$primaryKeyField];

        $sql = "UPDATE {$this->table} SET $set WHERE $whereClause";

        $stmt = $this->pdo->prepare($sql);
        return $stmt->execute($params);
    }

    /**
     * 查询数据，如果不存在则抛出异常
     * @return array 查询到的数据
     * @throws Exception 当查询不到数据时抛出异常
     * @author zhaosong
     */
    public function findOrFail() {
        $sql = "SELECT {$this->field} FROM {$this->table} {$this->where} {$this->order} LIMIT 1";

        $stmt = $this->pdo->query($sql);
        $this->debug_addmsg($sql);
        $result = $stmt->fetch(PDO::FETCH_ASSOC);
        if (empty($result)) {
            throw new Exception(Lang('The query data does not exist!'));
        }
        return $result;
    }

    /**
     * 保存数据，如果存在主键则更新，否则插入新记录
     * @param array $data 要保存的数据
     * @return bool 保存操作是否成功
     * @author zhaosong
     */
    public function save(array $data) {
        $primaryKey = $this->getPrimaryKey();
        if (isset($data[$primaryKey])) {
            return $this->updateKey($data);
        } else {
            return $this->insert($data);
        }
    }

    /**
     * 获取指定表和字段的值列表
     *
     * @param string $field 要查询的字段名
     * @param string $table 要查询的表名
     * @param PDO    $pdo   数据库连接对象
     *
     * @return array 查询到的值列表
     * @author zhaosong
     */    
    public function column()
    {
        $sql = "SELECT {$this->field} FROM {$this->table}";
        if (!empty($this->where)) {
            $sql .= $this->where;
        }
        $stmt = $this->pdo->query($sql);
        $this->debug_addmsg($sql);
        
        $values = [];
        while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
            $values[] = $row[$this->field];
        }

        return $values;
    } 

    /**
     * 选择并返回多个表中的查询结果。
     *
     * @param array $tables 要查询的表名数组
     * @return array 查询结果的二维数组
     * @author zhaosong
     */
    public function selectResults(array $tables)
    {
        $data = [];
        foreach ($tables as $table) {
            $sql = "SELECT {$this->field} FROM {$table} {$this->where} {$this->order} {$this->limit}";
            $this->debug_addmsg($sql);
            $stmt = $this->pdo->query($sql);
            $data[] = $stmt->fetchAll(PDO::FETCH_ASSOC);
        }
    
        return $data;
    }

    /**
     * 构建子查询SQL（不执行，只返回SQL字符串）
     * @return string 子查询SQL
     */
    public function buildSql()
    {
        $sqlParts = [
            "SELECT {$this->field} FROM {$this->table}",
            $this->alias ? "AS {$this->alias}" : "",
            $this->join ? "{$this->join}" : "",
            $this->where ? "{$this->where}" : "",
            $this->group ? "{$this->group}" : "",
            $this->order ? "{$this->order}" : "",
            $this->limit ? "{$this->limit}" : ""
        ];
        
        $sql = implode(' ', array_filter($sqlParts));
        
        $this->reset();
        
        return "( $sql )";
    }
    
    /**
     * 重置查询条件
     */
    protected function reset()
    {
        $this->where = '';
        $this->field = '*';
        $this->order = '';
        $this->limit = '';
        $this->alias = '';
        $this->join = '';
        $this->group = '';
    }
    
    
    /**
     * 添加原生 SQL 条件
     * @param string $sql 原生SQL条件（可含占位符）
     * @param array $bindings 参数绑定（可选）
     * @return $this
     */
    public function whereRaw($sql, array $bindings = [])
    {
        // 处理参数绑定（防SQL注入）
        if (!empty($bindings)) {
            foreach ($bindings as $key => $value) {
                $quoted = $this->pdo->quote($value);
                $sql = preg_replace('/\?/', $quoted, $sql, 1);
            }
        }
    
        // 拼接 WHERE 条件
        if ($this->where) {
            $this->where .= " AND ($sql)";
        } else {
            $this->where = " WHERE ($sql)";
        }
        
        return $this;
    }
    
}
