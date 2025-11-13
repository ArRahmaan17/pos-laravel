# API Authentication Documentation

This document describes the API authentication endpoints using Laravel Sanctum.

## Base URL
All API endpoints are prefixed with `/api`

## Authentication Flow

### 1. Login
**POST** `/api/login`

Request body:
```json
{
    "username": "user@example.com",
    "password": "password123"
}
```

Response:
```json
{
    "message": "Login successful",
    "token": "1|abc123...",
    "user": {
        "user": {
            "id": 1,
            "name": "John Doe",
            "username": "johndoe",
            "email": "user@example.com"
        },
        "role": {
            "id": 1,
            "name": "Manager"
        },
        "user_id": 1,
        "company": {
            "id": 1,
            "name": "Company Name",
            "address": {
                "place": "Building",
                "address": "Street Address",
                "city": "City",
                "province": "Province",
                "zip_code": "12345"
            }
        }
    },
    "token_type": "Bearer"
}
```

### 2. Using the Token
Include the token in the Authorization header for all protected endpoints:
```
Authorization: Bearer 1|abc123...
```

## Protected Endpoints

### Get Current User
**GET** `/api/me`

Returns the current authenticated user's information.

### Logout
**POST** `/api/logout`

Revokes the current token.

### Logout All Devices
**POST** `/api/logout-all`

Revokes all tokens for the current user.

### Select Company
**POST** `/api/select-company`

Request body:
```json
{
    "company_id": 1
}
```

### Login As Another User
**POST** `/api/login-as/{user_id}`

Request body:
```json
{
    "company_id": 1
}
```

Returns a new token for the target user.

### Change Password
**POST** `/api/change-password`

Request body:
```json
{
    "current_password": "oldpassword",
    "new_password": "newpassword123",
    "confirm_password": "newpassword123"
}
```

### Activate Access Pin
**POST** `/api/activate-access-pin`

Request body:
```json
{
    "current_access_pin": "123456",
    "access_pin": "654321",
    "confirm_access_pin": "654321"
}
```

### Unlock Screen
**POST** `/api/unlock-screen`

Request body:
```json
{
    "access_pin": "123456"
}
```

### Get Customer Companies
**GET** `/api/customer-companies`

Returns list of companies associated with the current user.

## Public Endpoints

### Register
**POST** `/api/register`

Request body:
```json
{
    "user": {
        "name": "John Doe",
        "username": "johndoe",
        "email": "user@example.com",
        "phone_number": "08123456789",
        "password": "password123"
    },
    "company": {
        "name": "Company Name",
        "email": "company@example.com",
        "phone_number": "08123456789"
    },
    "address": {
        "place": "Building",
        "address": "Street Address",
        "city": "City",
        "province": "Province",
        "zip_code": "12345"
    }
}
```

### Check Available User
**GET** `/api/check-available-user`

Query parameters:
- `name`
- `username`
- `email`
- `phone_number`

### Get Company Types
**GET** `/api/company-types`

### Check Company Availability
**GET** `/api/check-company-availability`

Query parameters:
- `name`
- `email`
- `phone_number`
- `bussiness_id`

## Error Responses

All endpoints return appropriate HTTP status codes:

- `200` - Success
- `201` - Created (for registration)
- `400` - Bad Request
- `401` - Unauthorized
- `403` - Forbidden
- `404` - Not Found
- `422` - Validation Error
- `500` - Server Error

Error response format:
```json
{
    "message": "Error description",
    "errors": {
        "field": ["Error message"]
    }
}
```

## Rate Limiting

All endpoints are rate limited to 100 requests per minute per IP address.
