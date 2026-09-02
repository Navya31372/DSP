
//=========================================
// PROJECT IMAGE PREVIEW
//=========================================
/*==================================================
        DIGITAL SKILL PASSPORT
             PROJECTS PAGE
===================================================*/


//=========================================
// PAGE LOAD
//=========================================

document.addEventListener("DOMContentLoaded", function () {

    initializeProjectPage();

});


//=========================================
// MAIN INITIALIZATION
//=========================================

function initializeProjectPage() {

    setupMobileSidebar();

    setupProjectImagePreview();

    setupProjectSearch();

    setupProjectFilter();

    setupDeleteButtons();

    setupEditButtons();

    setupNotification();

    setupProjectForm();

    setupDateValidation();

}


//=========================================
// MOBILE SIDEBAR
//=========================================

function setupMobileSidebar() {

    const menuButton =
        document.querySelector(".menu-toggle");

    const sidebar =
        document.querySelector(".sidebar");


    if (!menuButton || !sidebar) {

        return;

    }


    menuButton.addEventListener("click", function () {

        sidebar.classList.toggle("show-sidebar");

    });


    // Close sidebar when clicking outside

    document.addEventListener("click", function (event) {

        if (
            window.innerWidth <= 768 &&
            sidebar.classList.contains("show-sidebar") &&
            !sidebar.contains(event.target) &&
            !menuButton.contains(event.target)
        ) {

            sidebar.classList.remove("show-sidebar");

        }

    });

}


function setupProjectImagePreview() {

    const imageInput =
        document.getElementById("projectImage");

    const imagePreview =
        document.getElementById("imagePreview");


    if (!imageInput || !imagePreview) {

        return;

    }


    imageInput.addEventListener("change", function () {

        const file = this.files[0];


        if (!file) {

            imagePreview.style.display = "none";

            imagePreview.innerHTML = "";

            return;

        }


        // Check file type

        const allowedTypes = [

            "image/jpeg",
            "image/jpg",
            "image/png",
            "image/webp"

        ];


        if (!allowedTypes.includes(file.type)) {

            alert(
                "Please select a JPG, JPEG, PNG or WEBP image."
            );

            this.value = "";

            imagePreview.style.display = "none";

            return;

        }


        // Check file size

        const maxSize =
            5 * 1024 * 1024;


        if (file.size > maxSize) {

            alert(
                "Image size should not exceed 5 MB."
            );

            this.value = "";

            imagePreview.style.display = "none";

            return;

        }


        // Create image preview

        const reader =
            new FileReader();


        reader.onload = function (event) {

            imagePreview.innerHTML = `

                <img
                    src="${event.target.result}"
                    alt="Project Preview"
                >

            `;


            imagePreview.style.display = "block";

        };


        reader.readAsDataURL(file);

    });

}

//=========================================
// PROJECT SEARCH
//=========================================

function setupProjectSearch() {

    const searchInput =
        document.querySelector(".search-box input");

    if (!searchInput) {

        return;

    }


    searchInput.addEventListener("input", function () {

        applyProjectFilters();

    });

}


//=========================================
// APPLY SEARCH + STATUS FILTER
//=========================================

function applyProjectFilters() {

    const searchInput =
        document.querySelector(".search-box input");

    const filter =
        document.getElementById("projectFilter");

    const projectCards =
        document.querySelectorAll(".project-card");


    const searchValue =
        searchInput
            ? searchInput.value.toLowerCase().trim()
            : "";


    const selectedStatus =
        filter
            ? filter.value
            : "all";


    let visibleProjects = 0;


    projectCards.forEach(function (card) {

        const projectText =
            card.innerText.toLowerCase();


        const projectStatus =
            card.dataset.status;


        const matchesSearch =
            projectText.includes(searchValue);


        const matchesStatus =
            selectedStatus === "all" ||
            projectStatus === selectedStatus;


        if (
            matchesSearch &&
            matchesStatus
        ) {

            card.style.display = "";

            visibleProjects++;

        } else {

            card.style.display = "none";

        }

    });


    showNoProjectsMessage(
        visibleProjects === 0
    );

}


