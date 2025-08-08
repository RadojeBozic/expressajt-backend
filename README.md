# Agencija Express web – Backend (Laravel 10)

Ovo je backend aplikacija za projekat **Agencija Express Sajt**, izrađena u Laravel PHP frameworku.

Služi kao API server za frontend deo (`expressajt`) i obezbeđuje:
- autentifikaciju,
- validaciju i obradu podataka,
- čuvanje podataka u bazi,
- upload fajlova,
- pristup admin funkcijama.

---

## 📦 Tehnologije

- Laravel 10
- PHP 8.2+
- MySQL
- Laravel Sanctum (za autentifikaciju)
- File Storage (za slike i dokumenta)

---

## 🛠️ Instalacija


- composer install
- cp .env.example .env
- php artisan key:generate
- php artisan migrate
- php artisan storage:link
Prethodno kreiraj MySQL bazu npr: expressajt
Zatim u .env podesi konekciju sa bazom:


DB_DATABASE=expressajt
DB_USERNAME=root
DB_PASSWORD=
🚀 Pokretanje lokalnog servera

php artisan serve --port=8080
Server će biti dostupan na: http://localhost:8080

✅ Preporuka: koristi i php artisan storage:link za učitavanje slika

📁 Struktura API ruta
Sve rute su u fajlu: routes/api.php
Dostupne funkcionalnosti uključuju:

POST /api/register – Registracija korisnika

POST /api/login – Prijava korisnika

POST /api/contact – Slanje poruke sa kontakt forme

GET /api/site-request/{slug} – Pregled prezentacije

POST /api/free-site-request – Slanje zahteva za sajt

PUT /api/free-site-request/{slug} – Ažuriranje

DELETE /api/free-site-request/{slug} – Brisanje

GET /api/free-site-request/all-site-requests – Lista svih prezentacija (admin)

Admin rute (sa Sanctum tokenom):

GET /api/users – Lista korisnika

PATCH /api/users/{id}/role – Promena uloge

GET /api/messages – Sve poruke

GET /api/vulnerabilities – Prijave ranjivosti

🧠 Validacija i sigurnost
Koristi se Laravel FormRequest za validaciju podataka.

API koristi Laravel Sanctum za autentifikaciju korisnika i zaštitu admin ruta.

Fajlovi se čuvaju u storage/app/public i dostupni su preko public/storage.

📦 Seed / Test podaci (opciono)
Za testiranje možeš napraviti factory-je i seedere za korisnike i prezentacije.

php artisan db:seed
🧾 Licence 
Ovaj repozitorijum je deo vlasničkog projekta Agencija Express web.
Slobodno koristiš kao inspiraciju za edukaciju, ali za produkcijsku upotrebu obavezna je dozvola vlasnika.

📌 Kontakt
Agencija Express web
📧 info@express-web.express
🌐 https://express-web.express (u pripremi)