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
```

**Request flow:** every link points at `public/index.php?route=...`.
`public/index.php` is the single front controller — it maps the
`route` query parameter to a Controller + method, the Controller asks
a Model for data, then hands that data to a View to render. Models
never output HTML; Views never run SQL; Controllers never do either
directly — that separation is the whole point of MVC.

| Route                     | Controller@method              | What it does                        |
|---------------------------|---------------------------------|--------------------------------------|
| `login`                   | AuthController@showLogin        | Shows the login form                 |
| `login.submit`            | AuthController@login            | Verifies credentials, starts session |
| `logout`                  | AuthController@logout           | Destroys the session                 |
| `dashboard`                | DashboardController@index       | Schedule for a date + stats          |
| `appointment.complete`    | DashboardController@complete    | Marks an appointment completed       |
| `profile`                 | ProfileController@index         | Shows the profile form               |
| `profile.update`          | ProfileController@update        | Saves specialization/fee/time/room   |

## Setup (XAMPP)

1. Copy this whole `doctorconnect-mvc` folder into
   `C:\xampp\htdocs\` (Windows) or `/Applications/XAMPP/htdocs/` (Mac).
2. Start **Apache** and **MySQL**.
3. In phpMyAdmin, import `sql/schema.sql` to create the database and
   tables. Optionally also import `sql/more_sample_data.sql` for past
   and future appointments.
4. Open `http://localhost/doctorconnect-mvc/seed.php` once in your
   browser to create a test doctor account and a few appointments for
   today. Delete `seed.php` afterwards.
5. Go to `http://localhost/doctorconnect-mvc/` (or straight to
   `public/index.php?route=login`) and sign in with:
   - **Email:** doctor@doctorconnect.test
   - **Password:** doctor123

## Notes for merging with your teammates' parts

- `app/Config/Database.php`, `sql/schema.sql` and
  `public/assets/css/style.css` are meant to be shared across all four
  roles.
- Adding another role (patient, receptionist, admin) means: a new
  Controller in `app/Controllers/`, its Views in `app/Views/<role>/`,
  and new entries in the `$routes` array in `public/index.php` — the
  Models (`User`, `Doctor`, `Appointment`) can mostly be reused as-is.
- All database access uses `mysqli` prepared statements
  (`->prepare()` + `->bind_param()`), as required by the proposal.
