document.addEventListener('DOMContentLoaded', function() {
    const signInForm = document.getElementById('create_account');
    const signUpForm = document.getElementById('signIn');
    const registrationForm = document.querySelector('.registration_form');
    const loginForm = document.querySelector('.login_form');

    // Handle Registration Form Submission
    registrationForm.addEventListener('submit', function(event) {
        event.preventDefault();
        const formData = new FormData(registrationForm);

        fetch('register.php', {
            method: 'POST',
            body: formData
        })
        .then(response => response.json())
        .then(data => {
            alert(data.message);
            if (data.status === 'success') {
                window.location.href = 'main.php'; // Redirect to login page after successful registration
            }
        })
        .catch(error => console.error('Error:', error));
    });

    // Handle Login Form Submission
    loginForm.addEventListener('submit', function(e) {
        e.preventDefault();
        const formData = new FormData(loginForm);

        fetch('login.php', {
            method: 'POST',
            body: formData
        })
        .then(response => response.json())
        .then(data => {
            alert(data.message);
            if (data.status === 'success') {
                window.location.href = 'homepage.php'; // Redirect to homepage after successful login
            }
        })
        .catch(error => console.error('Error:', error));
    });

    // Show Registration Form
    document.querySelector('.create_account').addEventListener('click', function() {
        signUpForm.style.display = 'none';
        signInForm.style.display = 'block';
    });

    // Show Login Form
    document.querySelector('.signIn').addEventListener('click', function() {
        signUpForm.style.display = 'block';
        signInForm.style.display = 'none';
    });
});
