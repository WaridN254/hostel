<?php
defined('BASEPATH') OR exit('No direct script access allowed');

// Load the database config
include_once 'spymvc/config/database.php';

// Create connection
$conn = new mysqli($db['default']['hostname'], $db['default']['username'], $db['default']['password'], $db['default']['database']);

// Check connection
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

// Query to get all admin users
$sql = "SELECT id, user_email, user_type, user_pass FROM tbl_users";
$result = $conn->query($sql);

echo "<h2>Admin Users</h2>";
echo "<table border='1'>";
echo "<tr><th>ID</th><th>Email</th><th>Type</th><th>Password Hash</th></tr>";

if ($result->num_rows > 0) {
    while($row = $result->fetch_assoc()) {
        echo "<tr>";
        echo "<td>" . $row["id"] . "</td>";
        echo "<td>" . $row["user_email"] . "</td>";
        echo "<td>" . $row["user_type"] . "</td>";
        echo "<td>" . $row["user_pass"] . "</td>";
        echo "</tr>";
    }
} else {
    echo "<tr><td colspan='4'>No admin users found</td></tr>";
}
echo "</table>";

// Close connection
$conn->close();
?>
