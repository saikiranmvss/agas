<?php 

class database extends stdClass {

    public $conn='';

    public function __construct() {
         $this->conn=mysqli_connect('localhost','u156912075_root','7E+P+VeVG1;p','u156912075_agstones');
        return $conn=$this->conn;
    }

    public function execute($query){

        return mysqli_query($this->conn,$query);

    }

}

$dbConn=new database();

?>