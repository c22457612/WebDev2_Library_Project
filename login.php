    <!DOCTYPE html>
    <html lang="en">
    <head>
        <title>Document</title>
        <style>
    /* login.css */

    body {
        font-family: Arial, sans-serif;
        margin: 0;
        padding: 0;
        background-color: #555;
        display: flex;
        justify-content: center;
        align-items: center;
        height: 100vh;
        position: relative; 
    }

    h1 {
        color: white; 
        font-size: 2em; 
        text-align: center;
        margin-bottom: 30px;
        position: absolute; 
        top: 20px; 
        left: 50%;
        transform: translateX(-50%);
    }

    .links {
        position: absolute; 
        top: 120px; 
        left: 50%;
        transform: translateX(-50%);
    }

    .links a {
        text-decoration: none;
        padding: 5px 10px;
        margin: 5px;
        color: red;
        background-color: #f0f0f0;
        border-radius: 5px;
    }

    .links a:first-of-type {
        margin-right: 10px; 
    }


    form {
        background-color: #222;
        border-radius: 8px;
        padding: 20px;
        box-shadow: 0 0 10px rgba(0, 0, 0, 0.1);
        width: 300px;
        color:white;
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
    padding: 5px 0;
    position: absolute;
    bottom: 0;
    width: 100%;
    }

    .error-message {
            color: white;
            font-size: 0.8em;
            text-align: center;
            margin-top: 10px;
        }

    </style>
    </head>
    <body>
    <div class="container">
        <h1>Login</h1>
        <div class="links">
        <a href="login.php">Login</a>
        <a href="register.php">Register</a>
        </div>
        <form method="post" action="login.php">
            <p>User Name:
                <input type="text" name="LoginUsername">
            </p>
            <p>Password:
                <input type="password" name="LoginPassword">
            </p>
            <p><input type="submit" value="Login"></p>
        </form>

        <?php
    require_once "database.php";

    // Check if the login form is submitted
    if (isset($_POST['LoginUsername'], $_POST['LoginPassword'])) {
        $loginUsername = mysqli_real_escape_string($conn, $_POST['LoginUsername']);
        $loginPassword = mysqli_real_escape_string($conn, $_POST['LoginPassword']);

        // Retrieve the user with the entered username and password using prepared statements
        $loginQuery = "SELECT * FROM user WHERE Username=? AND Password=?";
        $stmt = $conn->prepare($loginQuery);

        // Bind parameters and execute the query
        $stmt->bind_param('ss', $loginUsername, $loginPassword); //check both at once
        $stmt->execute();
        $loginResult = $stmt->get_result();

        if ($loginResult->num_rows > 0) {
            

            session_start();
            $_SESSION['Username'] = $loginUsername;
            session_write_close();

            header("Location: mainHome.php");
            exit();
        } else {
            echo "<span class='error-message'>Login failed. User does not exist or password is incorrect.";
        }
    }
    ?>
     </div>


    <footer>
        <p>Developed by Glenn Collins 2023</p>
    </footer>
    </body>
    </html> 