# Ecommerce BigSport

## Deskripsi

Project website e-commerce berbasis Laravel dengan desain minimalis, monokrom, dan fokus pada pengalaman belanja modern.

---

## Cara Install

1. Clone repository

```
git clone https://github.com/Wisnuazi000/ecommerce_bigsport.git
cd ecommerce_bigsport
```

2. Install dependency

```
composer install
npm install
```

3. Copy file environment

```
cp .env.example .env
```

4. Generate key

```
php artisan key:generate
```

5. Setup database

* Buat database: `ecommerce_bigsport`
* Sesuaikan `.env`

6. Migrasi database

```
php artisan migrate
```

7. Jalankan project

```
php artisan serve
```

---

## Struktur Fitur (Saat Ini)

* Homepage
* Product Listing
* Detail Product
* Cart
* Checkout
* Auth (Login/Register)

---

## Aturan Kolaborasi

* Jangan push langsung ke `main`
* Gunakan branch:

```
feature/nama-fitur
```

* Gunakan commit message jelas:

```
feat: add checkout page
fix: cart calculation bug
```

---

## Teknologi

* Laravel
* Bootstrap 5
* Blade Template
