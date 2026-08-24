<?php

namespace App\Controllers;

use App\Models\PayrollModel;
use App\Models\EmployeeModel;
use App\Models\DeductionModel;

class Payroll extends BaseController
{
public function index()
{
    $officeId = $this->request->getVar('office_id');
    $period = date('Y-m');
    $empModel = new EmployeeModel();
    $officeModel = new \App\Models\OfficeModel();

    $data['offices'] = $officeModel->getOfficesOrdered();

    // Default to MAYOR'S office when no office_id is set (first page load)
    if (!$officeId && !empty($data['offices'])) {
        foreach ($data['offices'] as $office) {
            if (stripos($office['office_name'], 'MAYOR') !== false) {
                $officeId = $office['id'];
                break;
            }
        }
    }

$empModel->select('employees.*,
                    deductions.gsis_premium, deductions.gsis_policy, deductions.gsis_other,
                    deductions.pagibig_premium, deductions.pagibig_loan,
                    deductions.phic, deductions.withholding_tax,
                    deductions.bank_lbp, deductions.bank_mcc, deductions.bank_1stvb,
                    payroll_records.id as payroll_id, payroll_records.refund_rata,
                    payroll_records.total_deductions, payroll_records.net_pay,
                    payroll_records.first_quincena, payroll_records.second_quincena')
             ->join('deductions', 'deductions.employee_id = employees.id', 'left')
             ->join('payroll_records', "payroll_records.employee_id = employees.id AND payroll_records.payroll_period = '{$period}'", 'left');

    $data['employees'] = $officeId ?
        $empModel->where('employees.office_id', $officeId)->findAll() :
        $empModel->findAll();

    $data['office_id'] = $officeId;

    return view('payroll/index', $data);
}

public function process($employee_id)
    {
        $empModel = new EmployeeModel();
        $deductModel = new DeductionModel();
        $payrollModel = new PayrollModel();

        $emp = $empModel->find($employee_id);

        if (!$emp) {
            return redirect()->to('/payroll')->with('error', 'Employee not found.');
        }

        $deduct = $deductModel->where('employee_id', $employee_id)->first();

        $deduct = $deduct ?? [
            'withholding_tax' => 0,
            'loans'           => 0,
            'government_cont' => 0,
            'other_deduct'    => 0,
            'gsis_premium'    => 0,
            'gsis_policy'     => 0,
            'gsis_other'      => 0,
            'pagibig_premium' => 0,
            'pagibig_loan'    => 0,
            'phic'            => 0,
            'bank_lbp'        => 0,
            'bank_mcc'        => 0,
            'bank_1stvb'      => 0,
        ];

        $refund_rata = (float) ($this->request->getVar('refund_rata') ?? 0);

        $gross_pay = $emp['salary_rate'];

        $total_deductions =
            ($deduct['withholding_tax'] ?? 0) +
            ($deduct['loans'] ?? 0) +
            ($deduct['government_cont'] ?? 0) +
            ($deduct['other_deduct'] ?? 0) +
            ($deduct['gsis_premium'] ?? 0) +
            ($deduct['gsis_policy'] ?? 0) +
            ($deduct['gsis_other'] ?? 0) +
            ($deduct['pagibig_premium'] ?? 0) +
            ($deduct['pagibig_loan'] ?? 0) +
            ($deduct['phic'] ?? 0) +
            ($deduct['bank_lbp'] ?? 0) +
            ($deduct['bank_mcc'] ?? 0) +
            ($deduct['bank_1stvb'] ?? 0);

        $net_pay = $gross_pay - $total_deductions;

        $first_quincena_input  = (float) ($this->request->getVar('first_quincena') ?? 0);
        $second_quincena_input = (float) ($this->request->getVar('second_quincena') ?? 0);

        if ($first_quincena_input > 0 && $second_quincena_input > 0) {
            $first_quincena  = round($first_quincena_input, 2);
            $second_quincena = round($second_quincena_input, 2);
        } else {
            $first_quincena  = round($net_pay / 2, 2);
            $second_quincena = round($net_pay - $first_quincena, 2);
        }

        $period = date('Y-m');

        $payrollData = [
            'employee_id'       => $employee_id,
            'payroll_period'    => $period,
            'period_of_service' => $this->request->getVar('period_of_service') ?? date('m/01/Y') . '-' . date('m/t/Y'),
            'refund_rata'       => $refund_rata,
            'gross_pay'         => $gross_pay,
            'total_deductions'  => $total_deductions,
            'net_pay'           => $net_pay,
            'first_quincena'    => $first_quincena,
            'second_quincena'   => $second_quincena,
            'cash_paid'         => $net_pay,
        ];

        $existing = $payrollModel->where('employee_id', $employee_id)
                                  ->where('payroll_period', $period)
                                  ->first();

        if ($existing) {
            $payrollModel->update($existing['id'], $payrollData);
        } else {
            $payrollModel->insert($payrollData);
        }

        return redirect()->to('/payroll')->with('success', 'Payroll calculated for ' . $emp['full_name']);
    }

    public function export()
    {
        $officeId = $this->request->getVar('office_id');
        $period = date('Y-m');

        $empModel = new EmployeeModel();
        $officeModel = new \App\Models\OfficeModel();

        $officeName = '';
        if ($officeId) {
            $office = $officeModel->find($officeId);
            $officeName = $office ? $office['office_name'] : 'Unknown Office';
        }

        $empModel->select('employees.*,
                            offices.office_name,
                            deductions.gsis_premium, deductions.gsis_policy, deductions.gsis_other,
                            deductions.pagibig_premium, deductions.pagibig_loan,
                            deductions.phic, deductions.withholding_tax,
                            deductions.loans, deductions.government_cont, deductions.other_deduct,
                            deductions.bank_lbp, deductions.bank_mcc, deductions.bank_1stvb,
                            payroll_records.refund_rata,
                            payroll_records.total_deductions, payroll_records.net_pay,
                            payroll_records.first_quincena, payroll_records.second_quincena,
                            payroll_records.cash_paid, payroll_records.gross_pay,
                            payroll_records.processed_at')
                 ->join('offices', 'offices.id = employees.office_id', 'left')
                 ->join('deductions', 'deductions.employee_id = employees.id', 'left')
                 ->join('payroll_records', "payroll_records.employee_id = employees.id AND payroll_records.payroll_period = '{$period}'", 'left');

        if ($officeId) {
            $empModel->where('employees.office_id', $officeId);
        }

        $employees = $empModel->orderBy('offices.office_name', 'ASC')->findAll();

        $spreadsheet = new \PhpOffice\PhpSpreadsheet\Spreadsheet();
        $sheet = $spreadsheet->getActiveSheet();

        $headers = [
            'A' => 'No.',
            'B' => 'Employee ID',
            'C' => 'Full Name',
            'D' => 'Office',
            'E' => 'Position',
            'F' => 'Monthly Rate',
            'G' => 'Refund/Rata',
            'H' => 'GSIS Premium',
            'I' => 'GSIS Policy',
            'J' => 'GSIS Other',
            'K' => 'PAG-IBIG Premium',
            'L' => 'PAG-IBIG Loan',
            'M' => 'PHIC',
            'N' => 'Bank LBP',
            'O' => 'Bank MCC',
            'P' => '1stVB',
            'Q' => 'BIR Tax',
            'R' => 'Loans',
            'S' => 'Govt. Contrib.',
            'T' => 'Other Deduct.',
            'U' => 'Total Deductions',
            'V' => 'Gross Pay',
            'W' => 'Net Pay',
            'X' => 'Cash Paid',
            'Y' => '1st Quincena',
            'Z' => '2nd Quincena',
        ];

        $sheet->setCellValue('A1', 'Payroll Export — ' . date('F Y'));
        $sheet->mergeCells('A1:Z1');
        $sheet->getStyle('A1')->getFont()->setBold(true)->setSize(14);
        $sheet->getStyle('A1')->getAlignment()->setHorizontal(\PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_CENTER);

        $row = 2;
        foreach ($headers as $col => $header) {
            $sheet->setCellValue($col . $row, $header);
        }

        $headerStyle = [
            'font' => ['bold' => true, 'color' => ['rgb' => 'FFFFFF']],
            'fill' => ['fillType' => \PhpOffice\PhpSpreadsheet\Style\Fill::FILL_SOLID, 'color' => ['rgb' => '0D2D27']],
            'alignment' => ['horizontal' => \PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_CENTER],
        ];
        $sheet->getStyle('A2:Z2')->applyFromArray($headerStyle);

        $row++;
        $no = 1;
        foreach ($employees as $emp) {
            $sheet->setCellValue('A' . $row, $no++);
            $sheet->setCellValue('B' . $row, $emp['employee_id']);
            $sheet->setCellValue('C' . $row, $emp['full_name']);
            $sheet->setCellValue('D' . $row, $emp['office_name'] ?? '-');
            $sheet->setCellValue('E' . $row, $emp['position'] ?? '-');
            $sheet->setCellValue('F' . $row, $emp['salary_rate']);
            $sheet->setCellValue('G' . $row, $emp['refund_rata'] ?? 0);
            $sheet->setCellValue('H' . $row, $emp['gsis_premium'] ?? 0);
            $sheet->setCellValue('I' . $row, $emp['gsis_policy'] ?? 0);
            $sheet->setCellValue('J' . $row, $emp['gsis_other'] ?? 0);
            $sheet->setCellValue('K' . $row, $emp['pagibig_premium'] ?? 0);
            $sheet->setCellValue('L' . $row, $emp['pagibig_loan'] ?? 0);
            $sheet->setCellValue('M' . $row, $emp['phic'] ?? 0);
            $sheet->setCellValue('N' . $row, $emp['bank_lbp'] ?? 0);
            $sheet->setCellValue('O' . $row, $emp['bank_mcc'] ?? 0);
            $sheet->setCellValue('P' . $row, $emp['bank_1stvb'] ?? 0);
            $sheet->setCellValue('Q' . $row, $emp['withholding_tax'] ?? 0);
            $sheet->setCellValue('R' . $row, $emp['loans'] ?? 0);
            $sheet->setCellValue('S' . $row, $emp['government_cont'] ?? 0);
            $sheet->setCellValue('T' . $row, $emp['other_deduct'] ?? 0);
            $sheet->setCellValue('U' . $row, $emp['total_deductions'] ?? 0);
            $sheet->setCellValue('V' . $row, $emp['gross_pay'] ?? $emp['salary_rate']);
            $sheet->setCellValue('W' . $row, $emp['net_pay'] ?? 0);
            $sheet->setCellValue('X' . $row, $emp['cash_paid'] ?? 0);
            $sheet->setCellValue('Y' . $row, $emp['first_quincena'] ?? 0);
            $sheet->setCellValue('Z' . $row, $emp['second_quincena'] ?? 0);
            $row++;
        }

        $lastDataRow = $row - 1;

        $currencyColumns = range('A', 'Z');
        foreach ($currencyColumns as $col) {
            if (in_array($col, ['A'])) continue;
            $sheet->getStyle($col . '2:' . $col . $lastDataRow)->getNumberFormat()
                ->setFormatCode('#,##0.00');
        }

        $sheet->getStyle('A2:A' . $lastDataRow)->getAlignment()->setHorizontal(\PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_CENTER);
        $sheet->getStyle('F2:Z' . $lastDataRow)->getAlignment()->setHorizontal(\PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_RIGHT);

        $sheet->freezePane('A3');
        $sheet->setAutoFilter('A2:Z' . $lastDataRow);

        foreach (range('A', 'Z') as $col) {
            $sheet->getColumnDimension($col)->setWidth(18);
        }
        $sheet->getColumnDimension('C')->setWidth(30);
        $sheet->getColumnDimension('E')->setWidth(22);
        $sheet->getColumnDimension('A')->setWidth(6);

        if ($lastDataRow >= 3) {
            $totalsRow = $lastDataRow + 1;
            $sheet->insertNewRowBefore($totalsRow, 1);
            $sheet->setCellValue('C' . $totalsRow, 'TOTAL');
            $sheet->getStyle('C' . $totalsRow)->getFont()->setBold(true);

            $sumCols = ['F', 'G', 'H', 'I', 'J', 'K', 'L', 'M', 'N', 'O', 'P', 'Q', 'R', 'S', 'T', 'U', 'V', 'W', 'X', 'Y', 'Z'];
            foreach ($sumCols as $col) {
                $sheet->setCellValue($col . $totalsRow, '=SUM(' . $col . '3:' . $col . $lastDataRow)');
            }
            $sheet->getStyle('F' . $totalsRow . ':Z' . $totalsRow)->getFont()->setBold(true);
            $sheet->getStyle('F' . $totalsRow . ':Z' . $totalsRow)->getNumberFormat()->setFormatCode('#,##0.00');
            $sheet->getStyle('A2:Z' . $totalsRow)->getBorders()->getAllBorders()->setBorderStyle(\PhpOffice\PhpSpreadsheet\Style\Border::BORDER_THIN);
        }

        $writer = new \PhpOffice\PhpSpreadsheet\Writer\Xlsx($spreadsheet);

        $filename = 'payroll_export_' . date('Y-m-d') . ($officeName ? '_' . preg_replace('/[^a-zA-Z0-9]/', '', $officeName) : '') . '.xlsx';

        header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
        header('Content-Disposition: attachment;filename="' . $filename . '"');
        header('Cache-Control: max-age=0');
        header('Cache-Control: max-age=1');
        header('Expires: 0');

        $writer->save('php://output');
        exit;
    }
}