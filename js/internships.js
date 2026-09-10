/*==================================================
        DIGITAL SKILL PASSPORT
             INTERNSHIPS PAGE
===================================================*/

document.addEventListener("DOMContentLoaded", function () {

    /*==================================================
                    ELEMENTS
    ===================================================*/

    const internshipForm = document.getElementById("internshipForm");
    const internshipFormTitle =
    document.getElementById("internshipFormTitle");

const internshipFormDescription =
    document.getElementById("internshipFormDescription");
    const internshipGrid = document.getElementById("internshipsGrid");
    const internshipFilter = document.getElementById("internshipFilter");
    const searchInput = document.getElementById("internshipSearch");
    const noInternships = document.getElementById("noInternships");

    const addInternshipBtn = document.getElementById("addInternshipBtn");
    const emptyAddInternshipBtn =
        document.getElementById("emptyAddInternshipBtn");

    const resetBtn = document.getElementById("resetInternshipBtn");

    const certificateInput =
        document.getElementById("internshipCertificate");

    const supportingDocumentInput =
        document.getElementById("supportingDocument");

    const internshipModal =
        document.getElementById("internshipModal");

    const closeModal =
        document.getElementById("closeInternshipModal");

    const modalCloseButton =
        document.getElementById("modalCloseButton");

    const modalOverlay =
        document.querySelector(".modal-overlay");

    const modalTitle =
        document.getElementById("modalInternshipTitle");

    const modalContent =
        document.getElementById("modalInternshipContent");

    
    // AUTO-HIDE SUCCESS MESSAGE

const successMessage =
    document.getElementById("successMessage");

if (successMessage) {

    setTimeout(function () {

        successMessage.style.opacity = "0";

        setTimeout(function () {
            successMessage.remove();
        }, 300);

    }, 3000);
}



    /*==================================================
                    MOBILE SIDEBAR
    ===================================================*/

    const menuToggle = document.querySelector(".menu-toggle");
    const sidebar = document.querySelector(".sidebar");

    if (menuToggle && sidebar) {

        menuToggle.addEventListener("click", function () {
            sidebar.classList.toggle("show-sidebar");
        });

    }

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
                    FORM ELEMENTS
    ===================================================*/

    const companyName =
        document.getElementById("companyName");

    const internshipTitle =
        document.getElementById("internshipTitle");

    const internshipRole =
        document.getElementById("internshipRole");

    const internshipType =
        document.getElementById("internshipType");

    const startDate =
        document.getElementById("internshipStartDate");

    const endDate =
        document.getElementById("internshipEndDate");

    const location =
        document.getElementById("internshipLocation");

    const workMode =
        document.getElementById("workMode");

    const description =
        document.getElementById("internshipDescription");

    const skillsInput =
        document.getElementById("skillsGained");


    /*==================================================
                DATE VALIDATION
    ===================================================*/

    if (startDate && endDate) {

        startDate.addEventListener("change", function () {

            if (endDate.value &&
                endDate.value < startDate.value) {

                endDate.value = "";
                endDate.classList.add("invalid");

                alert("End date cannot be before start date.");

            } else {

                endDate.classList.remove("invalid");

            }

        });


        endDate.addEventListener("change", function () {

            if (startDate.value &&
                endDate.value < startDate.value) {

                endDate.value = "";
                endDate.classList.add("invalid");

                alert("End date cannot be before start date.");

            } else {

                endDate.classList.remove("invalid");

            }

        });

    }


    /*==================================================
                    FORM VALIDATION
    ===================================================*/

    function validateField(field) {

        if (!field) {
            return true;
        }

        if (field.value.trim() === "") {

            field.classList.remove("valid");
            field.classList.add("invalid");

            return false;

        }

        field.classList.remove("invalid");
        field.classList.add("valid");

        return true;

    }


    const requiredFields = [
        internshipTitle,
        internshipType,
        companyName,
        internshipRole,
        startDate,
        endDate,
        workMode,
        description
    ];


    requiredFields.forEach(function (field) {

        if (!field) {
            return;
        }

        field.addEventListener("blur", function () {
            validateField(field);
        });

        field.addEventListener("input", function () {

            if (field.value.trim() !== "") {
                field.classList.remove("invalid");
            }

        });

        field.addEventListener("change", function () {
            validateField(field);
        });

    });


    /*==================================================
                    RESET FORM
    ===================================================*/

    if (resetBtn) {

        resetBtn.addEventListener("click", function () {

            setTimeout(function () {

                const fields =
                    internshipForm.querySelectorAll(
                        "input, select, textarea"
                    );

                fields.forEach(function (field) {
                    field.classList.remove("valid", "invalid");
                });

            }, 50);

        });

    }


    /*==================================================
              CERTIFICATE FILE VALIDATION
    ===================================================*/

    function validateFile(input) {

        if (!input || !input.files.length) {
            return true;
        }

        const file = input.files[0];

        const allowedTypes = [
            "image/jpeg",
            "image/png",
            "image/webp",
            "application/pdf"
        ];

        const maxSize =
            5 * 1024 * 1024;

        if (!allowedTypes.includes(file.type)) {

            alert(
                "Please upload a JPG, JPEG, PNG, WEBP or PDF file."
            );

            input.value = "";

            return false;
        }

        if (file.size > maxSize) {

            alert(
                "File size must be less than 5 MB."
            );

            input.value = "";

            return false;
        }

        return true;

    }


    if (certificateInput) {

        certificateInput.addEventListener("change", function () {
            validateFile(certificateInput);
        });

    }


    if (supportingDocumentInput) {

        supportingDocumentInput.addEventListener("change", function () {
            validateFile(supportingDocumentInput);
        });

    }


    /*==================================================
                    FORM SUBMISSION
    ===================================================*/

    if (internshipForm) {

        internshipForm.addEventListener("submit", function (event) {

            let formValid = true;

            requiredFields.forEach(function (field) {

                if (!validateField(field)) {
                    formValid = false;
                }

            });


            if (startDate &&
                endDate &&
                startDate.value &&
                endDate.value &&
                endDate.value < startDate.value) {

                alert(
                    "End date cannot be before start date."
                );

                formValid = false;
            }


            if (certificateInput &&
                !validateFile(certificateInput)) {

                formValid = false;
            }


            if (supportingDocumentInput &&
                !validateFile(supportingDocumentInput)) {

                formValid = false;
            }


            if (!formValid) {

                event.preventDefault();

                alert(
                    "Please correct the highlighted fields."
                );

                return;
            }

            /*
             * If validation succeeds, allow the PHP form
             * to submit normally.
             */

        });

    }


    /*==================================================
                    ADD INTERNSHIP
    ===================================================*/

    function scrollToForm() {

        const formSection =
            document.querySelector(
                ".internship-form-section"
            );

        if (formSection) {

            formSection.scrollIntoView({
                behavior: "smooth",
                block: "start"
            });

        }

    }


    if (addInternshipBtn) {

        addInternshipBtn.addEventListener(
            "click",
            scrollToForm
        );

    }

    if (emptyAddInternshipBtn) {

        emptyAddInternshipBtn.addEventListener(
            "click",
            scrollToForm
        );

    }


  /*==================================================
                    EDIT INTERNSHIP
===================================================*/

if (internshipGrid) {

    internshipGrid.addEventListener(
        "click",
        function (event) {

            const button =
                event.target.closest(".edit-internship");

            if (!button) {
                return;
            }

            const data = button.dataset;


            /*==================================================
                    GET ADDITIONAL FORM FIELDS
            ===================================================*/

            const department =
                document.getElementById("department");

            const supervisor =
                document.getElementById("supervisor");

            const duration =
                document.getElementById("internshipDuration");

            const status =
                document.getElementById("internshipStatus");

            const companyWebsite =
                document.getElementById("companyWebsite");

            const responsibilities =
                document.getElementById("responsibilities");

            const technologies =
                document.getElementById("technologies");


            /*==================================================
                    LOAD DATA INTO FORM
            ===================================================*/

            if (companyName) {
                companyName.value =
                    data.company || "";
            }

            if (internshipTitle) {
                internshipTitle.value =
                    data.title || "";
            }

            if (internshipRole) {
                internshipRole.value =
                    data.role || "";
            }

            if (internshipType) {
                internshipType.value =
                    data.type || "";
            }

            if (startDate) {
                startDate.value =
                    data.startDate || "";
            }

            if (endDate) {
                endDate.value =
                    data.endDate || "";
            }

            if (location) {
                location.value =
                    data.location || "";
            }

            if (workMode) {
                workMode.value =
                    data.workMode || "";
            }

            if (description) {
                description.value =
                    data.description || "";
            }

            if (skillsInput) {
                skillsInput.value =
                    data.skillsGained || "";
            }

            if (department) {
                department.value =
                    data.department || "";
            }

            if (supervisor) {
                supervisor.value =
                    data.supervisor || "";
            }

            if (duration) {
                duration.value =
                    data.duration || "";
            }

            if (status) {
                status.value =
                    data.status || "";
            }

            if (companyWebsite) {
                companyWebsite.value =
                    data.companyWebsite || "";
            }

            if (responsibilities) {
                responsibilities.value =
                    data.responsibilities || "";
            }

            if (technologies) {
                technologies.value =
                    data.technologies || "";
            }


            /*==================================================
                    STORE INTERNSHIP ID
            ===================================================*/

            const editingInternshipId =
    document.getElementById("editingInternshipId");

if (editingInternshipId) {
    editingInternshipId.value =
        data.internshipId || "";
}


            /*==================================================
                    CHANGE SUBMIT BUTTON
            ===================================================*/

            if (internshipFormTitle) {
    internshipFormTitle.textContent =
        "Update Internship";
}

if (internshipFormDescription) {
    internshipFormDescription.textContent =
        "Update the details of your internship experience.";
}

            if (internshipForm) {

                const submitButton =
                    internshipForm.querySelector(
                        'button[type="submit"]'
                    );

                if (submitButton) {

                    submitButton.innerHTML = `
                        <i class="fa-solid fa-pen"></i>
                        Update Internship
                    `;

                }

            }


            /*==================================================
                    SCROLL TO FORM
            ===================================================*/

            const formSection =
                document.querySelector(
                    ".internship-form-section"
                );

            if (formSection) {

                formSection.scrollIntoView({
                    behavior: "smooth",
                    block: "start"
                });

            }

        }
    );

}


/*==================================================
                    DELETE INTERNSHIP
===================================================*/

if (internshipGrid) {

    internshipGrid.addEventListener(
        "click",
        function (event) {

            const button =
                event.target.closest(".delete-internship");

            if (!button) {
                return;
            }

            const internshipId =
                button.dataset.internshipId;

            if (!internshipId) {
                return;
            }

            const confirmed =
                confirm(
                    "Are you sure you want to delete this internship?"
                );

            if (!confirmed) {
                return;
            }

            const form =
                document.createElement("form");

            form.method = "POST";
            form.action = "internships.php";

            const actionInput =
                document.createElement("input");

            actionInput.type = "hidden";
            actionInput.name = "delete_internship";
            actionInput.value = "1";

            const idInput =
                document.createElement("input");

            idInput.type = "hidden";
            idInput.name = "internship_id";
            idInput.value = internshipId;

            form.appendChild(actionInput);
            form.appendChild(idInput);

            document.body.appendChild(form);

            form.submit();
        }
    );
}

    /*==================================================
                SEARCH + FILTER
    ===================================================*/
    

    function filterInternships() {

        if (!internshipGrid) {
            return;
        }

        const cards =
            internshipGrid.querySelectorAll(
                ".internship-card"
            );

        const searchTerm =
            searchInput
                ? searchInput.value.toLowerCase().trim()
                : "";

        const selectedFilter =
            internshipFilter
                ? internshipFilter.value.toLowerCase()
                : "all";

        let visibleCount = 0;


        cards.forEach(function (card) {

            const cardText =
                card.textContent.toLowerCase();

            const cardStatus =
                card.dataset.status
                ? card.dataset.status.toLowerCase()
                : "";


            const matchesSearch =
                cardText.includes(searchTerm);

            const matchesFilter =
                selectedFilter === "all" ||
                cardStatus === selectedFilter;


            if (matchesSearch && matchesFilter) {

                card.style.display = "";

                visibleCount++;

            } else {

                card.style.display = "none";

            }

        });


        if (noInternships) {

            if (cards.length === 0 ||
                visibleCount === 0) {

                noInternships.style.display = "block";

            } else {

                noInternships.style.display = "none";

            }

        }

    }


    if (searchInput) {

        searchInput.addEventListener(
            "input",
            filterInternships
        );

    }


    if (internshipFilter) {

        internshipFilter.addEventListener(
            "change",
            filterInternships
        );

    }


    /*==================================================
                    VIEW INTERNSHIP MODAL
    ===================================================*/

    function openInternshipModal(card) {

    if (!internshipModal ||
        !modalTitle ||
        !modalContent) {

        return;
    }

    const data =
        card.querySelector(".edit-internship")?.dataset;

    if (!data) {
        return;
    }

    const title =
        data.title || "Internship Details";

    const company =
        data.company || "-";

    const role =
        data.role || "-";

    const type =
        data.type || "-";

    const department =
        data.department || "-";

    const supervisor =
        data.supervisor || "-";

    const startDate =
        data.startDate || "-";

    const endDate =
        data.endDate || "-";

    const duration =
        data.duration || "-";

    const status =
        data.status || "-";

    const workMode =
        data.workMode || "-";

    const location =
        data.location || "-";

    const companyWebsite =
        data.companyWebsite || "";

    const description =
        data.description || "-";

    const responsibilities =
        data.responsibilities || "-";

    const technologies =
        data.technologies || "";

    const skillsGained =
        data.skillsGained || "";

    const certificate =
        data.certificate || "";

    const supportingDocument =
        data.supportingDocument || "";


    modalTitle.textContent = title;


    let technologyHTML =
        "<span>No technologies specified</span>";

    if (technologies) {

        const technologyList =
            technologies
                .split(",")
                .map(function (technology) {
                    return technology.trim();
                })
                .filter(function (technology) {
                    return technology !== "";
                });

        if (technologyList.length > 0) {

            technologyHTML =
                technologyList
                    .map(function (technology) {
                        return `<span>${technology}</span>`;
                    })
                    .join("");
        }
    }


    let skillsHTML =
        "<span>No skills specified</span>";

    if (skillsGained) {

        const skillsList =
            skillsGained
                .split(",")
                .map(function (skill) {
                    return skill.trim();
                })
                .filter(function (skill) {
                    return skill !== "";
                });

        if (skillsList.length > 0) {

            skillsHTML =
                skillsList
                    .map(function (skill) {
                        return `<span>${skill}</span>`;
                    })
                    .join("");
        }
    }


    let websiteHTML = "-";

    if (companyWebsite) {

        websiteHTML = `
            <a href="${companyWebsite}"
               target="_blank"
               rel="noopener noreferrer">
                Visit Company Website
                <i class="fa-solid fa-arrow-up-right-from-square"></i>
            </a>
        `;
    }


    let certificateHTML =
        "No certificate uploaded";

    if (certificate) {

        certificateHTML = `
            <a href="${certificate}"
               target="_blank"
               rel="noopener noreferrer">
                <i class="fa-solid fa-file-lines"></i>
                View Certificate
            </a>
        `;
    }


    let supportingDocumentHTML =
        "No supporting document uploaded";

    if (supportingDocument) {

        supportingDocumentHTML = `
            <a href="${supportingDocument}"
               target="_blank"
               rel="noopener noreferrer">
                <i class="fa-solid fa-file-lines"></i>
                View Supporting Document
            </a>
        `;
    }


    modalContent.innerHTML = `

        <div class="modal-detail-grid">

            <div class="modal-detail-item">
                <i class="fa-solid fa-building"></i>
                <strong>Company / Organization</strong>
                <span>${company}</span>
            </div>

            <div class="modal-detail-item">
                <i class="fa-solid fa-user-tie"></i>
                <strong>Role / Position</strong>
                <span>${role}</span>
            </div>

            <div class="modal-detail-item">
                <i class="fa-solid fa-briefcase"></i>
                <strong>Internship Type</strong>
                <span>${type}</span>
            </div>

            <div class="modal-detail-item">
                <i class="fa-solid fa-building-user"></i>
                <strong>Department</strong>
                <span>${department}</span>
            </div>

            <div class="modal-detail-item">
                <i class="fa-solid fa-user"></i>
                <strong>Supervisor</strong>
                <span>${supervisor}</span>
            </div>

            <div class="modal-detail-item">
                <i class="fa-regular fa-calendar"></i>
                <strong>Start Date</strong>
                <span>${startDate}</span>
            </div>

            <div class="modal-detail-item">
                <i class="fa-regular fa-calendar-check"></i>
                <strong>End Date</strong>
                <span>${endDate}</span>
            </div>

            <div class="modal-detail-item">
                <i class="fa-regular fa-clock"></i>
                <strong>Duration</strong>
                <span>${duration}</span>
            </div>

            <div class="modal-detail-item">
                <i class="fa-solid fa-circle-check"></i>
                <strong>Status</strong>
                <span>${status}</span>
            </div>

            <div class="modal-detail-item">
                <i class="fa-solid fa-location-dot"></i>
                <strong>Location</strong>
                <span>${location}</span>
            </div>

            <div class="modal-detail-item">
                <i class="fa-solid fa-laptop"></i>
                <strong>Work Mode</strong>
                <span>${workMode}</span>
            </div>

            <div class="modal-detail-item">
                <i class="fa-solid fa-globe"></i>
                <strong>Company Website</strong>
                <span>${websiteHTML}</span>
            </div>

        </div>


        <div class="modal-text-section">

            <h3>
                <i class="fa-solid fa-align-left"></i>
                Description
            </h3>

            <p>${description}</p>

        </div>


        <div class="modal-text-section">

            <h3>
                <i class="fa-solid fa-list-check"></i>
                Responsibilities
            </h3>

            <p>${responsibilities}</p>

        </div>


        <div class="modal-skills">

            <h3>
                <i class="fa-solid fa-code"></i>
                Technologies Used
            </h3>

            <div class="internship-skills">
                ${technologyHTML}
            </div>

        </div>


        <div class="modal-skills">

            <h3>
                <i class="fa-solid fa-lightbulb"></i>
                Skills Gained
            </h3>

            <div class="internship-skills">
                ${skillsHTML}
            </div>

        </div>


        <div class="modal-documents">

            <h3>
                <i class="fa-solid fa-paperclip"></i>
                Documents
            </h3>

            <div class="modal-document-links">

                <div class="modal-document-item">
                    ${certificateHTML}
                </div>

                <div class="modal-document-item">
                    ${supportingDocumentHTML}
                </div>

            </div>

        </div>

    `;


    internshipModal.classList.add("show");

    document.body.style.overflow = "hidden";
}


    function closeInternshipModal() {

        if (!internshipModal) {
            return;
        }

        internshipModal.classList.remove("show");

        document.body.style.overflow = "";

    }


    if (internshipGrid) {

        internshipGrid.addEventListener(
            "click",
            function (event) {

                const button =
                    event.target.closest(
                        ".view-internship"
                    );

                if (!button) {
                    return;
                }

                const card =
                    button.closest(
                        ".internship-card"
                    );

                if (card) {
                    openInternshipModal(card);
                }

            }
        );

    }


    if (closeModal) {

        closeModal.addEventListener(
            "click",
            closeInternshipModal
        );

    }


    if (modalCloseButton) {

        modalCloseButton.addEventListener(
            "click",
            closeInternshipModal
        );

    }


    if (modalOverlay) {

        modalOverlay.addEventListener(
            "click",
            closeInternshipModal
        );

    }


    document.addEventListener(
        "keydown",
        function (event) {

            if (event.key === "Escape") {
                closeInternshipModal();
            }

        }
    );


    /*==================================================
                    NOTIFICATIONS
    ===================================================*/

    const notification =
        document.querySelector(".notification");


    if (notification) {

        notification.addEventListener(
            "click",
            function (event) {

                event.stopPropagation();

                notification.classList.toggle(
                    "show-notifications"
                );

            }
        );

    }


    document.addEventListener(
        "click",
        function (event) {

            if (
                notification &&
                !notification.contains(event.target)
            ) {

                notification.classList.remove(
                    "show-notifications"
                );

            }

        }
    );


    /*==================================================
                    INITIAL FILTER
    ===================================================*/

    filterInternships();

});