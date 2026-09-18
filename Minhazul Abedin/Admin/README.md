# Admin Module — DoctorConnect

Minhazul Abedin · 21-44625-1 · Group 06, Section F

The administrator part of the group project. The admin manages the people and
structure of the hospital: doctors and departments.

## Features

| Feature | File |
|---|---|
| Overview dashboard — appointment totals by status, per-department stats, recent bookings | `PHP/admin_dashboard.php` |
| Manage doctors — add, edit, delete, with referential checks | `PHP/manage_doctors.php` |
| Manage departments — add, rename, delete | `PHP/manage_departments.php` |

## Folder layout

```
Admin/
├── HTML/    static renders of each admin screen
├── CSS/     admin.css and the web fonts
└── PHP/     the working pages, plus the shared files they depend on
```

`PHP/` includes `db.php`, `auth.php`, `header.php`, `footer.php` and `icons.php`
because the admin pages include them — without those the pages cannot run.

## How to run

1. Copy `PHP/` into your XAMPP web root (`htdocs`).
2. Start Apache and MySQL.
3. Open `login.php`. The database and its tables are created automatically on
   first load by `db.php`.
4. Log in as **admin@doctorconnect.com** with password **1234**.

## Things worth pointing out

**Referential integrity is enforced.** A department that still has doctors cannot
be deleted, and a doctor who still has appointments cannot be deleted. Both are
checked with a `COUNT(*)` query before the `DELETE` runs, so the database is never
left with orphaned rows.

**Adding a doctor writes to two tables.** A row goes into `users` (for the login)
and a linked row into `doctors` (for the fee, room, specialization and department).
The default password for a newly added doctor is `1234`.

**Every query uses a prepared statement.** `mysqli_prepare` with `bind_param`,
so no user input is ever concatenated into SQL.

**Access is role-guarded.** Each page calls `require_role("admin")` at the top.
Any other role is redirected to their own dashboard.
