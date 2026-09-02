/*=========================================
        DIGITAL SKILL PASSPORT
              LOGIN PAGE
=========================================*/


//=========================================
// SHOW / HIDE PASSWORD
//=========================================

function togglePassword(){

    const password =
    document.getElementById("password");

    const eyeIcon =
    document.getElementById("eyeIcon");

    if(password.type==="password"){

        password.type="text";

        eyeIcon.classList.remove("fa-eye");

        eyeIcon.classList.add("fa-eye-slash");

    }

    else{

        password.type="password";

        eyeIcon.classList.remove("fa-eye-slash");

        eyeIcon.classList.add("fa-eye");

    }

}



//=========================================
// FORM VALIDATION
//=========================================

const loginForm =
document.querySelector("form");

loginForm.addEventListener("submit",function(event){

    const email =
    document.querySelector("input[name='email']").value.trim();

    const password =
    document.getElementById("password").value.trim();

    const emailPattern =
    /^[^\s@]+@[^\s@]+\.[^\s@]+$/;

    if(email===""){

        alert("Please enter your email address.");

        event.preventDefault();

        return;

    }

    if(!emailPattern.test(email)){

        alert("Please enter a valid email address.");

        event.preventDefault();

        return;

    }

    if(password===""){

        alert("Please enter your password.");

        event.preventDefault();

        return;

    }

    if(password.length<8){

        alert("Password must contain at least 8 characters.");

        event.preventDefault();

        return;

    }

});



//=========================================
// INPUT FIELD FOCUS EFFECT
//=========================================

const inputs =
document.querySelectorAll(".input-box input");

inputs.forEach(function(input){

    input.addEventListener("focus",function(){

        this.parentElement.style.transform="scale(1.02)";

    });

    input.addEventListener("blur",function(){

        this.parentElement.style.transform="scale(1)";

    });

});



//=========================================
// EMAIL TO LOWERCASE
//=========================================

const emailField =
document.querySelector("input[name='email']");

emailField.addEventListener("keyup",function(){

    this.value=this.value.toLowerCase();

});



//=========================================
// PASSWORD STRENGTH COLOR
//=========================================

const passwordField =
document.getElementById("password");

passwordField.addEventListener("keyup",function(){

    if(this.value.length>=8){

        this.style.borderColor="#22c55e";

    }

    else{

        this.style.borderColor="#ef4444";

    }

});



//=========================================
// LOGIN BUTTON HOVER EFFECT
//=========================================

const loginButton =
document.querySelector(".login-btn");

loginButton.addEventListener("mouseenter",function(){

    this.style.transform="translateY(-3px)";

});

loginButton.addEventListener("mouseleave",function(){

    this.style.transform="translateY(0px)";

});



//=========================================
// PAGE LOAD ANIMATION
//=========================================

window.addEventListener("load",function(){

    const card =
    document.querySelector(".login-card");

    card.style.opacity="0";

    card.style.transform="translateY(30px)";

    setTimeout(function(){

        card.style.transition="0.8s";

        card.style.opacity="1";

        card.style.transform="translateY(0)";

    },200);

});



//=========================================
// ENTER KEY SUPPORT
//=========================================

document.addEventListener("keydown",function(event){

    if(event.key==="Enter"){

        loginButton.click();

    }

});



//=========================================
// REMEMBER ME
//=========================================

const remember =
document.querySelector("input[name='remember']");

remember.addEventListener("change",function(){

    if(this.checked){

        console.log("Remember Me Enabled");

    }

    else{

        console.log("Remember Me Disabled");

    }

});



//=========================================
// DISABLE MULTIPLE CLICKS
//=========================================

loginForm.addEventListener("submit",function(){

    loginButton.disabled=true;

    loginButton.innerHTML="<i class='fa-solid fa-spinner fa-spin'></i> Logging In...";

});



//=========================================
// END OF FILE
//=========================================