/*=========================================
      DIGITAL SKILL PASSPORT
            PROFILE PAGE
=========================================*/


//=========================================
// PAGE LOADING ANIMATION
//=========================================

window.addEventListener("load", function () {

    const container = document.querySelector(".profile-container");

    if (container) {

        container.style.opacity = "0";

        setTimeout(function () {

            container.style.transition = "opacity 0.8s ease";

            container.style.opacity = "1";

        }, 150);

    }

});



//=========================================
// MOBILE SIDEBAR TOGGLE
//=========================================

const menuButton = document.querySelector(".menu-toggle");
const sidebar = document.querySelector(".sidebar");

if (menuButton && sidebar) {

    menuButton.addEventListener("click", function () {

        sidebar.classList.toggle("show-sidebar");

    });

}



//=========================================
// ACTIVE MENU
//=========================================

const menuItems = document.querySelectorAll(".menu li");

menuItems.forEach(function(item){

    item.addEventListener("click",function(){

        menuItems.forEach(function(i){

            i.classList.remove("active");

        });

        this.classList.add("active");

    });

});



//=========================================
// NOTIFICATION
//=========================================

const notification = document.querySelector(".notification");
const notificationPopup = document.querySelector(".notification-popup");

if(notification && notificationPopup){

    notification.addEventListener("click", function(){

        notificationPopup.style.display =
            notificationPopup.style.display === "block"
            ? "none"
            : "block";

    });

}



//=========================================
// PROFILE IMAGE EFFECT
//=========================================

const profileImage = document.querySelector(".profile-image img");

if(profileImage){

    profileImage.addEventListener("mouseenter",function(){

        this.style.transform="scale(1.05)";

    });

    profileImage.addEventListener("mouseleave",function(){

        this.style.transform="scale(1)";

    });

}



//=========================================
// PROGRESS BAR ANIMATION
//=========================================

window.addEventListener("load",function(){

    const bars=document.querySelectorAll(".progress-fill");

    bars.forEach(function(bar){

        const value=bar.innerHTML;

        bar.style.width="0";

        setTimeout(function(){

            bar.style.width=value;

        },300);

    });

});



//=========================================
// EDIT PROFILE BUTTON
//=========================================

const editButton=document.querySelector(".edit-btn");

if(editButton){

    editButton.addEventListener("click",function(e){

        e.preventDefault();

        alert("Edit Profile feature will be added later.");

    });

}



//=========================================
// QUICK ACTION BUTTONS
//=========================================

const actionButtons=document.querySelectorAll(".action-btn");

actionButtons.forEach(function(button){

    button.addEventListener("mouseenter",function(){

        this.style.transform="translateY(-5px) scale(1.03)";

    });

    button.addEventListener("mouseleave",function(){

        this.style.transform="translateY(0px) scale(1)";

    });

});



//=========================================
// STATISTICS CARD HOVER
//=========================================

const statCards=document.querySelectorAll(".stat-card");

statCards.forEach(function(card){

    card.addEventListener("mouseenter",function(){

        this.style.transform="translateY(-8px)";

    });

    card.addEventListener("mouseleave",function(){

        this.style.transform="translateY(0px)";

    });

});



//=========================================
// INFORMATION CARD EFFECT
//=========================================

const infoCards=document.querySelectorAll(".info-card");

infoCards.forEach(function(card){

    card.addEventListener("mouseenter",function(){

        this.style.boxShadow="0 18px 35px rgba(0,0,0,0.12)";

    });

    card.addEventListener("mouseleave",function(){

        this.style.boxShadow="0 10px 25px rgba(0,0,0,0.08)";

    });

});



//=========================================
// PROFESSIONAL LINK CARDS
//=========================================

const linkCards=document.querySelectorAll(".link-card");

linkCards.forEach(function(card){

    card.addEventListener("mouseenter",function(){

        this.style.transform="translateY(-8px)";

    });

    card.addEventListener("mouseleave",function(){

        this.style.transform="translateY(0px)";

    });

});



//=========================================
// RECENT ACTIVITY EFFECT
//=========================================

const activities=document.querySelectorAll(".activity-list li");

activities.forEach(function(activity){

    activity.addEventListener("mouseenter",function(){

        this.style.background="#f8fafc";

    });

    activity.addEventListener("mouseleave",function(){

        this.style.background="transparent";

    });

});



//=========================================
// LOGOUT CONFIRMATION
//=========================================

const logout=document.querySelector("a[href='logout.php']");

if(logout){

    logout.addEventListener("click",function(event){

        const answer=confirm("Are you sure you want to logout?");

        if(!answer){

            event.preventDefault();

        }

    });

}



//=========================================
// CURRENT DATE
//=========================================

const today=new Date();

console.log("Today : "+today.toDateString());



//=========================================
// END OF FILE
//=========================================