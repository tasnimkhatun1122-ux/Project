function searchDoctors() {

    let search = document
        .getElementById("searchInput")
        .value
        .toLowerCase();

    let department = document
        .getElementById("departmentFilter")
        .value
        .toLowerCase();

    let doctors = document.querySelectorAll(".doctor-card");

    doctors.forEach(function(doctor) {

        let name = doctor.dataset.name;
        let dept = doctor.dataset.department;

        if (
            name.includes(search) &&
            (department === "" || dept === department)
        ) {
            doctor.style.display = "flex";
        } else {
            doctor.style.display = "none";
        }

    });
}


function openBooking(id, name) {

    document.getElementById("doctorId").value = id;
    document.getElementById("selectedDoctor").innerText = name;

    document.getElementById("bookingModal").style.display = "flex";
}


function closeBooking() {

    document.getElementById("bookingModal").style.display = "none";
}


// Close popup when clicking outside it.
window.onclick = function(event) {

    let modal = document.getElementById("bookingModal");

    if (event.target === modal) {
        modal.style.display = "none";
    }
}