# 🚀 Laravel URL Shortener API

[![PHP Version](https://img.shields.io/badge/PHP-8.2-blue.svg)](https://www.php.net/)  
[![Laravel Version](https://img.shields.io/badge/Laravel-12-orange.svg)](https://laravel.com/)  
[![License](https://img.shields.io/badge/License-MIT-green.svg)](LICENSE)  
[![Tests](https://img.shields.io/badge/Tests-Passing-brightgreen.svg)](https://github.com/mortezash/laravel-url-shortener)

یک پروژه RESTful برای کوتاه کردن لینک‌ها با قابلیت‌های زیر:
- کوتاه کردن لینک‌ها
- ریدایرکت به لینک اصلی
- مشاهده لینک‌ها با Pagination
- شمارش تعداد کلیک‌ها
- حذف نرم (Soft Delete) و بازگردانی

---

## 🔧 پیش‌نیازها

- PHP >= 8.x
- Composer
- MySQL
- Laravel 12
- (اختیاری) Docker برای محیط توسعه

---

## ⚡ نصب پروژه

1. کلون کردن ریپازیتوری:

```bash
git clone https://github.com/mortezash/laravel-url-shortener.git
cd laravel-url-shortener
```

2. نصب وابستگی‌ها:
```bash
composer install
```

3. کپی کردن فایل .env.example و تنظیمات محیط:
```bash
cp .env.example .env
```
مقادیر دیتابیس را بر اساس محیط خود تغییر دهید:
DB_HOST, DB_PORT, DB_DATABASE, DB_USERNAME, DB_PASSWORD



4. ایجاد کلید اپلیکیشن:
```bash
php artisan key:generate
```

5. اجرای مایگریشن‌ها:
```bash
php artisan migrate
```

اجرا در محیط توسعه
```bash
php artisan serve
```
پروژه در http://localhost:8000 در دسترس است.

##مستندات API (Swagger)

1. تولید مستندات Swagger:
```bash
php artisan l5-swagger:generate
```

2. مشاهده مستندات در مرورگر:
```bash
http://localhost:8000/api/documentation
```
تمام مسیرهای API با نمونه Request و Response قابل مشاهده است.

##تست پروژه

1. اجرای تست‌ها
```bash
php artisan test
```