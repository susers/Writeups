<?php
class Db{
    protected $conn;
    protected $feilds;

    function __construct($dbhost,$dbuser,$dbpass,$dbname,$feilds)
        {
            if(!$this->conn){
                $this->conn = new mysqli($dbhost, $dbuser, $dbpass, $dbname);
                if(mysqli_connect_error()){
                    die("Database connect error! Please connect the manager!");
                }
            }
            $this->feilds=$feilds;
        }

    function getFeilds($tablename){
        $feild = array_key_exists($tablename,$this->feilds) ? $this->feilds[$tablename]:die('Something error!');
        if(is_array($feild))
            $sql = ' `'.implode('`,`',$feild).'` ';
        else
            $sql = ' `'.$feild.'` ';
        return $sql;
    }

    function Insert($tablename,$values){
        $sql = "insert into ".$tablename." (".$this->getFeilds($tablename).") ";
        $sql .= "values (";
        $last = array_pop($values);
        foreach ($values as $value) {
            $sql.=$value." , ";
        }
        $sql.=$last." ";
        $sql .= ")";
        return $this->conn->query($sql);

    }

    function Delete($tablename,$where){
        $sql = "delete from ".$tablename." ";
        $sql .= "where ";
        $last = array_slice($where,-1,1);
        array_pop($values);
        foreach ($where as $key => $value) {
            $sql.=$key."=".$value." and ";
        }
        foreach ($last as $key => $value) {
            $sql.=$key."=".$value." ";
        }
        return $this->conn->query($sql);
    }

    function Update($tablename,$where,$values){
        $sql = "update ".$tablename." ";
        $sql.= "set ";
        $last = array_slice($values,-1,1);
        array_pop($values);
        foreach ($values as $key => $value) {
            $sql.=$key."=".$value." and ";
        }
        foreach ($last as $key => $value) {
            $sql.=$key."=".$value." ";
        }
        $sql.="where ";
        $last = array_slice($where,-1,1);
        array_pop($where);
        foreach ($where as $key => $value) {
            $sql.=$key."=".$value." and ";
        }
        foreach ($last as $key => $value) {
            $sql.=$key."=".$value." ";
        }
        return $this->conn->query($sql);
    }

    function Select($tablename,$where=array(),$feilds=array("*"),$order=array()){
        $sql = "select ";
        $last = array_pop($feilds);
        foreach ($feilds as $value) {
            $sql.=$value." , ";
        }
        $sql.=$last." ";
        $sql.="from ".$tablename." ";
        if(count($where)!=0){
            $sql.="where ";
            $last = array_slice($where,-1,1);
            array_pop($where);
            foreach ($where as $key => $value) {
                $sql.=$key."=".$value." and ";
            }
            foreach ($last as $key => $value) {
                $sql.=$key."=".$value." ";
            }
        }
        if(count($order)!=0){
            $sql.="order by ";
            $last = array_pop($order);
            foreach ($order as $value) {
                $sql.=$value." , ";
            }
            $sql.=$last." ";
        }
        return $sql;
    }

    function One($tablename,$where=array(),$feilds=array("*"),$order=array()){
        $sql=$this->Select($tablename,$where,$feilds,$order);
        $result=$this->conn->query($sql);
        if($result)
            return $result->fetch_row();
        else
            return false;
    }

    function All($tablename,$where=array(),$feilds=array("*"),$order=array()){
        $sql=$this->Select($tablename,$where,$feilds,$order);
        $result=$this->conn->query($sql);
        if($result)
            return $result->fetch_all();
        else
            return false;
    }
}
?>
