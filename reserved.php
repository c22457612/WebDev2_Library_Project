<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Reserved Books</title>
    <style>
        
body, h1, h2, h3, h4, h5, h6, ul, ol, li, p {
    margin: 0;
    padding: 0;
}

body {
    font-family: Arial, sans-serif;
    background-color: #f7f7f7;
    margin: 0;
    padding: 4px;
    font-size:110%;
    
    flex-direction: column;
    min-height: 100vh;
    margin: 0;
    
}

hr.rounded {
    border-top: 4px solid ;
    border-radius: 5px;
}

header {
    background-color: #333;
    color: #fff;
    text-align: center;
    padding: 20px;
}

header a {
    color: white;
    text-decoration: none;
    margin: 0 10px;
}

header a:hover {
    text-decoration: underline;
    color: white;
}

main {
    margin: 20px;
}

table {
    width: 100%;
    border-collapse: collapse;
    margin-top: 20px;
    padding-bottom:100px;
}

th, td {
    border: 1px solid #ddd;
    padding: 8px;
}

th {
    background-color: white;
    text-align: left;
}

tr:nth-child(even) {
    background-color: #f9f9f9;
}

tr:hover {
    background-color: white;
}


input[type="submit"] {
    background-color: #333;
    color: white;
    cursor: pointer;
    
}

.fill-action-box {
    height: 100%;
}

.fill-action-box form {
    height: 100%;
    display: flex; 
    justify-content: center; 
    align-items: flex; 
}

.fill-action-box input[type="submit"] {
    width: 100%;
    height: 100%;
    display: fill; 
}


footer {
    margin-top:auto;
    background-color: #333;
    color: white;
    text-align: center;
    padding: 3px 0;
    width: 100%;
    bottom: 0;
    left: 0;
}
    
</style>
</head>


<header >
    <a href="logout.php" style="color: white;">Logout</a>
    <a href="mainHome.php" style="color: white;">Search</a>
    <a href="reserved.php" style="color: white;">Reserved Books</a>
</header>


<body>

<br>

<?php
    require_once "database.php";
    session_start();

    if (isset($_SESSION['Username'])) {
        $Username = $_SESSION['Username'];
        echo "Welcome, $Username!<br>";
        echo "View reservations:";
       
    } else {
        // Redirect to the logout page if the username is not set
        header("Location: logout.php");
        exit();
    }

    $searchQuery = "SELECT * FROM reservations WHERE Username ='$Username'";
    $searchResult = $conn->query($searchQuery);

    if ($searchResult->num_rows > 0) {
        while ($row = $searchResult->fetch_assoc()) {
            echo '<hr class="rounded">';
            echo "<br><b>Reservation Date:</b> " . $row['ReservedDate'] . "<br>"; // Display reservation date

            $ISBN = $row['ISBN'];
            $searchQueryBooks = "SELECT * FROM books WHERE ISBN = '$ISBN'";
            $searchResultBooks = $conn->query($searchQueryBooks);

            if ($searchResultBooks && $searchResultBooks->num_rows > 0) {
                
                echo "<table border='1'>";
                echo "<tr><th>ISBN</th><th>Book Title</th><th>Author</th><th>Edition</th><th>Year</th><th>Category</th><th>Reserved</th><th>Action</th></tr>";
                while ($bookRow = $searchResultBooks->fetch_assoc()) {
                    echo "<tr>";
                    echo "<td>" . $bookRow['ISBN'] . "</td>";
                    echo "<td>" . $bookRow['BookTitle'] . "</td>";
                    echo "<td>" . $bookRow['Author'] . "</td>";
                    echo "<td>" . $bookRow['Edition'] . "</td>";
                    echo "<td>" . $bookRow['Year'] . "</td>";
                    echo "<td>" . $bookRow['Category'] . "</td>";
                    echo "<td>" . $bookRow['Reserved'] . "</td>";

                    echo "<td class='action-box fill-action-box'>"; // Start action box container
                    echo "<form method='post' class='fill-action-box'>";
                    echo "<input type='hidden' name='ISBN' value='" . $bookRow['ISBN'] . "'>"; // Hidden input to pass ISBN
                    echo "<input type='submit' name='remove' value='Remove' class='fill-action-box'>";
                    echo "</form>";
                    echo "</td>"; // End action box container

                    echo "</tr>";
                }

                echo "</table>";
            } else {
                echo "No corresponding books found for this reservation.";
            }
        }
    } else {
        echo "You have no reservations";
    }


    if (isset($_POST['remove'])) 
    {
        if (isset($_POST['ISBN'])) 
        {
            $ISBN = $_POST['ISBN'];

            $checkQuery = "SELECT * FROM reservations WHERE Username ='$Username' AND ISBN ='$ISBN'";
            $checkResult = $conn->query($checkQuery);

            $check2Query = "SELECT * FROM books WHERE Reserved ='N' AND ISBN ='$ISBN'";
            $check2Result = $conn->query($check2Query);

            if ($checkResult->num_rows > 0) 
            {
                $remove2Query = "UPDATE reservations SET Username='', ReservedDate='' WHERE ISBN='$ISBN' AND Username='$Username'";

                if ($conn->query($remove2Query) === TRUE) {
                    //echo "$ISBN Removed from your reservations!"; //debugging code
                } else {
                    echo "Error removing the book. Please try again.";
                }

                $removeQuery = "UPDATE books SET Reserved='N' WHERE ISBN='$ISBN'";

                if ($conn->query($removeQuery) === TRUE) 
                {
                    echo "$ISBN Removed from reserved books!";
                    
                } else {
                    echo "Error removing the book from reserved list. Please try again.";
                }

            }
        } else 
        {
            echo "Error: ISBN is not set!";
        }
    }
?>


</body>

<footer>
    <p>Developed by Glenn Collins 2023</p>
</footer>

</html>
