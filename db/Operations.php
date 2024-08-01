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
}