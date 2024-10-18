<?php

namespace App\Models;

use App\Databases\Database;
use App\Interfaces\IModel;

trait Check
{
    public static function checkParam(string $col, string $opt, $data)
    {
        // Допустимые операторы
        $allowedOperators = ['=', '!=', '<', '>', '<=', '>=', 'LIKE'];
        if (!in_array($opt, $allowedOperators)) {
            http_response_code(500);
            throw new \InvalidArgumentException("Неподдерживаемый оператор: $opt");
        }

        // Допустимые имена столбцов
        $allowedColumns = static::$columns;
        if (!in_array($col, $allowedColumns)) {
            http_response_code(500);
            throw new \InvalidArgumentException("Неподдерживаемое имя столбца: $col");
        }

        if (!is_string($data) && !is_numeric($data)) {
            http_response_code(500);
            throw new \InvalidArgumentException("Неверное значение данных.");
        }

        return true;
    }

    public static function checkColumn(string $col){
        // Допустимые имена столбцов
        $allowedColumns = static::$columns;
        if (!in_array($col, $allowedColumns)) {
            http_response_code(500);
            throw new \InvalidArgumentException("Неподдерживаемое имя столбца: $col");
        }

        return true;
    }
}

abstract class Model extends Database implements IModel
{
    public static $limit = 10;
    use Check;
    public static function Create($data)
    {
        if (isset($data["password"])) {
            $data["password"] = md5($data["password"]);
        }

        $columns = implode(",", array_keys($data));
        $placeholders = implode(",", array_fill(0, count($data), '?'));
        $sql = "INSERT INTO " . static::$table . " ($columns) VALUES ($placeholders)";
        $stmt = self::NewConnect()->prepare($sql);
        $stmt->execute(array_values($data));
    } #Защищено от sql атаки

    public static function Update(int $id, array $data)
    {
        $update = "";
        $params = [];

        foreach ($data as $key => $value) {
            if ($key == "password") {
                $value = md5($value);
            }
            $update .= $key . "=:" . $key . ",";
            $params[$key] = $value;
        }

        $update = substr($update, 0, -1);
        $sql = "UPDATE " . static::$table . " SET {$update} WHERE id=:id";
        $stmt = self::NewConnect()->prepare($sql);

        foreach ($params as $key => $value) {
            $stmt->bindValue(":{$key}", $value, \PDO::PARAM_STR);
        }

        $stmt->bindValue(":id", $id);

        $stmt->execute();
    } #Защищено от sql атаки

    public static function Delete(int $id)
    {
        $sql = "DELETE FROM " . static::$table . " WHERE id=:id";
        $stmt = self::NewConnect()->prepare($sql);
        $stmt->bindValue(":id", $id);
        $stmt->execute();
    } #Защищено от sql атаки

    public static function DeleteAll()
    {
        $sql = "DELETE FROM " . static::$table;
        self::NewConnect()->exec($sql);
    } #Защищено от sql атаки

    public static function DeleteWhere(string $col, string $opt, $data)
    {
        Model::checkParam($col, $opt, $data);

        // Создание SQL-запроса
        $sql = "DELETE FROM " . static::$table . " WHERE $col $opt :data";
        $stmt = self::NewConnect()->prepare($sql);

        // Привязываем значение
        $stmt->bindValue(":data", $data, \PDO::PARAM_STR);

        // Выполняем запрос
        $stmt->execute();
    }

    public static function SelectAll(int $start)
    {
        $sql = "SELECT * FROM " . static::$table . " LIMIT :start,99999";
        $statement = self::NewConnect()->prepare($sql);
        $statement->bindValue(":start", $start, \PDO::PARAM_INT);

        $statement->execute();

        return $statement->fetchAll(\PDO::FETCH_OBJ);
    } #Защищено от sql атаки

    public static function SelectToQuery($sql)
    {
        try {
            $sqlReady = $sql;

            $statement = self::NewConnect()->query($sql);
            return $statement->fetchAll(\PDO::FETCH_OBJ);
        } catch (\PDOException $e) {
            return false;
        }
    } #Не защищено от sql атаки

