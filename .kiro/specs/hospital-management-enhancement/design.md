# Design Document

## Overview

This design document outlines the technical approach for enhancing the existing Hospital Management System (HMS) built in PHP with MySQL. The enhancements focus on security hardening, input validation, error handling, and adding essential features like edit functionality, search/filter capabilities, CSRF protection, appointment conflict detection, pagination, and improved session management.

The system follows a traditional server-side rendered architecture using PHP with MySQL database. The design maintains the existing structure while introducing new utility classes and functions to handle validation, security, and data operations more robustly.

## Architecture

### Current Architecture
- **Presentation Layer**: PHP files with embedded HTML (patients.php, doctors.php, appointments.php, dashboard.php)
- **Business Logic**: Inline PHP code within presentation files
- **Data Access**: Direct mysqli queries and prepared statements
- **Session Management**: PHP sessions via config/database.php

### Enhanced Architecture
The enhanced system will introduce:
- **Validation Layer**: Centralized input validation and sanitization utilities
- **Security Layer**: CSRF token management, session security, SQL injection prevention
- **Service Layer**: Business logic for appointments, patients, doctors
- **Error Handling**: Centralized error logging and user-friendly error messages

### Directory Structure
```
/
├── config/
│   ├── database.php (enhanced with secure session config)
│   └── security.php (new - CSRF and security utilities)
├── includes/
│   ├── header.php
│   ├── footer.php
│   └── validators.php (new - input validation functions)
├── services/
│   ├── AppointmentService.php (new - appointment business logic)
│   ├── PatientService.php (new - patient operations)
│   └── DoctorService.php (new - doctor operations)
├── utils/
│   ├── ErrorHandler.php (new - error logging and display)
│   └── Pagination.php (new - pagination utility)
├── [existing PHP files - enhanced]
```

## Components and Interfaces

### 1. Validation Component (`includes/validators.php`)

**Purpose**: Centralized input validation and sanitization

**Functions**:
```php
function validateName(string $name): array
function validateAge(int $age): array
function validatePhone(string $phone): array
function validateEmail(string $email): array
function sanitizeInput(string $input): string
function validateDate(string $date): array
function validateTime(string $time): array
```

**Return Format**: `['valid' => bool, 'error' => string|null]`

### 2. Security Component (`config/security.php`)

**Purpose**: CSRF protection and session security

**Functions**:
```php
function generateCSRFToken(): string
function validateCSRFToken(string $token): bool
function getCSRFTokenField(): string
function configureSecureSession(): void
function regenerateSessionId(): void
function checkSessionTimeout(): bool
```

### 3. Service Classes

#### AppointmentService (`services/AppointmentService.php`)
```php
class AppointmentService {
    public function createAppointment(array $data): array
    public function updateAppointment(int $id, array $data): array
    public function deleteAppointment(int $id): array
    public function getAppointment(int $id): ?array
    public function checkConflict(int $doctorId, string $date, string $time, ?int $excludeId): bool
    public function getAvailableSlots(int $doctorId, string $date): array
    public function getAppointments(int $page, int $perPage, array $filters): array
}
```

#### PatientService (`services/PatientService.php`)
```php
class PatientService {
    public function createPatient(array $data): array
    public function updatePatient(int $id, array $data): array
    public function deletePatient(int $id): array
    public function getPatient(int $id): ?array
    public function searchPatients(string $query, int $page, int $perPage): array
    public function hasAppointments(int $patientId): bool
}
```

#### DoctorService (`services/DoctorService.php`)
```php
class DoctorService {
    public function createDoctor(array $data): array
    public function updateDoctor(int $id, array $data): array
    public function deleteDoctor(int $id): array
    public function getDoctor(int $id): ?array
    public function searchDoctors(string $query, int $page, int $perPage): array
    public function hasAppointments(int $doctorId): bool
}
```

### 4. Error Handler (`utils/ErrorHandler.php`)

```php
class ErrorHandler {
    public static function logError(string $message, array $context): void
    public static function displayError(string $userMessage): string
    public static function handleDatabaseError(mysqli $conn, string $operation): array
}
```

### 5. Pagination Utility (`utils/Pagination.php`)

```php
class Pagination {
    public function __construct(int $totalRecords, int $perPage, int $currentPage)
    public function getOffset(): int
    public function getTotalPages(): int
    public function renderPagination(): string
}
```

## Data Models

### Existing Database Schema (No Changes)
- **users**: id, username, password, role, created_at
- **patients**: id, name, age, gender, phone, address, created_at
- **doctors**: id, name, specialization, phone, email, created_at
- **appointments**: id, patient_id, doctor_id, appointment_date, appointment_time, status, notes, created_at

