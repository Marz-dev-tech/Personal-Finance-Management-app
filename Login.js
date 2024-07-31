document.querySelector('.login_form').addEventListener('submit',
    function(e){
        e.preventDefault();
        //window.location.href='Home.html';
        //alert('Log in successful!');
        //add database code here
    }); 

document.querySelector('.forgot_password').addEventListener('click',
    function(e){
        e.preventDefault();
        alert('Answer these security questions');
        //connect to the password reset point
    });

document.querySelector('.create_account').addEventListener('click',
    function(e){
        e.preventDefault();
        window.location.href='register.html';
        //alert('Redirecting to account form');
        //connect to the new account registration form
    });