    public static function SelectToLimit(int $start)
    {
        $sql = "SELECT * FROM " . static::$table . " LIMIT :start,15";
        $statement = self::NewConnect()->prepare($sql);
        $statement->bindValue(":start", $start, \PDO::PARAM_INT);
        $statement->execute();
        return $statement->fetchAll(\PDO::FETCH_OBJ);
    } #Защищено от sql атаки

    public static function Show(int $id)
    {
        $sql = "SELECT * FROM " . static::$table . " WHERE id=:id";
        $statement = self::NewConnect()->prepare($sql);
        $statement->bindValue(":id", $id, \PDO::PARAM_INT);
        $statement->execute();

        return $statement->fetch(\PDO::FETCH_OBJ);
    } #Защищено от sql атаки

    public static function WhereCol($col, $opt, $data)
    {
        Model::checkParam($col, $opt, $data);

        $sql = "SELECT * FROM " . static::$table . " WHERE {$col}{$opt}:data";

        $stmt = self::NewConnect()->prepare($sql);
        $stmt->bindParam(":data", $data, PDO::PARAM_STR);
        $stmt->execute();

        return $stmt->fetchAll(\PDO::FETCH_OBJ);
    } #Защищено от sql атаки

    public static function WhereCol2(string $col,string $opt, $data, string $col2, string $opt2, $data2)
    {
        Model::checkParam($col, $opt, $data);
        Model::checkParam($col2, $opt2, $data2);

        $sql = "SELECT * FROM " . static::$table . " WHERE {$col} {$opt} :data AND {$col2} {$opt2} :data2";

        $stmt = self::NewConnect()->prepare($sql);

        $stmt->bindValue(":data", $data, is_numeric($data) ? \PDO::PARAM_INT : \PDO::PARAM_STR);
        $stmt->bindValue(":data2", $data2, is_numeric($data2) ? \PDO::PARAM_INT : \PDO::PARAM_STR);

        $stmt->execute();

        return $stmt->fetchAll(\PDO::FETCH_OBJ);
    } #Защищено от sql атаки
    public static function WhereColLimit($col, $opt, $data, $start)
    {
        Model::checkParam($col, $opt, $data);

        $sql = "SELECT * FROM " . static::$table . " WHERE {$col}{$opt}:data LIMIT :start, 15";

        $stmt = self::NewConnect()->prepare($sql);
        $stmt->bindParam(":data", $data, \PDO::PARAM_STR);
        $stmt->bindParam(":start", $start, \PDO::PARAM_INT);
        $stmt->execute();

        return $stmt->fetchAll(\PDO::FETCH_OBJ);
    } #Защищено от sql атаки

    public static function WhereLike(string $column, string $word)
    {
        Model::checkColumn($column );
        $sql = "SELECT * FROM " . static::$table . " WHERE {$column} LIKE :word";
        $stmt = self::NewConnect()->prepare($sql);
        $stmt->bindValue(":word", "%" . $word . "%", \PDO::PARAM_STR);
        $stmt->execute();
        return $stmt->fetchAll(\PDO::FETCH_OBJ);
    } #Защищено от sql атаки

    public static function attach($data)
    {
        $set = [];
        $params = [];

        foreach ($data as $key => $value) {

            if ($key == "password") {
                $value = md5($value);
            }

            $set[] = "{$key} = :{$key}";
            $params[$key] = $value;
        }
        $set = implode(' AND ', $set);
        
        $sql = "SELECT * FROM ". static::$table . " WHERE {$set}";
        $stmt = self::NewConnect()->prepare($sql);

        foreach ($params as $paramKey => $paramValue) {
            $stmt->bindValue(":{$paramKey}", $paramValue, \PDO::PARAM_STR);
        }

        $stmt->execute();

        return $stmt->fetch(\PDO::FETCH_OBJ);      
    } #Защищено от sql атаки

    public static function Pagenet()
    {

        $sqlCountUser = "SELECT * FROM " . static::$table;
        $sqlCountUserRes = self::NewConnect()->query($sqlCountUser);
        $sqlCountUserRow = $sqlCountUserRes->fetchAll(\PDO::FETCH_OBJ);

        return ceil(count($sqlCountUserRow) / self::$limit);
    } #Защищено от sql атак
}
