<?php 

namespace App\Interfaces;

use App\Interfaces\IAdditionally;

#IAdditionaly <- Допольнительные функции Моделя
interface IModel extends IAdditionally
{
    public static function Create($data);
    public static function Update(int $id, array $data);
    public static function Delete(int $id);
    public static function DeleteAll();
    public static function DeleteWhere(string $col,string $opt, $data);
    public static function SelectAll(int $start);
    public static function SelectToQuery(string $sql);
    public static function SelectToLimit(int $start);
    public static function Show(int $id);
    public static function WhereCol(string $col, string $opt, $data);
    public static function WhereColLimit(string $col, string $opt, $data, int $start);
    public static function WhereLike(string $column, string $word);
    public static function WhereCol2(string $col, string $opt, $data, string $col2, string $opt2, $data2);
    public static function attach($data);
}

?>