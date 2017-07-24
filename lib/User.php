<?php

  include_once 'Session.php';
  include 'Database.php';
  
  class User{

  	  private $db;
      public function __construct(){

         $this->db = new Database(); 
         
      }

      public function userRegistration($data){
      	$name     = $data['name'];
      	$username = $data['username'];
      	$email    = $data['email'];
      	$password = $data['password'];
      	$chk_email = $this->emailCheck($email);

      	if( $name == "" OR $username == "" OR $email == "" OR $password == "" ){
      		$msg = "<div class='alert alert-danger'><strong>Error!</strong>Feild must not be empty</div>";
      		return $msg;
      	}

      	if( strlen($username) < 3 ){

      		$msg = "<div class='alert alert-danger'><strong>Error!</strong>User Name is too short</div>";
      		return $msg;

      	}

      	else if(preg_match('/[^a-z0-9_-]+/i',$username) ){
      		$msg = "<div class='alert alert-danger'><strong>Error!</strong>User Name only contain alpha numeric or dashes or underscores</div>";
      		return $msg;
      	}

      	if( filter_var($email,FILTER_VALIDATE_EMAIL) == false ){
      		$msg = "<div class='alert alert-danger'><strong>Error!</strong>the email address is not valid</div>";
      		return $msg;
      	}

      	if( $chk_email == true ){
      		$msg = "<div class='alert alert-danger'><strong>Error!</strong>the email address already exist</div>";
      		return $msg;
      	}

            $password = md5($data['password']);

      	$sql = "INSERT INTO tbl_user (name,username,email,password) VALUES(:name,:username,:email,:password)";
      	$query = $this->db->pdo->prepare($sql);
      	$query->bindValue(':name',$name);
      	$query->bindValue(':username',$username);
      	$query->bindValue(':email',$email);
      	$query->bindValue(':password',$password);
      	$result = $query->execute();

      	if($result){
      		$msg = "<div class='alert alert-success'><strong>Success!</strong>You have been registered</div>";
      		return $msg;
      	}
      	else{
      		$msg = "<div class='alert alert-danger'><strong>Error!</strong>Insert Correctly</div>";
      		return $msg;
      	}

      }


      public function emailCheck($email){
      	$sql = 'select email from tbl_user where email=:email';
      	$query = $this->db->pdo->prepare($sql);
      	$query->bindValue(':email',$email);
      	$query->execute();

      	if( $query->rowCount()>0 ){
      		return true;
      	}else{
      		return false;
      	}

      }

      public function getLoginUser($email,$password){
      	$sql = 'select * from tbl_user where email=:email and password=:password limit 1';
      	$query = $this->db->pdo->prepare($sql);
      	$query->bindValue(':email',$email);
      	$query->bindValue(':password',$password);
      	$query->execute();
            $result = $query->fetch(PDO::FETCH_OBJ);
            return $result;
      }

      public function usrLogin($data){

      	$email    = $data['email'];
      	$password = md5($data['password']);
      	$chk_email = $this->emailCheck($email);

      	if( $email == "" OR $password == "" ){
      		$msg = "<div class='alert alert-danger'><strong>Error!</strong>Feild must not be empty</div>";
      		return $msg;
      	}

      	if( filter_var($email,FILTER_VALIDATE_EMAIL) == false ){
      		$msg = "<div class='alert alert-danger'><strong>Error!</strong>the email address is not valid</div>";
      		return $msg;
      	}

      	if( $chk_email == false ){
      		$msg = "<div class='alert alert-danger'><strong>Error!</strong>the email address not exist</div>";
      		return $msg;
      	}

      	$result = $this->getLoginUser($email,$password);

            if( $result ){
                  SESSION::init();
                  SESSION::set("login",true);
                  SESSION::set("id",$result->id);
                  SESSION::set("name",$result->name);
                  SESSION::set("username",$result->username);
                  SESSION::set("loginmsg","<div class='alert alert-success'><strong>Success!</strong>You are logged in</div>");
                  header("Location:index.php");
            }else{

                  $msg = "<div class='alert alert-danger'><strong>Error!</strong>data not found</div>";
                  return $msg;
            }

      }

      public function getUserData(){
            $sql = 'select * from tbl_user order by id desc';
            $query = $this->db->pdo->prepare($sql);
            $query->execute();
            $result = $query->fetchAll();
            return $result;
      }

      public function getUserById($id){
            $sql = 'select * from tbl_user where id=:id limit 1';
            $query = $this->db->pdo->prepare($sql);
            $query->bindValue(':id',$id);
            $query->execute();
            $result = $query->fetch(PDO::FETCH_OBJ);
            return $result; 
      }

      public function updateUserData($id,$data){

            $name     = $data['name'];
            $username = $data['username'];
            $email    = $data['email'];
            $password = md5($data['password']);
            $chk_email = $this->emailCheck($email);

            if( $name == "" OR $username == "" OR $email == "" ){
                  $msg = "<div class='alert alert-danger'><strong>Error!</strong>Feild must not be empty</div>";
                  return $msg;
            }

            if( strlen($username) < 3 ){

                  $msg = "<div class='alert alert-danger'><strong>Error!</strong>User Name is too short</div>";
                  return $msg;

            }

            else if(preg_match('/[^a-z0-9_-]+/i',$username) ){
                  $msg = "<div class='alert alert-danger'><strong>Error!</strong>User Name only contain alpha numeric or dashes or underscores</div>";
                  return $msg;
            }

            if( filter_var($email,FILTER_VALIDATE_EMAIL) == false ){
                  $msg = "<div class='alert alert-danger'><strong>Error!</strong>the email address is not valid</div>";
                  return $msg;
            }

            if( $chk_email == true ){
                  $msg = "<div class='alert alert-danger'><strong>Error!</strong>the email address already exist</div>";
                  return $msg;
            }

            $sql = "update tbl_user set
                 name     =:name,
                 username =:username,
                 email    =:email
                 where id =:id";

            $query = $this->db->pdo->prepare($sql);
            $query->bindValue(':name',$name);
            $query->bindValue(':username',$username);
            $query->bindValue(':email',$email);
            $query->bindValue(':id',$id);
            $result = $query->execute();

            if($result){
                  $msg = "<div class='alert alert-success'><strong>Success!</strong>User Data Updated Successfully</div>";
                  return $msg;
            }
            else{
                  $msg = "<div class='alert alert-danger'><strong>Error!</strong>Insert Correctly</div>";
                  return $msg;
            }

      }

      private function checkPassword($old_pass,$id){
            $password = md5($old_pass);
            $sql = 'select password from tbl_user where password=:password and id=:id';
            $query = $this->db->pdo->prepare($sql);
            $query->bindValue(':id',$id);
            $query->bindValue(':password',$password);
            $query->execute();

            if( $query->rowCount()>0 ){
                  return true;
            }else{
                  return false;
            }

      }

      public function updatePassword($id,$data){
            $old_pass = $data['old_pass'];
            $new_pass = $data['password'];
            $chk_pass = $this->checkPassword($old_pass,$id);

            if($old_pass=="" or $new_pass==""){
                  $msg = "<div class='alert alert-danger'><strong>Error!</strong>Feild must not be empty</div>";
                  return $msg;
            }

            
            if( $chk_pass == false ){
                $msg = "<div class='alert alert-danger'><strong>Error!</strong>Old password not exist</div>";
                return $msg;
            }

            if( strlen($new_pass) <6 ){
               $msg = "<div class='alert alert-danger'><strong>Error!</strong>Password is too short</div>";
               return $msg;    
            }

            $password = md5($new_pass);

            $sql = "update tbl_user set
                 password =:password
                 where id =:id";

            $query = $this->db->pdo->prepare($sql);
            $query->bindValue(':password',$password);
            $query->bindValue(':id',$id);
            $result = $query->execute();

            if($result){
                  $msg = "<div class='alert alert-success'><strong>Success!</strong>Password Updated Successfully</div>";
                  return $msg;
            }
            else{
                  $msg = "<div class='alert alert-danger'><strong>Error!</strong>Insert Correctly</div>";
                  return $msg;
            }

            
      }
  }

?>