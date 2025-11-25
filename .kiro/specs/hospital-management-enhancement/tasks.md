# Implementation Plan

- [ ] 1. Set up project structure and utilities
  - Create directory structure for services, utils, and enhanced includes
  - Set up Composer for dependency management (PHPUnit, Eris)
  - Create logs directory for error logging
  - _Requirements: 3.1_

- [ ] 2. Implement validation utilities
  - [ ] 2.1 Create validation functions file
    - Write `includes/validators.php` with all validation functions
    - Implement validateName, validateAge, validatePhone, validateEmail
    - Implement validateDate, validateTime, sanitizeInput functions
    - _Requirements: 1.1, 1.2, 1.3, 1.4, 1.6_

  - [ ] 2.2 Write property test for name validation
    - **Property 1: Name validation accepts only valid characters**
    - **Validates: Requirements 1.1**

  - [ ] 2.3 Write property test for age validation
    - **Property 2: Age validation enforces range boundaries**
    - **Validates: Requirements 1.2**

  - [ ] 2.4 Write property test for phone validation
    - **Property 3: Phone validation enforces format**
    - **Validates: Requirements 1.3**

  - [ ] 2.5 Write property test for email validation
    - **Property 4: Email validation follows RFC 5322**
    - **Validates: Requirements 1.4**

  - [ ] 2.6 Write property test for validation error messages
    - **Property 5: Validation errors provide specific messages**
    - **Validates: Requirements 1.5**

  - [ ] 2.7 Write property test for XSS sanitization
    - **Property 6: Input sanitization neutralizes XSS**
    - **Validates: Requirements 1.6**

- [ ] 3. Implement security utilities
  - [ ] 3.1 Create CSRF protection system
    - Write `config/security.php` with CSRF token functions
    - Implement generateCSRFToken, validateCSRFToken, getCSRFTokenField
    - Implement configureSecureSession, regenerateSessionId, checkSessionTimeout
    - _Requirements: 6.1, 6.2, 6.3, 6.4, 9.1, 9.2, 9.5_

  - [ ] 3.2 Write property test for CSRF token uniqueness
    - **Property 21: Forms include unique CSRF tokens**
    - **Validates: Requirements 6.1**

  - [ ] 3.3 Write property test for CSRF token validation
    - **Property 22: Form submission validates CSRF token**
    - **Validates: Requirements 6.2**

  - [ ] 3.4 Write property test for invalid CSRF token rejection
    - **Property 23: Invalid CSRF tokens are rejected**
    - **Validates: Requirements 6.3**

  - [ ] 3.5 Write property test for CSRF token regeneration
    - **Property 24: CSRF tokens are regenerated after use**
    - **Validates: Requirements 6.4**

  - [ ] 3.6 Enhance database.php with secure session configuration
    - Update `config/database.php` to call configureSecureSession
    - Add session timeout checking to requireLogin function
    - _Requirements: 9.2, 9.5_

  - [ ] 3.7 Write property test for session ID regeneration on login
    - **Property 34: Login regenerates session ID**
    - **Validates: Requirements 9.1**

- [ ] 4. Implement error handling system
  - [ ] 4.1 Create ErrorHandler utility class
    - Write `utils/ErrorHandler.php` with static methods
    - Implement logError, displayError, handleDatabaseError methods
    - _Requirements: 3.1, 3.5_

  - [ ] 4.2 Create logs directory and configure error logging
    - Ensure logs directory exists with proper permissions
    - Test error logging functionality
    - _Requirements: 3.1_

- [ ] 5. Implement pagination utility
  - [ ] 5.1 Create Pagination class
    - Write `utils/Pagination.php` with pagination logic
    - Implement constructor, getOffset, getTotalPages, renderPagination methods
    - _Requirements: 8.1, 8.2, 8.3, 8.4, 8.5_

  - [ ] 5.2 Write property test for pagination at 20 records
    - **Property 29: Patient list paginates at 20 records**
    - **Property 30: Doctor list paginates at 20 records**
    - **Property 31: Appointment list paginates at 20 records**
    - **Validates: Requirements 8.1, 8.2, 8.3**

  - [ ] 5.3 Write property test for page navigation
    - **Property 32: Page navigation returns correct subset**
    - **Validates: Requirements 8.4**

  - [ ] 5.4 Write property test for filtered pagination
    - **Property 33: Filtered results maintain pagination**
    - **Validates: Requirements 8.5**

- [ ] 6. Implement PatientService class
  - [ ] 6.1 Create PatientService with CRUD operations
    - Write `services/PatientService.php` class
    - Implement createPatient, updatePatient, deletePatient, getPatient methods
    - Implement searchPatients and hasAppointments methods
    - Use validation functions and prepared statements throughout
    - _Requirements: 1.1, 1.2, 1.3, 2.1, 2.3, 3.3, 4.1, 4.2, 5.1_

  - [ ] 6.2 Write property test for patient deletion with appointments
    - **Property 9: Patients with appointments cannot be deleted**
    - **Validates: Requirements 3.3**

  - [ ] 6.3 Write property test for patient search
    - **Property 16: Patient search returns only matches**
    - **Validates: Requirements 5.1**

  - [ ] 6.4 Write property test for patient update persistence
    - **Property 12: Patient updates persist correctly**
    - **Validates: Requirements 4.2**

