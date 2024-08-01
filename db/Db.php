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
use qhphp\config\Config;
use qhphp\page\Pagination;
use Exception;
use qhphp\debug\Debug;
class Db extends Operations
{
    private $host; // 数据库主机
    private $user; // 数据库用户名
    private $password; // 数据库密码
    private $database; // 数据库名
    private $charset = 'utf8'; // 数据库字符集
    private $prefix; // 数据库表前缀
    protected $pdo; // PDO对象
    private $query; // 查询对象
    protected $table; // 当前操作的表名
    private $params = []; // 参数数组
    protected $where = ''; // 查询条件
    protected $field = '*'; // 查询字段
    protected $order = ''; // 排序字段
    protected $limit = ''; // 查询限制
    protected $alias; // 表别名
    protected $join; // 连接查询语句
    protected $page; // 分页查询
    protected $group;// 设置分组字段
    protected $paginate_params;
    protected $cache;
 
    public function __construct()
    {
        $this->host = C('db_host');
        $this->user = C('db_user');
        $this->password = C('db_pwd');
        $this->database = C('db_name');
        $this->prefix = C('db_prefix');
        
        try {
            $dsn = "mysql:host={$this->host};dbname={$this->database};charset={$this->charset}";
            $this->pdo = new PDO($dsn, $this->user, $this->password);
            $this->pdo->setAttribute(PDO::ATTR_ERRMODE, C('db_errmode') ? PDO::ERRMODE_SILENT:PDO::ERRMODE_EXCEPTION);
            $sql_mode = C('db_sql_mode');
            if(!empty($sql_mode)){
                $this->pdo->exec("SET SESSION sql_mode = '$sql_mode'");
            }
        } catch (PDOException $e) {
            throw new Exception($e->getMessage(),500);
        }
    }
     /**
     * 设置当前操作的表名
     *
     * @param string $table 表名
     * @return object 返回当前类实例，以支持链式调用
     */
    public function table($table)
    {
        $this->table = $this->prefix.$table;
        return $this;
    }

    /**
     * 设置查询条件
     *
     * @param mixed $condition 查询条件，可以是一个字符串或一个关联数组
     * @param array $params 参数数组，用于绑定条件中的参数
     * @return object 返回当前类实例，以支持链式调用
     */
    public function where($condition, $params = [])
    {
        if (is_array($condition)) {
            $conditions = [];
            foreach ($condition as $key => $value) {
                if ($value === null) {
                    continue; // 跳过 null 值
                }
                
                if (is_array($value) && isset($value['in']) && is_array($value['in'])) {
                    $inValues = implode("', '", $value['in']);
                    $conditions[] = "$key IN ('$inValues')";
                    continue;
                }
               
                if (is_array($value) && $value[0] === 'between' && is_array($value[1])) {
                    $startTime = removeLastTenCharacters($value[1][0]);
                    $endTime = removeLastTenCharacters($value[1][1]);
                    $conditions[] = "$key BETWEEN '$startTime' AND '$endTime'";
                    continue;
                }
                
                if (is_array($value) && count($value) === 2) {
                    $operator = $value[0];
                    $operand = $value[1];
                    
                    if (is_int($operand)) {
                        $conditions[] = "$key $operator $operand";
                    } else {
                        $conditions[] = "$key $operator '$operand'";
                    }
                    continue;
                }else {
                    $conditions[] = "`$key`='$value'";
                }
                
            }
            if (!empty($conditions)) {
                $this->where = " WHERE " . implode(' AND ', $conditions);
            }
        } else {
            $this->where = " WHERE $condition";
        }
    
        if (!empty($params)) {
            $this->where = $this->bindParams($this->where, $params);
        }
    
        return $this;
    }


    /**
     * 设置查询字段
     *
     * @param string $field 查询字段
     * @return object 返回当前类实例，以支持链式调用
     */
    public function field($field)
    {
        $this->field = $field;
        return $this;
    }
    
    
    /**
     * 设置分组字段
     *
     * @param string $field 要分组的字段名
     * @return $this 返回当前对象
     */
    public function group($field)
    {
        $this->group = "GROUP BY $field";
        return $this;
    }
    