### Data Validation Rules

**Patient**:
- name: 2-100 characters, letters/spaces/hyphens only
- age: 0-150
- phone: 10-20 characters, digits/spaces/dashes/parentheses
- email: valid RFC 5322 format
- address: max 500 characters

**Doctor**:
- name: 2-100 characters, letters/spaces/hyphens only
- specialization: 2-100 characters
- phone: 10-20 characters, digits/spaces/dashes/parentheses
- email: valid RFC 5322 format

**Appointment**:
- appointment_date: valid date, not in the past
- appointment_time: valid time format (HH:MM)
- patient_id: must exist in patients table
- doctor_id: must exist in doctors table
- status: one of ['Scheduled', 'Completed', 'Cancelled']
- notes: max 1000 characters

## Correctness Properties

*A property is a characteristic or behavior that should hold true across all valid executions of a system—essentially, a formal statement about what the system should do. Properties serve as the bridge between human-readable specifications and machine-verifiable correctness guarantees.*

### Input Validation Properties

**Property 1: Name validation accepts only valid characters**
*For any* string input to name validation, the validator should accept only strings containing letters, spaces, and hyphens, and reject all others.
**Validates: Requirements 1.1**

**Property 2: Age validation enforces range boundaries**
*For any* integer input to age validation, the validator should accept values between 0 and 150 inclusive, and reject all values outside this range.
**Validates: Requirements 1.2**

**Property 3: Phone validation enforces format**
*For any* string input to phone validation, the validator should accept only strings matching valid phone formats (10-20 characters with digits, spaces, dashes, parentheses) and reject invalid formats.
**Validates: Requirements 1.3**

**Property 4: Email validation follows RFC 5322**
*For any* string input to email validation, the validator should accept only strings that conform to RFC 5322 email format and reject non-conforming strings.
**Validates: Requirements 1.4**

**Property 5: Validation errors provide specific messages**
*For any* invalid input to any validator, the validation result should include a specific error message indicating what is wrong with the input.
**Validates: Requirements 1.5**

**Property 6: Input sanitization neutralizes XSS**
*For any* string containing HTML/JavaScript special characters, the sanitization function should return a string where those characters are escaped or removed, preventing script execution.
**Validates: Requirements 1.6**

### Database Security Properties

**Property 7: Query operations with user input are safe**
*For any* database query operation with user input, executing the query with malicious SQL injection attempts should not alter the intended query behavior or expose data.
**Validates: Requirements 2.4**

### Error Handling Properties

**Property 8: Doctors with appointments cannot be deleted**
*For any* doctor record that has associated appointments, attempting to delete that doctor should fail and return an error message.
**Validates: Requirements 3.2**

**Property 9: Patients with appointments cannot be deleted**
*For any* patient record that has associated appointments, attempting to delete that patient should fail and return an error message.
**Validates: Requirements 3.3**

**Property 10: Invalid appointment dates are rejected**
*For any* appointment creation or update with an invalid date or time format, the operation should fail with a validation error message.
**Validates: Requirements 3.4**

### Edit Functionality Properties

**Property 11: Edit form displays current patient data**
*For any* patient record, requesting the edit form should return a form pre-populated with that patient's current name, age, gender, phone, and address.
**Validates: Requirements 4.1**

**Property 12: Patient updates persist correctly**
*For any* valid patient update, after submission the database should contain the new values and retrieving the patient should return the updated data.
**Validates: Requirements 4.2**

**Property 13: Edit form displays current doctor data**
*For any* doctor record, requesting the edit form should return a form pre-populated with that doctor's current name, specialization, phone, and email.
**Validates: Requirements 4.3**

**Property 14: Doctor updates persist correctly**
*For any* valid doctor update, after submission the database should contain the new values and retrieving the doctor should return the updated data.
**Validates: Requirements 4.4**

**Property 15: Edit cancellation preserves data**
*For any* edit operation, if the user cancels before submitting, the original record data should remain unchanged in the database.
**Validates: Requirements 4.5**

### Search and Filter Properties

**Property 16: Patient search returns only matches**
*For any* search query on the patients page, all returned results should have either a name or phone number that contains the search term as a substring.
**Validates: Requirements 5.1**

**Property 17: Doctor search returns only matches**
*For any* search query on the doctors page, all returned results should have either a name or specialization that contains the search term as a substring.
**Validates: Requirements 5.2**

**Property 18: Appointment date filter returns only matches**
*For any* date range filter on appointments, all returned appointments should have an appointment_date within the specified start and end dates inclusive.
**Validates: Requirements 5.3**