- [ ] 7. Implement DoctorService class
  - [ ] 7.1 Create DoctorService with CRUD operations
    - Write `services/DoctorService.php` class
    - Implement createDoctor, updateDoctor, deleteDoctor, getDoctor methods
    - Implement searchDoctors and hasAppointments methods
    - Use validation functions and prepared statements throughout
    - _Requirements: 1.1, 1.3, 1.4, 2.1, 2.3, 3.2, 4.3, 4.4, 5.2_

  - [ ] 7.2 Write property test for doctor deletion with appointments
    - **Property 8: Doctors with appointments cannot be deleted**
    - **Validates: Requirements 3.2**

  - [ ] 7.3 Write property test for doctor search
    - **Property 17: Doctor search returns only matches**
    - **Validates: Requirements 5.2**

  - [ ] 7.4 Write property test for doctor update persistence
    - **Property 14: Doctor updates persist correctly**
    - **Validates: Requirements 4.4**

- [ ] 8. Implement AppointmentService class
  - [ ] 8.1 Create AppointmentService with CRUD operations
    - Write `services/AppointmentService.php` class
    - Implement createAppointment, updateAppointment, deleteAppointment, getAppointment methods
    - Implement getAppointments with filtering support
    - _Requirements: 2.1, 2.3, 3.4, 5.3, 5.4, 10.1, 10.2, 10.3_

  - [ ] 8.2 Implement appointment conflict detection
    - Add checkConflict method to AppointmentService
    - Add getAvailableSlots method to suggest alternatives
    - _Requirements: 7.1, 7.2, 7.3, 7.4_

  - [ ] 8.3 Write property test for appointment conflict detection
    - **Property 25: Conflicting appointments are detected**
    - **Validates: Requirements 7.1**

  - [ ] 8.4 Write property test for conflict with alternatives
    - **Property 26: Conflict rejection includes alternatives**
    - **Validates: Requirements 7.2**

  - [ ] 8.5 Write property test for appointment update conflicts
    - **Property 27: Appointment updates detect conflicts**
    - **Validates: Requirements 7.3**

  - [ ] 8.6 Write property test for cancelled appointments
    - **Property 28: Cancelled appointments don't cause conflicts**
    - **Validates: Requirements 7.4**

  - [ ] 8.7 Write property test for appointment date filtering
    - **Property 18: Appointment date filter returns only matches**
    - **Validates: Requirements 5.3**

  - [ ] 8.8 Write property test for appointment status filtering
    - **Property 19: Appointment status filter returns only matches**
    - **Validates: Requirements 5.4**

  - [ ] 8.9 Write property test for invalid appointment dates
    - **Property 10: Invalid appointment dates are rejected**
    - **Validates: Requirements 3.4**

- [ ] 9. Checkpoint - Ensure all core services and tests pass
  - Ensure all tests pass, ask the user if questions arise.

- [ ] 10. Enhance login.php with security improvements
  - [ ] 10.1 Update login.php to use session regeneration
    - Add session_regenerate_id() call after successful login
    - Add CSRF token to login form
    - Improve error handling with ErrorHandler
    - _Requirements: 6.1, 6.2, 9.1_

  - [ ] 10.2 Write property test for session logout
    - **Property 35: Logout clears session data**
    - **Validates: Requirements 9.3**

  - [ ] 10.3 Write property test for unauthenticated access
    - **Property 36: Unauthenticated access redirects to login**
    - **Validates: Requirements 9.4**

- [ ] 11. Enhance logout.php with proper session cleanup
  - [ ] 11.1 Update logout.php to destroy session properly
    - Clear all session variables
    - Destroy session and regenerate ID
    - _Requirements: 9.3_