//=========================================
// PROJECT FILTER
//=========================================

function setupProjectFilter() {

    const filter =
        document.getElementById("projectFilter");


    if (!filter) {

        return;

    }


    filter.addEventListener("change", function () {

        applyProjectFilters();

    });

}


//=========================================
// NO PROJECTS MESSAGE
//=========================================

function showNoProjectsMessage(show) {

    let message =
        document.querySelector(".no-projects");


    if (show) {

        if (!message) {

            const projectGrid =
                document.getElementById("projectGrid");


            if (!projectGrid) {

                return;

            }


            message =
                document.createElement("div");


            message.className =
                "no-projects";


            message.innerHTML = `

                <i class="fa-solid fa-folder-open"></i>

                <h3>No Projects Found</h3>

                <p>
                    No projects match your search or filter.
                </p>

            `;


            projectGrid.appendChild(message);

        }

    } else {

        if (message) {

            message.remove();

        }

    }

}


//=========================================
// DELETE PROJECT
//=========================================

function setupDeleteButtons() {

    document.addEventListener(
        "click",
        function (event) {

            const deleteButton =
                event.target.closest(".delete-project");


            if (!deleteButton) {

                return;

            }


            const confirmation =
                confirm(
                    "Are you sure you want to permanently delete this project?"
                );


            if (!confirmation) {

                event.preventDefault();

            }

        }
    );

}


//=========================================
// EDIT PROJECT
//=========================================

function setupEditButtons() {

    document.addEventListener(
        "click",
        function (event) {

            const editButton =
                event.target.closest(".edit-project");


            if (!editButton) {

                return;

            }


            const projectId =
                editButton.dataset.projectId;


            if (!projectId) {

                alert("Project ID not found.");

                return;

            }


            window.location.href =
                "projects.php?edit_id=" + projectId;

        }
    );

}


//=========================================
// NOTIFICATION
//=========================================

function setupNotification() {

    const notification =
        document.querySelector(".notification");

    const popup =
        document.querySelector(".notification-popup");


    if (!notification || !popup) {

        return;

    }


    notification.addEventListener("click", function (event) {

        event.stopPropagation();

        popup.style.display =
            popup.style.display === "block"
                ? "none"
                : "block";

    });


    document.addEventListener("click", function () {

        popup.style.display = "none";

    });

}


//=========================================
// PROJECT FORM
//=========================================

function setupProjectForm() {

    const form =
        document.querySelector(
            ".project-form-section form"
        );


    if (!form) {

        return;

    }


    form.addEventListener(
    "submit",
    function (event) {

        if (!validateProjectForm(form)) {

            event.preventDefault();

            return;

        }

        }
    );


    // Reset form

    form.addEventListener(
        "reset",
        function () {

            setTimeout(function () {

                const preview =
                    document.getElementById(
                        "imagePreview"
                    );


                if (preview) {

                    preview.innerHTML = "";

                    preview.style.display =
                        "none";

                }


                removeValidationClasses(form);

            }, 50);

        }
    );

}


//=========================================
// FORM VALIDATION
//=========================================

function validateProjectForm(form) {

    let isValid = true;


    const projectName =
        document.getElementById("projectName");


    const category =
        document.getElementById("projectCategory");


    const status =
        document.getElementById("projectStatus");


    const technologies =
        document.getElementById("technologies");


    const description =
        document.getElementById("description");


    const requiredFields = [

        projectName,

        category,

        status,

        technologies,

        description

    ];


    requiredFields.forEach(function (field) {

        if (!field) {

            return;

        }


        if (field.value.trim() === "") {

            field.classList.remove("valid");

            field.classList.add("invalid");

            isValid = false;

        } else {

            field.classList.remove("invalid");

            field.classList.add("valid");

        }

    });


    if (!isValid) {

        alert(
            "Please fill in all required project details."
        );

        return false;

    }


    return true;

}


//=========================================
// REMOVE VALIDATION CLASSES
//=========================================

function removeValidationClasses(form) {

    const fields =
        form.querySelectorAll(
            "input, select, textarea"
        );


    fields.forEach(function (field) {

        field.classList.remove("valid");

        field.classList.remove("invalid");

    });

}


