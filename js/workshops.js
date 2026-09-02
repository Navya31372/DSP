/*==================================================
        DIGITAL SKILL PASSPORT
              WORKSHOPS PAGE
===================================================*/


document.addEventListener("DOMContentLoaded", function () {


    /*==================================================
                    ELEMENTS
    ===================================================*/

    const workshopForm =
        document.getElementById("workshopForm");

    const certificateInput =
        document.getElementById("workshopCertificate");

    const workshopImageInput =
        document.getElementById("workshopImage");

    const certificatePreview =
        document.getElementById("workshopPreview");

    const workshopFilter =
        document.getElementById("workshopFilter");

    const workshopSearch = 
    document.getElementById("workshopSearch");

    const workshopsGrid =
        document.getElementById("workshopsGrid");

    const noWorkshops =
        document.getElementById("noWorkshops");

    const workshopModal =
        document.getElementById("workshopModal");

    const modalContent =
        document.getElementById("modalWorkshopContent");

    const closeModal =
        document.getElementById("closeWorkshopModal");

    const modalOverlay =
        document.querySelector(".modal-overlay");

    const menuToggle =
        document.querySelector(".menu-toggle");

    const sidebar =
        document.querySelector(".sidebar");


    /*==================================================
                  MOBILE SIDEBAR
    ===================================================*/

    if (menuToggle && sidebar) {

        menuToggle.addEventListener("click", function () {

            sidebar.classList.toggle("show-sidebar");

        });

    }


    /* Close sidebar when a menu item is clicked */

    const sidebarLinks =
        document.querySelectorAll(".sidebar a");

    sidebarLinks.forEach(function (link) {

        link.addEventListener("click", function () {

            if (window.innerWidth <= 768) {

                sidebar.classList.remove("show-sidebar");

            }

        });

    });


    /*==================================================
                CERTIFICATE FILE PREVIEW
    ===================================================*/

    if (certificateInput && certificatePreview) {

        certificateInput.addEventListener(
            "change",
            function () {

                const file = this.files[0];

                certificatePreview.innerHTML = "";

                certificatePreview.style.display = "none";


                if (!file) {
                    return;
                }


                /* Maximum 5 MB */

                if (file.size > 5 * 1024 * 1024) {

                    alert(
                        "Certificate file size must be less than 5 MB."
                    );

                    this.value = "";

                    return;
                }


                const fileName =
                    file.name.toLowerCase();


                /* Image preview */

                if (
                    fileName.endsWith(".jpg") ||
                    fileName.endsWith(".jpeg") ||
                    fileName.endsWith(".png") ||
                    fileName.endsWith(".webp")
                ) {

                    const reader =
                        new FileReader();


                    reader.onload =
                        function (event) {

                            const image =
                                document.createElement("img");

                            image.src =
                                event.target.result;

                            image.alt =
                                "Certificate Preview";

                            certificatePreview
                                .appendChild(image);

                            certificatePreview.style.display =
                                "block";

                        };


                    reader.readAsDataURL(file);

                }


                /* PDF preview */

                else if (
                    fileName.endsWith(".pdf")
                ) {

                    const pdfMessage =
                        document.createElement("div");

                    pdfMessage.className =
                        "pdf-preview-message";

                    pdfMessage.innerHTML = `
                        <i class="fa-solid fa-file-pdf"></i>
                        <strong>${escapeHTML(file.name)}</strong>
                        <span>PDF certificate selected</span>
                    `;

                    certificatePreview
                        .appendChild(pdfMessage);

                    certificatePreview.style.display =
                        "block";

                }

            }
        );

    }


    /*==================================================
                  WORKSHOP IMAGE PREVIEW
    ===================================================*/

    if (workshopImageInput) {

        workshopImageInput.addEventListener(
            "change",
            function () {

                const file = this.files[0];

                if (!file) {
                    return;
                }


                const allowedTypes = [
                    "image/jpeg",
                    "image/png",
                    "image/webp"
                ];


                if (
                    !allowedTypes.includes(file.type)
                ) {

                    alert(
                        "Please select a JPG, PNG or WEBP image."
                    );

                    this.value = "";

                    return;
                }


                /* 5 MB limit */

                if (file.size > 5 * 1024 * 1024) {

                    alert(
                        "Workshop image must be less than 5 MB."
                    );

                    this.value = "";

                    return;
                }

            }
        );

    }


    /*================================================== 
              SEARCH + FILTER WORKSHOPS
===================================================*/

if (workshopFilter) {

    workshopFilter.addEventListener(
        "change",
        filterAndSearchWorkshops
    );

}


if (workshopSearch) {

    workshopSearch.addEventListener(
        "input",
        filterAndSearchWorkshops
    );

}


function filterAndSearchWorkshops() {

    if (!workshopsGrid) {
        return;
    }


    const cards =
        workshopsGrid.querySelectorAll(
            ".workshop-card"
        );


    const filterValue =
        workshopFilter
            ? workshopFilter.value
            : "all";


    const searchValue =
        workshopSearch
            ? workshopSearch.value
                .toLowerCase()
                .trim()
            : "";


    let visibleCount = 0;


    cards.forEach(function (card) {


        const mode =
            card.dataset.mode
                ?.toLowerCase() || "";


        const status =
            card.dataset.status
                ?.toLowerCase() || "";


        const category =
            card.dataset.category
                ?.toLowerCase() || "";


        const title =
            card.querySelector("h3")
                ?.textContent
                .toLowerCase() || "";


        const organization =
            card.querySelector(".organization")
                ?.textContent
                .toLowerCase() || "";


        /* Filter condition */

        const filterMatch =
            filterValue === "all" ||
            filterValue === mode ||
            filterValue === status;


        /* Search condition */

        const searchMatch =
            searchValue === "" ||
            title.includes(searchValue) ||
            organization.includes(searchValue) ||
            category.includes(searchValue);


        /* Show only if BOTH match */

        if (filterMatch && searchMatch) {

            card.style.display = "";
            visibleCount++;

        }

        else {

            card.style.display = "none";

        }

    });


    if (noWorkshops) {

        noWorkshops.style.display =
            visibleCount === 0
                ? "block"
                : "none";

    }

}

/*==================================================
                    VIEW WORKSHOP
===================================================*/

document.addEventListener("click", function (event) {

    const viewButton = event.target.closest(".view-workshop");

    if (!viewButton) {
        return;
    }

    const card = viewButton.closest(".workshop-card");

    if (!card) {
        return;
    }

    const category =
        card.querySelector(".workshop-category")?.textContent.trim() || "";

    const title =
        card.querySelector("h3")?.textContent.trim() || "";

    const organization =
        card.querySelector(".organization")?.textContent.trim() ||
        "Not specified";

    const details =
        card.querySelectorAll(".workshop-details span");

    const date =
        details[0]?.textContent.trim() || "Not specified";

    const duration =
        details[1]?.textContent.trim() || "Not specified";

    const status =
        card.querySelector(".workshop-status")?.textContent.trim() ||
        "Not specified";

    const mode =
        card.querySelector(".workshop-mode")?.textContent.trim() ||
        "Not specified";

    const skills =
        card.querySelectorAll(".skill-tags span");

    let skillsHTML = "";

    skills.forEach(function (skill) {

        skillsHTML += `
            <span class="skill-tags-modal">
                ${escapeHTML(skill.textContent.trim())}
            </span>
        `;

    });


    modalContent.innerHTML = `

        <div class="modal-workshop-header">

            <div class="modal-workshop-icon">

                <i class="fa-solid fa-graduation-cap"></i>

            </div>

            <div>

                <span class="workshop-category">
                    ${escapeHTML(category)}
                </span>

                <h2>
                    ${escapeHTML(title)}
                </h2>

            </div>

        </div>


        <div class="modal-workshop-info">

            <div>
                <i class="fa-solid fa-building"></i>
                <strong>Organization</strong>
                <span>${escapeHTML(organization)}</span>
            </div>


            <div>
                <i class="fa-regular fa-calendar"></i>
                <strong>Date</strong>
                <span>${escapeHTML(date)}</span>
            </div>


            <div>
                <i class="fa-regular fa-clock"></i>
                <strong>Duration</strong>
                <span>${escapeHTML(duration)}</span>
            </div>


            <div>
                <i class="fa-solid fa-circle-check"></i>
                <strong>Status</strong>
                <span>${escapeHTML(status)}</span>
            </div>


            <div>
                <i class="fa-solid fa-laptop"></i>
                <strong>Mode</strong>
                <span>${escapeHTML(mode)}</span>
            </div>

        </div>


        <div class="modal-skills">

            <h3>
                <i class="fa-solid fa-lightbulb"></i>
                Skills Learned
            </h3>

            <div class="skill-tags">

                ${
                    skillsHTML ||
                    "<span>No skills added</span>"
                }

            </div>

        </div>

    `;


    /* Open modal */

    workshopModal.classList.add("show");

    workshopModal.style.display = "flex";

    document.body.style.overflow = "hidden";

});


   

    /*==================================================
                    CLOSE MODAL
    ===================================================*/

    function closeWorkshopModal() {

        workshopModal.classList.remove("show");

        document.body.style.overflow = "";

    }


    if (closeModal) {

        closeModal.addEventListener(
            "click",
            closeWorkshopModal
        );

    }


    if (modalOverlay) {

        modalOverlay.addEventListener(
            "click",
            closeWorkshopModal
        );

    }


    /* Escape key */

    document.addEventListener(
        "keydown",
        function (event) {

            if (
                event.key === "Escape" &&
                workshopModal &&
                workshopModal.classList.contains("show")
            ) {

                closeWorkshopModal();

            }

        }
    );


    /*==================================================
              DELETE WORKSHOP
===================================================*/

const deleteButtons =
    document.querySelectorAll(
        ".delete-workshop"
    );


deleteButtons.forEach(function (button) {

    button.addEventListener(
        "click",
        function () {

            const card =
                this.closest(".workshop-card");


            if (!card) {
                return;
            }


            const workshopId =
                card.dataset.id;


            const title =
                card.querySelector("h3")
                    ?.textContent.trim()
                    || "this workshop";


            const confirmed =
                confirm(
                    `Are you sure you want to delete "${title}"?`
                );


            if (!confirmed) {
                return;
            }


            fetch("delete_workshop.php", {

                method: "POST",

                headers: {
                    "Content-Type":
                        "application/x-www-form-urlencoded"
                },

                body:
                    "workshop_id=" +
                    encodeURIComponent(workshopId)

            })

            .then(function (response) {

                return response.text();

            })

            .then(function (result) {

                if (result === "success") {

                    card.style.opacity = "0";

                    card.style.transform =
                        "scale(0.95)";


                    setTimeout(function () {

                        card.remove();

                        updateWorkshopCount();

                        checkEmptyWorkshops();

                    }, 250);

                }

                else {

                    alert(
                        "Unable to delete the workshop."
                    );

                }

            })

            .catch(function () {

                alert(
                    "An error occurred while deleting the workshop."
                );

            });

        }
    );

});


    /*==================================================
                 UPDATE WORKSHOP COUNT
    ===================================================*/

    function updateWorkshopCount() {

        const cards =
            document.querySelectorAll(
                ".workshop-card"
            );


        const totalCount =
            document.querySelector(
                "#totalWorkshopCount"
            );


        if (totalCount) {

            totalCount.textContent =
                cards.length;

        }

    }


    /*==================================================
                 CHECK EMPTY WORKSHOPS
    ===================================================*/

    function checkEmptyWorkshops() {

        if (!workshopsGrid) {
            return;
        }


        const cards =
            workshopsGrid.querySelectorAll(
                ".workshop-card"
            );


        const visibleCards =
            Array.from(cards).filter(
                function (card) {

                    return (
                        card.style.display !==
                        "none"
                    );

                }
            );


        if (noWorkshops) {

            noWorkshops.style.display =
                visibleCards.length === 0
                    ? "block"
                    : "none";

        }

    }


    /*==================================================
              EDIT WORKSHOP
===================================================*/

const editButtons =
    document.querySelectorAll(
        ".edit-workshop"
    );


editButtons.forEach(function (button) {

    button.addEventListener(
        "click",
        function () {

            const workshopId =
                this.dataset.id;


            if (!workshopId) {
                return;
            }


            fetch(
                "get_workshop.php?id=" +
                encodeURIComponent(workshopId)
            )

            .then(function (response) {

                return response.json();

            })

            .then(function (workshop) {

                if (workshop.error) {

                    alert(
                        "Unable to load workshop."
                    );

                    return;

                }


                document.getElementById(
                    "workshopId"
                ).value =
                    workshop.workshop_id;


                document.getElementById(
                    "workshopTitle"
                ).value =
                    workshop.workshop_title;


                document.getElementById(
                    "workshopCategory"
                ).value =
                    workshop.category;


                document.getElementById(
                    "workshopOrganization"
                ).value =
                    workshop.organization;


                document.getElementById(
                    "trainerName"
                ).value =
                    workshop.trainer || "";


                document.getElementById(
                    "workshopDate"
                ).value =
                    workshop.workshop_date;


                document.getElementById(
                    "workshopEndDate"
                ).value =
                    workshop.end_date || "";


                document.getElementById(
                    "workshopDuration"
                ).value =
                    workshop.duration || "";


                document.getElementById(
                    "workshopStatus"
                ).value =
                    workshop.status;


                document.getElementById(
                    "workshopMode"
                ).value =
                    workshop.mode;


                document.getElementById(
                    "workshopLocation"
                ).value =
                    workshop.location || "";


                document.getElementById(
                    "workshopDescription"
                ).value =
                    workshop.description || "";


                document.getElementById(
                    "skillsLearned"
                ).value =
                    workshop.skills_learned || "";


                document.getElementById(
                    "saveWorkshopText"
                ).textContent =
                    "Update Workshop";

                document.getElementById(
    "workshopFormTitle"
).textContent =
    "Edit Workshop";


                document.querySelector(
                    ".workshop-form-section"
                ).scrollIntoView({

                    behavior: "smooth",

                    block: "start"

                });

            })

            .catch(function () {

                alert(
                    "An error occurred while loading the workshop."
                );

            });

        }
    );

});


    /*==================================================
                 FORM VALIDATION
    ===================================================*/

    if (workshopForm) {

        workshopForm.addEventListener(
            "submit",
            function (event) {

                event.preventDefault();


                const requiredFields =
                    workshopForm.querySelectorAll(
                        "[required]"
                    );


                let valid = true;


                requiredFields.forEach(
                    function (field) {

                        field.classList.remove(
                            "invalid"
                        );


                        if (
                            !field.value.trim()
                        ) {

                            field.classList.add(
                                "invalid"
                            );

                            valid = false;

                        }

                        else {

                            field.classList.add(
                                "valid"
                            );

                        }

                    }
                );


                if (!valid) {

                    alert(
                        "Please fill in all required fields."
                    );


                    const firstInvalid =
                        workshopForm.querySelector(
                            ".invalid"
                        );


                    if (firstInvalid) {

                        firstInvalid.focus();

                    }


                    return;

                }


                /*
                 * This is currently a front-end demo.
                 * Later, this form can be connected
                 * to PHP + MySQL.
                 */

                alert(
                    "Workshop information is ready to be saved."
                );


                /*
                 * When your PHP backend is ready,
                 * remove the alert above and allow
                 * the form to submit normally.
                 */

            }
        );

    }


    /*==================================================
             REMOVE VALIDATION AFTER EDITING
    ===================================================*/

    if (workshopForm) {

        const fields =
            workshopForm.querySelectorAll(
                "input, select, textarea"
            );


        fields.forEach(function (field) {

            field.addEventListener(
                "input",
                function () {

                    this.classList.remove(
                        "invalid"
                    );

                }
            );


            field.addEventListener(
                "change",
                function () {

                    this.classList.remove(
                        "invalid"
                    );

                }
            );

        });

    }


    /*==================================================
                 DATE VALIDATION
    ===================================================*/

    const startDate =
        document.getElementById(
            "workshopDate"
        );


    const endDate =
        document.getElementById(
            "workshopEndDate"
        );


    if (startDate && endDate) {

        startDate.addEventListener(
            "change",
            function () {

                endDate.min =
                    this.value;

            }
        );


        endDate.addEventListener(
            "change",
            function () {

                if (
                    startDate.value &&
                    endDate.value &&
                    endDate.value <
                    startDate.value
                ) {

                    alert(
                        "End date cannot be before the workshop start date."
                    );

                    endDate.value = "";

                }

            }
        );

    }


    /*==================================================
                  CHARACTER COUNTER
    ===================================================*/

    const description =
        document.getElementById(
            "workshopDescription"
        );


    if (description) {

        const counter =
            document.createElement("small");

        counter.className =
            "description-counter";

        counter.style.display =
            "block";

        counter.style.textAlign =
            "right";

        counter.style.marginTop =
            "5px";

        counter.style.fontSize =
            "10px";

        counter.style.color =
            "#94a3b8";


        description.parentNode.appendChild(
            counter
        );


        function updateCounter() {

            counter.textContent =
                `${description.value.length} characters`;

        }


        description.addEventListener(
            "input",
            updateCounter
        );


        updateCounter();

    }


    /*==================================================
                SEARCH WORKSHOPS
    ===================================================*/

    const searchInput =
        document.querySelector(
            ".search-box input"
        );


    if (searchInput) {

        searchInput.addEventListener(
            "input",
            function () {

                const searchText =
                    this.value
                        .toLowerCase()
                        .trim();


                const cards =
                    document.querySelectorAll(
                        ".workshop-card"
                    );


                let visibleCount = 0;


                cards.forEach(function (card) {

                    const cardText =
                        card.textContent
                            .toLowerCase();


                    if (
                        cardText.includes(
                            searchText
                        )
                    ) {

                        card.style.display = "";

                        visibleCount++;

                    }

                    else {

                        card.style.display =
                            "none";

                    }

                });


                if (noWorkshops) {

                    noWorkshops.style.display =
                        visibleCount === 0
                            ? "block"
                            : "none";

                }

            }
        );

    }


    /*==================================================
                 ESCAPE HTML HELPER
    ===================================================*/

    function escapeHTML(value) {

        const div =
            document.createElement("div");


        div.textContent =
            value;


        return div.innerHTML;

    }

    // ========================================
// NOTIFICATION DROPDOWN
// ========================================

const notification = document.querySelector(".notification");

if (notification) {

    notification.addEventListener("click", function () {

        notification.classList.toggle("show-notifications");

    });

}


    /*==================================================
                  INITIAL SETUP
    ===================================================*/

    updateWorkshopCount();

});