- [ ] 12. Enhance patients.php with new features
  - [ ] 12.1 Add edit functionality to patients.php
    - Add edit form that pre-fills patient data
    - Add cancel button to return to list view
    - Use PatientService for all operations
    - _Requirements: 4.1, 4.2, 4.5_

  - [ ] 12.2 Write property test for patient edit form
    - **Property 11: Edit form displays current patient data**
    - **Validates: Requirements 4.1**

  - [ ] 12.3 Write property test for edit cancellation
    - **Property 15: Edit cancellation preserves data**
    - **Validates: Requirements 4.5**

  - [ ] 12.4 Add search functionality to patients.php
    - Add search form with text input
    - Implement search using PatientService
    - Add clear filters button
    - _Requirements: 5.1, 5.5_

  - [ ] 12.5 Write property test for clearing filters
    - **Property 20: Clearing filters restores full dataset**
    - **Validates: Requirements 5.5**

  - [ ] 12.6 Add pagination to patients.php
    - Integrate Pagination utility
    - Display pagination controls
    - _Requirements: 8.1, 8.4_

  - [ ] 12.7 Add CSRF protection to all forms
    - Add CSRF token fields to create and edit forms
    - Validate tokens on form submission
    - _Requirements: 6.1, 6.2, 6.3_

  - [ ] 12.8 Enhance validation and error handling
    - Use validation functions for all inputs
    - Display field-specific error messages
    - Use ErrorHandler for database errors
    - _Requirements: 1.1, 1.2, 1.3, 1.5, 3.1, 3.3_

  - [ ] 12.9 Update delete operation with referential integrity check
    - Use PatientService.hasAppointments before deletion
    - Display appropriate error if patient has appointments
    - Use prepared statements for delete
    - _Requirements: 2.2, 3.3_

- [ ] 13. Enhance doctors.php with new features
  - [ ] 13.1 Add edit functionality to doctors.php
    - Add edit form that pre-fills doctor data
    - Add cancel button to return to list view
    - Use DoctorService for all operations
    - _Requirements: 4.3, 4.4, 4.5_

  - [ ] 13.2 Write property test for doctor edit form
    - **Property 13: Edit form displays current doctor data**
    - **Validates: Requirements 4.3**

  - [ ] 13.3 Add search functionality to doctors.php
    - Add search form with text input
    - Implement search using DoctorService
    - Add clear filters button
    - _Requirements: 5.2, 5.5_

  - [ ] 13.4 Add pagination to doctors.php
    - Integrate Pagination utility
    - Display pagination controls
    - _Requirements: 8.2, 8.4_

  - [ ] 13.5 Add CSRF protection to all forms
    - Add CSRF token fields to create and edit forms
    - Validate tokens on form submission
    - _Requirements: 6.1, 6.2, 6.3_

  - [ ] 13.6 Enhance validation and error handling
    - Use validation functions for all inputs
    - Display field-specific error messages
    - Use ErrorHandler for database errors
    - _Requirements: 1.1, 1.3, 1.4, 1.5, 3.1, 3.2_

  - [ ] 13.7 Update delete operation with referential integrity check
    - Use DoctorService.hasAppointments before deletion
    - Display appropriate error if doctor has appointments
    - Use prepared statements for delete
    - _Requirements: 2.2, 3.2_

- [ ] 14. Enhance appointments.php with new features
  - [ ] 14.1 Add conflict detection to appointment creation
    - Use AppointmentService.checkConflict before creating
    - Display available time slots on conflict
    - _Requirements: 7.1, 7.2_

  - [ ] 14.2 Add conflict detection to appointment updates
    - Use AppointmentService.checkConflict before updating
    - Exclude current appointment from conflict check
    - _Requirements: 7.3, 7.4_

  - [ ] 14.3 Add date range filter to appointments
    - Add date range input fields (start date, end date)
    - Filter appointments using AppointmentService
    - _Requirements: 5.3_

  - [ ] 14.4 Add status filter to appointments
    - Add status dropdown filter
    - Filter appointments by selected status
    - _Requirements: 5.4_

  - [ ] 14.5 Add pagination to appointments.php
    - Integrate Pagination utility
    - Maintain filters across pages
    - _Requirements: 8.3, 8.4, 8.5_

  - [ ] 14.6 Add CSRF protection to all forms
    - Add CSRF token fields to create and edit forms
    - Validate tokens on form submission
    - _Requirements: 6.1, 6.2, 6.3_

  - [ ] 14.7 Enhance validation and error handling
    - Use validation functions for date and time
    - Display field-specific error messages
    - Use ErrorHandler for database errors
    - _Requirements: 1.5, 3.1, 3.4_

  - [ ] 14.8 Update all database operations to use prepared statements
    - Ensure all queries use AppointmentService
    - Remove any direct SQL concatenation
    - _Requirements: 2.1, 2.2, 2.3_

- [ ] 15. Create appointment detail view page
  - [ ] 15.1 Create appointment_detail.php page
    - Display complete patient information
    - Display complete doctor information
    - Display full appointment notes without truncation
    - Add edit and cancel buttons
    - _Requirements: 10.1, 10.2, 10.3, 10.4_

  - [ ] 15.2 Write property test for appointment detail completeness
    - **Property 37: Appointment details include complete patient data**
    - **Property 38: Appointment details include complete doctor data**
    - **Property 39: Appointment details include full notes**
    - **Validates: Requirements 10.1, 10.2, 10.3**

- [ ] 16. Update dashboard.php with security improvements
  - [ ] 16.1 Add CSRF protection and secure queries
    - Ensure all queries use prepared statements
    - Add proper error handling
    - _Requirements: 2.1, 3.1_

- [ ] 17. Final checkpoint - Ensure all tests pass
  - Ensure all tests pass, ask the user if questions arise.
