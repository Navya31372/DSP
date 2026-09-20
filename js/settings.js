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

            formData.set(
    "achievement_notifications",
    document.getElementById(
        "achievementNotifications"
    ).checked ? "1" : "0"
);

formData.set(
    "certificate_notifications",
    document.getElementById(
        "certificateNotifications"
    ).checked ? "1" : "0"
);

formData.set(
    "workshop_notifications",
    document.getElementById(
        "workshopNotifications"
    ).checked ? "1" : "0"
);

formData.set(
    "security_notifications",
    document.getElementById(
        "securityNotifications"
    ).checked ? "1" : "0"
);

formData.set(
    "profile_visibility",
    document.getElementById(
        "profileVisibility"
    ).value
);

const skillsVisibilityInput =
    document.getElementById("skillsVisibility");

formData.delete("skills_visibility");

formData.append(
    "skills_visibility",
    skillsVisibilityInput &&
    skillsVisibilityInput.checked
        ? "1"
        : "0"
);

formData.set(
    "contact_visibility",
    document.getElementById(
        "contactVisibility"
    ).checked ? "1" : "0"
);


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
Real password update
----------------------------------------------
*/

const originalText =
    this.innerHTML;


this.disabled = true;

this.innerHTML =
    '<i class="fa-solid fa-spinner fa-spin"></i> Updating...';


const formData =
    new FormData();

formData.append(
    "current_password",
    currentPassword.value
);

formData.append(
    "new_password",
    newPassword.value
);

formData.append(
    "confirm_password",
    confirmPassword.value
);


fetch(
    "change_password.php",
    {
        method: "POST",
        body: formData
    }
)

.then(function (response) {

    return response.json();

})

.then(function (data) {

    changePasswordBtn.disabled =
        false;

    changePasswordBtn.innerHTML =
        originalText;


    if (data.success) {

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

    console.error(error);

    changePasswordBtn.disabled =
        false;

    changePasswordBtn.innerHTML =
        originalText;


    showMessage(
        "Failed to update password.",
        "error"
    );

});

            }
        );

    }


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

            saveUserSettings();

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
Real account deletion
------------------------------------------------
*/

const currentPassword =
    prompt(
        "For security, enter your current password:"
    );


if (currentPassword === null) {

    return;

}


if (currentPassword.trim() === "") {

    showMessage(
        "Current password is required.",
        "error"
    );

    return;

}


this.disabled = true;

this.innerHTML =
    '<i class="fa-solid fa-spinner fa-spin"></i> Deleting...';


const formData =
    new FormData();

formData.append(
    "current_password",
    currentPassword
);


fetch(
    "delete_account.php",
    {
        method: "POST",
        body: formData
    }
)

.then(function (response) {

    return response.json();

})

.then(function (data) {

    if (data.success) {

        showMessage(
            data.message,
            "success"
        );


        setTimeout(function () {

            window.location.href =
                "login.php";

        }, 1200);

    }

    else {

        deleteAccountBtn.disabled =
            false;

        deleteAccountBtn.innerHTML =
            '<i class="fa-solid fa-trash"></i> Delete Account';


        showMessage(
            data.message,
            "error"
        );

    }

})

.catch(function (error) {

    console.error(error);

    deleteAccountBtn.disabled =
        false;

    deleteAccountBtn.innerHTML =
        '<i class="fa-solid fa-trash"></i> Delete Account';


    showMessage(
        "Account deletion failed. Please try again.",
        "error"
    );

});

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

/*==================================================
              NOTIFICATION POPUP
==================================================*/

const notificationBtn =
    document.getElementById("notificationBtn");

const notificationPopup =
    document.getElementById("notificationPopup");

const notificationBadge =
    document.getElementById("notificationBadge");


if (notificationBtn && notificationPopup) {

    notificationBtn.addEventListener(
        "click",
        function () {

            notificationPopup.classList.toggle(
                "show"
            );

            if (
                notificationBadge &&
                notificationBadge.textContent.trim() !== "0"
            ) {

                fetch(
                    "mark_notifications_read.php",
                    {
                        method: "POST"
                    }
                )
                .then(function () {

                    notificationBadge.textContent =
                        "0";

                })
                .catch(function (error) {

                    console.error(error);

                });

            }

        }
    );

}