**Property 19: Appointment status filter returns only matches**
*For any* status filter on appointments, all returned appointments should have a status field matching the selected status value.
**Validates: Requirements 5.4**

**Property 20: Clearing filters restores full dataset**
*For any* filtered view, clearing all filters should return the complete unfiltered dataset (subject to pagination).
**Validates: Requirements 5.5**

### CSRF Protection Properties

**Property 21: Forms include unique CSRF tokens**
*For any* form page load, the generated HTML should contain a CSRF token field, and loading the same form twice should produce different token values.
**Validates: Requirements 6.1**

**Property 22: Form submission validates CSRF token**
*For any* form submission, if the submitted CSRF token matches the session token the submission should proceed, otherwise it should be rejected.
**Validates: Requirements 6.2**

**Property 23: Invalid CSRF tokens are rejected**
*For any* form submission with a missing or invalid CSRF token, the submission should fail and return an error message.
**Validates: Requirements 6.3**

**Property 24: CSRF tokens are regenerated after use**
*For any* successful form submission with a valid CSRF token, the session should contain a new CSRF token different from the one that was just used.
**Validates: Requirements 6.4**

### Appointment Conflict Detection Properties

**Property 25: Conflicting appointments are detected**
*For any* appointment creation attempt, if a non-cancelled appointment already exists for the same doctor at the same date and time, the creation should fail with a conflict error.
**Validates: Requirements 7.1**

**Property 26: Conflict rejection includes alternatives**
*For any* appointment creation that fails due to a conflict, the error response should include a list of available time slots for that doctor on that date.
**Validates: Requirements 7.2**

**Property 27: Appointment updates detect conflicts**
*For any* appointment update that changes the time, if the new time conflicts with another non-cancelled appointment for the same doctor, the update should fail with a conflict error.
**Validates: Requirements 7.3**

**Property 28: Cancelled appointments don't cause conflicts**
*For any* appointment with status 'Cancelled', that appointment should not prevent creation or updates of other appointments at the same doctor/date/time.
**Validates: Requirements 7.4**

### Pagination Properties

**Property 29: Patient list paginates at 20 records**
*For any* patient list query with more than 20 total records, the first page should return exactly 20 records and pagination controls should be displayed.
**Validates: Requirements 8.1**

**Property 30: Doctor list paginates at 20 records**
*For any* doctor list query with more than 20 total records, the first page should return exactly 20 records and pagination controls should be displayed.
**Validates: Requirements 8.2**

**Property 31: Appointment list paginates at 20 records**
*For any* appointment list query with more than 20 total records, the first page should return exactly 20 records and pagination controls should be displayed.
**Validates: Requirements 8.3**

**Property 32: Page navigation returns correct subset**
*For any* page number N in a paginated list, the returned records should be records (N-1)*20+1 through N*20 of the total ordered dataset.
**Validates: Requirements 8.4**

**Property 33: Filtered results maintain pagination**
*For any* search or filter operation that returns more than 20 results, the results should be paginated with 20 records per page.
**Validates: Requirements 8.5**

### Session Management Properties

**Property 34: Login regenerates session ID**
*For any* successful login, the session ID after login should be different from the session ID before login.
**Validates: Requirements 9.1**

**Property 35: Logout clears session data**
*For any* logout operation, after logout completes, all session variables should be unset and the session should be destroyed.
**Validates: Requirements 9.3**

**Property 36: Unauthenticated access redirects to login**
*For any* request to a protected page without a valid authenticated session, the response should be a redirect to the login page.
**Validates: Requirements 9.4**

### Appointment Detail View Properties

**Property 37: Appointment details include complete patient data**
*For any* appointment detail view, the displayed information should include all patient fields: name, age, gender, phone, and address.
**Validates: Requirements 10.1**

**Property 38: Appointment details include complete doctor data**
*For any* appointment detail view, the displayed information should include all doctor fields: name, specialization, phone, and email.
**Validates: Requirements 10.2**

**Property 39: Appointment details include full notes**
*For any* appointment detail view, the complete notes field should be displayed without truncation.
**Validates: Requirements 10.3**

## Error Handling

### Error Logging Strategy
- All database errors will be logged to `logs/error.log` with timestamp, error message, and context
- Log entries will include: timestamp, error type, file/line, user ID (if available), and sanitized query/operation
- Sensitive information (passwords, tokens) will never be logged

### User-Facing Error Messages
- Database connection errors: "System is currently under maintenance. Please try again later."
- Validation errors: Specific field-level messages (e.g., "Age must be between 0 and 150")
- Referential integrity errors: "Cannot delete [entity] because it has associated [related entities]"
- Conflict errors: "This time slot is already booked. Available times: [list]"
- CSRF errors: "Security token invalid. Please refresh the page and try again."
- Generic errors: "An error occurred. Please try again or contact support."

