# SignUpGO Registration API Documentation

## Base URL
```
http://your-domain.com/api
```

All API endpoints are publicly accessible without authentication.

---

## Endpoints

### 1. Get All Registrations (with filters)

Get a paginated list of registrations with optional filtering.

**Endpoint:** `GET /registrations`

**Query Parameters:**

| Parameter | Type | Required | Description |
|-----------|------|----------|-------------|
| `event_id` | integer | No | Filter by specific event ID |
| `role` | string | No | Filter by role: `participant`, `reviewer`, or `jury` |
| `status` | string | No | Filter by status: `pending`, `approved`, or `rejected` |
| `from_date` | date | No | Filter registrations from this date (YYYY-MM-DD) |
| `to_date` | date | No | Filter registrations until this date (YYYY-MM-DD) |
| `per_page` | integer | No | Results per page (default: 50, max: 100) |

**Example Requests:**
```bash
# Get all approved participants for event ID 1
curl "http://your-domain.com/api/registrations?event_id=1&role=participant&status=approved"

# Get registrations from a specific date range
curl "http://your-domain.com/api/registrations?from_date=2025-01-01&to_date=2025-12-31"

# Get first 20 registrations
curl "http://your-domain.com/api/registrations?per_page=20"
```

**Response:**
```json
{
  "success": true,
  "data": [
    {
      "id": 1,
      "event": {
        "id": 1,
        "title": "International Conference 2025"
      },
      "user": {
        "id": 5,
        "name": "John Doe",
        "email": "john@example.com"
      },
      "role": "participant",
      "status": "approved",
      "phone": "+60123456789",
      "organization": "University of XYZ",
      "emergency_contact": {
        "name": "Jane Doe",
        "phone": "+60123456788"
      },
      "application_notes": "Looking forward to attending",
      "admin_notes": null,
      "approved_at": "2025-11-18T10:30:00+00:00",
      "registered_at": "2025-11-18T10:00:00+00:00"
    }
  ],
  "pagination": {
    "current_page": 1,
    "per_page": 50,
    "total": 100,
    "last_page": 2
  }
}
```

---

### 2. Get Event Registrations

Get all registrations for a specific event.

**Endpoint:** `GET /events/{eventId}/registrations`

**Path Parameters:**

| Parameter | Type | Required | Description |
|-----------|------|----------|-------------|
| `eventId` | integer | Yes | The ID of the event |

**Example Request:**
```bash
curl "http://your-domain.com/api/events/1/registrations"
```

**Response:**
```json
{
  "success": true,
  "data": {
    "event_id": 1,
    "event_title": "International Conference 2025",
    "total_registrations": 45,
    "registrations": [
      {
        "id": 1,
        "event": {
          "id": 1,
          "title": "International Conference 2025"
        },
        "user": {
          "id": 5,
          "name": "John Doe",
          "email": "john@example.com"
        },
        "role": "participant",
        "status": "approved",
        "phone": "+60123456789",
        "organization": "University of XYZ",
        "emergency_contact": {
          "name": "Jane Doe",
          "phone": "+60123456788"
        },
        "application_notes": null,
        "admin_notes": null,
        "approved_at": "2025-11-18T10:30:00+00:00",
        "registered_at": "2025-11-18T10:00:00+00:00"
      }
    ]
  }
}
```

---

### 3. Get Registration Statistics

Get statistical summary of registrations.

**Endpoints:** 
- `GET /registrations/stats` - Statistics for all registrations
- `GET /events/{eventId}/registrations/stats` - Statistics for specific event

**Example Requests:**
```bash
# Get stats for all registrations
curl "http://your-domain.com/api/registrations/stats"

# Get stats for specific event
curl "http://your-domain.com/api/events/1/registrations/stats"
```

**Response:**
```json
{
  "success": true,
  "data": {
    "event": {
      "id": 1,
      "title": "International Conference 2025"
    },
    "total_registrations": 45,
    "by_status": {
      "approved": 42,
      "pending": 2,
      "rejected": 1
    },
    "by_role": {
      "participant": 38,
      "reviewer": 5,
      "jury": 2
    }
  }
}
```

---

## Data Fields

### Registration Object

| Field | Type | Description |
|-------|------|-------------|
| `id` | integer | Unique registration ID |
| `event.id` | integer | Event ID |
| `event.title` | string | Event name |
| `user.id` | integer | User ID |
| `user.name` | string | User's full name |
| `user.email` | string | User's email address |
| `role` | string | Role: `participant`, `reviewer`, or `jury` |
| `status` | string | Status: `pending`, `approved`, or `rejected` |
| `phone` | string | Contact phone number |
| `organization` | string | User's organization/institution |
| `emergency_contact.name` | string | Emergency contact person |
| `emergency_contact.phone` | string | Emergency contact phone |
| `application_notes` | string | Notes from user |
| `admin_notes` | string | Notes from admin |
| `approved_at` | string | ISO 8601 datetime when approved |
| `registered_at` | string | ISO 8601 datetime when registered |

---

## Error Responses

### 404 Not Found
```json
{
  "success": false,
  "message": "Event not found"
}
```

### 422 Validation Error
```json
{
  "success": false,
  "message": "Validation failed",
  "errors": {
    "event_id": ["The selected event id is invalid."],
    "role": ["The selected role is invalid."]
  }
}
```

---

## Integration Examples

