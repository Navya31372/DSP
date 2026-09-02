/*==================================================
        DIGITAL SKILL PASSPORT
              SETTINGS PAGE
===================================================*/


document.addEventListener("DOMContentLoaded", function () {


    /*==================================================
                     ELEMENTS
    ==================================================*/

    const menuToggle =
        document.getElementById("menuToggle");

    const sidebar =
        document.querySelector(".sidebar");

    const settingsNavItems =
        document.querySelectorAll(".settings-nav-item");

    const settingsPanels =
        document.querySelectorAll(".settings-panel");

    const togglePasswordButtons =
        document.querySelectorAll(".toggle-password");

    const saveAccountBtn =
        document.getElementById("saveAccountBtn");

    const changePasswordBtn =
        document.getElementById("changePasswordBtn");

    const deleteAccountBtn =
        document.getElementById("deleteAccountBtn");

    const accountForm =
        document.getElementById("accountSettingsForm");

    const securityForm =
        document.getElementById("securityForm");



    /*==================================================
                    MOBILE SIDEBAR
    ==================================================*/

    if (menuToggle && sidebar) {

        menuToggle.addEventListener("click", function () {

            sidebar.classList.toggle("show-sidebar");

        });

    }



    /*==================================================
                 CLOSE SIDEBAR ON OUTSIDE CLICK
    ==================================================*/

    document.addEventListener("click", function (event) {

        if (!sidebar || !menuToggle) {
            return;
        }


        if (
            window.innerWidth <= 768 &&
            sidebar.classList.contains("show-sidebar") &&
            !sidebar.contains(event.target) &&
            !menuToggle.contains(event.target)
        ) {

            sidebar.classList.remove("show-sidebar");

        }

    });



    /*==================================================
                    SETTINGS TABS
    ==================================================*/

    settingsNavItems.forEach(function (button) {

        button.addEventListener("click", function () {

            const targetSection =
                this.getAttribute("data-section");


            /*
            ----------------------------------------------
            Remove active state from all navigation items
            ----------------------------------------------
            */

            settingsNavItems.forEach(function (item) {

                item.classList.remove("active");

            });


            /*
            ----------------------------------------------
            Add active state to selected item
            ----------------------------------------------
            */

            this.classList.add("active");


            /*
            ----------------------------------------------
            Hide all panels
            ----------------------------------------------
            */

            settingsPanels.forEach(function (panel) {

                panel.classList.remove("active");

            });


            /*
            ----------------------------------------------
            Show selected panel
            ----------------------------------------------
            */

            const targetPanel =
                document.getElementById(targetSection);


            if (targetPanel) {

                targetPanel.classList.add("active");

            }


            /*
            ----------------------------------------------
            Scroll to settings content on small screens
            ----------------------------------------------
            */

            if (window.innerWidth <= 768) {

                const settingsContent =
                    document.querySelector(".settings-content");


                if (settingsContent) {

                    settingsContent.scrollIntoView({
                        behavior: "smooth",
                        block: "start"
                    });

                }

            }

        });

    });



    /*==================================================
                PASSWORD SHOW / HIDE
    ==================================================*/

    togglePasswordButtons.forEach(function (button) {

        button.addEventListener("click", function () {

            const targetId =
                this.getAttribute("data-target");

            const passwordInput =
                document.getElementById(targetId);

            const icon =
                this.querySelector("i");


            if (!passwordInput) {
                return;
            }


            if (passwordInput.type === "password") {

                passwordInput.type = "text";


                if (icon) {

                    icon.classList.remove(
                        "fa-eye"
                    );

                    icon.classList.remove(
                        "fa-regular"
                    );

                    icon.classList.add(
                        "fa-eye-slash"
                    );

                }

            }

            else {

                passwordInput.type = "password";


                if (icon) {

                    icon.classList.remove(
                        "fa-eye-slash"
                    );

                    icon.classList.add(
                        "fa-eye"
                    );

                }

            }

        });

    });



    /*==================================================
              PASSWORD VALIDATION HELPERS
    ==================================================*/

    function validatePassword(password) {

        /*
        Minimum 8 characters
        */

        if (password.length < 8) {

            return false;

        }


        /*
        At least one letter
        */

        if (!/[A-Za-z]/.test(password)) {

            return false;

        }


        /*
        At least one number
        */

        if (!/[0-9]/.test(password)) {

            return false;

        }


        return true;

    }



    function setInputState(input, valid) {

        if (!input) {
            return;
        }


        const parent =
            input.closest(
                ".password-input, .settings-input"
            );


        if (!parent) {
            return;
        }


        parent.classList.remove("valid");
        parent.classList.remove("invalid");


        if (valid === true) {

            parent.classList.add("valid");

        }

        else if (valid === false) {

            parent.classList.add("invalid");

        }

    }



    /*==================================================
                PASSWORD LIVE VALIDATION
    ==================================================*/

    const newPassword =
        document.getElementById("newPassword");

    const confirmPassword =
        document.getElementById("confirmPassword");


    if (newPassword) {

        newPassword.addEventListener(
            "input",
            function () {

                if (this.value === "") {

                    setInputState(this, null);

                    return;

                }


                setInputState(
                    this,
                    validatePassword(this.value)
                );

            }
        );

    }



    if (confirmPassword) {

        confirmPassword.addEventListener(
            "input",
            function () {

                if (this.value === "") {

                    setInputState(this, null);

                    return;

                }


                const matches =
                    newPassword &&
                    this.value === newPassword.value;


                setInputState(
                    this,
                    matches
                );

            }
        );

    }



    /*==================================================
                 ACCOUNT FORM VALIDATION
    ==================================================*/

    function validateAccountForm() {

        if (!accountForm) {
            return false;
        }


        const name =
            document.getElementById("settingsName");

        const email =
            document.getElementById("settingsEmail");


        let valid = true;


        /*
        ----------------------------------------------
        Name validation
        ----------------------------------------------
        */

        if (!name || name.value.trim().length < 2) {

            setInputState(name, false);

            valid = false;

        }

        else {

            setInputState(name, true);

        }



        /*
        ----------------------------------------------
        Email validation
        ----------------------------------------------
        */

        if (
            !email ||
            !/^[^\s@]+@[^\s@]+\.[^\s@]+$/.test(
                email.value.trim()
            )
        ) {

            setInputState(email, false);

            valid = false;

        }

        else {

            setInputState(email, true);

        }


        return valid;

    }



    
        

    
    /*==================================================
                   SAVE ACCOUNT
==================================================*/

if (saveAccountBtn) {

    saveAccountBtn.addEventListener(
        "click",
        function () {

            if (!validateAccountForm()) {

                showMessage(
                    "Please enter valid account information.",
                    "error"
                );

                return;
            }


            const originalText =
                this.innerHTML;


            this.disabled = true;

            this.innerHTML =
                '<i class="fa-solid fa-spinner fa-spin"></i> Saving...';


            const formData =
                new FormData(accountForm);


            fetch("save_settings.php", {

                method: "POST",

                body: formData

            })

            .then(function (response) {

                return response.json();

            })

            .then(function (data) {

                saveAccountBtn.disabled = false;

                saveAccountBtn.innerHTML =
                    originalText;


                if (data.success) {

                    showMessage(
                        data.message,
                        "success"
                    );

                }

                else {

                    showMessage(
                        data.message,
                        "error"
                    );

                }

            })

            .catch(function (error) {

                saveAccountBtn.disabled = false;

                saveAccountBtn.innerHTML =
                    originalText;


                showMessage(
                    "Something went wrong while saving.",
                    "error"
                );

                console.error(error);

            });

        }
    );

}



    /*==================================================
                 CHANGE PASSWORD
    ==================================================*/

    if (changePasswordBtn) {

        changePasswordBtn.addEventListener(
            "click",
            function () {

                const currentPassword =
                    document.getElementById(
                        "currentPassword"
                    );


                const newPassword =
                    document.getElementById(
                        "newPassword"
                    );


                const confirmPassword =
                    document.getElementById(
                        "confirmPassword"
                    );


                /*
                ----------------------------------------------
                Current password
                ----------------------------------------------
                */

                if (
                    !currentPassword ||
                    currentPassword.value.trim() === ""
                ) {

                    setInputState(
                        currentPassword,
                        false
                    );


                    showMessage(
                        "Please enter your current password.",
                        "error"
                    );


                    return;

                }


                setInputState(
                    currentPassword,
                    true
                );



                /*
                ----------------------------------------------
                New password
                ----------------------------------------------
                */

                if (
                    !newPassword ||
                    !validatePassword(
                        newPassword.value
                    )
                ) {

                    setInputState(
                        newPassword,
                        false
                    );


                    showMessage(
                        "New password must contain at least 8 characters, including letters and numbers.",
                        "error"
                    );


                    return;

                }


                setInputState(
                    newPassword,
                    true
                );



                /*
                ----------------------------------------------
                Confirm password
                ----------------------------------------------
                */

                if (
                    !confirmPassword ||
                    confirmPassword.value !==
                    newPassword.value
                ) {

                    setInputState(
                        confirmPassword,
                        false
                    );


                    showMessage(
                        "New passwords do not match.",
                        "error"
                    );


                    return;

                }


                setInputState(
                    confirmPassword,
                    true
                );



                /*
                ----------------------------------------------
                Simulated password update
                ----------------------------------------------
                */

                const originalText =
                    this.innerHTML;


                this.disabled = true;

                this.innerHTML =
                    '<i class="fa-solid fa-spinner fa-spin"></i> Updating...';


                setTimeout(function () {

                    changePasswordBtn.disabled = false;

                    changePasswordBtn.innerHTML =
                        originalText;


                    if (securityForm) {

                        securityForm.reset();

                    }


                    setInputState(
                        currentPassword,
                        null
                    );

                    setInputState(
                        newPassword,
                        null
                    );

                    setInputState(
                        confirmPassword,
                        null
                    );


                    showMessage(
                        "Password updated successfully.",
                        "success"
                    );

                }, 800);

            }
        );

    }



    /*==================================================
                NOTIFICATION SETTINGS
    ==================================================*/

    const notificationSwitches =
        document.querySelectorAll(
            "#notifications .switch input"
        );


    notificationSwitches.forEach(function (toggle) {

        toggle.addEventListener(
            "change",
            function () {

                const toggleRow =
                    this.closest(".toggle-row");


                if (!toggleRow) {
                    return;
                }


                const title =
                    toggleRow.querySelector(
                        ".toggle-title"
                    );


                let notificationName =
                    "Notification preference";


                if (title) {

                    notificationName =
                        title.textContent.trim();

                }


                if (this.checked) {

                    showMessage(
                        notificationName +
                        " enabled.",
                        "success"
                    );

                }

                else {

                    showMessage(
                        notificationName +
                        " disabled.",
                        "info"
                    );

                }

            }
        );

    });



    /*==================================================
                    PRIVACY SETTINGS
    ==================================================*/

    const privacySwitches =
        document.querySelectorAll(
            "#privacy .switch input"
        );


    privacySwitches.forEach(function (toggle) {

        toggle.addEventListener(
            "change",
            function () {

                if (this.checked) {

                    showMessage(
                        "Privacy preference enabled.",
                        "success"
                    );

                }

                else {

                    showMessage(
                        "Privacy preference disabled.",
                        "info"
                    );

                }

            }
        );

    });



    /*==================================================
                PROFILE VISIBILITY
    ==================================================*/

    const profileVisibility =
        document.getElementById(
            "profileVisibility"
        );


    if (profileVisibility) {

        profileVisibility.addEventListener(
            "change",
            function () {

                let message = "";


                if (this.value === "public") {

                    message =
                        "Your profile is now public.";

                }

                else if (this.value === "private") {

                    message =
                        "Your profile is now private.";

                }

                else {

                    message =
                        "Your profile visibility is limited.";

                }


                showMessage(
                    message,
                    "success"
                );

            }
        );

    }



    /*==================================================
                 DELETE ACCOUNT
    ==================================================*/

    if (deleteAccountBtn) {

        deleteAccountBtn.addEventListener(
            "click",
            function () {

                const confirmed =
                    confirm(
                        "Are you sure you want to delete your account?\n\nThis action cannot be undone."
                    );


                if (!confirmed) {

                    return;

                }


                const secondConfirmation =
                    confirm(
                        "This will permanently remove your Digital Skill Passport account and its information.\n\nContinue?"
                    );


                if (!secondConfirmation) {

                    return;

                }


                /*
                ------------------------------------------------
                Frontend demonstration.
                Connect this section to PHP/MySQL later.
                ------------------------------------------------
                */

                this.disabled = true;

                this.innerHTML =
                    '<i class="fa-solid fa-spinner fa-spin"></i> Processing...';


                setTimeout(function () {

                    showMessage(
                        "Account deletion request submitted.",
                        "success"
                    );


                    deleteAccountBtn.disabled =
                        false;


                    deleteAccountBtn.innerHTML =
                        '<i class="fa-solid fa-trash"></i> Delete Account';

                }, 1000);

            }
        );

    }



    /*==================================================
                 MESSAGE / TOAST SYSTEM
    ==================================================*/

    function showMessage(message, type) {


        /*
        ----------------------------------------------
        Remove existing toast
        ----------------------------------------------
        */

        const oldToast =
            document.querySelector(
                ".settings-toast"
            );


        if (oldToast) {

            oldToast.remove();

        }



        /*
        ----------------------------------------------
        Create toast
        ----------------------------------------------
        */

        const toast =
            document.createElement("div");


        toast.className =
            "settings-toast";



        /*
        ----------------------------------------------
        Icon
        ----------------------------------------------
        */

        let icon =
            "fa-circle-info";


        if (type === "success") {

            icon =
                "fa-circle-check";

        }

        else if (type === "error") {

            icon =
                "fa-circle-exclamation";

        }



        toast.innerHTML =

            `
            <i class="fa-solid ${icon}"></i>
            <span>${message}</span>
            `;



        /*
        ----------------------------------------------
        Toast styling
        ----------------------------------------------
        */

        toast.style.position =
            "fixed";

        toast.style.right =
            "25px";

        toast.style.bottom =
            "25px";

        toast.style.zIndex =
            "5000";

        toast.style.maxWidth =
            "360px";

        toast.style.padding =
            "13px 17px";

        toast.style.borderRadius =
            "10px";

        toast.style.display =
            "flex";

        toast.style.alignItems =
            "center";

        toast.style.gap =
            "9px";

        toast.style.fontSize =
            "11px";

        toast.style.fontWeight =
            "500";

        toast.style.background =
            "#ffffff";

        toast.style.boxShadow =
            "0 12px 30px rgba(15, 23, 42, 0.15)";

        toast.style.border =
            "1px solid #e2e8f0";

        toast.style.animation =
            "settingsToastIn 0.25s ease";



        /*
        ----------------------------------------------
        Icon color
        ----------------------------------------------
        */

        const toastIcon =
            toast.querySelector("i");


        if (type === "success") {

            toastIcon.style.color =
                "#10b981";

        }

        else if (type === "error") {

            toastIcon.style.color =
                "#ef4444";

        }

        else {

            toastIcon.style.color =
                "#6366f1";

        }



        document.body.appendChild(toast);



        /*
        ----------------------------------------------
        Automatically remove
        ----------------------------------------------
        */

        setTimeout(function () {

            toast.style.opacity =
                "0";

            toast.style.transform =
                "translateY(10px)";

            toast.style.transition =
                "0.25s ease";


            setTimeout(function () {

                if (toast.parentNode) {

                    toast.remove();

                }

            }, 250);

        }, 3000);

    }



    /*==================================================
             TOAST ANIMATION STYLE
    ==================================================*/

    const toastStyle =
        document.createElement("style");


    toastStyle.textContent = `

        @keyframes settingsToastIn {

            from {
                opacity: 0;
                transform: translateY(10px);
            }

            to {
                opacity: 1;
                transform: translateY(0);
            }

        }

    `;


    document.head.appendChild(toastStyle);



    /*==================================================
                 SEARCH BOX
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
                    this.value.trim().toLowerCase();


                if (searchText === "") {

                    settingsNavItems.forEach(
                        function (item) {

                            item.style.display =
                                "flex";

                        }
                    );

                    return;

                }


                settingsNavItems.forEach(
                    function (item) {

                        const text =
                            item.textContent
                                .trim()
                                .toLowerCase();


                        if (
                            text.includes(searchText)
                        ) {

                            item.style.display =
                                "flex";

                        }

                        else {

                            item.style.display =
                                "none";

                        }

                    }
                );

            }
        );

    }



    /*==================================================
             PREVENT EMPTY FORM SUBMISSION
    ==================================================*/

    if (accountForm) {

        accountForm.addEventListener(
            "submit",
            function (event) {

                event.preventDefault();

            }
        );

    }


    if (securityForm) {

        securityForm.addEventListener(
            "submit",
            function (event) {

                event.preventDefault();

            }
        );

    }



    /*==================================================
                  WINDOW RESIZE
    ==================================================*/

    window.addEventListener(
        "resize",
        function () {

            if (
                window.innerWidth > 768 &&
                sidebar
            ) {

                sidebar.classList.remove(
                    "show-sidebar"
                );

            }

        }
    );

});