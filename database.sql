-- PayTrack Employee Payroll Management System Database Dump

CREATE TABLE IF NOT EXISTS `users` (
  `id` INT(11) UNSIGNED AUTO_INCREMENT PRIMARY KEY,
  `username` VARCHAR(100) NOT NULL UNIQUE,
  `password` VARCHAR(255) NOT NULL,
  `name` VARCHAR(150) DEFAULT NULL,
  `role` VARCHAR(50) DEFAULT 'user',
  `created_at` DATETIME DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

CREATE TABLE IF NOT EXISTS `offices` (
  `id` INT(11) UNSIGNED AUTO_INCREMENT PRIMARY KEY,
  `office_name` VARCHAR(150) NOT NULL UNIQUE,
  `description` VARCHAR(255) DEFAULT NULL,
  `created_at` DATETIME DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

CREATE TABLE IF NOT EXISTS `employees` (
  `id` INT(11) UNSIGNED AUTO_INCREMENT PRIMARY KEY,
  `employee_id` VARCHAR(50) NOT NULL UNIQUE,
  `full_name` VARCHAR(150) NOT NULL,
  `office_id` INT(11) UNSIGNED DEFAULT NULL,
  `position` VARCHAR(150) DEFAULT 'N/A',
  `contact_number` VARCHAR(50) DEFAULT NULL,
  `salary_rate` DECIMAL(12,2) DEFAULT 0.00,
  `employment_status` VARCHAR(50) DEFAULT 'Regular',
  `is_active` TINYINT(1) DEFAULT 1,
  `atm_account_no` VARCHAR(100) DEFAULT NULL,
  `created_at` DATETIME DEFAULT CURRENT_TIMESTAMP,
  `updated_at` DATETIME DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  KEY `office_id` (`office_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

CREATE TABLE IF NOT EXISTS `deductions` (
  `id` INT(11) UNSIGNED AUTO_INCREMENT PRIMARY KEY,
  `employee_id` INT(11) UNSIGNED NOT NULL,
  `withholding_tax` DECIMAL(12,2) DEFAULT 0.00,
  `loans` DECIMAL(12,2) DEFAULT 0.00,
  `government_cont` DECIMAL(12,2) DEFAULT 0.00,
  `other_deduct` DECIMAL(12,2) DEFAULT 0.00,
  `gsis_premium` DECIMAL(12,2) DEFAULT 0.00,
  `gsis_policy` DECIMAL(12,2) DEFAULT 0.00,
  `gsis_other` DECIMAL(12,2) DEFAULT 0.00,
  `gsis_ouli` DECIMAL(12,2) DEFAULT 0.00,
  `gsis_diff` DECIMAL(12,2) DEFAULT 0.00,
  `pagibig_premium` DECIMAL(12,2) DEFAULT 0.00,
  `pagibig_loan` DECIMAL(12,2) DEFAULT 0.00,
  `pagibig_mp2` DECIMAL(12,2) DEFAULT 0.00,
  `phic` DECIMAL(12,2) DEFAULT 0.00,
  `phic_diff` DECIMAL(12,2) DEFAULT 0.00,
  `bank_lbp` DECIMAL(12,2) DEFAULT 0.00,
  `bank_other_payables` DECIMAL(12,2) DEFAULT 0.00,
  `bank_mcc` DECIMAL(12,2) DEFAULT 0.00,
  `bank_1stvb` DECIMAL(12,2) DEFAULT 0.00,
  `bank_rbt` DECIMAL(12,2) DEFAULT 0.00,
  `created_at` DATETIME DEFAULT CURRENT_TIMESTAMP,
  `updated_at` DATETIME DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  KEY `employee_id` (`employee_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

CREATE TABLE IF NOT EXISTS `payroll_records` (
  `id` INT(11) UNSIGNED AUTO_INCREMENT PRIMARY KEY,
  `employee_id` INT(11) UNSIGNED NOT NULL,
  `payroll_period` VARCHAR(20) NOT NULL,
  `period_of_service` VARCHAR(100) DEFAULT NULL,
  `refund_rata` DECIMAL(12,2) DEFAULT 0.00,
  `gross_pay` DECIMAL(12,2) DEFAULT 0.00,
  `total_deductions` DECIMAL(12,2) DEFAULT 0.00,
  `net_pay` DECIMAL(12,2) DEFAULT 0.00,
  `first_quincena` DECIMAL(12,2) DEFAULT 0.00,
  `second_quincena` DECIMAL(12,2) DEFAULT 0.00,
  `cash_paid` DECIMAL(12,2) DEFAULT 0.00,
  `processed_by` INT(11) UNSIGNED DEFAULT NULL,
  `created_at` DATETIME DEFAULT CURRENT_TIMESTAMP,
  `updated_at` DATETIME DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  KEY `employee_id` (`employee_id`),
  KEY `payroll_period` (`payroll_period`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- Seed default admin account (password: admin123)
INSERT INTO `users` (`username`, `password`, `name`, `role`)
VALUES ('admin', '$2y$10$wK1yRz9G5D9wYq.X6.S.mO6p4N3s8J7v1B5x7g8h9i0j1k2l3m4n5', 'System Administrator', 'admin')
ON DUPLICATE KEY UPDATE `id`=`id`;
