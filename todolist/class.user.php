<?php
class USER{
    public $db;
 
    function __construct($DB_con){
      $this->db = $DB_con;
    }
 
    public function register($username,$email,$password){
       try{
           $new_password = password_hash($password, PASSWORD_DEFAULT);
   
           $stmt = $this->db->prepare("INSERT INTO users(username,email,password) VALUES(:username, :email, :password)");           
           $stmt->bindparam(":username", $username);
           $stmt->bindparam(":email", $email);
           $stmt->bindparam(":password", $new_password);            
           $stmt->execute(); 
   
           return $stmt; 
       }
       catch(PDOException $e){
           echo $e->getMessage();
       }    
    }


    public function authLogin($user){
      $username = $_POST['username_email'];
      $email = $_POST['username_email'];
      $password = $_POST['password'];
         
      if($this->login($username,$email,$password)){
         $this->redirect('Dashboard.php');
      }
      elseif($username === "admin" && $password === "admin"){
         $_SESSION['admin'] = 'admin';
         $this->redirect('admin.php');
      }
      else{
       echo "<p style='color:red;'>Invalid username/email or password</p>";
      } 
    }

   public function authRegister($user){
      $username = $_POST['username'];
      $email = $_POST['email'];
      $password = $_POST['password']; 
      $conpassword = $_POST['conpassword'];
   
         if($username=="") {
            echo "<p style='color:red;'>Provide username!</p>"; 
         }
         else if($email=="") {
            echo "<p style='color:red;'>Provide email!</p>"; 
         }
         else if(!filter_var($email, FILTER_VALIDATE_EMAIL)) {
            echo "<p style='color:red;'>Please enter a valid email address!</p>";
         }
         else if($password=="") {
           echo "<p style='color:red;'>Provide password!</p>";
         }
         else if(strlen($password) < 6){
           echo "<p style='color:red;'>Password must be atleast 6 characters</p>"; 
         }
         else if($password !== $conpassword){
            echo "<p style='color:red;'>Passwords you entered do not match</p>";
         }
         else{
            try{
               $stmt = $this->db->prepare("SELECT username,email FROM users WHERE username=? OR email=?");
               $stmt->execute([$username,$email]);
               $row=$stmt->fetch(PDO::FETCH_ASSOC);
         
               if(is_array($row)){
                  if($row['username']==$username) {
                      echo "<p style='color:red;'>Sorry username already taken!</p>";
                  }
                  else if($row['email']==$email) {
                      echo "<p style='color:red;'>Sorry email id already taken!</p>";
                  }
              }
              else{
                  if($this->register($username,$email,$password)){
                      $this->redirect('index.php');
                  }
              }
         }
         catch(PDOException $e){
            echo $e->getMessage();
         }
      }
    } 

    public function login($username,$email,$password){
       try{
          $stmt = $this->db->prepare("SELECT * FROM users WHERE username=:username OR email=:email LIMIT 1");
          $stmt->execute(array(':username'=>$username, ':email'=>$email));
          $userRow=$stmt->fetch(PDO::FETCH_ASSOC);
          if($stmt->rowCount() > 0){
             if(password_verify($password, $userRow['password'])){
                $_SESSION['user_session'] = $userRow['id'];
                return true;
             }
             else{
                return false;
             }
          }
       }
       catch(PDOException $e){
           echo $e->getMessage();
       }
   }
 
   public function is_loggedin(){
      if(isset($_SESSION['user_session']) || isset($_SESSION['admin'])){
         return true;
      }
   }
 
   public function redirect($url){
       header("Location: $url");
   }

  public function insertTask($task, $user_id){
   try{
      $stmt = $this->db->prepare("INSERT INTO tasks(task,user_id) VALUES(:task,:email)");
      $stmt->bindparam(":task", $task);
      $stmt->bindparam(":email", $user_id);
      $stmt->execute();
         return true;
      }
      catch(PDOException $e){
        echo $e->getMessage();
        return false;
      }
   }

   public function name($user_id){
      $stmt = $this->db->prepare("SELECT * FROM users WHERE id=:user_id");
      $stmt->execute(array(":user_id"=>$user_id));
      $user = $stmt->fetch(PDO::FETCH_ASSOC);
      return $user;
   }
  
   public function editTask($task_id, $new_task){
      try{
          $stmt = $this->db->prepare("UPDATE tasks SET  task=:new_task WHERE id=:task_id");
          $stmt->bindparam(":new_task", $new_task);
          $stmt->bindparam(":task_id", $task_id);
          $stmt->execute();
          
          return $this->redirect('Dashboard.php');

      }
      catch(PDOException $e){
          echo $e->getMessage();
          return false;
      }
  }

  
}

?>