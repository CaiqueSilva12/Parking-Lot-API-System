# Parking Lot API System

## Overview
This project is a Laravel-based API for automated parking lot management. It supports vehicle parking, unparking, and real-time capacity monitoring, applying business and spacing rules for weekdays and weekends. The API is designed for integration with automated camera systems and real-time display screens.

## Features
- Park a vehicle with spacing rules (weekday/weekend)
- Unpark a vehicle
- Get available parking spots in real time
- Input validation and business rule enforcement
- PostgreSQL database support

## Requirements
- PHP 8.1+
- Composer
- PostgreSQL
- (Optional) Postman or similar API client for testing

## Setup Instructions

1. **Clone the repository**
   ```sh
   git clone <your-repo-url>
   cd Parking-Lot-API-System
   ```

2. **Install dependencies**
   ```sh
   composer install
   ```

3. **Configure environment**
   - Copy `.env.example` to `.env` and update database settings for PostgreSQL:
     ```env
     DB_CONNECTION=pgsql
     DB_HOST=127.0.0.1
     DB_PORT=5432
     DB_DATABASE=parking_lot
     DB_USERNAME=your_postgres_user
     DB_PASSWORD=your_postgres_password
     ```

4. **Generate application key**
   ```sh
   php artisan key:generate
   ```

5. **Create the database**
   - In PostgreSQL, create a database named `parking_lot`.

6. **Run migrations**
   ```sh
   php artisan migrate
   ```

7. **Start the server**
   ```sh
   php artisan serve
   ```
   The API will be available at `http://localhost:8000/api`.

## API Endpoints

### 1. Park Vehicle
- **POST** `/api/park`
- **Body (JSON):**
  ```json
  {
    "license_plate": "ABC1234",
    "timestamp": "2025-07-12T10:00:00Z"
  }
  ```
- **Response:**
  - `201 Created` on success
  - `409 Conflict` if already parked or no space
  - `422 Unprocessable Entity` for validation errors

### 2. Unpark Vehicle
- **POST** `/api/unpark`
- **Body (JSON):**
  ```json
  {
    "license_plate": "ABC1234",
    "timestamp": "2025-07-12T12:00:00Z"
  }
  ```
- **Response:**
  - `200 OK` on success
  - `404 Not Found` if vehicle not found
  - `422 Unprocessable Entity` for validation errors

### 3. Get Available Spots
- **GET** `/api/available-spots`
- **Response:**
  ```json
  {
    "available_spots": 97
  }
  ```

## Testing the Endpoints

You can use [Postman](https://www.postman.com/) or `curl` to test the API:

### Example: Park a Vehicle (Saturday)
```sh
curl -X POST http://localhost:8000/api/park \
  -H "Content-Type: application/json" \
  -d '{"license_plate": "SAT1234", "timestamp": "2025-07-12T10:00:00Z"}'
```

### Example: Unpark a Vehicle
```sh
curl -X POST http://localhost:8000/api/unpark \
  -H "Content-Type: application/json" \
  -d '{"license_plate": "SAT1234", "timestamp": "2025-07-12T12:00:00Z"}'
```

### Example: Get Available Spots
```sh
curl http://localhost:8000/api/available-spots
```

## Notes
- All timestamps must be in UTC (ISO 8601 format recommended).
- Spacing rules are applied automatically based on the day of the week.
- The system enforces all business rules as described in the project requirements.

---

Feel free to reach out if you have any questions or need further assistance.
