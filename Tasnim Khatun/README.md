# DoctorConnect — Doctor Module (MVC)

The doctor part of the DoctorConnect group project, restructured into
**Model–View–Controller**, built with HTML, CSS, JavaScript, PHP and
MySQL (no framework — a small hand-rolled front controller).

## Architecture

```
doctorconnect-mvc/
├── index.php                    redirects to public/index.php
├── seed.php                     one-time sample data loader
├── sql/
│   ├── schema.sql               table definitions
│   └── more_sample_data.sql     extra past/future appointments
├── public/                      the web root — point Apache here
│   ├── index.php                FRONT CONTROLLER — routes every request
│   └── assets/css/style.css
└── app/
    ├── Config/
    │   └── Database.php         mysqli connection (singleton)
    ├── Core/
    │   ├── Auth.php             session/role guard
    │   └── View.php             tiny view-rendering helper
    ├── Models/                  MODEL — talks to the database only
    │   ├── User.php
    │   ├── Doctor.php
    │   └── Appointment.php
    ├── Controllers/             CONTROLLER — request handling, no SQL, no HTML
    │   ├── AuthController.php
    │   ├── DashboardController.php
    │   └── ProfileController.php
    └── Views/                   VIEW — HTML templates only, no SQL
        ├── auth/login.php
        ├── doctor/dashboard.php
        ├── doctor/profile.php
        └── partials/sidebar.php

