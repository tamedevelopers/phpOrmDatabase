<?php

declare(strict_types=1);

namespace builder\Database;

use PDOException;
use builder\Database\DB;
use builder\Database\Traits\DBImportTrait;


class DBImport extends DB{

    use DBImportTrait;
    
    private $db_connection;
    private $realpath;
    public $error;
    public $message;
    
    /**
     * Construct Instance of Database
     */
    public function __construct() {
        parent::__construct();
        $this->error = self::ERROR_404;
        $this->db_connection = $this->getConnection();
    }

    /**
     * Database Importation
     * @param string path_to_sql
     * 
     * @return object\builder\Database\DatabaseImport
     */
    public function DatabaseImport($path_to_sql = NULL)
    {
        $this->realpath = (string) $path_to_sql;
        
        /**
         * If SQL file does'nt exists
         */
        if(!file_exists($this->realpath) || is_dir($this->realpath)){
            $this->message  = "Failed to open stream: `{$path_to_sql}` does'nt exist.";
        } else{

            // read a file into an array
            $readFile = file($this->realpath);

            // is readable
            if(!$this->isReadable($readFile)){
                $this->message  = "Failed to read file or empty data.";
            } else{

                // check if connection test is okay
                if($this->DBConnect()){
                    try{
                        // connection driver
                        $Driver = $this->connection['driver'];

                        // get content
                        $sql = file_get_contents($this->realpath);

                        // execute query
                        $Driver->exec($sql);

                        $this->error    = self::ERROR_200;
                        $this->message  = "- Database has been imported successfully.";
                    } catch(PDOException $e){
                        $this->message  = "- Performing query: <strong style='color: #000'>{$e->getMessage()}</strong>";
                        $this->error    = self::ERROR_400;
                    }
                } else{
                    $this->message  = $this->db_connection['message'];
                }
            }
        }
        
        /*
        | ----------------------------------------------------------------------------
        | Database importation use. Below are the response code
        | ----------------------------------------------------------------------------
        |   if ->response === 404 (Failed to read file or File does'nt exists
        |   if ->response === 400 (Query to database error
        |   if ->response === 200 (Success importing to database
        */ 
        
        return (object) [
            'response' => $this->error, 
            'message'  => is_array($this->message) 
                            ? implode('\n<br>', $this->message)
                            : $this->message
        ];
    }
    
    /**
     * Check Database connection 
     * 
     * @return boolean\DBConnect
    */
    private function DBConnect()
    {
        // status
        if($this->db_connection['status'] != self::ERROR_200){
            return false;
        }

        return true;
    }
    
}