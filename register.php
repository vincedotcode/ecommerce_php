<?php
    use PHPMailer\PHPMailer\PHPMailer;
    use PHPMailer\PHPMailer\Exception;

    include 'includes/session.php';

    if(isset($_POST['signup'])){
        $firstname = $_POST['firstname'];
        $lastname = $_POST['lastname'];
        $email = $_POST['email'];
        $password = $_POST['password'];
        $repassword = $_POST['repassword'];

        $_SESSION['firstname'] = $firstname;
        $_SESSION['lastname'] = $lastname;
        $_SESSION['email'] = $email;

      
        if($password != $repassword){
            $_SESSION['error'] = 'Passwords did not match';
            header('location: signup.php');
        }
        else{
            $conn = $pdo->open();

            $stmt = $conn->prepare("SELECT COUNT(*) AS numrows FROM users WHERE email=:email");
            $stmt->execute(['email'=>$email]);
            $row = $stmt->fetch();
            if($row['numrows'] > 0){
                $_SESSION['error'] = 'Email already taken';
                header('location: signup.php');
            }
            else{
                $now = date('Y-m-d');
                $password = password_hash($password, PASSWORD_DEFAULT);

                try{
                    // Insert active status in the user row
                    $stmt = $conn->prepare("INSERT INTO users (email, password, firstname, lastname, created_on, status) VALUES (:email, :password, :firstname, :lastname, :now, '1')");
                    $stmt->execute(['email'=>$email, 'password'=>$password, 'firstname'=>$firstname, 'lastname'=>$lastname, 'now'=>$now]);

                    unset($_SESSION['firstname']);
                    unset($_SESSION['lastname']);
                    unset($_SESSION['email']);

                    $_SESSION['success'] = 'Account created and activated.';
                    header('location: signup.php');

                }
                catch(PDOException $e){
                    $_SESSION['error'] = $e->getMessage();
                    header('location: register.php');
                }

                $pdo->close();
            }
        }
    }
    else{
        $_SESSION['error'] = 'Fill up signup form first';
        header('location: signup.php');
    }
?>
