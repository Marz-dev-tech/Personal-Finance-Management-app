<!DOCTYPE html>
<html>
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>360 Financial Services</title>
    <link rel="stylesheet" href="main.css">
    <link rel="stylesheet" href="register.css">
</head>
<body>
<!-- Register form -->
    <div class="form_container" id="create_account" style="display:none;">
        <form class="registration_form">
            <h2>New Account</h2>
            <div class="form_group">
                <label for="first-name">First Name</label>
                <input type="text" id="first-name" name="first-name" required>
            </div>
            <div class="form_group">
                <label for="last-name">Last Name</label>
                <input type="text" id="last-name" name="last-name" required>
            </div>
            <div class="form_group">
                <label for="email">Email</label>
                <input type="email" id="email" name="email" required>
            </div>
            <div class="form_group">
                <label for="password">Password</label>
                <input type="password" id="password" name="password" required>
            </div>
            <button type="submit">Create</button>
        </form>
        <a href="#" class="signIn">Already have an account? <span>Sign In</span></a>
    </div>
<!-- Login form -->
<div class="container" id="signIn">
    <div class="logo">
        <img src="360logo1.png" alt="logo">
    </div>
    <h1>360 Financial Services</h1>
    <form class="login_form" method="post" action="login.php">
        <input type="text" placeholder="Email" name="email" required>
        <input type="password" placeholder="Password" name="password" required>
        <button type="submit">Log In</button>
        <a href="#" class="forgot_password">Forgot Password?</a>
    </form>
    <a href="#" class="create_account">Don't have an account? <span>Create Account</span></a>
    <div class="footer">
        <h2>We are more than just a company</h2>
        <p>Here at 360 Financial Services, we offer you a 360 degree view of all your liquid assets in one place as well as current investments that are currently being offered.</p>
    </div>
</div>

   <script src="main.js"></script>
</body>
</html>
