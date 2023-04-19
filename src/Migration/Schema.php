<?php

declare(strict_types=1);

namespace UltimateOrmDatabase\Migration;


use PDOException;
use UltimateOrmDatabase\DB;
use UltimateOrmDatabase\Constants;
use UltimateOrmDatabase\Migration\Blueprint;

class Schema extends Constants{
    
    static private $db;
    static protected $tableName;
    static protected $object;


    /**
     * Creating Instance of Database
     * 
     * @return void
     */
    static private function initSchemaDatabase() 
    {
        self::$db = new DB();
    }

    /**
     * Creating Indexs
     * @param string $tableName 
     * @param callable $callback
     * 
     * @return void
     */
    static public function create(?string $tableName, callable $callback) 
    {
        self::$object = new self;

        $callback(new Blueprint($tableName));
    }

    /**
     * Drop table
     * @param string $tableName 
     * 
     * @return array\dropTable
     */
    static public function dropTable(?string $tableName)
    {
        self::initSchemaDatabase();

        // Handle query
        try{
            // DROP TABLE IF EXISTS
            self::$db->raw( "DROP TABLE {$tableName};" )
                    ->compileQuery()
                    ->execute();

            echo "Table `{$tableName}` dropped successfully <br> \n";
        } catch (PDOException $e){
            echo preg_replace(
                '/^[ \t]+|[ \t]+$/m', '', 
                sprintf("<<\\Error code>> %s
                    <br><br>
                    <<\\PDO::ERROR>> %s <br> \n
                ", self::ERROR_404, $e->getMessage())
            );
            exit();
        }
    }

    /**
     * Drop column
     * @param string $tableName 
     * @param string $columnName 
     * 
     * @return object\dropColumn
     */
    static public function dropColumn(?string $tableName, ?string $columnName)
    {
        self::initSchemaDatabase();

        // if null
        if(is_null($columnName)){
            throw new \Exception('Table column name cannot be empty. Please pass a value.');
            return;
        }

        // Handle query
        try{
            // DROP COLUMN IF EXISTS
            self::$db->raw( "ALTER TABLE {$tableName} DROP COLUMN {$columnName};" )
                    ->compileQuery()
                    ->execute();

            echo "Column `{$columnName}` on `{$tableName}` dropped successfully <br> \n";
        } catch (PDOException $e){
            echo preg_replace(
                '/^[ \t]+|[ \t]+$/m', '', 
                sprintf("<<\\Error code>> %s
                    <br><br>
                    <<\\PDO::ERROR>> %s <br> \n
                ", self::ERROR_404, $e->getMessage())
            );
            exit();
        }
    }
    
}