### Error Response Format
All service methods return a consistent format:
```php
[
    'success' => bool,
    'data' => mixed|null,
    'error' => string|null,
    'errors' => array|null  // field-specific validation errors
]
```

## Testing Strategy

### Unit Testing Approach
The system will use PHPUnit for unit testing. Unit tests will cover:

- **Validation Functions**: Test specific examples of valid/invalid inputs for each validator
  - Example: `testValidateNameRejectsNumbers()` - verify "John123" is rejected
  - Example: `testValidateAgeAcceptsBoundaries()` - verify 0 and 150 are accepted
  - Example: `testValidateEmailRejectsInvalid()` - verify "notanemail" is rejected

- **Service Methods**: Test specific scenarios for CRUD operations
  - Example: `testCreatePatientWithValidData()` - verify patient creation succeeds
  - Example: `testDeleteDoctorWithAppointmentsFails()` - verify referential integrity
  - Example: `testUpdateAppointmentWithConflictFails()` - verify conflict detection

- **CSRF Functions**: Test token generation and validation
  - Example: `testGenerateCSRFTokenCreatesUniqueToken()` - verify uniqueness
  - Example: `testValidateCSRFTokenRejectsInvalid()` - verify rejection

- **Error Handling**: Test error logging and message formatting
  - Example: `testDatabaseErrorLogsCorrectly()` - verify log entry format
  - Example: `testUserFriendlyMessageHidesDetails()` - verify no sensitive data exposed

### Property-Based Testing Approach
The system will use **Eris** (a property-based testing library for PHP) for property-based testing. Property-based tests will verify universal properties across many randomly generated inputs.

**Configuration**: Each property-based test will run a minimum of 100 iterations to ensure thorough coverage of the input space.

**Tagging**: Each property-based test will include a comment tag in this exact format:
```php
/**
 * Feature: hospital-management-enhancement, Property {number}: {property_text}
 */
```

**Property Test Coverage**:
- **Validation Properties (1-6)**: Generate random strings, integers, and special characters to verify validators correctly accept/reject inputs across the entire input space
- **Database Security (7)**: Generate SQL injection payloads to verify queries remain safe
- **Referential Integrity (8-9)**: Generate random patient/doctor/appointment combinations to verify deletion constraints
- **Edit Operations (11-15)**: Generate random valid updates to verify data persistence and rollback
- **Search/Filter (16-20)**: Generate random datasets and queries to verify search results correctness
- **CSRF Protection (21-24)**: Generate random token values to verify validation logic
- **Conflict Detection (25-28)**: Generate random appointment combinations to verify conflict logic
- **Pagination (29-33)**: Generate datasets of varying sizes to verify pagination math
- **Session Management (34-36)**: Generate random session states to verify security behavior
- **Detail Views (37-39)**: Generate random appointments to verify data completeness

Each correctness property defined in this document will be implemented by a SINGLE property-based test that validates the property across many randomly generated inputs.

## Implementation Notes

### Security Considerations
1. **SQL Injection Prevention**: All database queries use prepared statements with bound parameters
2. **XSS Prevention**: All user input is sanitized before display using `htmlspecialchars()` with ENT_QUOTES
3. **CSRF Protection**: All state-changing forms include and validate CSRF tokens
4. **Session Security**: Sessions use httponly and secure flags, with 30-minute timeout
5. **Password Security**: Passwords use `password_hash()` with bcrypt (already implemented)

### Performance Considerations
1. **Pagination**: Limits query results to 20 records per page to prevent memory issues
2. **Indexed Queries**: Ensure foreign keys and frequently searched fields have indexes
3. **Connection Pooling**: Reuse database connection across requests (already implemented)

### Backward Compatibility
- All existing functionality remains intact
- Database schema unchanged (no migrations needed)
- Existing URLs and routes preserved
- Enhanced pages maintain same visual structure

### Browser Compatibility
- Target: Modern browsers (Chrome, Firefox, Safari, Edge - last 2 versions)
- Progressive enhancement: Core functionality works without JavaScript
- Forms use HTML5 validation as first line of defense

## Dependencies

### Required PHP Extensions
- mysqli (already in use)
- session (already in use)
- mbstring (for string operations)

### Testing Dependencies
- PHPUnit: ^9.0 (unit testing framework)
- Eris: ^0.13 (property-based testing library for PHP)

### Development Tools
- PHP 7.4+ (current system requirement)
- MySQL 5.7+ (current database)
- Composer (for dependency management)