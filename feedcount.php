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


// Retrieve and display total number of feedbacks

$total_feedbacks_sql = "SELECT COUNT(*) AS total_feedbacks FROM feedback";
$result = $conn->query($total_feedbacks_sql);

if ($result->num_rows > 0) {
$row = $result->fetch_assoc();
$total_feedbacks = $row['total_feedbacks'];
echo "Total feedbacks received: $total_feedbacks";
} else {
echo "No feedbacks yet.";
}

// Close database connection
$conn->close();
?>
