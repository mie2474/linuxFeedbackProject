<?php
// Database configuration
$db_host = 'localhost'; #add your localhost or ip here
$db_user = 'claude'; #add your database username here
$db_pass = 'TeamClaude-6'; #Add your database password
$db_name = 'feedbackproject'; #add your db name


// Connect to MySQL database
$conn = new mysqli($db_host, $db_user, $db_pass, $db_name);

// Check connection
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

// Handle form submission
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $first_name = $conn->real_escape_string($_POST['fname']);
    $last_name = $conn->real_escape_string($_POST['lname']);
    $rating = $conn->real_escape_string($_POST['rating']);
    $comment = $conn->real_escape_string($_POST['comment']);

    // Prepare SQL statement to insert data
    $insert_sql = "INSERT INTO feedback (first_name, last_name, rating, comment) VALUES ('$first_name', '$last_name', '$rating', '$comment')";

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

