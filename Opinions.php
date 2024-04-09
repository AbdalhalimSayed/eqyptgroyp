<?php
ob_start();
$title=" أراء عملائنا";
include("include/header.php");
INCLUDE("config.php");
// $name=$email=$opinion="";
$checkerr=array();
$checkerr["name_err"]="";
$checkerr["email_err"]="";
$checkerr["opinion_err"]="";
function test_input($data) {
    $data = trim($data);
    $data = stripslashes($data);
    $data = htmlspecialchars($data);
    return $data;
  }
function isEmailNotExist( $email) {
        // Prepare the SQL SELECT statement to check if the email exists
        global $conn;
        $stmt = $conn->prepare("SELECT COUNT(*) FROM opinions WHERE email = :email");
        
        // Bind the email parameter
        $stmt->bindParam(':email', $email);

        // Execute the prepared statement
        $stmt->execute();

        // Fetch the result
        $count = $stmt->fetchColumn();

        // If count is 0, email does not exist; return true
        return $count;
    
}
    if ($_SERVER["REQUEST_METHOD"] == "POST") {
        
        $name = test_input($_POST["name"]);
        $email = test_input($_POST["email"]);
        $opinion = test_input($_POST["opinion"]);
        if(empty($name)){
            $checkerr["name_err"]="<p class='alert alert-danger'>please enter name.</p>";
        }
        if (!preg_match("/^[a-zA-Z-' ]*$/",$name)) {
            $checkerr["name_err"] = " <p class='alert alert-danger'>Only letters and white space allowed</p>";
        }

        if(empty($email)){
            $checkerr["email_err"]="<p class='alert alert-danger'>please enter email.</p>";
        }
        if(isEmailNotExist($email) != 0){
            $checkerr["email_err"]="<p class='alert alert-danger'>please enter other email.</p>";
        }

        if(empty($opinion)){
            $checkerr["opinion_err"]="<p class='alert alert-danger'>please enter opinion.</p>";
        }
        if(strlen($opinion)<=10){
            $checkerr["opinion_err"]="<p class='alert alert-danger'>please enter opinion more than 15 letter.</p>";
        }

        if ($checkerr["name_err"]=="" && $checkerr["email_err"]=="" && $checkerr["opinion_err"]==""){
            $qery="INSERT INTO opinions (name, email, opinion) VALUES (?, ? ,?)";
            $stmt=$conn->prepare($qery);
            $stmt->execute(array($name,$email,$opinion));
            // Check if the insertion was successful
            if ($stmt->rowCount() > 0) {
                echo "<p class= 'container alert alert-success'>New record created successfully </p>";
                header("location:Opinions.php");
                exit();
            } else {
                echo "<p class= 'container alert alert-danger'>No rows were affected </p>";
            }
        }
    }
  
?>

<div class="container mt-5 text-right">
    <h1 class="mb-4">نموذج الملاحظات:</h1>
    <form action="" method="POST">
        <div class="mb-3">
            <label for="name" class="form-label">الأسم:</label>
            <?php
                if($checkerr["name_err"]!=""){
                   echo $checkerr["name_err"];
                }
            ?>
            <input type="text" class="form-control" id="name" name="name" required>
        </div>
        <div class="mb-3">
            <label for="email" class="form-label">الأيميل:</label>
            <?php
                if($checkerr["email_err"]!=""){
                   echo $checkerr["email_err"];
                }
            ?>
            <input type="email" class="form-control" id="email" name="email" required>
        </div>
        <div class="mb-3">
            <label for="opinion" class="form-label">رائك:</label>
            <?php
                if($checkerr["opinion_err"]!=""){
                   echo $checkerr["opinion_err"];
                }
            ?>
            <textarea class="form-control" id="opinion" name="opinion" rows="4" required></textarea>
        </div>
        <button type="submit" class="btn btn-primary " style="width:100%; color:#f39c12 ; font-size:22px;">نشر</button>
    </form>
  
</div>
<hr>


<?php

     // Prepare the SQL SELECT statement
     $stmt = $conn->prepare("SELECT name, email, opinion, is_puplish FROM opinions");
    
     // Execute the prepared statement
     $stmt->execute();
     
     // Fetch all rows as an associative array
     $rows = $stmt->fetchAll(PDO::FETCH_ASSOC);
     
     // Output the data in HTML cards
     foreach ($rows as $row) {
        if($row["is_puplish"]!=0){
            echo '<div class="container card " style="width:80%;">';
            echo '<div class="card-body" >';
            echo '<h5 class="card-title" style="color:#2ecc71; font-size:25px;">' . $row['name'] . '</h5>';
           //  echo '<h6 class="card-subtitle mb-2 text-muted">' . $row['email'] . '</h6>';
            echo '<p class="card-text" style="font-size:25px;" >' . $row['opinion'] . '</p>';
            echo '</div>';
            echo '</div>';
        }
     }

    include("include/footer.php");
    ob_end_flush();
?>