### PHP (cURL)
```php
<?php

$eventId = 1;
$url = "http://your-domain.com/api/events/{$eventId}/registrations";

$ch = curl_init();
curl_setopt($ch, CURLOPT_URL, $url);
curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
curl_setopt($ch, CURLOPT_HTTPHEADER, [
    'Accept: application/json',
]);

$response = curl_exec($ch);
$httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
curl_close($ch);

if ($httpCode === 200) {
    $data = json_decode($response, true);
    
    if ($data['success']) {
        foreach ($data['data']['registrations'] as $registration) {
            echo "Name: " . $registration['user']['name'] . "\n";
            echo "Email: " . $registration['user']['email'] . "\n";
            echo "Phone: " . $registration['phone'] . "\n";
            echo "Organization: " . $registration['organization'] . "\n";
            echo "Role: " . $registration['role'] . "\n";
            echo "Status: " . $registration['status'] . "\n";
            echo "Emergency Contact: " . $registration['emergency_contact']['name'] . 
                 " (" . $registration['emergency_contact']['phone'] . ")\n\n";
        }
    }
}
```

### Python (requests)
```python
import requests

event_id = 1
url = f"http://your-domain.com/api/events/{event_id}/registrations"

response = requests.get(url)
data = response.json()

if data['success']:
    print(f"Total Registrations: {data['data']['total_registrations']}\n")
    
    for registration in data['data']['registrations']:
        print(f"Name: {registration['user']['name']}")
        print(f"Email: {registration['user']['email']}")
        print(f"Phone: {registration['phone']}")
        print(f"Organization: {registration['organization']}")
        print(f"Role: {registration['role']}")
        print(f"Status: {registration['status']}")
        print(f"Emergency Contact: {registration['emergency_contact']['name']} "
              f"({registration['emergency_contact']['phone']})\n")
```

### JavaScript (fetch)
```javascript
const eventId = 1;
const url = `http://your-domain.com/api/events/${eventId}/registrations`;

fetch(url)
  .then(response => response.json())
  .then(data => {
    if (data.success) {
      console.log(`Total Registrations: ${data.data.total_registrations}\n`);
      
      data.data.registrations.forEach(registration => {
        console.log(`Name: ${registration.user.name}`);
        console.log(`Email: ${registration.user.email}`);
        console.log(`Phone: ${registration.phone}`);
        console.log(`Organization: ${registration.organization}`);
        console.log(`Role: ${registration.role}`);
        console.log(`Status: ${registration.status}`);
        console.log(`Emergency Contact: ${registration.emergency_contact.name} ` +
                   `(${registration.emergency_contact.phone})\n`);
      });
    }
  })
  .catch(error => console.error('Error:', error));
```

### Node.js (axios)
```javascript
const axios = require('axios');

async function getRegistrations(eventId) {
  try {
    const response = await axios.get(
      `http://your-domain.com/api/events/${eventId}/registrations`
    );
    
    if (response.data.success) {
      const { registrations, total_registrations } = response.data.data;
      
      console.log(`Total Registrations: ${total_registrations}\n`);
      
      registrations.forEach(reg => {
        console.log(`Name: ${reg.user.name}`);
        console.log(`Email: ${reg.user.email}`);
        console.log(`Phone: ${reg.phone}`);
        console.log(`Organization: ${reg.organization}`);
        console.log(`Role: ${reg.role}`);
        console.log(`Status: ${reg.status}`);
        console.log(`Emergency: ${reg.emergency_contact.name} ` +
                   `(${reg.emergency_contact.phone})\n`);
      });
    }
  } catch (error) {
    console.error('Error:', error.message);
  }
}

getRegistrations(1);
```

---

## Database Direct Access

If your friend prefers direct database access, they can query the `event_registrations` table:

### Table: `event_registrations`

| Column | Type | Description |
|--------|------|-------------|
| `id` | bigint | Primary key |
| `user_id` | bigint | Foreign key to users table |
| `event_id` | bigint | Foreign key to events table |
| `role` | string | participant, reviewer, or jury |
| `status` | string | pending, approved, or rejected |
| `phone` | string | Contact phone number |
| `organization` | string | Organization/institution |
| `emergency_contact_name` | string | Emergency contact person |
| `emergency_contact_phone` | string | Emergency contact phone |
| `certificate_path` | string | Path to jury certificate (if applicable) |
| `certificate_filename` | string | Original certificate filename |
| `application_notes` | text | Notes from applicant |
| `admin_notes` | text | Notes from administrator |
| `approved_at` | timestamp | When registration was approved |
| `approved_by` | bigint | User ID who approved |
| `rejected_at` | timestamp | When registration was rejected |
| `rejected_reason` | text | Reason for rejection |
| `created_at` | timestamp | Registration timestamp |
| `updated_at` | timestamp | Last update timestamp |

### Example SQL Query
```sql
SELECT 
    er.id,
    u.name as user_name,
    u.email as user_email,
    e.title as event_title,
    er.role,
    er.status,
    er.phone,
    er.organization,
    er.emergency_contact_name,
    er.emergency_contact_phone,
    er.application_notes,
    er.approved_at,
    er.created_at
FROM event_registrations er
JOIN users u ON er.user_id = u.id
JOIN events e ON er.event_id = e.id
WHERE er.event_id = 1
  AND er.status = 'approved'
ORDER BY er.created_at DESC;
```

---

## Rate Limiting & Best Practices

1. **No Authentication Required**: These endpoints are public for easy integration
2. **CORS**: Configure if accessing from different domain
3. **Caching**: Consider caching responses for frequently accessed data
4. **Pagination**: Use `per_page` parameter to control response size
5. **Filtering**: Always use filters to reduce payload size
6. **Error Handling**: Always check the `success` field in responses

---

## Support

For questions or issues with the API, contact the SignUpGO development team.
