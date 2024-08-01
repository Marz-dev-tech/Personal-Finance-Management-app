<?php
include 'connect.php';
session_start();

$response = array('status' => 'error', 'message' => '');

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $email = $conn->real_escape_string($_POST['email']);
    $password = $_POST['password'];

    $sql = "SELECT * FROM users WHERE email = '$email'";
    $result = $conn->query($sql);

    if ($result->num_rows == 1) {
        $user = $result->fetch_assoc();

        if (password_verify($password, $user['password'])) {
            $_SESSION['loggedin'] = true;
            $_SESSION['user_id'] = $user['id'];
            $_SESSION['user_name'] = $user['first_name'];
            $response['status'] = 'success';
            $response['message'] = 'Login successful!';
          //  header("Location: homepage.php"); // Redirect after login
        } else {
            $response['message'] = 'Incorrect password.';
        }
    } else {
        $response['message'] = 'No user found with this email.';
    }
}

header('Content-Type: application/json');
echo json_encode($response);
$conn->close();
?>
