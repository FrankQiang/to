<?php

namespace Blog\BlogBundle\Admin;

  class SqlHelper{


       private $mysqli;
	private static $host="localhost";
	private static $userName="root";
	private static $password="w199223";
	private static $db="userUploadFile";

	public function __construct(){

		$this->mysqli=new mysqli(self::$host,self::$userName,self::$password,self::$db);
		if($this->mysqli->connect_errno){
			die("connect error".$this->mysqli->connect_error);
		}
            //保证php以utf-8的方式来操作数据库
		$this->mysqli->query("set names utf8");
	}
	
	public function execute_dql($sql){

		$result=$this->mysqli->query($sql)or die("deal dql error".$this->mysqli->error);
		return $result;

	}

	public function closeConnect(){
		$this->mysqli->close();
	}

	public function closeCommit(){
		$this->mysqli->autocommit(false);
	}

	public function openCommit(){
		$this->mysqli->autocommit(true);
	}
      
	public function rollback(){
		$this->mysqli->rollback();
	}

	public function commit(){
		$this->mysqli->commit();
	}

	public function showError(){
		$this->mysqli->error;
	}
	
	public function execute_dml($sql){
		
		$b=$this->mysqli->query($sql)or die("deal dql error".$this->mysqli->error);
        
		if(!$b){
			return 0;  //表示失败
		}
		else{
			if($this->mysqli->affected_rows>0){
			return 1; //表示成功
			 }
			 else{
				return 2; //无数据影响
			}

		}
	}
}
?>