//=========================================
// REAL-TIME FORM VALIDATION
//=========================================

const formFields =
    document.querySelectorAll(
        ".project-form-section input:not([type='file']), " +
        ".project-form-section select, " +
        ".project-form-section textarea"
    );


formFields.forEach(function (field) {

    field.addEventListener("blur", function () {

        if (
            this.hasAttribute("required") &&
            this.value.trim() === ""
        ) {

            this.classList.remove("valid");

            this.classList.add("invalid");

        } else if (this.value.trim() !== "") {

            this.classList.remove("invalid");

            this.classList.add("valid");

        }

    });


    field.addEventListener("input", function () {

        if (this.value.trim() !== "") {

            this.classList.remove("invalid");

            this.classList.add("valid");

        }

    });

});


//=========================================
// DATE VALIDATION
//=========================================

function setupDateValidation() {

    const startDate =
        document.getElementById("startDate");


    const endDate =
        document.getElementById("endDate");


    if (!startDate || !endDate) {

        return;

    }


    endDate.addEventListener("change", function () {

        if (
            startDate.value &&
            endDate.value
        ) {

            if (
                new Date(endDate.value) <
                new Date(startDate.value)
            ) {

                alert(
                    "End date cannot be earlier than the start date."
                );

                endDate.value = "";

                endDate.classList.add("invalid");

            } else {

                endDate.classList.remove("invalid");

                endDate.classList.add("valid");

            }

        }

    });

}


//=========================================
// UPDATE PROJECT COUNT
//=========================================

function updateProjectCount() {

    const cards =
        document.querySelectorAll(".project-card");


    const totalCard =
        document.querySelector(
            ".summary-card.total h2"
        );


    const completedCard =
        document.querySelector(
            ".summary-card.completed h2"
        );


    const ongoingCard =
        document.querySelector(
            ".summary-card.ongoing h2"
        );


    if (totalCard) {

        totalCard.textContent =
            String(cards.length).padStart(2, "0");

    }


    let completed = 0;

    let ongoing = 0;


    cards.forEach(function (card) {

        if (
            card.dataset.status ===
            "completed"
        ) {

            completed++;

        }


        if (
            card.dataset.status ===
            "ongoing"
        ) {

            ongoing++;

        }

    });


    if (completedCard) {

        completedCard.textContent =
            String(completed).padStart(2, "0");

    }


    if (ongoingCard) {

        ongoingCard.textContent =
            String(ongoing).padStart(2, "0");

    }

}


//=========================================
// SMOOTH SCROLL TO ADD PROJECT
//=========================================

const addProjectButton =
    document.querySelector(".add-project-btn");


if (addProjectButton) {

    addProjectButton.addEventListener(
        "click",
        function () {

            const formSection =
                document.getElementById(
                    "addProjectForm"
                );


            if (formSection) {

                setTimeout(function () {

                    const firstInput =
                        document.getElementById(
                            "projectName"
                        );


                    if (firstInput) {

                        firstInput.focus();

                    }

                }, 500);

            }

        }
    );

}


//=========================================
// LOGOUT CONFIRMATION
//=========================================

const logoutLink =
    document.querySelector(
        "a[href='logout.php']"
    );


if (logoutLink) {

    logoutLink.addEventListener(
        "click",
        function (event) {

            const confirmation =
                confirm(
                    "Are you sure you want to logout?"
                );


            if (!confirmation) {

                event.preventDefault();

            }

        }
    );

}


//=========================================
// CURRENT YEAR
//=========================================

const currentYear =
    new Date().getFullYear();


const footer =
    document.querySelector(
        ".projects-footer p"
    );


if (footer) {

    footer.innerHTML =
        footer.innerHTML.replace(
            "2026",
            currentYear
        );

}


//=========================================
// AUTO HIDE SUCCESS MESSAGE
//=========================================

const successMessage =
    document.querySelector(".success-message");

if (successMessage) {

    setTimeout(function () {

        successMessage.style.display = "none";

    }, 3000);

}


//=========================================
// END OF PROJECTS.JS
//=========================================