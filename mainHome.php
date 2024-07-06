<?php
    session_start();
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Main Home Page</title>
    <style>
        /* CSS */
body {
    font-family: Arial, sans-serif;
    background-color: #f4f4f4;
    margin: 0;
    padding: 0px;
    padding-left:10px;
    padding-right:10px;
    font-size:110%;
    font-weight:normal;
}

header {
    background-color: #333;
    color: white;
    text-align: center;
    padding: 1rem 0;
}

header a {
    color: white;
    text-decoration: none;
    margin: 0 10px;
}

header a:hover {
    text-decoration: underline;
}

hr.rounded {
    border-top: 4px solid black;
    border-radius: 5px;
}

main {
    max-width: 1200px;
    margin: 20px auto;
    padding: 20px;
    background-color: white;
    box-shadow: 0 0 10px rgba(0, 0, 0, 0.1);
    border-radius: 5px;
    padding-bottom: 20px;
}

form {
    margin-bottom: 20px;
}

label {
    display: block;
    margin-bottom: 5px;
}

input[type="text"],
select,
input[type="submit"] {
    padding: 0px;
    margin-bottom: 10px;
    border: 1px solid #ccc;
    border-radius: 3px;
    width: 100%;
    box-sizing: border-box;
}

input[type="submit"] {
    background-color: #333;
    color: white;
    cursor: pointer;
}

input[type="submit"]:hover {
    background-color: #555;
}

table {
    width: 100%;
    border-collapse: collapse;
    margin-bottom: 20px;
}

th,
td {
    border: 1px solid #ccc;
    padding: 8px;
    text-align: left;
}

th {
    background-color: #f4f4f4;
}

.pagination {
    margin-top: 20px;
    text-align: center;
}

.pagination a {
    display: inline-block;
    padding: 8px 16px;
    text-decoration: none;
    color: #333;
    border: 1px solid #ccc;
    margin: 0 4px;
    border-radius: 3px;
}

.pagination a.active {
    background-color: #333;
    color: white;
}


.pagination a.next-page {

    background-color: #555;
    color: white;
    
}

select {
    padding: 8px;
    margin-bottom: 10px;
    width: 220px; 
    border-radius: 4px;
    border: 1px solid #ccc;
    transition: border-color 0.3s ease-in-out;
}

/* Style for the input boxes */

input[type="text"],
input[type="submit"],
select {
    width: calc(100% - 10px);
    padding: 8px;
    margin-bottom: 10px;
    border-radius: 4px;
    border: 1px solid #ccc;
    transition: border-color 0.3s ease-in-out;
}

form:first-of-type input[type="text"] {
    width: calc(100% - 80px); 
    padding: 8px;
    margin-bottom: 10px;
    border-radius: 4px;
    border: 1px solid #ccc;
    transition: border-color 0.3s ease-in-out;
}

input[type="text"]:focus,
select:focus,
input[type="submit"]:hover {
    outline: none;
    border-color: #007bff;
}

footer {
    position: relative;
    top: 110px;
    left: 0;
    width: 100%;
    background-color: #333;
    color: white;
    text-align: center;
    padding: 3px 0;
}


.bold-text {
    font-weight: bold;
}



</style>  
</head>

<body>
<header >
    <a href="logout.php" style="color: white;">Logout</a>
    <a href="mainHome.php" style="color: white;">Search</a>
    <a href="reserved.php" style="color: white;">Reserved Books</a>
</header>

<?php
    require_once "database.php";

    if (isset($_SESSION['Username'])) {
        $Username = $_SESSION['Username'];
        echo"<br>";
        echo "Welcome, $Username!<br>";
        echo '<hr class="rounded">';
    } else {
        // Redirect to the logout page if the username is not set
        header("Location: logout.php");
        exit();
    }
?>
 
 <form method="post"> 
    <label for="dropdown" class="bold-text">Search by Category:</label> 
    <select id="dropdown" name="dropdown"> //check to see if you can put
    <?php
        
        $query = "SELECT CategoryID,CategoryDescription FROM categories"; 
        $result = $conn->query($query);

        
        while ($row = $result->fetch_assoc()) {
            echo '<option value="' . $row['CategoryID'] . '">' . $row['CategoryDescription'] . '</option>';
        }
    ?>
    </select>
    <input type="submit" value="Submit">
    </form>


