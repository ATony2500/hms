# Requirements Document

## Introduction

This specification defines enhancements to the existing Hospital Management System (HMS) built in PHP. The system currently manages patients, doctors, and appointments with basic CRUD operations. This enhancement focuses on improving security, data validation, error handling, and adding critical missing features to make the system production-ready and maintainable.

## Glossary

- **HMS**: Hospital Management System - the PHP-based web application for managing hospital operations
- **User**: An authenticated person using the system (admin, doctor, or receptionist)
- **Patient**: A person receiving medical care, stored in the patients table
- **Doctor**: A medical professional providing care, stored in the doctors table
- **Appointment**: A scheduled meeting between a patient and doctor
- **Session**: Server-side storage of user authentication state
- **SQL Injection**: A security vulnerability where malicious SQL code is inserted into queries
- **XSS**: Cross-Site Scripting - a security vulnerability where malicious scripts are injected into web pages
- **CSRF**: Cross-Site Request Forgery - an attack that forces users to execute unwanted actions
- **Input Validation**: The process of verifying user input meets expected format and constraints

## Requirements

### Requirement 1

**User Story:** As a system administrator, I want robust input validation and sanitization, so that the system prevents invalid data and security vulnerabilities.

#### Acceptance Criteria

1. WHEN a user submits a form with patient data, THE HMS SHALL validate that the name contains only letters, spaces, and hyphens
2. WHEN a user submits a form with patient age, THE HMS SHALL validate that the age is between 0 and 150
3. WHEN a user submits a form with phone number, THE HMS SHALL validate that the phone matches a valid format
4. WHEN a user submits a form with email address, THE HMS SHALL validate that the email matches RFC 5322 format
5. WHEN validation fails for any field, THE HMS SHALL display specific error messages indicating which fields are invalid
6. WHEN a user submits a form with special characters in text fields, THE HMS SHALL sanitize the input to prevent XSS attacks

### Requirement 2

**User Story:** As a system administrator, I want secure database operations, so that the system is protected against SQL injection attacks.

#### Acceptance Criteria

1. WHEN the HMS executes any database query with user input, THE HMS SHALL use prepared statements with parameter binding
2. WHEN the HMS deletes a record, THE HMS SHALL use prepared statements instead of direct query concatenation
3. WHEN the HMS updates a record, THE HMS SHALL use prepared statements with bound parameters
4. WHEN the HMS retrieves records based on user input, THE HMS SHALL sanitize and validate the input before query execution

### Requirement 3

**User Story:** As a user, I want proper error handling and user feedback, so that I understand what went wrong and how to fix it.

#### Acceptance Criteria

1. WHEN a database operation fails, THE HMS SHALL log the error details and display a user-friendly message
2. WHEN a user attempts to delete a doctor with existing appointments, THE HMS SHALL prevent the deletion and display an appropriate error message
3. WHEN a user attempts to delete a patient with existing appointments, THE HMS SHALL prevent the deletion and display an appropriate error message
4. WHEN a user attempts to create an appointment with invalid date or time, THE HMS SHALL reject the input and display validation errors
5. WHEN a database connection fails, THE HMS SHALL display a maintenance message instead of exposing connection details

### Requirement 4

**User Story:** As a user, I want to edit existing patient and doctor records, so that I can update information without deleting and recreating records.

#### Acceptance Criteria

1. WHEN a user clicks edit on a patient record, THE HMS SHALL display a form pre-filled with the patient's current data
2. WHEN a user submits updated patient data, THE HMS SHALL validate the input and update the database record
3. WHEN a user clicks edit on a doctor record, THE HMS SHALL display a form pre-filled with the doctor's current data
4. WHEN a user submits updated doctor data, THE HMS SHALL validate the input and update the database record
5. WHEN a user cancels an edit operation, THE HMS SHALL return to the list view without making changes

### Requirement 5

**User Story:** As a user, I want to search and filter records, so that I can quickly find specific patients, doctors, or appointments.

#### Acceptance Criteria

1. WHEN a user enters a search term in the patients page, THE HMS SHALL display only patients whose name or phone matches the search term
2. WHEN a user enters a search term in the doctors page, THE HMS SHALL display only doctors whose name or specialization matches the search term
3. WHEN a user filters appointments by date range, THE HMS SHALL display only appointments within the specified dates
4. WHEN a user filters appointments by status, THE HMS SHALL display only appointments matching the selected status
5. WHEN a user clears search filters, THE HMS SHALL display all records again

### Requirement 6

**User Story:** As a system administrator, I want CSRF protection on all forms, so that the system prevents cross-site request forgery attacks.

#### Acceptance Criteria

1. WHEN a user loads any form, THE HMS SHALL generate a unique CSRF token and include it in the form
2. WHEN a user submits a form, THE HMS SHALL validate that the CSRF token matches the session token
3. WHEN a CSRF token is invalid or missing, THE HMS SHALL reject the form submission and display an error
4. WHEN a CSRF token is used, THE HMS SHALL regenerate a new token for subsequent requests

### Requirement 7

**User Story:** As a user, I want appointment conflict detection, so that double-booking of doctors is prevented.

#### Acceptance Criteria

1. WHEN a user creates an appointment, THE HMS SHALL check if the doctor already has an appointment at the same date and time
2. WHEN a doctor is already booked at the requested time, THE HMS SHALL reject the appointment and display available time slots
3. WHEN a user updates an appointment time, THE HMS SHALL validate that the new time does not conflict with existing appointments
4. WHEN checking for conflicts, THE HMS SHALL exclude cancelled appointments from the validation

### Requirement 8

**User Story:** As a user, I want pagination for large lists, so that the system performs well with many records.

#### Acceptance Criteria

1. WHEN a user views the patients list with more than 20 records, THE HMS SHALL display 20 records per page with pagination controls
2. WHEN a user views the doctors list with more than 20 records, THE HMS SHALL display 20 records per page with pagination controls
3. WHEN a user views the appointments list with more than 20 records, THE HMS SHALL display 20 records per page with pagination controls
4. WHEN a user clicks a page number, THE HMS SHALL display the corresponding page of results
5. WHEN a user applies search filters, THE HMS SHALL maintain pagination for the filtered results

### Requirement 9

**User Story:** As a system administrator, I want proper session management, so that user sessions are secure and properly expired.

#### Acceptance Criteria

1. WHEN a user logs in successfully, THE HMS SHALL regenerate the session ID to prevent session fixation
2. WHEN a user is inactive for 30 minutes, THE HMS SHALL expire the session and require re-authentication
3. WHEN a user logs out, THE HMS SHALL destroy the session and clear all session data
4. WHEN a user attempts to access protected pages without authentication, THE HMS SHALL redirect to the login page
5. WHEN session data is stored, THE HMS SHALL use secure session configuration with httponly and secure flags

### Requirement 10

**User Story:** As a user, I want to view detailed appointment information, so that I can see complete patient and doctor details for each appointment.

#### Acceptance Criteria

1. WHEN a user clicks on an appointment, THE HMS SHALL display a detailed view with full patient information
2. WHEN viewing appointment details, THE HMS SHALL display complete doctor information including specialization
3. WHEN viewing appointment details, THE HMS SHALL display the full notes and appointment history
4. WHEN viewing appointment details, THE HMS SHALL provide options to edit or cancel the appointment
