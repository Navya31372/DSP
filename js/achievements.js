/*==================================================
        DIGITAL SKILL PASSPORT
             ACHIEVEMENTS PAGE
===================================================*/

document.addEventListener("DOMContentLoaded", function () {

    /*==================================================
                    ELEMENTS
    ===================================================*/
const formTitle =
    document.getElementById("achievementFormTitle");

const submitButton =
    document.getElementById("achievementSubmitBtn");


    const achievementForm =
        document.getElementById("achievementForm");

    const formSection =
        document.getElementById("achievementFormSection");

    const addAchievementBtn =
        document.getElementById("addAchievementBtn");

    const emptyAddAchievementBtn =
        document.getElementById("emptyAddAchievementBtn");

    const resetAchievementBtn =
        document.getElementById("resetAchievementBtn");

    const achievementUploadArea =
        document.getElementById("achievementUploadArea");

    const achievementProof =
        document.getElementById("achievementProof");

    const selectedAchievementFile =
        document.getElementById("selectedAchievementFile");

    const achievementPreview =
        document.getElementById("achievementPreview");

    const achievementPreviewImage =
        document.getElementById("achievementPreviewImage");

    const achievementGrid =
        document.getElementById("achievementsGrid");

    const noAchievements =
        document.getElementById("noAchievements");

    const achievementSearch =
        document.getElementById("achievementSearch");

    const achievementFilter =
        document.getElementById("achievementFilter");

    const achievementModal =
        document.getElementById("achievementModal");

    const closeAchievementModal =
        document.getElementById("closeAchievementModal");

    const modalCloseButton =
        document.getElementById("modalCloseButton");

    const modalOverlay =
        achievementModal
            ? achievementModal.querySelector(".modal-overlay")
            : null;

    const achievementId =
        document.getElementById("achievementId");


    /*==================================================
                    FORM FIELDS
    ===================================================*/

    const titleInput =
        achievementForm
            ? achievementForm.querySelector(
                '[name="achievement_title"]'
            )
            : null;

    const typeInput =
        achievementForm
            ? achievementForm.querySelector(
                '[name="achievement_type"]'
            )
            : null;

    const organizationInput =
        achievementForm
            ? achievementForm.querySelector(
                '[name="organization"]'
            )
            : null;

    const dateInput =
        achievementForm
            ? achievementForm.querySelector(
                '[name="achievement_date"]'
            )
            : null;

    const positionInput =
        achievementForm
            ? achievementForm.querySelector(
                '[name="position"]'
            )
            : null;

    const categoryInput =
        achievementForm
            ? achievementForm.querySelector(
                '[name="achievement_category"]'
            )
            : null;

    const descriptionInput =
        achievementForm
            ? achievementForm.querySelector(
                '[name="achievement_description"]'
            )
            : null;

    const skillsInput =
        achievementForm
            ? achievementForm.querySelector(
                '[name="achievement_skills"]'
            )
            : null;


    /*==================================================
                    HELPER FUNCTIONS
    ===================================================*/

    function escapeHTML(value) {

        const div = document.createElement("div");

        div.textContent = value || "";

        return div.innerHTML;

    }


    function getCardValue(card, selector) {

        const element =
            card.querySelector(selector);

        return element
            ? element.textContent.trim()
            : "";

    }


    function showForm() {

        if (formSection) {

            formSection.style.display = "";

            formSection.scrollIntoView({
                behavior: "smooth",
                block: "start"
            });

        }

    }


    function hideForm() {

        if (formSection) {

            formSection.style.display = "none";

        }

    }


    function resetForm() {

        if (!achievementForm) {
            return;
        }

        achievementForm.reset();

        if (achievementId) {
            achievementId.value = "";
        }

        const actionInput =
            achievementForm.querySelector(
                '[name="action"]'
            );

        if (actionInput) {
            actionInput.value = "add";
        }

        if (achievementPreview) {
            achievementPreview.style.display = "none";
        }

        if (selectedAchievementFile) {
            selectedAchievementFile.textContent = "";
        }

        if (achievementPreviewImage) {
            achievementPreviewImage.src = "";
        }

        if (achievementUploadArea) {
            achievementUploadArea.classList.remove("has-file");
        }

        if (formTitle) {
    formTitle.textContent = "Add New Achievement";
}

if (submitButton) {
    submitButton.innerHTML =
        '<i class="fa-solid fa-floppy-disk"></i> Save Achievement';
}

    }


    /*==================================================
                    ADD ACHIEVEMENT
    ===================================================*/

    if (addAchievementBtn) {

        addAchievementBtn.addEventListener(
            "click",
            function () {

                resetForm();

                showForm();

                if (titleInput) {
                    titleInput.focus();
                }

            }
        );

    }


    if (emptyAddAchievementBtn) {

        emptyAddAchievementBtn.addEventListener(
            "click",
            function () {

                resetForm();

                showForm();

                if (titleInput) {
                    titleInput.focus();
                }

            }
        );

    }


    /*==================================================
                    RESET FORM
    ===================================================*/

    if (resetAchievementBtn) {

        resetAchievementBtn.addEventListener(
            "click",
            function () {

                resetForm();

            }
        );

    }


    /*==================================================
                    FILE UPLOAD
    ===================================================*/

    if (achievementUploadArea &&
        achievementProof) {

        achievementUploadArea.addEventListener(
            "click",
            function (event) {

                if (
                    event.target === achievementProof
                ) {
                    return;
                }

                achievementProof.click();

            }
        );


        achievementUploadArea.addEventListener(
            "dragover",
            function (event) {

                event.preventDefault();

                achievementUploadArea.classList.add(
                    "drag-over"
                );

            }
        );


        achievementUploadArea.addEventListener(
            "dragleave",
            function () {

                achievementUploadArea.classList.remove(
                    "drag-over"
                );

            }
        );


        achievementUploadArea.addEventListener(
            "drop",
            function (event) {

                event.preventDefault();

                achievementUploadArea.classList.remove(
                    "drag-over"
                );

                if (
                    event.dataTransfer.files.length > 0
                ) {

                    achievementProof.files =
                        event.dataTransfer.files;

                    handleSelectedFile(
                        achievementProof.files[0]
                    );

                }

            }
        );

    }


    if (achievementProof) {

        achievementProof.addEventListener(
            "change",
            function () {

                if (this.files.length > 0) {

                    handleSelectedFile(
                        this.files[0]
                    );

                }

            }
        );

    }


    function handleSelectedFile(file) {

        if (!file) {
            return;
        }

        if (selectedAchievementFile) {

            selectedAchievementFile.textContent =
                file.name;

        }

        if (achievementUploadArea) {

            achievementUploadArea.classList.add(
                "has-file"
            );

        }

        if (
            achievementPreview &&
            achievementPreviewImage
        ) {

            if (
                file.type.startsWith("image/")
            ) {

                const reader =
                    new FileReader();

                reader.onload =
                    function (event) {

                        achievementPreviewImage.src =
                            event.target.result;

                        achievementPreview.style.display =
                            "block";

                    };

                reader.readAsDataURL(file);

            } else {

                achievementPreview.style.display =
                    "none";

            }

        }

    }


    /*==================================================
                    SEARCH + FILTER
    ===================================================*/

    function filterAchievements() {

        if (!achievementGrid) {
            return;
        }

        const cards =
            achievementGrid.querySelectorAll(
                ".achievement-card"
            );

        const searchTerm =
            achievementSearch
                ? achievementSearch.value
                    .trim()
                    .toLowerCase()
                : "";

        const selectedFilter =
            achievementFilter
                ? achievementFilter.value
                : "all";

        let visibleCount = 0;


        cards.forEach(function (card) {

            const title =
                (
                    card.dataset.title ||
                    ""
                ).toLowerCase();

            const type =
                (
                    card.dataset.type ||
                    ""
                ).toLowerCase();

            const category =
                (
                    card.dataset.category ||
                    ""
                ).toLowerCase();

            const organization =
                getCardValue(
                    card,
                    ".achievement-organization span"
                ).toLowerCase();

            const description =
                getCardValue(
                    card,
                    ".achievement-description"
                ).toLowerCase();

            const matchesSearch =
                searchTerm === "" ||
                title.includes(searchTerm) ||
                type.includes(searchTerm) ||
                category.includes(searchTerm) ||
                organization.includes(searchTerm) ||
                description.includes(searchTerm);

            const matchesFilter =
                selectedFilter === "all" ||
                type === selectedFilter;

            if (
                matchesSearch &&
                matchesFilter
            ) {

                card.style.display = "";

                visibleCount++;

            } else {

                card.style.display = "none";

            }

        });


        if (noAchievements) {

            noAchievements.style.display =
                visibleCount === 0
                    ? ""
                    : "none";

        }

    }


    if (achievementSearch) {

        achievementSearch.addEventListener(
            "input",
            filterAchievements
        );

    }


    if (achievementFilter) {

        achievementFilter.addEventListener(
            "change",
            filterAchievements
        );

    }


    /*==================================================
                    VIEW DETAILS
    ===================================================*/

    function openAchievementModal(card) {

        if (!achievementModal || !card) {
            return;
        }

        const title =
            getCardValue(
                card,
                ".achievement-card-content h3"
            );

        const organization =
            getCardValue(
                card,
                ".achievement-organization span"
            );

        const date =
            getCardValue(
                card,
                ".achievement-date span"
            );

        const position =
            getCardValue(
                card,
                ".achievement-rank"
            );

        const type =
            getCardValue(
                card,
                ".achievement-label"
            );

        const description =
            getCardValue(
                card,
                ".achievement-description"
            );

        const skills =
            card.querySelectorAll(
                ".achievement-skills span"
            );

        const certificateFile =
    card.dataset.certificate || "";


        const modalTitle =
            document.getElementById(
                "modalAchievementTitle"
            );

        const modalOrganization =
            document.getElementById(
                "modalOrganization"
            );

        const modalDate =
            document.getElementById(
                "modalDate"
            );

        const modalPosition =
            document.getElementById(
                "modalPosition"
            );

        const modalType =
            document.getElementById(
                "modalType"
            );

        const modalDescription =
            document.getElementById(
                "modalDescription"
            );

        const modalSkills =
            document.getElementById(
                "modalSkills"
            );


        if (modalTitle) {
            modalTitle.textContent = title;
        }

        if (modalOrganization) {
            modalOrganization.textContent =
                organization || "Not specified";
        }

        if (modalDate) {
            modalDate.textContent =
                date || "Not specified";
        }

        if (modalPosition) {
            modalPosition.textContent =
                position || "Not specified";
        }

        if (modalType) {
            modalType.textContent =
                type || "Not specified";
        }

        if (modalDescription) {
            modalDescription.textContent =
                description || "No description added.";
        }


        if (modalSkills) {

            modalSkills.innerHTML = "";

            if (skills.length === 0) {

                const span =
                    document.createElement("span");

                span.textContent =
                    "No skills added";

                modalSkills.appendChild(span);

            } else {

                skills.forEach(function (skill) {

                    const span =
                        document.createElement("span");

                    span.textContent =
                        skill.textContent.trim();

                    modalSkills.appendChild(span);

                });

            }

        }

                /*==================================================
                    CERTIFICATE
        ===================================================*/

        let certificateSection =
            document.getElementById(
                "modalCertificateSection"
            );

        if (!certificateSection) {

            certificateSection =
                document.createElement("div");

            certificateSection.id =
                "modalCertificateSection";

            certificateSection.className =
                "modal-certificate-section";

            modalSkills.parentElement.insertAdjacentElement(
                "afterend",
                certificateSection
            );

        }

        certificateSection.innerHTML = "";

        const certificateTitle =
            document.createElement("h3");

        certificateTitle.textContent =
            "Certificate / Proof";

        certificateSection.appendChild(
            certificateTitle
        );


        if (certificateFile !== "") {

            const certificateLink =
                document.createElement("a");

            certificateLink.href =
    "view_achievement_certificate.php?id=" +
    card.dataset.id;

            certificateLink.target =
                "_blank";

            certificateLink.rel =
                "noopener noreferrer";

            certificateLink.textContent =
                "View Certificate";

            certificateLink.className =
                "view-certificate-btn";

            certificateSection.appendChild(
                certificateLink
            );

        } else {

            const noCertificate =
                document.createElement("p");

            noCertificate.textContent =
                "No certificate uploaded.";

            certificateSection.appendChild(
                noCertificate
            );

        }

        achievementModal.classList.add("show");

        document.body.style.overflow = "hidden";

    }


    document.addEventListener(
        "click",
        function (event) {

            const viewButton =
                event.target.closest(
                    ".view-achievement"
                );

            if (!viewButton) {
                return;
            }

            const card =
                viewButton.closest(
                    ".achievement-card"
                );

            if (card) {
                openAchievementModal(card);
            }

        }
    );


    /*==================================================
                    CLOSE MODAL
    ===================================================*/

    function closeModal() {

        if (!achievementModal) {
            return;
        }

        achievementModal.classList.remove(
            "show"
        );

        document.body.style.overflow = "";

    }


    if (closeAchievementModal) {

        closeAchievementModal.addEventListener(
            "click",
            closeModal
        );

    }


    if (modalCloseButton) {

        modalCloseButton.addEventListener(
            "click",
            closeModal
        );

    }


    if (modalOverlay) {

        modalOverlay.addEventListener(
            "click",
            closeModal
        );

    }


    document.addEventListener(
        "keydown",
        function (event) {

            if (
                event.key === "Escape" &&
                achievementModal &&
                achievementModal.classList.contains(
                    "show"
                )
            ) {

                closeModal();

            }

        }
    );


    /*==================================================
                    EDIT ACHIEVEMENT
    ===================================================*/

    document.addEventListener(
        "click",
        function (event) {

            const editButton =
                event.target.closest(
                    ".edit-achievement"
                );

            if (!editButton) {
                return;
            }

            const card =
                editButton.closest(
                    ".achievement-card"
                );

            if (!card || !achievementForm) {
                return;
            }


            const id =
                card.dataset.id || "";


            const title =
                getCardValue(
                    card,
                    ".achievement-card-content h3"
                );

            const type =
                card.dataset.type || "";

            const organization =
                getCardValue(
                    card,
                    ".achievement-organization span"
                );

            const dateText =
    card.dataset.date || "";

            const position =
                getCardValue(
                    card,
                    ".achievement-rank"
                );

            const category =
                card.dataset.category || "";

            const description =
                getCardValue(
                    card,
                    ".achievement-description"
                );

            const skills =
                Array.from(
                    card.querySelectorAll(
                        ".achievement-skills span"
                    )
                )
                .map(function (element) {
                    return element.textContent.trim();
                })
                .join(", ");


            if (achievementId) {
                achievementId.value = id;
            }

            if (titleInput) {
                titleInput.value = title;
            }

            if (typeInput) {
                typeInput.value = type;
            }

            if (organizationInput) {
                organizationInput.value =
                    organization;
            }

            if (dateInput) {
    dateInput.value = dateText;
}

            if (positionInput) {
                positionInput.value =
                    position;
            }

            if (categoryInput) {
                categoryInput.value =
                    category;
            }

            if (descriptionInput) {
                descriptionInput.value =
                    description;
            }

            if (skillsInput) {
                skillsInput.value =
                    skills;
            }


            const visibility =
    card.dataset.visibility || "";

const visibilityInputs =
    achievementForm.querySelectorAll(
        'input[name="visibility"]'
    );

visibilityInputs.forEach(function (input) {
    input.checked =
        input.value === visibility;
});

            const actionInput =
                achievementForm.querySelector(
                    '[name="action"]'
                );

            if (actionInput) {
                actionInput.value = "update";

            }
            if (formTitle) {
    formTitle.textContent = "Edit Achievement";
}

if (submitButton) {
    submitButton.innerHTML =
        '<i class="fa-solid fa-pen"></i> Update Achievement';
}


            showForm();

            if (titleInput) {
                titleInput.focus();
            }

        }
    );


    /*==================================================
                    DELETE ACHIEVEMENT
    ===================================================*/

    document.addEventListener(
        "click",
        function (event) {

            const deleteButton =
                event.target.closest(
                    ".delete-achievement"
                );

            if (!deleteButton) {
                return;
            }

            const card =
                deleteButton.closest(
                    ".achievement-card"
                );

            if (!card) {
                return;
            }

            const id =
                card.dataset.id || "";

            if (!id) {
                return;
            }

            const title =
                getCardValue(
                    card,
                    ".achievement-card-content h3"
                ) ||
                "this achievement";


            const confirmed =
                window.confirm(
                    `Are you sure you want to delete "${title}"?`
                );


            if (!confirmed) {
                return;
            }


            const form =
                document.createElement("form");

            form.method = "POST";
            form.action = "";
            form.style.display = "none";


            const action =
                document.createElement("input");

            action.type = "hidden";
            action.name = "action";
            action.value = "delete";


            const idInput =
                document.createElement("input");

            idInput.type = "hidden";
            idInput.name = "achievement_id";
            idInput.value = id;


            form.appendChild(action);
            form.appendChild(idInput);

            document.body.appendChild(form);

            form.submit();

        }
    );


    /*==================================================
                    INITIAL FILTER
    ===================================================*/

    filterAchievements();


    /*==================================================
                    SUCCESS / ERROR MESSAGE
    ===================================================*/

    const pageMessages =
        document.querySelectorAll(
            ".success-message, .error-message"
        );

    pageMessages.forEach(function (message) {

        setTimeout(
            function () {

                message.style.opacity = "0";

                setTimeout(
                    function () {
                        message.style.display = "none";
                    },
                    300
                );

            },
            5000
        );

    });


    /*==================================================
                NOTIFICATION POPUP
===================================================*/

const notification =
    document.querySelector(".notification");

const notificationPopup =
    document.querySelector(".notification-popup");


if (notification && notificationPopup) {

    notification.addEventListener(
    "click",
    function (event) {

        event.stopPropagation();

        notificationPopup.classList.toggle("show");

        if (notificationPopup.classList.contains("show")) {

            fetch("mark_notifications_read.php")
                .then(function () {

                    const notificationCount =
                        notification.querySelector("span");

                    if (notificationCount) {
                        notificationCount.textContent = "0";
                    }

                })
                .catch(function (error) {

                    console.error(
                        "Unable to mark notifications as read:",
                        error
                    );

                });

        }

    }
);

}


document.addEventListener(
    "click",
    function (event) {

        if (
            notification &&
            notificationPopup &&
            !notification.contains(event.target) &&
            !notificationPopup.contains(event.target)
        ) {

            notificationPopup.classList.remove("show");

        }

    }
);

});