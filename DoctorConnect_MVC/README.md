# DoctorConnect — MVC version

The same application as `../doctorconnect`, restructured into **Model–View–Controller**.
The procedural version still exists and still runs; this is a parallel rebuild, not a replacement.

Open at: `http://localhost/Web-Technology-/doctorconnect-mvc/`

## The three layers

| Layer | Folder | Job | Knows about |
|---|---|---|---|
| **Model** | `app/models/` | Data and database queries | The database only |
| **View** | `app/views/` | The HTML the user sees | The data handed to it |
| **Controller** | `app/controllers/` | Receives the request, asks the Model, picks the View | Both |

No SQL appears in a View. No HTML appears in a Model. A Controller contains neither —
it only coordinates.

## How a request flows

```
Browser:  index.php?url=patient/book/3
              |
              v
index.php  ->  Router  ->  PatientController->book(3)
                                  |
                                  |-- Appointment model  -> database
                                  |-- Doctor model       -> database
                                  v
                          views/patient/book.php
                                  |
                                  v
                          views/layouts/app.php  ->  HTML out
```

`Router` splits the URL into `controller/action/parameters`, so `patient/book/3`
calls `book(3)` on `PatientController`.

## Folder map

```
doctorconnect-mvc/
├── index.php              front controller — every request enters here
├── .htaccess              pretty URLs (falls back to ?url= if mod_rewrite is off)
├── config/config.php      database credentials, BASE_URL, time slots
├── app/
│   ├── core/
│   │   ├── Database.php   one shared connection, prepared statements only
│   │   ├── Model.php      base class every model extends
│   │   ├── Controller.php base class — view(), redirect(), requireRole()
│   │   ├── Auth.php       sessions, login, roles
│   │   └── Router.php     URL -> controller + action
│   ├── models/            User, Doctor, Department, Appointment
│   ├── controllers/       Home, Auth, Patient, Doctor, Reception, Admin, Profile
│   └── views/
│       ├── layouts/       app (sidebar), auth (split), plain (public)
│       └── …              one folder per controller
└── public/css/style.css
```

## What improved over the procedural version

- **SQL lives in one place per table.** Changing how appointments are read means editing
  `Appointment.php`, not hunting through 23 files.
- **One connection, one bind helper.** `Database.php` builds the `bind_param` type string
  automatically, so no call site can get the types wrong.
- **Access control is inherited.** `requireRole()` sits on the base `Controller`, so every
  controller gets it for free instead of each page repeating the check.
- **Layouts are shared.** Three layout files replace the header/footer include pair.

## What stayed the same

Prepared statements on every query, `password_hash()` / `password_verify()`,
`htmlspecialchars()` on input, `session_regenerate_id()` at login, and the same
four-role model — patient, doctor, receptionist, admin.

It uses the **same `doctorconnect_db` database**, so both versions show the same data.
