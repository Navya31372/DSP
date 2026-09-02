//======================================
// SHOW / HIDE PASSWORD
//======================================

function togglePassword(inputId, icon){

    const input = document.getElementById(inputId);

    if(input.type === "password"){

        input.type = "text";
        icon.classList.remove("fa-eye");
        icon.classList.add("fa-eye-slash");

    }
    else{

        input.type = "password";
        icon.classList.remove("fa-eye-slash");
        icon.classList.add("fa-eye");

    }

}


//======================================
// FORM VALIDATION
//======================================

const form = document.querySelector("form");

form.addEventListener("submit", function(event){

    const fullName = document.querySelector(
        "input[name='fullname']"
    ).value.trim();

    const email = document.querySelector(
        "input[name='email']"
    ).value.trim();

    const phone = document.querySelector(
        "input[name='phone']"
    ).value.trim();

    const password = document.getElementById("password").value;

    const confirmPassword =
    document.getElementById("confirmPassword").value;

    // Name Validation

    if(fullName.length < 3){

        alert("Full Name must contain at least 3 characters.");
        event.preventDefault();
        return;

    }

    // Email Validation

    const emailPattern =
    /^[^\s@]+@[^\s@]+\.[^\s@]+$/;

    if(!emailPattern.test(email)){

        alert("Please enter a valid email address.");
        event.preventDefault();
        return;

    }

    // Phone Validation

    const phonePattern = /^[0-9]{10}$/;

    if(!phonePattern.test(phone)){

        alert("Phone number must contain exactly 10 digits.");
        event.preventDefault();
        return;

    }

    // Password Length

    if(password.length < 8){

        alert("Password must contain at least 8 characters.");
        event.preventDefault();
        return;

    }

    // Password Match

    if(password !== confirmPassword){

        alert("Password and Confirm Password do not match.");
        event.preventDefault();
        return;

    }

    alert("Registration Successful!");

});


//======================================
// INPUT FOCUS EFFECT
//======================================

const inputs = document.querySelectorAll("input");

inputs.forEach(input=>{

    input.addEventListener("focus",()=>{

        input.style.borderColor="#2563eb";

    });

    input.addEventListener("blur",()=>{

        input.style.borderColor="#dbeafe";

    });

});


//======================================
// BUTTON HOVER EFFECT
//======================================

const registerButton =
document.querySelector(".register-btn");

registerButton.addEventListener("mouseenter",()=>{

    registerButton.style.transform="scale(1.02)";

});

registerButton.addEventListener("mouseleave",()=>{

    registerButton.style.transform="scale(1)";

});


//======================================
// PAGE LOAD ANIMATION
//======================================

window.addEventListener("load",()=>{

    document.querySelector(".container").style.opacity="1";

});