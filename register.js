document.addEventListener('DOMContentLoaded', function() {
    const form = document.querySelector('.registration_form');
    
    form.addEventListener('submit', function(event) {
        event.preventDefault();
        
        // Form submission logic here to add to the databases
        alert('Form submitted successfully!');
        window.location.href = 'Login.html';//either login automatically or go back to the login page to login with the newly created account
    });
});