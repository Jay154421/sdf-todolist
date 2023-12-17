<?php
 require_once 'dbconfig.php';

 if(isset($_GET['task_id'])){
    $task_id = $_GET['task_id'];
  
    $stmt = $DB_con->prepare("DELETE FROM tasks WHERE id=:task_id");
    $stmt->bindparam(":task_id", $task_id);
    $stmt->execute();
    $user->redirect('Dashboard.php');
    
    }
   elseif(isset($_GET['id'])){
    $id = $_GET['id'];

    $stmt = $DB_con->prepare("DELETE FROM users WHERE id=:user_id");
    $stmt->bindparam(":user_id", $id);
    $stmt->execute();
    $user->redirect('admin.php');
    
}     