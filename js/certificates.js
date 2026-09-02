/*==================================================
        DIGITAL SKILL PASSPORT
           CERTIFICATES PAGE
===================================================*/


document.addEventListener("DOMContentLoaded", function () {


    /*==================================================
                    ELEMENTS
    ==================================================*/

    const certificateFile =
        document.getElementById("certificateFile");

    const certificateSearch =
    document.getElementById("certificateSearch");

    const certificatePreview =
        document.getElementById("certificatePreview");

    const certificateGrid =
        document.getElementById("certificateGrid");

    const certificateFilter =
        document.getElementById("certificateFilter");

    const noCertificates =
        document.getElementById("noCertificates");

    const certificateModal =
        document.getElementById("certificateModal");

    const modalCertificateContent =
        document.getElementById("modalCertificateContent");

    const closeCertificateModal =
        document.getElementById("closeCertificateModal");

    const modalOverlay =
        document.querySelector(".modal-overlay");

    const menuToggle =
        document.querySelector(".menu-toggle");

    const sidebar =
        document.querySelector(".sidebar");

    const certificateForm =
        document.querySelector(
            ".certificate-form-section form"
        );



    /*==================================================
              MOBILE SIDEBAR
    ==================================================*/

    if (menuToggle && sidebar) {

        menuToggle.addEventListener("click", function () {

            sidebar.classList.toggle(
                "show-sidebar"
            );

        });

    }



    /*==================================================
              CLOSE SIDEBAR
    ==================================================*/

    document.addEventListener("click", function (event) {

        if (!sidebar || !menuToggle) {
            return;
        }


        const clickedInsideSidebar =
            sidebar.contains(event.target);

        const clickedMenuButton =
            menuToggle.contains(event.target);


        if (
            window.innerWidth <= 768 &&
            !clickedInsideSidebar &&
            !clickedMenuButton
        ) {

            sidebar.classList.remove(
                "show-sidebar"
            );

        }

    });



    /*==================================================
             CERTIFICATE FILE PREVIEW
    ==================================================*/

    if (certificateFile) {

        certificateFile.addEventListener(
            "change",
            function () {

                certificatePreview.innerHTML = "";

                certificatePreview.style.display =
                    "none";


                const file =
                    this.files[0];


                if (!file) {
                    return;
                }


                /*--------------------------------------
                    Maximum file size = 5 MB
                --------------------------------------*/

                const maxSize =
                    5 * 1024 * 1024;


                if (file.size > maxSize) {

                    alert(
                        "File size must be less than 5 MB."
                    );

                    this.value = "";

                    return;

                }


                /*--------------------------------------
                    Allowed file types
                --------------------------------------*/

                const allowedTypes = [
                    "image/jpeg",
                    "image/png",
                    "image/webp",
                    "application/pdf"
                ];


                if (
                    !allowedTypes.includes(
                        file.type
                    )
                ) {

                    alert(
                        "Please upload a JPG, JPEG, PNG, WEBP or PDF file."
                    );

                    this.value = "";

                    return;

                }


                certificatePreview.style.display =
                    "block";


                /*--------------------------------------
                    IMAGE PREVIEW
                --------------------------------------*/

                if (
                    file.type.startsWith("image/")
                ) {

                    const reader =
                        new FileReader();


                    reader.onload =
                        function (event) {

                            const image =
                                document.createElement(
                                    "img"
                                );


                            image.src =
                                event.target.result;


                            image.alt =
                                "Certificate Preview";


                            certificatePreview.appendChild(
                                image
                            );

                        };


                    reader.readAsDataURL(file);

                }


                /*--------------------------------------
                    PDF PREVIEW
                --------------------------------------*/

                else if (
                    file.type ===
                    "application/pdf"
                ) {

                    const iframe =
                        document.createElement(
                            "iframe"
                        );


                    iframe.src =
                        URL.createObjectURL(
                            file
                        );


                    iframe.title =
                        "Certificate PDF Preview";


                    certificatePreview.appendChild(
                        iframe
                    );

                }

            }
        );

    }


/*==================================================
        CERTIFICATE SEARCH + CATEGORY FILTER
==================================================*/

if (certificateSearch && certificateFilter && certificateGrid) {

    function filterCertificates() {

        const searchText =
            certificateSearch.value.toLowerCase().trim();

        const selectedCategory =
            certificateFilter.value.toLowerCase();

        const certificateCards =
            certificateGrid.querySelectorAll(
                ".certificate-card"
            );

        let visibleCards = 0;


        certificateCards.forEach(function (card) {

            const cardText =
                card.textContent.toLowerCase();

            const cardCategory =
                (card.dataset.category || "").toLowerCase();


            const matchesSearch =
                cardText.includes(searchText);

            const matchesCategory =
                selectedCategory === "all" ||
                cardCategory === selectedCategory;


            if (matchesSearch && matchesCategory) {

                card.style.display = "";

                visibleCards++;

            } else {

                card.style.display = "none";

            }

        });


        if (noCertificates) {

            noCertificates.style.display =
                visibleCards === 0
                    ? "block"
                    : "none";

        }

    }


    certificateSearch.addEventListener(
        "input",
        filterCertificates
    );


    certificateFilter.addEventListener(
        "change",
        filterCertificates
    );

}
    


    


    /*==================================================
              CERTIFICATE VIEW MODAL
    ==================================================*/

    const viewButtons =
        document.querySelectorAll(
            ".view-certificate"
        );


    viewButtons.forEach(
        function (button) {

            button.addEventListener(
                "click",
                function () {

                    const card =
                        this.closest(
                            ".certificate-card"
                        );


                    if (!card) {
                        return;
                    }


                    const image =
                        card.querySelector(
                            ".certificate-image img"
                        );


                    if (!image) {
                        return;
                    }


                    modalCertificateContent.innerHTML =
                        "";


                    const modalImage =
                        document.createElement(
                            "img"
                        );


                    modalImage.src =
                        image.src;


                    modalImage.alt =
                        image.alt;


                    modalCertificateContent.appendChild(
                        modalImage
                    );


                    certificateModal.classList.add(
                        "show"
                    );


                    document.body.style.overflow =
                        "hidden";

                }
            );

        }
    );



    /*==================================================
                CLOSE CERTIFICATE MODAL
    ==================================================*/

    function closeModal() {

        if (!certificateModal) {
            return;
        }


        certificateModal.classList.remove(
            "show"
        );


        modalCertificateContent.innerHTML =
            "";


        document.body.style.overflow =
            "";

    }


    if (closeCertificateModal) {

        closeCertificateModal.addEventListener(
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


    /* Close modal using ESC */

    document.addEventListener(
        "keydown",
        function (event) {

            if (
                event.key === "Escape" &&
                certificateModal &&
                certificateModal.classList.contains(
                    "show"
                )
            ) {

                closeModal();

            }

        }
    );



    /*==================================================
                 DELETE CERTIFICATE
    ==================================================*/

    const deleteButtons =
        document.querySelectorAll(
            ".delete-certificate"
        );


    deleteButtons.forEach(
        function (button) {

            button.addEventListener(
                "click",
                function () {

                    const card =
                        this.closest(
                            ".certificate-card"
                        );


                    if (!card) {
                        return;
                    }


                    const certificateName =
                        card.querySelector(
                            "h3"
                        );


                    const name =
                        certificateName
                            ? certificateName.textContent.trim()
                            : "this certificate";


                    const confirmed =
                        confirm(
                            "Are you sure you want to delete " +
                            name +
                            "?"
                        );


                    if (confirmed) {

                        card.style.opacity =
                            "0";


                        card.style.transform =
                            "scale(0.95)";


                        setTimeout(
                            function () {

                                card.remove();

                                updateCertificateCount();

                            },
                            300
                        );

                    }

                }
            );

        }
    );



    /*==================================================
              FORM VALIDATION
    ==================================================*/

    if (certificateForm) {

        certificateForm.addEventListener(
            "submit",
            function (event) {

                const certificateName =
                    document.getElementById(
                        "certificateName"
                    );

                const category =
                    document.getElementById(
                        "certificateCategory"
                    );

                const organization =
                    document.getElementById(
                        "issuingOrganization"
                    );

                const issueDate =
                    document.getElementById(
                        "issueDate"
                    );


                let isValid = true;


                /*--------------------------------------
                    Remove previous states
                --------------------------------------*/

                const fields = [
                    certificateName,
                    category,
                    organization,
                    issueDate
                ];


                fields.forEach(
                    function (field) {

                        if (field) {

                            field.classList.remove(
                                "invalid"
                            );

                            field.classList.remove(
                                "valid"
                            );

                        }

                    }
                );


                /*--------------------------------------
                    Certificate name
                --------------------------------------*/

                if (
                    !certificateName ||
                    certificateName.value.trim() === ""
                ) {

                    certificateName.classList.add(
                        "invalid"
                    );

                    isValid = false;

                }

                else {

                    certificateName.classList.add(
                        "valid"
                    );

                }


                /*--------------------------------------
                    Category
                --------------------------------------*/

                if (
                    !category ||
                    category.value === ""
                ) {

                    category.classList.add(
                        "invalid"
                    );

                    isValid = false;

                }

                else {

                    category.classList.add(
                        "valid"
                    );

                }


                /*--------------------------------------
                    Organization
                --------------------------------------*/

                if (
                    !organization ||
                    organization.value.trim() === ""
                ) {

                    organization.classList.add(
                        "invalid"
                    );

                    isValid = false;

                }

                else {

                    organization.classList.add(
                        "valid"
                    );

                }


                /*--------------------------------------
                    Issue date
                --------------------------------------*/

                if (
                    !issueDate ||
                    issueDate.value === ""
                ) {

                    issueDate.classList.add(
                        "invalid"
                    );

                    isValid = false;

                }

                else {

                    issueDate.classList.add(
                        "valid"
                    );

                }


                /*--------------------------------------
                    Stop form if invalid
                --------------------------------------*/

                if (!isValid) {

                    event.preventDefault();


                    alert(
                        "Please fill in all required fields."
                    );


                    return;

                }


                /*
                 * Currently the form does not have
                 * a PHP database processing action.
                 *
                 * The backend can be connected later.
                 */

            }
        );

    }



    /*==================================================
                RESET FORM
    ==================================================*/

    if (certificateForm) {

        certificateForm.addEventListener(
            "reset",
            function () {

                setTimeout(
                    function () {

                        if (certificatePreview) {

                            certificatePreview.innerHTML =
                                "";

                            certificatePreview.style.display =
                                "none";

                        }


                        const fields =
                            certificateForm.querySelectorAll(
                                ".valid, .invalid"
                            );


                        fields.forEach(
                            function (field) {

                                field.classList.remove(
                                    "valid"
                                );

                                field.classList.remove(
                                    "invalid"
                                );

                            }
                        );

                    },
                    0
                );

            }
        );

    }



    /*==================================================
             SEARCH CERTIFICATES
    ==================================================*/

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
                        ".certificate-card"
                    );


                let visibleCards = 0;


                cards.forEach(
                    function (card) {

                        const cardText =
                            card.textContent
                                .toLowerCase();


                        if (
                            cardText.includes(
                                searchText
                            )
                        ) {

                            card.style.display =
                                "";

                            visibleCards++;

                        }

                        else {

                            card.style.display =
                                "none";

                        }

                    }
                );


                if (noCertificates) {

                    if (visibleCards === 0) {

                        noCertificates.style.display =
                            "block";

                    }

                    else {

                        noCertificates.style.display =
                            "none";

                    }

                }

            }
        );

    }



    /*==================================================
             UPDATE CERTIFICATE COUNT
    ==================================================*/

    function updateCertificateCount() {

        const cards =
            document.querySelectorAll(
                ".certificate-card"
            );


        const totalCount =
            document.querySelector(
                ".summary-card.total h2"
            );


        if (totalCount) {

            totalCount.textContent =
                cards.length;

        }


        /*
         * Update empty state if all cards
         * have been deleted.
         */

        if (
            noCertificates &&
            cards.length === 0
        ) {

            noCertificates.style.display =
                "block";

        }

    }



    /*==================================================
             DATE VALIDATION
    ==================================================*/

    const issueDate =
        document.getElementById(
            "issueDate"
        );


    const expiryDate =
        document.getElementById(
            "expiryDate"
        );


    if (
        issueDate &&
        expiryDate
    ) {

        expiryDate.addEventListener(
            "change",
            function () {

                if (
                    issueDate.value &&
                    expiryDate.value
                ) {

                    if (
                        expiryDate.value <
                        issueDate.value
                    ) {

                        alert(
                            "Expiry date cannot be earlier than the issue date."
                        );


                        expiryDate.value =
                            "";

                    }

                }

            }
        );

    }



    /*==================================================
              CERTIFICATE NAME LIVE CHECK
    ==================================================*/

    const certificateName =
        document.getElementById(
            "certificateName"
        );


    if (certificateName) {

        certificateName.addEventListener(
            "input",
            function () {

                if (
                    this.value.trim() !== ""
                ) {

                    this.classList.remove(
                        "invalid"
                    );

                    this.classList.add(
                        "valid"
                    );

                }

                else {

                    this.classList.remove(
                        "valid"
                    );

                }

            }
        );

    }



    /*==================================================
              ORGANIZATION LIVE CHECK
    ==================================================*/

    const organization =
        document.getElementById(
            "issuingOrganization"
        );


    if (organization) {

        organization.addEventListener(
            "input",
            function () {

                if (
                    this.value.trim() !== ""
                ) {

                    this.classList.remove(
                        "invalid"
                    );

                    this.classList.add(
                        "valid"
                    );

                }

                else {

                    this.classList.remove(
                        "valid"
                    );

                }

            }
        );

    }

    /*==================================================
        AUTO HIDE SUCCESS MESSAGE
==================================================*/

const successMessage =
    document.querySelector(".success-message");

if (successMessage) {

    setTimeout(function () {

        successMessage.style.opacity = "0";

        setTimeout(function () {

            successMessage.remove();

        }, 500);

    }, 3000);

}
if (
    window.location.search.includes("certificate_added") ||
    window.location.search.includes("certificate_updated") ||
    window.location.search.includes("certificate_deleted")
) {

    window.history.replaceState(
        {},
        document.title,
        "certificates.php"
    );

}


/*==================================================
        NOTIFICATION POPUP
==================================================*/

const notificationBell =
    document.getElementById("notificationBell");

const notificationPopup =
    document.getElementById("notificationPopup");


if (notificationBell && notificationPopup) {

    notificationBell.addEventListener("click", function (event) {

        event.stopPropagation();

        notificationPopup.classList.toggle("show");

    });


    document.addEventListener("click", function () {

        notificationPopup.classList.remove("show");

    });

}

    /*==================================================
               INITIAL COUNT
    ==================================================*/

    updateCertificateCount();


});