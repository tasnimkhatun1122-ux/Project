
        const navItems =
            document.querySelectorAll(".nav-item");


        navItems.forEach(function (item) {

            item.addEventListener("click", function () {

                navItems.forEach(function (i) {
                    i.classList.remove("active");
                });

                item.classList.add("active");

                if (item.dataset.nav === "book-walkin") {

                    const form =
                        document.getElementById("walkinForm");

                    if (form) {
                        form.scrollIntoView({
                            behavior: "smooth",
                            block: "center"
                        });
                    }
                }

                if (item.dataset.nav === "todays-queue") {

                    const queue =
                        document.querySelector(".queue-card");

                    if (queue) {
                        queue.scrollIntoView({
                            behavior: "smooth",
                            block: "center"
                        });
                    }
                }

            });

        });


        const newWalkinBtn =
            document.getElementById("newWalkinBtn");

        const patientNameInput =
            document.getElementById("patientName");


        newWalkinBtn.addEventListener("click", function () {

            patientNameInput.scrollIntoView({
                behavior: "smooth",
                block: "center"
            });

            patientNameInput.focus();

        });




        const queueBody =
            document.getElementById("queueBody");


        queueBody.addEventListener("click", function (event) {

            const button =
                event.target.closest(".action-link");

            if (!button) {
                return;
            }

            const row =
                button.closest("tr");

            const apptId =
                row.dataset.apptId;

            const action =
                button.dataset.action;

            if (!apptId) {
                alert("Appointment ID is missing.");
                return;
            }

            button.disabled = true;

            const formData = new FormData();

            formData.append("apptId", apptId);
            formData.append("action", action);


            fetch("../../../public/index.php?action=updateAppointment", {
                method: "POST",
                body: formData
            })
                .then(function (response) {
                    return response.json();
                })
                .then(function (result) {

                    if (result.success) {

                        window.location.reload();

                    } else {

                        alert(
                            result.message ||
                            "Could not update this appointment."
                        );

                        button.disabled = false;
                    }

                })
                .catch(function () {

                    alert("Unable to connect to the server.");

                    button.disabled = false;

                });

        });


        const walkinForm =
            document.getElementById("walkinForm");

        const patientPhoneInput =
            document.getElementById("patientPhone");

        const walkinDoctorSelect =
            document.getElementById("walkinDoctor");

        const walkinSlotSelect =
            document.getElementById("walkinSlot");

        const patientNameError =
            document.getElementById("patientNameError");

        const patientPhoneError =
            document.getElementById("patientPhoneError");

        const walkinDoctorError =
            document.getElementById("walkinDoctorError");

        const walkinSlotError =
            document.getElementById("walkinSlotError");

        const bookingSuccess =
            document.getElementById("bookingSuccess");


        function isValidPhone(phone) {

            const phonePattern =
                /^[0-9+\-\s]{7,15}$/;

            return phonePattern.test(phone);

        }


        function toggleSelectPlaceholder(select) {

            if (select.value === "") {

                select.classList.add("placeholder");

            } else {

                select.classList.remove("placeholder");

            }

        }


        [
            walkinDoctorSelect,
            walkinSlotSelect
        ].forEach(function (select) {

            select.addEventListener("change", function () {

                toggleSelectPlaceholder(select);

            });

        });


        walkinForm.addEventListener("submit", function (event) {

            event.preventDefault();


            patientNameError.classList.remove("show");

            patientPhoneError.classList.remove("show");

            walkinDoctorError.classList.remove("show");

            walkinSlotError.classList.remove("show");

            bookingSuccess.classList.remove("show");

            bookingSuccess.style.color = "";


            let isValid = true;


            const patientName =
                patientNameInput.value.trim();


            if (patientName === "") {

                patientNameError.classList.add("show");

                isValid = false;

            }


            const patientPhone =
                patientPhoneInput.value.trim();


            if (patientPhone === "") {

                patientPhoneError.textContent =
                    "Please enter the patient's phone number.";

                patientPhoneError.classList.add("show");

                isValid = false;

            } else if (!isValidPhone(patientPhone)) {

                patientPhoneError.textContent =
                    "Please enter a valid phone number.";

                patientPhoneError.classList.add("show");

                isValid = false;

            }


            const doctorValue =
                walkinDoctorSelect.value;


            if (doctorValue === "") {

                walkinDoctorError.classList.add("show");

                isValid = false;

            }


            const slotValue =
                walkinSlotSelect.value;


            if (slotValue === "") {

                walkinSlotError.classList.add("show");

                isValid = false;

            }


            if (!isValid) {
                return;
            }


            const submitButton =
                walkinForm.querySelector(".btn-confirm");

            submitButton.disabled = true;


            const formData =
                new FormData(walkinForm);


            fetch("../../../public/index.php?action=bookWalkIn", {
                method: "POST",
                body: formData
            })
                .then(function (response) {
                    return response.json();
                })
                .then(function (result) {

                    if (result.success) {

                        bookingSuccess.textContent =
                            result.message;

                        bookingSuccess.classList.add("show");

                        setTimeout(function () {

                            window.location.reload();

                        }, 700);

                    } else {

                        bookingSuccess.textContent =
                            result.message ||
                            "Could not book this walk-in.";

                        bookingSuccess.style.color =
                            "#d64545";

                        bookingSuccess.classList.add("show");

                        submitButton.disabled = false;
                    }

                })
                .catch(function () {

                    bookingSuccess.textContent =
                        "Unable to connect to the server.";

                    bookingSuccess.style.color =
                        "#d64545";

                    bookingSuccess.classList.add("show");

                    submitButton.disabled = false;

                });

        });


        [
            patientNameInput,
            patientPhoneInput
        ].forEach(function (input) {

            input.addEventListener("input", function () {

                bookingSuccess.classList.remove("show");

            });

        });


        patientNameInput.addEventListener("input", function () {

            if (patientNameInput.value.trim() !== "") {

                patientNameError.classList.remove("show");

            }

        });


        patientPhoneInput.addEventListener("input", function () {

            if (patientPhoneInput.value.trim() !== "") {

                patientPhoneError.classList.remove("show");

            }

        });

