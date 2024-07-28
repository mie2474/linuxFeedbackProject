<?php
// Database configuration
$db_host = 'localhost'; #add your localhost or ip here
$db_user = 'djbanx'; #add your database username here
$db_pass = 'djbanX-1'; #Add your database password
$db_name = 'linuxPJsolo'; #add your db name


// Connect to MySQL database
$conn = new mysqli($db_host, $db_user, $db_pass, $db_name);

// Check connection
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

// Handle form submission
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $fname = $conn->real_escape_string($_POST['fname']);
    $lname = $conn->real_escape_string($_POST['lname']);
    $rating = $conn->real_escape_string($_POST['rating']);
    $comment = $conn->real_escape_string($_POST['comment']);

    // Prepare SQL statement to insert data
    $insert_sql = "INSERT INTO feedbacks (fname, lname, rating, comment) VALUES ('$fname', '$lname', '$rating', '$comment')";

    if ($conn->query($insert_sql) === TRUE) {
        echo "<h3>Feedback submitted successfully.</h3><br>";
    } else {
        echo "Error: " . $insert_sql . "<br>" . $conn->error;
    }
    

// Close database connection
    $conn->close();
} else {
    // Redirect to the homepage
    header("Location: http://localhost");
    exit();
}
?>

