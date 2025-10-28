# Aircraft REST API Documentation

A comprehensive REST API for managing aircraft data built with PHP and MySQL.

## 🚀 Overview

This API provides full CRUD (Create, Read, Update, Delete) operations for aircraft management with support for rich data relationships including aircraft types, engine types, and media attachments.

## 📋 Table of Contents

- [Quick Start](#quick-start)
- [API Endpoints](#api-endpoints)
- [Data Models](#data-models)
- [Request/Response Examples](#requestresponse-examples)
- [Error Handling](#error-handling)
- [Database Schema](#database-schema)
- [Installation](#installation)

## 🔧 Quick Start

**Base URL:** `http://localhost:8888/aircraft-api/aircrafts`

**Content Type:** `application/json`

**Supported Methods:** GET, POST, PUT, DELETE

## 🛠 API Endpoints

### 1. List All Aircraft
**GET** `/aircrafts`

Returns a list of all aircraft with basic information and navigation links.

**Response:**
```json
[
  {
    "id": 1,
    "model": "Boeing 747",
    "link": "http://localhost:8888/aircraft-api/aircrafts/?id=1"
  }
]
```

### 2. Get Specific Aircraft
**GET** `/aircrafts/{id}` or **GET** `/aircrafts/?id={id}`

Returns detailed information about a specific aircraft including related data.

**Response:**
```json
{
  "id": 1,
  "Model": "Boeing 747",
  "engine_amount": 4,
  "passenger_capacity": 416,
  "range_in_km": 13800,
  "airplane_type": "Wide-body",
  "engine_type": "Turbofan",
  "image_url": "https://example.com/boeing747.jpg"
}
```

### 3. Create New Aircraft
**POST** `/aircrafts`

Creates a new aircraft record.

**Request Body (form-data):**
```
model: "Airbus A380"
engine_amount: 4
passenger_capacity: 853
range_in_km: 15200
```

**Response:**
```json
{
  "message": "Aircraft created successfully"
}
```

### 4. Update Aircraft
**PUT** `/aircrafts/{id}`

Updates an existing aircraft record.

**Request Body (raw):**
```
model=Boeing 787&engine_amount=2&passenger_capacity=330&range_in_km=14800&media_id=1
```

**Response:**
```json
{
  "message": "Aircraft updated successfully"
}
```

### 5. Delete Aircraft
**DELETE** `/aircrafts/{id}`

Deletes an aircraft record.

**Response:**
```json
{
  "message": "Aircraft deleted successfully",
  "deleted_id": 1
}
```

## 📊 Data Models

### Aircraft Fields

| Field | Type | Description | Required |
|-------|------|-------------|----------|
| `id` | Integer | Unique identifier | Auto-generated |
| `Model` | String | Aircraft model name | Yes |
| `engine_amount` | Integer | Number of engines | Yes |
| `passenger_capacity` | Integer | Maximum passengers | Yes |
| `range_in_km` | Integer | Flight range in kilometers | Yes |
| `airplane_type_id` | Integer | Reference to airplane types | No |
| `engine_type_id` | Integer | Reference to engine types | No |
| `media_id` | Integer | Reference to media/images | No |

### Related Tables

**airplane_types:**
- `id` (Primary Key)
- `type_name` (e.g., "Wide-body", "Narrow-body")

**engine_types:**
- `id` (Primary Key) 
- `engine_name` (e.g., "Turbofan", "Turboprop")

**media:**
- `id` (Primary Key)
- `image_url` (Image URL)

## 🔍 Request/Response Examples

### Creating an Aircraft with Postman

1. **Method:** POST
2. **URL:** `http://localhost:8888/aircraft-api/aircrafts`
3. **Body:** Select "form-data"
4. **Add fields:**
   - `model`: Boeing 777
   - `engine_amount`: 2
   - `passenger_capacity`: 396
   - `range_in_km`: 17370

### Updating an Aircraft

1. **Method:** PUT
2. **URL:** `http://localhost:8888/aircraft-api/aircrafts/1`
3. **Body:** Select "raw"
4. **Content:** `model=Boeing 777-300ER&passenger_capacity=400`

### Deleting an Aircraft

1. **Method:** DELETE
2. **URL:** `http://localhost:8888/aircraft-api/aircrafts/1`
3. **Body:** Empty (ID comes from URL)

## ⚠️ Error Handling

The API returns appropriate HTTP status codes and error messages:

### Status Codes

- `200 OK` - Successful GET/PUT/DELETE
- `201 Created` - Successful POST
- `400 Bad Request` - Missing required parameters
- `404 Not Found` - Aircraft not found
- `500 Internal Server Error` - Database or server error

### Error Response Format

```json
{
  "error": "Error description"
}
```

### Common Errors

**400 Bad Request:**
```json
{
  "error": "Aircraft ID is required in URL path"
}
```

**404 Not Found:**
```json
{
  "error": "Aircraft not found"
}
```

## 🗄️ Database Schema

### Primary Table: `Aircrafts`

```sql
CREATE TABLE Aircrafts (
    id INT AUTO_INCREMENT PRIMARY KEY,
    Model VARCHAR(255) NOT NULL,
    engine_amount INT NOT NULL,
    passenger_capacity INT NOT NULL,
    range_in_km INT NOT NULL,
    airplane_type_id INT,
    engine_type_id INT,
    media_id INT,
    FOREIGN KEY (airplane_type_id) REFERENCES airplane_types(id),
    FOREIGN KEY (engine_type_id) REFERENCES engine_types(id),
    FOREIGN KEY (media_id) REFERENCES media(id)
);
```

## 📦 Installation

### Prerequisites

- PHP 7.4 or higher
- MySQL 5.7 or higher
- MAMP/XAMPP or similar local server environment

### Setup Steps

1. **Clone/Download** the project to your web server directory
   ```
   /Applications/MAMP/htdocs/AIRCRAFT-API/
   ```

2. **Database Setup:**
   - Create database: `aircraft_api`
   - Import schema from `aircraft_table.sql`

3. **Configuration:**
   - Update database credentials in `db.php`
   ```php
   $host = "localhost";
   $username = "root";
   $password = "root";
   $database = "aircraft_api";
   ```

4. **Start Server:**
   - Start MAMP/XAMPP
   - Access API at: `http://localhost:8888/aircraft-api/aircrafts`

## 🧪 Testing

### Using Postman

1. Import the provided test collection
2. Set base URL: `http://localhost:8888/aircraft-api/aircrafts`
3. Test all endpoints with sample data

### Using Browser

- **GET requests:** Visit URLs directly in browser
- **Full testing:** Use the provided HTML test file

## 🔗 URL Formats

The API supports both URL formats for backward compatibility:

- **REST style:** `/aircrafts/1`
- **Query parameter:** `/aircrafts/?id=1`

Both formats work for GET, PUT, and DELETE operations.

## 🚦 HTTP Methods Summary

| Method | Endpoint | Action | Body Required |
|--------|----------|--------|---------------|
| GET | `/aircrafts` | List all | No |
| GET | `/aircrafts/{id}` | Get specific | No |
| POST | `/aircrafts` | Create new | Yes (form-data) |
| PUT | `/aircrafts/{id}` | Update existing | Yes (raw) |
| DELETE | `/aircrafts/{id}` | Delete | No |

## 🔒 Security Notes

- Input validation is performed on all parameters
- SQL injection protection via prepared statements
- Proper HTTP status codes for all responses
- JSON-encoded responses for security

## 📝 Notes

- All responses are in JSON format with UTF-8 encoding
- Timestamps and created/modified dates can be added for audit trails
- The API can be extended with authentication and authorization
- Pagination can be implemented for large datasets

## 🤝 Contributing

1. Fork the repository
2. Create your feature branch
3. Commit your changes
4. Push to the branch
5. Create a Pull Request

---

**Built with ❤️ using PHP, MySQL, and REST principles**