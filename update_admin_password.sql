-- Update admin password to admin1234
USE hospital_db;
UPDATE users SET password = '$2y$10$n.PMviL2PJD95VY8UnRdfOn1mXH55cq4naUgr1bqtmbqk26z/00XC' WHERE username = 'admin';
