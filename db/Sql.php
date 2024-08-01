<?php
// +----------------------------------------------------------------------
// | QHPHP [ 代码创造未来，思维改变世界。 ]
// +----------------------------------------------------------------------
// | Copyright (c) 2006~2018 https://www.astrocms.cn/ All rights reserved.
// +----------------------------------------------------------------------
// | Licensed ( http://www.apache.org/licenses/LICENSE-2.0 )
// +----------------------------------------------------------------------
// | Author: ZHAOSONG <1716892803@qq.com>
// +----------------------------------------------------------------------
namespace qhphp\db;
use qhphp\db\Db;

/**
 * 数据库操作字段类
 *
 * @author zhaosong
 */
class Sql extends Db{
    
   /**
     * 创建表
     *
     * @param string $tableName 表名
     * @param array $fields 表字段数组
     */
    public  function createTable($tableName,$charset = 'utf8') {
		$sql = "CREATE TABLE `".C('db_prefix').$tableName."` (
		  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
		  PRIMARY KEY (`id`)
		  ) ENGINE=MyISAM DEFAULT CHARSET={$charset};";
		return $this->pdo->query($sql);
    }

    /**
     * 添加整型字段
     *
     * @param string $fieldName 字段名
     * @param int $length 字段长度，默认为 11
     * @return string 字段定义
     */
    public  function addIntField($tableName,$fieldName, $length = 11) {
        return $this->pdo->query("ALTER TABLE `".C('db_prefix').$tableName."` ADD COLUMN `$fieldName` INT($length)");
    }

    /**
     * 添加大整型字段
     *
     * @param string $fieldName 字段名
     * @param int $length 字段长度，默认为 20
     * @return string 字段定义
     */
    public  function addBigIntField($tableName,$fieldName, $length = 20) {
        return $this->pdo->query("ALTER TABLE `".C('db_prefix').$tableName."` ADD COLUMN `$fieldName` BIGINT($length)");
    }

    /**
     * 添加小整型字段
     *
     * @param string $fieldName 字段名
     * @param int $length 字段长度，默认为 4
     * @return string 字段定义
     */
    public  function addTinyIntField($tableName,$fieldName, $length = 4) {
        return $this->pdo->query("ALTER TABLE `".C('db_prefix').$tableName."` ADD COLUMN `$fieldName`  TINYINT($length)");
    }

    /**
     * 添加中整型字段
     *
     * @param string $fieldName 字段名
     * @param int $length 字段长度，默认为 6
     * @return string 字段定义
     */
    public  function addSmallIntField($tableName,$fieldName, $length = 6) {
        return $this->pdo->query("ALTER TABLE `".C('db_prefix').$tableName."` ADD COLUMN `$fieldName` SMALLINT($length)");
    }

    /**
     * 添加中整型字段
     *
     * @param string $fieldName 字段名
     * @param int $length 字段长度，默认为 9
     * @return string 字段定义
     */
    public  function addMediumIntField($tableName,$fieldName, $length = 9) {
        return $this->pdo->query("ALTER TABLE `".C('db_prefix').$tableName."` ADD COLUMN `$fieldName` MEDIUMINT($length)");
    }

    /**
     * 添加浮点型字段
     *
     * @param string $fieldName 字段名
     * @param int $length 总长度，默认为 10
     * @param int $decimals 小数位数，默认为 2
     * @return string 字段定义
     */
    public  function addFloatField($tableName,$fieldName, $length = 10, $decimals = 2) {
        return $this->pdo->query("ALTER TABLE `".C('db_prefix').$tableName."` ADD COLUMN `$fieldName` FLOAT($length, $decimals)");
    }

    /**
     * 添加双精度浮点型字段
     *
     * @param string $fieldName 字段名
     * @param int $length 总长度，默认为 10
     * @param int $decimals 小数位数，默认为 2
     * @return string 字段定义
     */
    public  function addDoubleField($tableName,$fieldName, $length = 10, $decimals = 2) {
        return $this->pdo->query("ALTER TABLE `".C('db_prefix').$tableName."` ADD COLUMN `$fieldName` DOUBLE($length, $decimals)");
    }

    /**
     * 添加定点数字字段
     *
     * @param string $fieldName 字段名
     * @param int $length 总长度，默认为 10
     * @param int $decimals 小数位数，默认为 2
     * @return string 字段定义
     */
    public  function addDecimalField($tableName,$fieldName, $length = 10, $decimals = 2) {
       return  $this->pdo->query("ALTER TABLE `".C('db_prefix').$tableName."` ADD COLUMN `$fieldName` DECIMAL($length, $decimals)");
    }

    /**
     * 添加可变字符串字段
     *
     * @param string $fieldName 字段名
     * @param int $length 字段长度，默认为 255
     * @return string 字段定义
     */
    public  function addVarCharField($tableName,$fieldName, $length = 255) {
        return $this->pdo->query("ALTER TABLE `".C('db_prefix').$tableName."` ADD COLUMN `$fieldName` VARCHAR($length)");
    }

    /**
     * 添加固定字符串字段
     *
     * @param string $fieldName 字段名
     * @param int $length 字段长度
     * @return string 字段定义
     */
    public  function addCharField($tableName,$fieldName, $length) {
        return $this->pdo->query("ALTER TABLE `".C('db_prefix').$tableName."` ADD COLUMN `$fieldName` CHAR($length)");
    }

    /**
     * 添加文本字段
     *
     * @param string $fieldName 字段名
     * @return string 字段定义
     */
    public  function addTextField($tableName,$fieldName) {
        return $this->pdo->query("ALTER TABLE `".C('db_prefix').$tableName."` ADD COLUMN `$fieldName` TEXT");
    }

    /**
     * 添加中等文本字段
     *
     * @param string $fieldName 字段名
     * @return string 字段定义
     */
    public  function addMediumTextField($tableName,$fieldName) {
        return $this->pdo->query("ALTER TABLE `".C('db_prefix').$tableName."` ADD COLUMN `$fieldName` MEDIUMTEXT");
    }

    /**
     * 添加长文本字段
     *
     * @param string $fieldName 字段名
     * @return string 字段定义
     */
    public  function addLongTextField($tableName,$fieldName) {
        return $this->pdo->query("ALTER TABLE `".C('db_prefix').$tableName."` ADD COLUMN `$fieldName`  LONGTEXT");
    }

    /**
     * 添加日期字段
     *
     * @param string $fieldName 字段名
     * @return string 字段定义
     */
    public  function addDateField($tableName,$fieldName) {
        return $this->pdo->query("ALTER TABLE `".C('db_prefix').$tableName."` ADD COLUMN `$fieldName` DATE");
    }

    /**
     * 添加日期时间字段
     *
     * @param string $fieldName 字段名
     * @return string 字段定义
     */
    public  function addDateTimeField($tableName,$fieldName) {
        return $this->pdo->query("ALTER TABLE `".C('db_prefix').$tableName."` ADD COLUMN `$fieldName` DATETIME");
    }

    /**
     * 添加时间戳字段
     *
     * @param string $fieldName 字段名
     * @return string 字段定义
     */
    public  function addTimestampField($tableName,$fieldName) {
        return $this->pdo->query("ALTER TABLE `".C('db_prefix').$tableName."` ADD COLUMN `$fieldName` TIMESTAMP");
    }

    /**
     * 添加时间字段
     *
     * @param string $fieldName 字段名
     * @return string 字段定义
     */
    public  function addTimeField($tableName,$fieldName) {
        return $this->pdo->query("ALTER TABLE `".C('db_prefix').$tableName."` ADD COLUMN `$fieldName` TIME");
    }
    /**
     * 添加年份字段
     * @param string $fieldName 字段名
     * @return string 字段定义
     */
    public  function addYearField($tableName,$fieldName) {
        return $this->pdo->query("ALTER TABLE `".C('db_prefix').$tableName."` ADD COLUMN `$fieldName` YEAR");
    }

    /**
     * 添加枚举字段
     * @param string $fieldName 字段名
     * @param array $values 枚举值数组
     * @return string 字段定义
     */
    public  function addEnumField($tableName,$fieldName, $values) {
        $values = implode(",", $values);
        return $this->pdo->query("ALTER TABLE `".C('db_prefix').$tableName."` ADD COLUMN `$fieldName` ENUM($values)");
    }

    /**
     * 添加集合字段
     * @param string $fieldName 字段名
     * @param array $values 集合值数组
     * @return string 字段定义
     */
    public  function addSetField($tableName,$fieldName, $values) {
        $values = implode(",", $values);
        return $this->pdo->query("ALTER TABLE `".C('db_prefix').$tableName."` ADD COLUMN `$fieldName` SET($values)");
    }

    /**
     * 添加布尔字段
     * @param string $fieldName 字段名
     * @return string 字段定义
     */
    public  function addBooleanField($tableName,$fieldName) {
        return $this->pdo->query("ALTER TABLE `".C('db_prefix').$tableName."` ADD COLUMN `$fieldName` BOOLEAN");
    }

    /**
     * 添加二进制字段
     * @param string $fieldName 字段名
     * @return string 字段定义
     */
    public  function addBinaryField($tableName,$fieldName) {
        return $this->pdo->query("ALTER TABLE `".C('db_prefix').$tableName."` ADD COLUMN `$fieldName` BINARY");
    }

    /**
     * 添加可变长度二进制字段
     * @param string $fieldName 字段名
     * @param int $length 长度，默认为255
     * @return string 字段定义
     */
    public  function addVarBinaryField($tableName,$fieldName, $length = 255) {
        return $this->pdo->query("ALTER TABLE `".C('db_prefix').$tableName."` ADD COLUMN `$fieldName` VARBINARY($length)");
    }

    /**
     * 添加小型二进制字段
     * @param string $fieldName 字段名
     * @return string 字段定义
     */
    public  function addTinyBlobField($tableName,$fieldName) {
        return $this->pdo->query("ALTER TABLE `".C('db_prefix').$tableName."` ADD COLUMN `$fieldName` TINYBLOB");
    }

    /**
     * 添加二进制字段
     * @param string $fieldName 字段名
     * @return string 字段定义
     */
    public  function addBlobField($tableName,$fieldName) {
        return $this->pdo->query("ALTER TABLE `".C('db_prefix').$tableName."` ADD COLUMN `$fieldName` BLOB");
    }

    /**
     * 添加中型二进制字段
     * @param string $fieldName 字段名
     * @return string 字段定义
     */
    public  function addMediumBlobField($tableName,$fieldName) {
        return $this->pdo->query("ALTER TABLE `".C('db_prefix').$tableName."` ADD COLUMN `$fieldName` MEDIUMBLOB");
    }

    /**
     * 添加大型二进制字段
     * @param string $fieldName 字段名
     * @return string 字段定义
     */
    public  function addLongBlobField($tableName,$fieldName) {
        return $this->pdo->query("ALTER TABLE `".C('db_prefix').$tableName."` ADD COLUMN `$fieldName` LONGBLOB");
    }
    
    /**
     * 删除数据库表
     *
     * @param string $fieldName 表名
     * @return mixed 查询结果
     * @author zhaosong
     */
    public  function deltable($fieldName)
    {
        $fieldName = C('db_prefix') . $fieldName;
        $sql = "DROP TABLE $fieldName";
        return $this->pdo->query($sql);
    }
    
    /**
     * 检查表中是否存在指定字段
     *
     * @param string $tableName 表名
     * @param string $fieldName 字段名
     * @return bool 如果字段存在，返回 true，否则返回 false
     * @author zhaosong
     */
    public  function checkField($tableName, $fieldName)
    {
        $sql = "SELECT COUNT(*) > 0
                FROM INFORMATION_SCHEMA.COLUMNS
                WHERE TABLE_NAME = '$tableName' AND COLUMN_NAME = '$fieldName'";
    
        $result = $this->pdo->query($sql);
        if ($result[0]) {
            return true;
        } else {
            return false;
        }
    }
    
    /**
     * 删除数据表中的指定字段
     *
     * @param string $tableName 表名
     * @param string $columnName 字段名
     * @return mixed 删除成功返回 true，失败返回 false
     * @author zhaosong
     */
    public  function delColumn($tableName, $columnName)
    {
        $tableName = C('db_prefix') . $tableName;
        $sql = "ALTER TABLE `$tableName` DROP COLUMN `$columnName`";
        return $this->pdo->query($sql);
    }
    
}
