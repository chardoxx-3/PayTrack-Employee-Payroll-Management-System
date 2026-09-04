<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class CreatePayrollTables extends Migration
{
    public function up()
    {
        // 1. Users Table
        $this->forge->addField([
            'id' => [
                'type'           => 'INT',
                'constraint'     => 11,
                'unsigned'       => true,
                'auto_increment' => true,
            ],
            'username' => [
                'type'       => 'VARCHAR',
                'constraint' => '100',
                'unique'     => true,
            ],
            'password' => [
                'type'       => 'VARCHAR',
                'constraint' => '255',
            ],
            'name' => [
                'type'       => 'VARCHAR',
                'constraint' => '150',
                'null'       => true,
            ],
            'role' => [
                'type'       => 'VARCHAR',
                'constraint' => '50',
                'default'    => 'user',
            ],
            'created_at DATETIME DEFAULT CURRENT_TIMESTAMP',
        ]);
        $this->forge->addKey('id', true);
        $this->forge->createTable('users', true);

        // 2. Offices Table
        $this->forge->addField([
            'id' => [
                'type'           => 'INT',
                'constraint'     => 11,
                'unsigned'       => true,
                'auto_increment' => true,
            ],
            'office_name' => [
                'type'       => 'VARCHAR',
                'constraint' => '150',
                'unique'     => true,
            ],
            'description' => [
                'type'       => 'VARCHAR',
                'constraint' => '255',
                'null'       => true,
            ],
            'created_at DATETIME DEFAULT CURRENT_TIMESTAMP',
        ]);
        $this->forge->addKey('id', true);
        $this->forge->createTable('offices', true);

        // 3. Employees Table
        $this->forge->addField([
            'id' => [
                'type'           => 'INT',
                'constraint'     => 11,
                'unsigned'       => true,
                'auto_increment' => true,
            ],
            'employee_id' => [
                'type'       => 'VARCHAR',
                'constraint' => '50',
                'unique'     => true,
            ],
            'full_name' => [
                'type'       => 'VARCHAR',
                'constraint' => '150',
            ],
            'office_id' => [
                'type'       => 'INT',
                'constraint' => 11,
                'unsigned'   => true,
                'null'       => true,
            ],
            'position' => [
                'type'       => 'VARCHAR',
                'constraint' => '150',
                'default'    => 'N/A',
            ],
            'contact_number' => [
                'type'       => 'VARCHAR',
                'constraint' => '50',
                'null'       => true,
            ],
            'salary_rate' => [
                'type'       => 'DECIMAL',
                'constraint' => '12,2',
                'default'    => 0.00,
            ],
            'employment_status' => [
                'type'       => 'VARCHAR',
                'constraint' => '50',
                'default'    => 'Regular',
            ],
            'is_active' => [
                'type'       => 'TINYINT',
                'constraint' => 1,
                'default'    => 1,
            ],
            'atm_account_no' => [
                'type'       => 'VARCHAR',
                'constraint' => '100',
                'null'       => true,
            ],
            'created_at DATETIME DEFAULT CURRENT_TIMESTAMP',
            'updated_at DATETIME DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP',
        ]);
        $this->forge->addKey('id', true);
        $this->forge->createTable('employees', true);

        // 4. Deductions Table
        $this->forge->addField([
            'id' => [
                'type'           => 'INT',
                'constraint'     => 11,
                'unsigned'       => true,
                'auto_increment' => true,
            ],
            'employee_id' => [
                'type'       => 'INT',
                'constraint' => 11,
                'unsigned'   => true,
            ],
            'withholding_tax'     => ['type' => 'DECIMAL', 'constraint' => '12,2', 'default' => 0.00],
            'loans'               => ['type' => 'DECIMAL', 'constraint' => '12,2', 'default' => 0.00],
            'government_cont'     => ['type' => 'DECIMAL', 'constraint' => '12,2', 'default' => 0.00],
            'other_deduct'        => ['type' => 'DECIMAL', 'constraint' => '12,2', 'default' => 0.00],
            'gsis_premium'        => ['type' => 'DECIMAL', 'constraint' => '12,2', 'default' => 0.00],
            'gsis_policy'         => ['type' => 'DECIMAL', 'constraint' => '12,2', 'default' => 0.00],
            'gsis_other'          => ['type' => 'DECIMAL', 'constraint' => '12,2', 'default' => 0.00],
            'gsis_ouli'           => ['type' => 'DECIMAL', 'constraint' => '12,2', 'default' => 0.00],
            'gsis_diff'           => ['type' => 'DECIMAL', 'constraint' => '12,2', 'default' => 0.00],
            'pagibig_premium'     => ['type' => 'DECIMAL', 'constraint' => '12,2', 'default' => 0.00],
            'pagibig_loan'        => ['type' => 'DECIMAL', 'constraint' => '12,2', 'default' => 0.00],
            'pagibig_mp2'         => ['type' => 'DECIMAL', 'constraint' => '12,2', 'default' => 0.00],
            'phic'                => ['type' => 'DECIMAL', 'constraint' => '12,2', 'default' => 0.00],
            'phic_diff'           => ['type' => 'DECIMAL', 'constraint' => '12,2', 'default' => 0.00],
            'bank_lbp'            => ['type' => 'DECIMAL', 'constraint' => '12,2', 'default' => 0.00],
            'bank_other_payables' => ['type' => 'DECIMAL', 'constraint' => '12,2', 'default' => 0.00],
            'bank_mcc'            => ['type' => 'DECIMAL', 'constraint' => '12,2', 'default' => 0.00],
            'bank_1stvb'          => ['type' => 'DECIMAL', 'constraint' => '12,2', 'default' => 0.00],
            'bank_rbt'            => ['type' => 'DECIMAL', 'constraint' => '12,2', 'default' => 0.00],
            'created_at DATETIME DEFAULT CURRENT_TIMESTAMP',
            'updated_at DATETIME DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP',
        ]);
        $this->forge->addKey('id', true);
        $this->forge->createTable('deductions', true);

        // 5. Payroll Records Table
        $this->forge->addField([
            'id' => [
                'type'           => 'INT',
                'constraint'     => 11,
                'unsigned'       => true,
                'auto_increment' => true,
            ],
            'employee_id' => [
                'type'       => 'INT',
                'constraint' => 11,
                'unsigned'   => true,
            ],
            'payroll_period' => [
                'type'       => 'VARCHAR',
                'constraint' => '20',
            ],
            'period_of_service' => [
                'type'       => 'VARCHAR',
                'constraint' => '100',
                'null'       => true,
            ],
            'refund_rata' => [
                'type'       => 'DECIMAL',
                'constraint' => '12,2',
                'default'    => 0.00,
            ],
            'gross_pay' => [
                'type'       => 'DECIMAL',
                'constraint' => '12,2',
                'default'    => 0.00,
            ],
            'total_deductions' => [
                'type'       => 'DECIMAL',
                'constraint' => '12,2',
                'default'    => 0.00,
            ],
            'net_pay' => [
                'type'       => 'DECIMAL',
                'constraint' => '12,2',
                'default'    => 0.00,
            ],
            'first_quincena' => [
                'type'       => 'DECIMAL',
                'constraint' => '12,2',
                'default'    => 0.00,
            ],
            'second_quincena' => [
                'type'       => 'DECIMAL',
                'constraint' => '12,2',
                'default'    => 0.00,
            ],
            'cash_paid' => [
                'type'       => 'DECIMAL',
                'constraint' => '12,2',
                'default'    => 0.00,
            ],
            'processed_by' => [
                'type'       => 'INT',
                'constraint' => 11,
                'unsigned'   => true,
                'null'       => true,
            ],
            'created_at DATETIME DEFAULT CURRENT_TIMESTAMP',
            'updated_at DATETIME DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP',
        ]);
        $this->forge->addKey('id', true);
        $this->forge->createTable('payroll_records', true);
    }

    public function down()
    {
        $this->forge->dropTable('payroll_records', true);
        $this->forge->dropTable('deductions', true);
        $this->forge->dropTable('employees', true);
        $this->forge->dropTable('offices', true);
        $this->forge->dropTable('users', true);
    }
}
