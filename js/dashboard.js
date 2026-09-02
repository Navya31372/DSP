/*=========================================
        DIGITAL SKILL PASSPORT
            DASHBOARD PAGE
=========================================*/


//=========================================
// PAGE LOADING ANIMATION
//=========================================

window.addEventListener("load",function(){

    document.querySelector(".dashboard-container").style.opacity="0";

    setTimeout(function(){

        document.querySelector(".dashboard-container").style.transition="0.8s";

        document.querySelector(".dashboard-container").style.opacity="1";

    },200);

});



//=========================================
// CARD HOVER EFFECT
//=========================================

const cards=document.querySelectorAll(".card");

cards.forEach(function(card){

    card.addEventListener("mouseenter",function(){

        this.style.transform="translateY(-8px)";

    });

    card.addEventListener("mouseleave",function(){

        this.style.transform="translateY(0px)";

    });

});



//=========================================
// NOTIFICATION ICON
//=========================================

const notification=document.querySelector(".notification");

notification.addEventListener("click",function(){

    alert("You have 3 new notifications.");

});



//=========================================
// MENU TOGGLE (FOR MOBILE)
//=========================================

const menuButton=document.querySelector(".menu-toggle");

const sidebar=document.querySelector(".sidebar");

menuButton.addEventListener("click",function(){

    sidebar.classList.toggle("show-sidebar");

});



//=========================================
// ACTIVE MENU
//=========================================

const menuLinks=document.querySelectorAll(".menu li");

menuLinks.forEach(function(link){

    link.addEventListener("click",function(){

        menuLinks.forEach(function(item){

            item.classList.remove("active");

        });

        this.classList.add("active");

    });

});



//=========================================
// PROGRESS BAR ANIMATION
//=========================================

window.addEventListener("load",function(){

    const profileProgress=document.querySelector(".profile-progress");

    const skillProgress=document.querySelector(".skill-progress");

    profileProgress.style.width="75%";

    skillProgress.style.width="60%";

});



//=========================================
// QUICK ACTION BUTTONS
//=========================================

const actionButtons=document.querySelectorAll(".action-btn");

actionButtons.forEach(function(button){

    button.addEventListener("mouseenter",function(){

        this.style.transform="translateY(-5px) scale(1.02)";

    });

    button.addEventListener("mouseleave",function(){

        this.style.transform="translateY(0px) scale(1)";

    });

});



//=========================================
// PROFILE IMAGE EFFECT
//=========================================

const profile=document.querySelector(".user-profile img");

profile.addEventListener("mouseenter",function(){

    this.style.transform="scale(1.1)";

});

profile.addEventListener("mouseleave",function(){

    this.style.transform="scale(1)";

});



//=========================================
// WELCOME BUTTON
//=========================================

const editButton=document.querySelector(".edit-btn");

editButton.addEventListener("mouseenter",function(){

    this.style.transform="translateY(-3px)";

});

editButton.addEventListener("mouseleave",function(){

    this.style.transform="translateY(0px)";

});



//=========================================
// RECENT ACTIVITY HOVER
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
// CURRENT DATE
//=========================================

const today=new Date();

console.log("Today's Date : "+today.toDateString());



//=========================================
// LOGOUT CONFIRMATION
//=========================================

const logout=document.querySelector("a[href='logout.php']");

logout.addEventListener("click",function(event){

    const answer=confirm("Are you sure you want to logout?");

    if(!answer){

        event.preventDefault();

    }

});



//=========================================
// SETTINGS BUTTON
//=========================================

const settings=document.querySelector("a[href='settings.php']");

settings.addEventListener("mouseenter",function(){

    this.style.paddingLeft="30px";

});

settings.addEventListener("mouseleave",function(){

    this.style.paddingLeft="18px";

});



//=========================================
// CARD COUNT ANIMATION
//=========================================

const counts=document.querySelectorAll(".card-details h2");

counts.forEach(function(counter){

    let target=parseInt(counter.innerText);

    let count=0;

    let speed=40;

    const updateCounter=function(){

        if(count<target){

            count++;

            counter.innerText=count;

            setTimeout(updateCounter,speed);

        }

        else{

            counter.innerText=target;

        }

    };

    updateCounter();

});



//=========================================
// SMOOTH SCROLL
//=========================================

document.querySelectorAll("a").forEach(function(anchor){

    anchor.addEventListener("click",function(){

        window.scrollTo({

            top:0,

            behavior:"smooth"

        });

    });

});



//=========================================
// END OF FILE
//=========================================