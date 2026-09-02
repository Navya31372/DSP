/*==================================================
        DIGITAL SKILL PASSPORT
             INTERNSHIPS PAGE
===================================================*/

document.addEventListener("DOMContentLoaded", function () {

    /*==================================================
                    ELEMENTS
    ===================================================*/

    const internshipForm =
        document.getElementById("internshipForm");

    const internshipGrid =
        document.getElementById("internshipsGrid");

    const internshipFilter =
        document.getElementById("internshipFilter");

    const searchInput =
        document.getElementById("internshipSearch");

    const noInternships =
        document.getElementById("noInternships");

    const addInternshipBtn =
        document.getElementById("addInternshipBtn");

    const emptyAddInternshipBtn =
        document.getElementById("emptyAddInternshipBtn");

    const resetBtn =
        document.getElementById("resetInternshipBtn");

    const certificateInput =
        document.getElementById("certificate");

    const certificatePreview =
        document.getElementById("certificatePreview");

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


    /*==================================================
                    SIDEBAR
    ===================================================*/

    const menuToggle =
        document.querySelector(".menu-toggle");

    const sidebar =
        document.querySelector(".sidebar");


    if (menuToggle && sidebar) {

        menuToggle.addEventListener("click", function () {

            sidebar.classList.toggle("show-sidebar");

        });

    }


    /*==================================================
              CLOSE SIDEBAR WHEN LINK CLICKED
    ===================================================*/

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
        document.getElementById("startDate");

    const endDate =
        document.getElementById("endDate");

    const location =
        document.getElementById("location");

    const workMode =
        document.getElementById("workMode");

    const description =
        document.getElementById("description");

    const skillsInput =
        document.getElementById("skills");



    /*==================================================
                DATE VALIDATION
    ===================================================*/

    if (start