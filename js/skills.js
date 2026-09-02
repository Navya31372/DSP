/*=========================================
      DIGITAL SKILL PASSPORT
            SKILLS PAGE
=========================================*/


//=========================================
// PAGE LOADING ANIMATION
//=========================================

window.addEventListener("load", function () {

    const container = document.querySelector(".skills-container");

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

    item.addEventListener("click", function(){

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
// RESET BUTTON
//=========================================

const resetButton = document.querySelector(".reset-btn");

if(resetButton){

    resetButton.addEventListener("click", function(){

        if(confirm("Clear all entered details?")){

            skillForm.reset();

        }

    });

}



//=========================================
// PROGRESS BAR ANIMATION
//=========================================

window.addEventListener("load", function(){

    const bars = document.querySelectorAll(".progress-fill");

    bars.forEach(function(bar){

        const value = bar.innerHTML;

        bar.style.width = "0";

        setTimeout(function(){

            bar.style.width = value;

        },300);

    });

});



//=========================================
// EDIT BUTTON
//=========================================

const editButtons = document.querySelectorAll(".edit");

editButtons.forEach(function(button){

    button.addEventListener("click", function(){

        const userSkillId = this.dataset.id;

        window.location.href = "skills.php?edit_id=" + userSkillId;

    });

});



//=========================================
// DELETE BUTTON
//=========================================

const deleteButtons = document.querySelectorAll(".delete");

deleteButtons.forEach(function(button){

    button.addEventListener("click", function(){

        if(confirm("Delete this skill?")){

            const userSkillId = this.dataset.id;

            const form = document.createElement("form");

            form.method = "POST";
            form.action = "skills.php";

            const actionInput = document.createElement("input");

            actionInput.type = "hidden";
            actionInput.name = "delete_skill";
            actionInput.value = "1";

            const idInput = document.createElement("input");

            idInput.type = "hidden";
            idInput.name = "user_skill_id";
            idInput.value = userSkillId;

            form.appendChild(actionInput);
            form.appendChild(idInput);

            document.body.appendChild(form);

            form.submit();

        }

    });

});



//=========================================
// SUMMARY CARD EFFECT
//=========================================

const summaryCards = document.querySelectorAll(".summary-card");

summaryCards.forEach(function(card){

    card.addEventListener("mouseenter", function(){

        this.style.transform = "translateY(-8px)";

    });

    card.addEventListener("mouseleave", function(){

        this.style.transform = "translateY(0px)";

    });

});



//=========================================
// CATEGORY CARD EFFECT
//=========================================

const categoryCards = document.querySelectorAll(".category-card");

categoryCards.forEach(function(card){

    card.addEventListener("mouseenter", function(){

        this.style.transform = "translateY(-8px)";

    });

    card.addEventListener("mouseleave", function(){

        this.style.transform = "translateY(0px)";

    });

});



//=========================================
// TABLE ROW HOVER
//=========================================

const rows = document.querySelectorAll("tbody tr");

rows.forEach(function(row){

    row.addEventListener("mouseenter", function(){

        this.style.background = "#f8fafc";

    });

    row.addEventListener("mouseleave", function(){

        this.style.background = "transparent";

    });

});



//=========================================
// SEARCH FILTER
//=========================================

const searchInput = document.querySelector(".search-box input");

if(searchInput){

    searchInput.addEventListener("keyup", function(){

        const value = this.value.toLowerCase();

        const tableRows = document.querySelectorAll("tbody tr");

        tableRows.forEach(function(row){

            const text = row.innerText.toLowerCase();

            row.style.display = text.includes(value) ? "" : "none";

        });

    });

}



//=========================================
// LOGOUT CONFIRMATION
//=========================================

const logout = document.querySelector("a[href='logout.php']");

if(logout){

    logout.addEventListener("click", function(event){

        if(!confirm("Are you sure you want to logout?")){

            event.preventDefault();

        }

    });

}



//=========================================
// CURRENT DATE
//=========================================

const today = new Date();

console.log("Today : " + today.toDateString());



//=========================================
// END OF FILE
//=========================================