    /**
     * 设置排序字段
     *
     * @param string $order 排序字段
     * @return object 返回当前类实例，以支持链式调用
     */
    public function order($order)
    {
        $this->order = " ORDER BY $order";
        return $this;
    }
    
    /**
     * 设置查询限制
     *
     * @param string $limit 查询限制
     * @return object 返回当前类实例，以支持链式调用
     */
    public function limit($limit)
    {
        $this->limit = " LIMIT $limit";
        return $this;
    }
    
    /**
     * 设置缓存键值对，并设置过期时间（默认60秒）。
     * 
     * @param string $key 缓存的键名
     * @param int $expireTime 缓存的过期时间（单位：秒），默认为60秒
     * @return $this 返回当前对象，以便链式调用
     * @author zhaosong
     */
    public function cache($key, $expireTime = 60)
    {
        $this->cache = [
            'key' => $key,
            'expire' => $expireTime
        ];
        return $this;
    }
    
    /**
     * 获取缓存数据，如果缓存不存在则将数组数据存入缓存。
     * 
     * @param array $array 要缓存的数据数组
     * @return mixed 返回缓存数据或新存入的数组数据
     * @author zhaosong
     */
    public function cachedata($array)
    {
        $cacheKey = $this->cache['key'];
        $expireTime = $this->cache['expire'];
        $data = Cache()->get($cacheKey);
        
        if (empty($data)) {
            Cache()->set($cacheKey, $array, $expireTime);
            $data = $array;
        }
        
        return $data;
    }
    
    
    /**
     * 执行SELECT查询并返回结果集
     *
     * @return array 返回查询结果集
     */
    public function select()
    {
        $sqlParts = [
            "SELECT {$this->field} FROM {$this->table}",
            $this->alias ? "{$this->alias}" : "",
            $this->join ? "{$this->join}" : "",
            $this->where ? "{$this->where}" : "",
            $this->group ? "{$this->group}" : "",
            $this->order ? "{$this->order}" : "",
            $this->limit ? "{$this->limit}" : "",
            $this->page ? "{$this->page}" : ""
        ];
        
        $sql = implode(' ', array_filter($sqlParts));
        
        $this->debug_addmsg($sql);
        $stmt = $this->pdo->query($sql);
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    /**
     * 执行SELECT查询并返回单条结果
     *
     * @return array|null 返回查询结果的关联数组，如果没有结果则返回null
     */
    public function find()
    {
        $sql = "SELECT {$this->field} FROM {$this->table} {$this->where} {$this->order} LIMIT 1";
        
        $stmt = $this->pdo->query($sql);
        $this->debug_addmsg($sql);
        return $stmt->fetch(PDO::FETCH_ASSOC);
    }
    
    /**
     * 获取数据表的字段
     *
     * @return array 数据表字段
     * @author zhaosong
     */
    protected function getTableFields()
    {
        $sql = "SHOW COLUMNS FROM {$this->table}";
        $stmt = $this->pdo->query($sql);
        $columns = $stmt->fetchAll(PDO::FETCH_ASSOC);
    
        $fields = [];
        foreach ($columns as $column) {
            $fields[] = $column['Field'];
        }
    
        return $fields;
    }
    
    /**
     * 过滤非表字段
     *
     * @param array $data 要过滤的数据
     * @param array $fields 表字段
     * @return array 过滤后的数据
     * @author zhaosong
     */
    protected function filterFields($data, $fields)
    {
        $filteredData = [];
        foreach ($data as $key => $value) {
            if (in_array($key, $fields)) {
                $filteredData[$key] = $value;
            }
        }
    
        return $filteredData;
    }
    

    /**
     * 执行INSERT操作
     *
     * @param array $data 要插入的数据，以字段名为键，字段值为值的关联数组
     * @return int 返回受影响的行数
     */
    public function insert($data)
    {
        if(!empty($data[$this->getPrimaryKey()])){
            unset($data[$this->getPrimaryKey()]);
        }
        $fields = implode(',', array_keys($data));
        
        $values = implode(',', array_map(function ($value) {
            return "'" . $value . "'";
        }, array_values($data)));
    
        $sql = "INSERT INTO {$this->table} ($fields) VALUES ($values)";
        return $this->pdo->exec($sql);
    }
    
    /**
     * 添加数据
     *
     * @param array $data 要添加的数据，以字段名为键，字段值为值的关联数组
     * @param bool $return_id 是否返回自增主键，默认为 false
     * @return int|string 返回受影响的行数或自增主键
     */
    public function insertGetId($data)
    {
        // 获取表字段
        $tableFields = $this->getTableFields();
    
        // 过滤非表字段
        $filteredData = $this->filterFields($data, $tableFields);
    
        $fields = array_keys($filteredData);
        $values = array_values($filteredData);
    
        $sql = "INSERT INTO {$this->table} (" . implode(',', $fields) . ") VALUES (" . implode(',', array_map(function($field) {
            return ":$field";
        }, $fields)) . ")";
    
        $stmt = $this->pdo->prepare($sql);
    
        foreach ($filteredData as $key => $value) {
            $stmt->bindValue(":$key", $value);
        }
    
        $stmt->execute();
    
        return $this->pdo->lastInsertId();
    }
    
    /**
     * 获取数据库表的信息。
     * 
     * @param string $type 需要获取的表信息类型，可选值有：'fields'（字段名），'type'（字段类型），'pk'（主键），默认为空表示获取所有信息
     * @return mixed 根据$type返回对应的表信息，如果$type为空则返回表中所有数据
     * @author zhaosong
     */
    public function getTableInfo($type = '')
    {
        $table = $this->table;
        
        switch ($type) {
            case 'fields':
                // 查询表的字段名
                $stmt = $this->pdo->query("SELECT COLUMN_NAME FROM INFORMATION_SCHEMA.COLUMNS WHERE TABLE_SCHEMA = DATABASE() AND TABLE_NAME = '$table'");
                $fields = $stmt->fetchAll(PDO::FETCH_COLUMN);
                return $fields;
            case 'type':
                // 查询表的字段名和对应的数据类型
                $stmt = $this->pdo->query("SELECT COLUMN_NAME, DATA_TYPE FROM INFORMATION_SCHEMA.COLUMNS WHERE TABLE_SCHEMA = DATABASE() AND TABLE_NAME = '$table'");
                $types = $stmt->fetchAll(PDO::FETCH_ASSOC);
                return $types;
            case 'pk':
                // 查询表的主键字段名
                $stmt = $this->pdo->query("SELECT COLUMN_NAME FROM INFORMATION_SCHEMA.KEY_COLUMN_USAGE WHERE TABLE_SCHEMA = DATABASE() AND TABLE_NAME = '$table' AND CONSTRAINT_NAME = 'PRIMARY'");
                $pk = $stmt->fetchAll(PDO::FETCH_COLUMN);
                return $pk;
            default:
                // 查询表中所有数据
                $stmt = $this->pdo->query("SELECT * FROM $table");
                $allInfo = $stmt->fetchAll(PDO::FETCH_ASSOC);
                return $allInfo;
        }
    }
    
    /**
     * 获取表的主键名。
     *
     * @return string 主键名
     */
    public function getPrimaryKey()
    {
        return $this->getTableInfo('pk')[0];
    }
    
    /**
     * 执行UPDATE操作
     *
     * @param array $data 要更新的数据，以字段名为键，字段值为值的关联数组
     * @return int 返回受影响的行数
     */
    public function update($data)
    {
        $set = implode(',', array_map(function ($key, $value) {
            return "$key='$value'";
        }, array_keys($data), array_values($data)));
        
        $sql = "UPDATE {$this->table} SET $set {$this->where}";
        return $this->pdo->exec($sql);
    }
    
    /**
     * 更新表中的数据
     *
     * @param array $data 要更新的数据
     * @return int 受影响的行数
     * @author zhaosong
     */
    public function updateFilter($data)
    {
        // 获取表字段
        $tableFields = $this->getTableFields();
        
        // 过滤非表字段
        $filteredData = $this->filterFields($data, $tableFields);
        
        // 构建 SET 子句
        $setClause = implode(', ', array_map(function($field) {
            return "$field = :$field";
        }, array_keys($filteredData)));
        
        $sql = "UPDATE {$this->table} SET $setClause {$this->where}";
        
        $stmt = $this->pdo->prepare($sql);
        
        // 绑定更新数据
        foreach ($filteredData as $key => $value) 
        {
            $stmt->bindValue(":$key", $value);
        }
        
        return $stmt->execute();
    }
    
    /**
     * 执行DELETE操作
     *
     * @param string|null $condition 删除条件，默认为null，表示删除所有记录
     * @return int 返回受影响的行数
     */
    public function delete($condition = null)
    {
        if ($condition !== null) {
            $this->where($condition);
        }
    
        $sql = "DELETE FROM {$this->table} {$this->where}";
        return $this->pdo->exec($sql);
    }
    
    
    /**
     * 执行DELETE操作
     *
     * @param mixed $id 主键值，可以是单个值或值的数组
     * @return int 返回受影响的行数
     */
    public function destroy($id = null)
    {
        if ($id !== null) {
            if (is_array($id)) {
                $where = $this->getPrimaryKey() . ' IN (' . implode(',', $id) . ')';
            } else {
                $where = $this->getPrimaryKey() . ' = ' . $id;
            }
        }
    
        $sql = "DELETE FROM {$this->table} WHERE {$where}";
        return $this->pdo->exec($sql);
    }
    
    /**
     * 获取单个字段的值
     *
     * @param string $field 字段名
     * @return mixed 返回字段值，如果没有结果则返回null
     */
    public function value($field)
    {
        $this->field = $field;
        $sql = "SELECT {$this->field} FROM {$this->table} {$this->where} {$this->order} LIMIT 1";
        $this->debug_addmsg($sql);
        $stmt = $this->pdo->query($sql);
        $result = $stmt->fetch(PDO::FETCH_ASSOC);
        return $result[$this->field] ?? null;
    }
    
    /**
     * 设置表的别名
     *
     * @param string $alias 别名
     * @return object 返回当前类实例，以支持链式调用
     */
    public function alias($alias)
    {
        $this->alias = "AS $alias";
        return $this;
    }
    
    /**
     * 添加JOIN子句
     *
     * @param string $table 表名
     * @param string $condition JOIN条件
     * @param string $type JOIN类型，默认为'INNER'
     * @return object 返回当前类实例，以支持链式调用
     */
    public function join($table, $condition, $type = 'INNER')
    {
        $this->join .= " $type JOIN $table ON $condition";
        return $this;
    }
    
    /**
     * 设置分页
     *
     * @param int $page 当前页码
     * @param int $pageSize 每页显示的记录数
     * @return object 返回当前类实例，以支持链式调用
     */
    public function page($page, $pageSize)
    {
        $offset = (($page - 1) * $pageSize);
        $this->page = " LIMIT $offset, $pageSize";
        return $this;
    }

    
        
    /**
     * 获取指定字段的记录数
     *
     * @param string $field 字段名，默认为'*'，表示所有字段
     * @return int 返回记录数
     */
    public function count($field = '*')
    {
        $sql = "SELECT COUNT({$field}) FROM {$this->table}";
    
        if ($this->where) {
            $sql .= " {$this->where}";
        }
        $this->debug_addmsg($sql);
        $stmt = $this->pdo->query($sql);
        return $stmt->fetchColumn();
    }
    
    /**
     * 获取指定字段的最大值
     *
     * @param string $field 字段名
     * @return mixed 返回最大值，如果没有结果则返回null
     */
    public function max($field)
    {
        $sql = "SELECT MAX({$field}) FROM {$this->table}";
    
        if ($this->where) {
            $sql .= "{$this->where}";
        }
        $this->debug_addmsg($sql);
        $stmt = $this->pdo->query($sql);
        return $stmt->fetchColumn();
    }
    
    /**
     * 获取指定字段的最小值
     *
     * @param string $field 字段名
     * @return mixed 返回最小值，如果没有结果则返回null
     */
    public function min($field)
    {
        $sql = "SELECT MIN({$field}) FROM {$this->table}";
    
        if ($this->where) {
            $sql .= " {$this->where}";
        }
        $this->debug_addmsg($sql);
        $stmt = $this->pdo->query($sql);
        return $stmt->fetchColumn();
    }
    
    /**
     * 获取指定字段的平均值
     *
     * @param string $field 字段名
     * @return mixed 返回平均值，如果没有结果则返回null
     */
    public function avg($field)
    {
        $sql = "SELECT AVG({$field}) FROM {$this->table}";
    
        if ($this->where) {
            $sql .= "{$this->where}";
        }
        $this->debug_addmsg($sql);
        $stmt = $this->pdo->query($sql);
        return $stmt->fetchColumn();
    }
    
    /**
     * 获取指定字段的总和
     *
     * @param string $field 字段名
     * @return mixed 返回总和，如果没有结果则返回null
     */
    public function sum($field)
    {
        $sql = "SELECT SUM({$field}) FROM {$this->table}";
    
        if ($this->where) {
            $sql .= "{$this->where}";
        }
        $this->debug_addmsg($sql);
        $stmt = $this->pdo->query($sql);
        return $stmt->fetchColumn();
    }
    
    /**
     * 字段自增
     *
     * @param string $field 字段名
     * @param int $value 自增值，默认为1
     * @return int 返回受影响的行数
     */
    public function setInc($field, $value = 1)
    {
        $sql = "UPDATE {$this->table} SET {$field} = {$field} + {$value}";
    
        if ($this->where) {
            $sql .= "{$this->where}";
        }
        $this->debug_addmsg($sql);
        $stmt = $this->pdo->query($sql);
        return $stmt->rowCount();
    }
    
    /**
     * 字段自减
     *
     * @param string $field 字段名
     * @param int $value 自减值，默认为1
     * @return int 返回受影响的行数
     */
    public function setDec($field, $value = 1)
    {
        $sql = "UPDATE {$this->table} SET {$field} = {$field} - {$value}";
    
        if ($this->where) {
            $sql .= "{$this->where}";
        }
        $this->debug_addmsg($sql);
        $stmt = $this->pdo->query($sql);
        return $stmt->rowCount();
    }
    
    /**
     * 绑定参数
     *
     * @param string $sql SQL语句
     * @param array $params 参数数组，以参数名为键，参数值为值的关联数组
     * @return string 返回绑定参数后的SQL语句
     */
    private function bindParams($sql, $params)
    {   
        foreach ($params as $key => $value) {
            $sql = str_replace(":$key", $this->pdo->quote($value), $sql);
        }
        return $sql;
    }
    
    /**
	 * 返回 MySQL 服务器版本信息
	 * @return string 
	 */	
	public function version(){
	    return $this->pdo->getAttribute(PDO::ATTR_SERVER_VERSION);	
	}
	
    /**
     * 检查数据表是否存在
     *
     * @param string $table 数据表名
     * @return bool 如果数据表存在，返回 true，否则返回 false
     * @author zhaosong
     */
    public function table_exists($table)
    {
        $table = C('db_prefix').$table;
        $sql = "SHOW TABLES LIKE '$table'";
        $result = $this->pdo->query($sql);
        if ($result->rowCount() > 0) {
            return true;
        } else {
            return false;
        }
    }
    
    /**
     * 添加调试信息
     * @param string $sql 要添加的 SQL 语句
     * @author zhaosong
     */
    public function debug_addmsg($sql)
    {
        // 当 APP_DEBUG 为 true 时，调用 dCreatedebug()->addmsg() 方法添加调试信息
        // 参数1：$sql - 要添加的 SQL 语句
        // 参数2：1 - 消息类型（具体类型可根据实际项目需求调整）
        // 参数3：microtime(true) - 当前微秒时间戳，用于记录消息添加的时间
        APP_DEBUG && Createdebug()::addmsg($sql, 1, microtime(true));
    }
    
    /**
     * 执行自定义 SQL 语句
     *
     * @param string $sql 自定义 SQL 语句
     * @param array $params 参数数组，以参数名为键，参数值为值的关联数组
     * @return mixed 返回执行结果，如果是查询语句返回数据数组，如果是更新或删除语句返回受影响的行数
     */
    public function query($sql, $params = [])
    {
        $this->debug_addmsg($sql);
        // 绑定参数
        $sql = $this->bindParams($sql, $params);
        // 执行 SQL 语句
        $stmt = $this->pdo->query($sql);
        // 如果是查询语句，返回数据数组
        if (strpos($sql, 'SELECT') === 0) {
            return $stmt->fetchAll(PDO::FETCH_ASSOC);
        }
        // 如果是更新或删除语句，返回受影响的行数
        $sql_command = strtoupper(substr($sql, 0, 6));

        switch ($sql_command) {
            case 'UPDATE':
            case 'DELETE':
            case 'CREATE':
            case 'DROP':
            case 'TRUNCATE':
            case 'ALTER':
            case 'FLUSH':
            case 'INSERT':
            case 'REPLACE':
            case 'SET':
            case 'CREATE':
                return $stmt->rowCount();
            default:
                return $stmt->fetchAll(PDO::FETCH_ASSOC);
                break;
        }
        
        if(preg_match("/^(?:SHOW|REPAIR|OPTIMIZE)\\s+/i", $sql)){
			return $stmt->fetchAll(PDO::FETCH_ASSOC);	 
		}
		
		return false;
        
    }
    
    /**
     * 添加时间范围查询条件
     *
     * @param string $field 字段名
     * @param string $expression 时间表达式，可选值为 'today'、'yesterday'、'week'、'last week'、'month'、'last month'、'year'、'last year' 或其他时间表达式
     * @return object 返回当前类实例，以支持链式调用
     */
    
    public function whereTime($field, $expression)
    {
        $currentTime = time();
        $start = '';
        $end = '';
    
        switch ($expression) {
            case 'today':
                $start = Times()::today()[0];
                $end = Times()::today()[1];
                break;
            case 'yesterday':
                $start = Times()::yesterday()[0];
                $end = Times()::yesterday()[1];
                break;
            case 'week':
                $start = Times()::week()[0];
                $end = Times()::week()[1];
                break;
            case 'last week':
                $start = Times()::lastWeek()[0];
                $end = Times()::lastWeek()[1];
                break;
            case 'month':
                $start = Times()::month()[0];
                $end = Times()::month()[1];
                break;
            case 'last month':
                $start = Times()::lastMonth()[0];
                $end = Times()::lastMonth()[1];
                break;
            case 'year':
                $start = Times()::year()[0];
                $end = Times()::year()[1];
                break;
            case 'last year':
                $start = Times()::lastYear()[0];
                $end = Times()::lastYear()[1];
                break;
            default:
                // 其他时间表达式的处理
                break;
        }
    
        $this->where .= "WHERE {$field} BETWEEN '{$start}' AND '{$end}'";
        return $this;
    }
    

    /**
     * 分页参数
     *
     * @param string $perPage 显示数
     * @param array $params 参数数组，以参数名为键，参数值为值的关联数组
     * @return array 返回数组
     */
    public function paginate($currentPage,$limit)
    {
        $total = $this->count();
        
        $this->page($currentPage,$limit);
        
        $data = $this->select();

        // 返回分页结果
        $this->paginate_params  =  [
            'data' => $data,
            'total' => $total,
            'perPage' => $limit,
            'current_page' => $currentPage,
            'last_page' => ceil($total / $limit),
        ];
        
        return $this->paginate_params;
    }

    
    /**
     * 输出分页
     *
     * @param array $params 参数数组，以参数名为键，参数值为值的关联数组
     * @return string 返回参数
     */
    public function render($params , $page_params ='' , $url = ''){
        $url = !empty($url) ? $url : url(Router()->formatAppnName().'/'.Router()->formatClassName().'/'.Router()->formatActionName());
        $pagination = new Pagination($params['total'], $params['perPage'], $params['current_page'], $url,$page_params);
        return $pagination->render();
    }
    
    
}