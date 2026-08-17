# EWU Course Registration & Advising System — Backend API

Plain PHP + PDO + MySQL (MariaDB) দিয়ে বানানো REST API। কোনো framework নেই।

## Folder structure

```
ewu-course-backend/
├── index.php                  # front controller — সব request এখান দিয়ে যায়
├── routes.php                 # API route list + role restriction
├── .htaccess                  # Apache-তে pretty URL
├── config/
│   └── database.php           # DB credentials (এডিট করবে)
├── app/
│   ├── autoload.php           # ছোট্ট autoloader
│   ├── helpers.php            # JSON response/request helpers
│   ├── Database.php           # PDO connection (singleton)
│   ├── Router.php             # URL router
│   ├── Auth.php               # token-based login/auth check
│   ├── Model.php              # generic CRUD base model
│   ├── Controller.php         # generic controller + validation
│   ├── Models/                # প্রতিটা таблицы একটা class
│   │   └── (Department, Faculty, Student, Advisor, Course, Prerequisite,
│   │        Section, Semester, Classroom, Registration, Grade, Approval)
│   └── Controllers/           # API logic (এখানে business rules)
│       └── (Auth, Health, Student, Department, Faculty, Advisor, Course,
│            Prerequisite, Section, Semester, Classroom,
│            Registration, Approval, Grade)Controller.php
└── db/
    ├── alter_login.sql         # login: student/faculty তে Password + auth_tokens table
    └── seed.php                # সব user-এর default password (123456)
```

## Setup (step by step)

1. **Database import** — phpMyAdmin-এ যাওয়ার আগে তুমার দেওয়া
   `ewu_course_registration(ayesha).sql` import করো।

2. **Login migration** — একই phpMyAdmin-এ `db/alter_login.sql` run করো
   (Password column + auth_tokens table তৈরি হয়)।

3. **DB credential** — `config/database.php` এ username/password/disen
   আপডেট করো (XAMPP default: `root` + খালি password)।

4. **Default password সেট** — project root থেকে:
   ```
   php db/seed.php
   ```
   সব student ও faculty-র password হয়ে যাবে `123456`।

5. **Server চালাও** — যেভাবে ইচ্ছা:
   - XAMPP: folder-টা `htdocs/` এ copy করো, তারপর
     `http://localhost/ewu-course-backend/api/...`
   - অথবা PHP built-in server:
     ```
     php -S localhost:8000 index.php
     ```
     তারপর `http://localhost:8000/api/...`

## Login information

Login `role` field দিয়ে বলে দাও `student` নাকি `faculty` (advisor-ও faculty account দিয়ে login করে)।

| Role    | Email                     | Password |
|---------|---------------------------|----------|
| student | ayesha1001@ewu.edu.bd     | 123456   |
| student | nafis1002@ewu.edu.bd      | 123456   |
| advisor | rahman@ewu.edu.bd         | 123456   |
| advisor | karim@ewu.edu.bd          | 123456   |
| faculty | hasan@ewu.edu.bd          | 123456   |

Login response এ একটা `token` আসবে। বাকি request-এ header দিতে হবে:

```
Authorization: Bearer <token>
```

## Main API endpoints

**Auth**
- `POST /api/auth/login` — body: `{ "email": "...", "password": "...", "role": "student|faculty" }`
- `POST /api/auth/logout` — token-টা block করে দেয়
- `GET  /api/auth/me` — বর্তমান user

**Lookup (সব logged-in user)**
- `GET /api/courses` · `GET /api/courses/{id}`
- `GET /api/sections` (+ query: `?course=&semester=&faculty=&room=`)
- `GET /api/students` · `GET /api/departments` · `GET /api/faculty`
- `GET /api/advisors` · `GET /api/semesters` · `GET /api/classrooms` · `GET /api/prerequisites`

**Registration**
- `POST /api/registrations` — body: `{ "section_id": 10001 }`
- `GET  /api/registrations` — student নিজেরটা, advisor তার advisee-দেরটা
- `POST /api/registrations/{id}/drop`

**Advising**
- `GET /api/approvals` — advisor তার advisee-দের রেজিস্ট্রেশন + approval status
- `PUT /api/approvals/{registrationId}` — body: `{ "status": "Approved", "remarks": "" }`

**Grades**
- `GET /api/grades` — student নিজের, faculty সব (query: `?student=`)
- `POST|PUT|DELETE /api/grades` — শুধু faculty

**Admin CRUD** — `POST/PUT/DELETE` সব table-এর জন্য আছে, কিন্তু শুধু staff (`faculty`/`advisor`) role দিতে পারে।

## Business rules (Registration এর মধ্যে)

1. **Time window** — শুধু semester-এর `RegistrationStart`–`RegistrationEnd`-এর মধ্যে register করা যায়।
2. **Duplicate block** — একই section আর একবার register করা যায় না।
3. **Capacity check** — classroom-এর capacity-এর বেশি student নেওয়া যায় না।
4. **Schedule conflict** — একই দিনে/সময়ে (ScheduleDay + StartTime–EndTime) আরেকটা active class থাকলে register করা যায় না।
5. **Prerequisite check** — prereq কোর্সে অন্তত 2.00 CGPA (C) না থাকলে block।
6. **Auto approval** — register করলেই student-এর advisor-এর জন্য `Pending` approval তৈরি হয়; advisor পরে approve/reject করে।

## Quick test (curl)

```bash
# login
curl -X POST http://localhost:8000/api/auth/login \
  -H "Content-Type: application/json" \
  -d '{"email":"ayesha1001@ewu.edu.bd","password":"123456","role":"student"}'

# courses list (token লাগে)
curl http://localhost:8000/api/courses \
  -H "Authorization: Bearer <token>"
```