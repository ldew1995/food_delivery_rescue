# 🥡 Food Rescue API – PHP & MySQLi

This is a **high-performance, scalable API** built using **PHP MySQLi-Database-Class** to support the "Food Rescue" feature for a food delivery system. It enables **customers within a 3 km radius** to purchase **canceled orders** at a **discounted price**.

---

## 📌 Features

✅ **Cancel Orders & List for Rescue**  
✅ **Find Nearby Canceled Orders (Haversine Formula)**  
✅ **Claim Orders & Secure Payment Processing**  
✅ **Optimized for 1M+ Orders Daily**  
✅ **Caching with Redis for Speed**  
✅ **Secure API Key Authentication**  

---

## 🚀 Installation & Setup

### **1️ Clone the Repository**
```bash
git clone https://github.com/ldew1995/food_delivery_rescue
cd food-rescue-api

2️ Install Dependencies
A. Install PHP & MySQL
Ensure you have PHP 7.4+ and MySQL 5.7+ installed.

📂 Project Structure
📂 food-delivery-rescue
├── 📁 api_doc             # API Endpoints Documenation
│   ├── docs
├── 📁 config          # Configurations
│   ├── config.php
├── 📁 includes          # connections and predefined function
│   ├── db.php
│   ├── function.php
│   ├── redis.php
├── 📁 routes             # API Endpoints
│   ├── cancel_order.php
│   ├── get_cancelled_orders.php
│   ├── claim_order.php
├── 📁 sql             # Database Schema & Dummy Data
│   ├── food_delivery_rescue.sql
├── 📁 storage             # to store logs and uploaded files
│   ├── logs
├── 📁 vendor          # Configurations
│   ├── MysqliDB
│   ├── predis
├── 📁 public          # Second entry point
│   ├── index.php
├── .htaccess          # First app route entry point
├── README.md          # Project Documentation

3. CREATE DATABASE food_rescue_db;
USE food_rescue_db;

4. API Authentication     // note: given schema has already provided seed data and generated api_key
Generate an API key and insert it into the api_keys table.
Include the api_key in every request.   

5. Please make sure Use cloud redis - https://cloud.redis.io/, provide credentials to continue

6. Define all the config details within config/config.php files 

7. Running the API Locally
    a. Start the PHP built-in server:

    php -S localhost:8000
    b. API will be accessible at:
    
    http://localhost:8000/

