// JQUERY ALREADY LOADED

$(document).ready(function(){

    $("#login-submit").click(function(e){
        e.preventDefault();

        if(!validateLogin()){
            return false;
        }

        $("#login").submit();

    });

    function validateLogin(){

        var user_min = $("#username_min").val();
        var user_max= $("#username_max").val();
        var pass_min = $("#password_min").val();
        var pass_max= $("#password_max").val();

        var username = $("#username").val();
        var password = $("#password").val();

        //match username
        var ur = '^(?=.*\\d)(?=.*[a-z])[0-9a-zA-Z]{'+user_min+','+user_max+'}$';
        var pr = '^(?=.*\\d)(?=.*[a-z])(?=.*[A-Z])[0-9a-zA-Z,\?\.@#!]{'+pass_min+','+pass_max+'}$';

        var usernameRegex = new RegExp(ur);
        var passwordRegex = new RegExp(pr);

        if(!usernameRegex.test(username)){
            alert("There was a problem with your username. Please try again.");
            return false;
        }

        if(!passwordRegex.test(password)){
            alert("There was a problem with your password. Please try again.");
            return false;
        }

        return true;
    }

});