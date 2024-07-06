<!DOCTYPE html>
<html>
<head>
    <title>User Registration</title>
</head>

<style>
    /* register.css */
body {
    font-family: Arial, sans-serif;
    margin: 0;
    padding: 0;
    background-color: #555;
    display: flex;
    flex-direction: column;
    align-items: center;
    min-height: 100vh;
}

.container {
    width: 80%; 
    max-width: 600px; 
    margin-top: 20px; 
    background-color: #333;
    border-radius: 8px;
    padding: 20px;
    box-shadow: 0 0 10px rgba(0, 0, 0, 0.1);
    color: white;
    display: flex;
    flex-direction: column;
    align-items: center;
}

.links {
    display: flex;
    justify-content: center;
    margin-bottom: 20px;
}

.links a {
    text-decoration: none;
    padding: 5px 10px;
    margin: 5px;
    color: red;
    background-color: #f0f0f0;
    border-radius: 5px;
}

h1 {
    color: white;
    font-size: 2em;
    text-align: center;
    margin-bottom: 30px;
}

form {
    color:white;
    width: 100%;
    max-width: 300px; 
    background-color: #222; /* Black background for the form */
    padding: 20px;
    border-radius: 8px; 
    text-align: center;
}

form p {
    margin-bottom: 10px;
    text-align: left;
}

input[type="text"],
input[type="password"],
input[type="submit"] {
    width: calc(100% - 10px);
    padding: 8px;
    margin-bottom: 10px;
    border-radius: 4px;
    border: 1px solid #ccc;
    transition: border-color 0.3s ease-in-out;
}

input[type="text"]:focus,
input[type="password"]:focus {
    outline: none;
    border-color: #007bff;
}

input[type="submit"] {
    background-color: #007bff;
    color: #fff;
    cursor: pointer;
    display: block; 
    margin: 0 auto;
}

input[type="submit"]:hover {
    background-color: #0056b3;
}

footer {
    background-color: #333;
    color: white;
    text-align: center;
    padding: 3px 0;
    width: 100%;
    bottom: 0;
    left: 0;
}


.error-message {
    color: white;
    margin-bottom: 10px; /* margin bottom to separate error messages */
    display: block;
}


</style>
    
<body>
<div class="error-container"> 
       
    
<?php
require_once "database.php";

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $errors = array();

    // Check for empty fields
    $required_fields = array('Username', 'Password', 'FirstName', 'Surname', 'AddressLine1', 'AddressLine2', 'City', 'Telephone', 'Mobile');
    foreach ($required_fields as $field) {
        if (empty(trim($_POST[$field]))) {
            $errors[] = "<span class='error-message'>Please fill in all fields.";
            break;
        }
    }

    // Validate mobile number
    $mobile = mysqli_real_escape_string($conn, $_POST['Mobile']);
    if (!ctype_digit($mobile) || strlen($mobile) !== 10) {
        $errors[] = "<span class='error-message'>Mobile number should be numeric and 10 characters in length.";
    }

    // Password validation and confirmation
    $confirm_password = mysqli_real_escape_string($conn, $_POST['ConfirmPassword']);
    if (strlen($confirm_password) != 6 || $_POST['Password'] !== $confirm_password) {
        $errors[] = "<span class='error-message'>Password should be at least six characters and match the confirmation.";
    }


    // Check for unique username
    $username = mysqli_real_escape_string($conn, $_POST['Username']);
    $check_username_query = "SELECT * FROM user WHERE Username='$username'";
    $result = $conn->query($check_username_query);
    if ($result->num_rows > 0) {
        $errors[] = "<span class='error-message'>Username already exists. Please choose a different username.";
    }

    if (empty($errors)) {
        // Perform the SQL insertion (Remember to sanitize inputs to prevent SQL injection)
        // Sanitize inputs before using them in my query
        $u = mysqli_real_escape_string($conn, $_POST['Username']);
        $p = mysqli_real_escape_string($conn, $_POST['Password']);
        $f = mysqli_real_escape_string($conn, $_POST['FirstName']);
        $s = mysqli_real_escape_string($conn, $_POST['Surname']);
        $a1 = mysqli_real_escape_string($conn, $_POST['AddressLine1']);
        $a2 = mysqli_real_escape_string($conn, $_POST['AddressLine2']);
        $c = mysqli_real_escape_string($conn, $_POST['City']);
        $t = mysqli_real_escape_string($conn, $_POST['Telephone']);
        $m = mysqli_real_escape_string($conn, $_POST['Mobile']);


        $sql = "INSERT INTO user (Username, Password, FirstName, Surname, AddressLine1, AddressLine2, City, Telephone, Mobile) VALUES ('$u', '$p', '$f', '$s', '$a1', '$a2', '$c', '$t', '$m')";
        
        if ($conn->query($sql) === TRUE) {
            session_write_close();
            echo "Account record created successfully";
            header("Location: login.php");
            exit();
        } else {
            echo "Error: " . $sql . "<br>" . $conn->error;
        }
    } else {
        foreach ($errors as $error) {
            echo $error . "<br>";
        }
    }
}

// Close the database connection
$conn->close();
?>

</div> 

<div class="links">
    <a href="login.php">Login</a>
    <a href="register.php">Register</a>
</div>

<form method="post">
    <p>User Name:
        <input type="text" name="Username">
    </p>
    <p>Password:
        <input type="password" name="Password">
    </p>
    <p>Confirm Password: <!-- input field for confirming password -->
        <input type="password" name="ConfirmPassword">
    <p>First Name:
        <input type="text" name="FirstName">
    </p>
    <p>Surname:
        <input type="text" name="Surname">
    </p>
    <p>Address Line 1:
        <input type="text" name="AddressLine1">
    </p>
    <p>Address Line 2:
        <input type="text" name="AddressLine2">
    </p>
    <p>City:
        <input type="text" name="City">
    </p>
    <p>Telephone:
        <input type="text" name="Telephone">
    </p>
    <p>Mobile:
        <input type="text" name="Mobile">
    </p>
    <p><input type="submit" value="Add New"></p>
</form>


<footer>
    <p>Developed by Glenn Collins 2023</p>
</footer>

</body>
</html>