<?php //search code
    echo "<div class='bold-text'>Search for a Book</div>";
    $current_page = isset($_GET['page']) ? $_GET['page'] : 1;
    $records_per_page = 5;
    $offset = ($current_page - 1) * $records_per_page;

    $SearchValue = isset($_GET['search']) ? mysqli_real_escape_string($conn, $_GET['search']) : '';


    // Form for searching
    echo "<form method='get'>";
    echo "<input type='text' name='search' value='$SearchValue' placeholder='Search by name or Author...'>";
    echo "<input type='submit' value='Search'>";
    echo "</form>";

    if (isset($_POST['dropdown'])) //if we put this under search function, website logic does not work as intended
    {
        unset($_GET['search']);
        $current_page = isset($_GET['page']) ? $_GET['page'] : 1;
        $records_per_page = 5;
        $offset = ($current_page - 1) * $records_per_page;
    
        $categoryValue = isset($_POST['dropdown']) ? $_POST['dropdown'] : '';
        
    
        $categoryQuery = "SELECT * FROM books WHERE Category LIKE '$categoryValue' LIMIT $records_per_page OFFSET $offset";
        $categoryResult = $conn->query($categoryQuery);
    
        echo "<div>";
        if ($current_page > 1) {
            echo "<a href='mainHome.php?page=" . ($current_page - 1) . "&dropdown=$categoryValue'>Previous Page</a>";
            echo " | "; //echoing nav links
        }
        echo "<a href='mainHome.php?page=" . ($current_page + 1) . "&dropdown=$categoryValue'>Next Page</a>";
        echo "</div>";
    
        if ($categoryResult && $categoryResult->num_rows > 0) {
            echo "Results found for Category '$categoryValue'.";
            echo "<table border='1'>";
            echo "<tr><th>ISBN</th><th>Book Title</th><th>Author</th><th>Edition</th><th>Year</th><th>Category</th><th>Reserved</th><th>Action</th></tr>";
            while ($row = $categoryResult->fetch_assoc()) {
                echo "<tr>";
                echo "<td>" . $row['ISBN'] . "</td>";
                echo "<td>" . $row['BookTitle'] . "</td>";
                echo "<td>" . $row['Author'] . "</td>";
                echo "<td>" . $row['Edition'] . "</td>";
                echo "<td>" . $row['Year'] . "</td>";
                echo "<td>" . $row['Category'] . "</td>";
                echo "<td>" . $row['Reserved'] . "</td>";
        
                echo "<td>"; // Opening the table cell for the form
                echo "<form method='post'>";
                echo "<input type='hidden' name='ISBN' value='" . $row['ISBN'] . "'>"; // this line sends the isbn
                echo "<input type='submit' name='reserve' value='Reserve'>";
                echo "<input type='submit' name='remove' value='Remove'>";
                echo "</form>";
                echo "</td>"; 
            }
            echo "</table>";
        } else {
            echo "No results found for Category '$categoryValue'.";
        }
    }

    
    if (isset($_GET['search']))
    {
        if(!empty($SearchValue)) // we dont want to display anything if search is empty
        {
            
            if (isset($_POST['dropdown']))
            {
                unset($_GET['search']);
                $current_page = isset($_GET['page']) ? $_GET['page'] : 1;
                $records_per_page = 5;
                $offset = ($current_page - 1) * $records_per_page;
            
                $categoryValue = isset($_POST['dropdown']) ? $_POST['dropdown'] : '';
                
            
                $categoryQuery = "SELECT * FROM books WHERE Category LIKE '$categoryValue' LIMIT $records_per_page OFFSET $offset";
                $categoryResult = $conn->query($categoryQuery);
            
                echo "<div>";
                if ($current_page > 1) 
                {
                    echo "<a href='mainHome.php?page=" . ($current_page - 1) . "&dropdown=$categoryValue'>Previous Page</a>";
                    echo " | ";
                }
                echo "<a href='mainHome.php?page=" . ($current_page + 1) . "&dropdown=$categoryValue'>Next Page</a>";
                echo "</div>";
            
                if ($categoryResult && $categoryResult->num_rows > 0) 
                {
                    echo "Category function successful";
                    echo "<table border='1'>";
                    echo "<tr><th>ISBN</th><th>Book Title</th><th>Author</th><th>Edition</th><th>Year</th><th>Category</th><th>Reserved</th><th>Action</th></tr>";
                    while ($row = $categoryResult->fetch_assoc()) {
                        echo "<tr>";
                        echo "<td>" . $row['ISBN'] . "</td>";
                        echo "<td>" . $row['BookTitle'] . "</td>";
                        echo "<td>" . $row['Author'] . "</td>";
                        echo "<td>" . $row['Edition'] . "</td>";
                        echo "<td>" . $row['Year'] . "</td>";
                        echo "<td>" . $row['Category'] . "</td>";
                        echo "<td>" . $row['Reserved'] . "</td>";
            
                        echo "<form method='post'>";
                        echo "<input type='hidden' name='ISBN' value='" . $row['ISBN'] . "'>"; // this line sends the isbn
                        echo "<input type='submit' name='reserve' value='Reserve'>";
                        echo "<input type='submit' name='remove' value='Remove'>";
                        echo "</form>";
            
                        echo "</tr>";
                    }
                    echo "</table>";
                } else 
                {
                    echo "No results found for this category.";
                }
                // dropdown code finishes here
            } else {
                unset($_POST['dropdown']); // search code starts here
                $searchQuery = "SELECT * FROM books WHERE BookTitle LIKE '%$SearchValue%' OR Author LIKE '%$SearchValue%' LIMIT $records_per_page OFFSET $offset";
                $searchResult = $conn->query($searchQuery);

                // Navigation through pages
                echo "<div>";
                if ($current_page > 1) 
                {
                    echo "<a href='mainHome.php?page=" . ($current_page - 1) . "&search=$SearchValue'>Previous Page</a>";
                    echo " | ";
                }
                echo "<a href='mainHome.php?page=" . ($current_page + 1) . "&search=$SearchValue'>Next Page</a>";
                echo "</div>";

                    if ($searchResult->num_rows > 0) 
                    {
                        echo "Results found for '$SearchValue'.";
                        echo "<table border='1'>";
                        echo "<tr><th>ISBN</th><th>Book Title</th><th>Author</th><th>Edition</th><th>Year</th><th>Category</th><th>Reserved</th><th>Action</th></tr>";
                        while ($row = $searchResult->fetch_assoc()) 
                        {
                            echo "<tr>";
                            echo "<td>" . $row['ISBN'] . "</td>";
                            echo "<td>" . $row['BookTitle'] . "</td>";
                            echo "<td>" . $row['Author'] . "</td>";
                            echo "<td>" . $row['Edition'] . "</td>";
                            echo "<td>" . $row['Year'] . "</td>";
                            echo "<td>" . $row['Category'] . "</td>";
                            echo "<td>" . $row['Reserved'] . "</td>";

                            echo "<td>"; // Opening the table cell for the form
                            echo "<form method='post'>";
                            echo "<input type='hidden' name='ISBN' value='" . $row['ISBN'] . "'>"; // this line sends the isbn
                            echo "<input type='submit' name='reserve' value='Reserve'>";
                            echo "<input type='submit' name='remove' value='Remove'>";
                            echo "</form>";
                            echo "</td>"; // Closing the table cell for the form

                            echo "</tr>";
                    }
                    echo "</table>";
                } else 
                {
                    echo "No results found for '$SearchValue'.";
                }
            }
        }
    }
    

    if (isset($_POST['reserve']) && isset($_POST['ISBN'])) 
    {
        $ISBN = $_POST['ISBN']; 
        // check if the book exists in the reservations table
        $verifyQuery = "SELECT * FROM reservations WHERE ISBN ='$ISBN'";
        $verifyResult = $conn->query($verifyQuery);
    
        if ($verifyResult->num_rows > 0) { // book exists in reservations
            // check if the book is already reserved
            $checkQuery = "SELECT Reserved FROM books WHERE Reserved ='Y' AND ISBN ='$ISBN'";
            $checkResult = $conn->query($checkQuery);
            if ($checkResult->num_rows > 0) {
                echo "$ISBN is already reserved!";
            } else {
                $reserveQuery = "UPDATE reservations SET Username = '$Username', ReservedDate = NOW() WHERE ISBN = '$ISBN'";
                if ($conn->query($reserveQuery) === TRUE) {
                    echo "$ISBN Reserved!";
                    $_SESSION['ISBN'] = $ISBN;
                } else {
                    echo "Error: " . $reserveQuery . "<br>" . $conn->error;
                }
                
                // Update the 'books' table to mark the book as reserved
                $reserveUpdateQuery = "UPDATE books SET Reserved = 'Y' WHERE ISBN = '$ISBN'";
                if ($conn->query($reserveUpdateQuery) !== TRUE) {
                    echo "Error: " . $reserveUpdateQuery . "<br>" . $conn->error;
                }
            }
        } else 
        { 
            // if the book doesn't exist in reservations, add it and mark it as reserved in 'books' table
            $reserveInsertQuery = "INSERT INTO reservations (ISBN, Username, ReservedDate) VALUES ('$ISBN', '$Username', NOW())";
    
            if ($conn->query($reserveInsertQuery) === TRUE) {
                echo "$ISBN Reserved and inserted into reservations!";
                $_SESSION['ISBN'] = $ISBN;
                $reserveUpdateQuery = "UPDATE books SET Reserved = 'Y' WHERE ISBN = '$ISBN'";
                if ($conn->query($reserveUpdateQuery) !== TRUE) {
                    echo "Error: " . $reserveUpdateQuery . "<br>" . $conn->error;
                }
            } else {
                echo "Error: " . $reserveInsertQuery . "<br>" . $conn->error;
            }
        }      
    }
    

    
    if (isset($_POST['remove'])) 
    {
        if (isset($_POST['ISBN'])) {
            $ISBN = $_POST['ISBN'];
    
            $checkQuery = "SELECT * FROM reservations WHERE Username ='$Username' AND ISBN ='$ISBN'";
            $checkResult = $conn->query($checkQuery);

            $check2Query = "SELECT * FROM books WHERE Reserved ='N' AND ISBN ='$ISBN'";
            $check2Result = $conn->query($check2Query);
            
            if ($checkResult->num_rows > 0) {
                $remove2Query = "UPDATE reservations SET Username='', ReservedDate='' WHERE ISBN='$ISBN' AND Username='$Username'";
                
                if ($conn->query($remove2Query) === TRUE) {
                    echo "$ISBN Removed!";
                } else {
                    echo "Error, you have not previously reserved this book";
                }
    
                $removeQuery = "UPDATE books SET Reserved='N' WHERE ISBN='$ISBN'";
                
                if ($conn->query($removeQuery) === TRUE) {
                    //echo "$ISBN Removed PART 1!"; //debugging message
                } else {
                    echo "Error: " . $reserveQuery . "<br>" . $conn->error;
                }
    
                $check2Query = "SELECT * FROM books WHERE Reserved ='N' AND ISBN ='$ISBN'";
                $check2Result = $conn->query($check2Query);
                
                if ($check2Result->num_rows > 0) {
                    //echo "$ISBN Removed PART 1!"; //debugging message
                }
            } elseif($check2Result->num_rows > 0)
            {
                echo "Error, you have not previously reserved this book";
            }
            else
            {
                echo "Another user has already reserved this book";
            }
        } else {
            echo "Error: ISBN is not set!";
        }
    }
    
echo"<br>";
echo"<br>";
echo"<br>";
echo"<br>";

?>
<footer>
    <p>Developed by Glenn Collins 2023</p>
</footer>


</body>
</html>




