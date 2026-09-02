//======================================
// MOBILE MENU TOGGLE
//======================================

const menuBtn = document.querySelector(".menu-btn");
const navMenu = document.querySelector(".nav-menu");

menuBtn.addEventListener("click", () => {

    navMenu.classList.toggle("show");

    if(navMenu.classList.contains("show")){
        menuBtn.innerHTML = '<i class="fa-solid fa-xmark"></i>';
    }
    else{
        menuBtn.innerHTML = '<i class="fa-solid fa-bars"></i>';
    }

});


//======================================
// CLOSE MENU AFTER CLICKING A LINK
//======================================

const navLinks = document.querySelectorAll(".nav-menu a");

navLinks.forEach(link => {

    link.addEventListener("click", () => {

        navMenu.classList.remove("show");
        menuBtn.innerHTML = '<i class="fa-solid fa-bars"></i>';

    });

});


//======================================
// ACTIVE NAVIGATION LINK
//======================================

const sections = document.querySelectorAll("section");

window.addEventListener("scroll", () => {

    let current = "";

    sections.forEach(section => {

        const sectionTop = section.offsetTop - 120;
        const sectionHeight = section.clientHeight;

        if(window.scrollY >= sectionTop &&
           window.scrollY < sectionTop + sectionHeight){

            current = section.getAttribute("id");

        }

    });

    navLinks.forEach(link => {

        link.classList.remove("active");

        if(link.getAttribute("href") === "#" + current){

            link.classList.add("active");

        }

    });

});


//======================================
// NAVBAR SHADOW ON SCROLL
//======================================

const header = document.querySelector("header");

window.addEventListener("scroll", () => {

    if(window.scrollY > 50){

        header.style.boxShadow =
        "0 6px 20px rgba(0,0,0,0.15)";

    }
    else{

        header.style.boxShadow =
        "0 3px 12px rgba(0,0,0,0.08)";

    }

});


//======================================
// SCROLL REVEAL ANIMATION
//======================================

const revealItems = document.querySelectorAll(

".feature-card, .timeline-step, .passport, .about"

);

revealItems.forEach(item => {

    item.style.opacity = "0";
    item.style.transform = "translateY(40px)";
    item.style.transition = "all 0.8s ease";

});

function revealOnScroll(){

    revealItems.forEach(item => {

        const windowHeight = window.innerHeight;
        const top = item.getBoundingClientRect().top;

        if(top < windowHeight - 120){

            item.style.opacity = "1";
            item.style.transform = "translateY(0)";

        }

    });

}

window.addEventListener("scroll", revealOnScroll);
window.addEventListener("load", revealOnScroll);


//======================================
// SMOOTH SCROLL
//======================================

document.querySelectorAll('a[href^="#"]').forEach(anchor => {

    anchor.addEventListener("click", function(e){

        e.preventDefault();

        const target = document.querySelector(
            this.getAttribute("href")
        );

        if(target){

            target.scrollIntoView({

                behavior:"smooth"

            });

        }

    });

});


//======================================
// BUTTON HOVER EFFECT
//======================================

const buttons = document.querySelectorAll(".btn");

buttons.forEach(button => {

    button.addEventListener("mouseenter", () => {

        button.style.transform = "scale(1.05)";

    });

    button.addEventListener("mouseleave", () => {

        button.style.transform = "scale(1)";

    });

});


//======================================
// FEATURE CARD EFFECT
//======================================

const cards = document.querySelectorAll(".feature-card");

cards.forEach(card => {

    card.addEventListener("mouseenter", () => {

        card.style.transform = "translateY(-12px)";

    });

    card.addEventListener("mouseleave", () => {

        card.style.transform = "translateY(0)";

    });

});


//======================================
// TIMELINE STEP EFFECT
//======================================

const steps = document.querySelectorAll(".timeline-step");

steps.forEach(step => {

    step.addEventListener("mouseenter", () => {

        step.style.transform = "scale(1.05)";

    });

    step.addEventListener("mouseleave", () => {

        step.style.transform = "scale(1)";

    });

});


//======================================
// PASSPORT CARD EFFECT
//======================================

const passport = document.querySelector(".passport");

passport.addEventListener("mouseenter", () => {

    passport.style.transform =
    "translateY(-10px) rotate(-2deg)";

});

passport.addEventListener("mouseleave", () => {

    passport.style.transform =
    "translateY(0) rotate(0deg)";

});