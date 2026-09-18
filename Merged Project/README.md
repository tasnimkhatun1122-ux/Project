# DoctorConnect — Merged Project

**Group 06 · Section F · CSC 3215 Web Technologies · Summer 2025–26**

The four individual modules merged into one working application, restructured
into **Model–View–Controller**. All four user roles run from a single codebase
and a single database.

| Role | What they can do |
|---|---|
| **Patient** | Search doctors by department, see live slot availability, book, cancel, track status |
| **Doctor** | See the day's queue, open a visit, record diagnosis and notes |
| **Receptionist** | Run the front desk, check patients in, register walk-ins |
| **Admin** | Manage doctors and departments |

## Setup

```
1. Copy this folder into XAMPP's htdocs.
2. Start Apache and MySQL.
3. Load the database:
      mysql -u root < sql/schema.sql
      mysql -u root < sql/seed.sql
4. Open http://localhost/<folder-name>/
```

`BASE_URL` detects its own location, so the project works under any folder name
without editing config.

### Demo accounts — password `1234` for all

| Role | Email |
|---|---|
| Admin | admin@doctorconnect.com |
| Doctor | salma@doctorconnect.com |
| Receptionist | reception@doctorconnect.com |
| Patient | nusrat@gmail.com |

## MVC structure

| Layer | Folder | Job | Knows about |
|---|---|---|---|
| **Model** | `app/models/` | Data and database queries | The database only |
| **View** | `app/views/` | The HTML the user sees | The data handed to it |
| **Controller** | `app/controllers/` | Receives the request, asks the Model, picks the View | Both |

No SQL appears in a View. No HTML appears in a Model. A Controller contains
neither — it only coordinates.

### How a request flows

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

### Folder map

```
Merged Project/
├── index.php              front controller — every request enters here
├── .htaccess              pretty URLs (falls back to ?url= without mod_rewrite)
├── config/config.php      database settings, time slots
├── sql/
│   ├── schema.sql         table definitions
│   └── seed.sql           demo accounts and sample data
├── app/
│   ├── core/              Database, Model, Controller, Auth, Router
│   ├── models/            User, Doctor, Department, Appointment
│   ├── controllers/       Home, Auth, Patient, Doctor, Reception, Admin, Profile
│   └── views/
│       ├── layouts/       app (sidebar), auth (split), plain (public)
│       └── …              one folder per controller
└── public/css/style.css
```

## Database

Four tables. `users` holds every account with a `role` column; `doctors` holds
the doctor-only extras and links back to `users`; `departments` groups doctors;
`appointments` joins a patient to a doctor at a date and time slot.

```
departments 1 ──< doctors 1 ──< appointments >── 1 users (patient)
                     └── 1 users (doctor login)
```

## Security

- **SQL injection** — every query is a prepared statement with `bind_param`.
  `Database.php` builds the type string automatically, so no call site can get it wrong.
- **Passwords** — `password_hash()` and `password_verify()`. Never stored readable.
- **XSS** — `htmlspecialchars()` applied to input before it is stored or shown.
- **Session fixation** — `session_regenerate_id(true)` on login; cookies are HttpOnly, SameSite=Lax.
- **Access control** — `requireRole()` lives on the base `Controller`, so every
  controller inherits it. A patient reaching an admin URL is redirected to their own dashboard.

## Double booking

Booking checks the slot twice: once when drawing the page, and again on submit
before the `INSERT`. Two patients clicking the same slot cannot both get it.
