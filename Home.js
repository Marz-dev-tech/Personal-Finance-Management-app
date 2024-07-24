document.addEventListener('DOMContentLoaded',function(){
    const balanceElement = document.querySelector('.balance p');//add an automatically updating balance
    const newBalance = 1200;

    //balanceElement.textContent = '$${newBalance}';

    const logoutbtn = document.querySelector('.logout_btn');
    logoutbtn.addEventListener  ('click',function(){
        window.location.href='Login.html';
    })
});