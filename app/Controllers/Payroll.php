<?php

namespace App\Controllers;

use App\Models\PayrollModel;
use App\Models\EmployeeModel;
use App\Models\DeductionModel;
use PhpOffice\PhpSpreadsheet\Style\Alignment;
use PhpOffice\PhpSpreadsheet\Style\Border;
use PhpOffice\PhpSpreadsheet\Style\Fill;
use PhpOffice\PhpSpreadsheet\Style\NumberFormat;

class Payroll extends BaseController
{
    // NOTE: these belong in a settings/config table long-term — hardcoded here
    // only because the source xls hardcodes them the same way and there's
    // nowhere else in the schema to pull them from yet.
    private const LGU_NAME        = 'LGU-MAHINOG';
    private const TREASURER_NAME  = 'MARY LUSSEL S. PACTO';
    private const TREASURER_TITLE = 'Disbursing Officer';
    private const MAYOR_NAME      = 'REY LAWRENCE K. TAN';
    private const MAYOR_TITLE     = 'Municipal Mayor';

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

    /**
     * Literal replica of the LGU-MAHINOG "Municipal Payroll" xls:
     * one sheet per office, acknowledgment header, grouped deduction
     * columns (GSIS / PAG-IBIG / PHIC / BANK's-COOP's / BIR W/T Tax),
     * a 4-sub-row block per employee, SUM-formula totals, and the
     * certification / approval / disbursing-officer signature footer.
     *
     * Simplification vs. the source file: the source repeats the header
     * and footer every ~6 employees for print pagination ("page 2" block
     * mid-sheet). This writes one continuous table per office instead —
     * numbers and layout match, just without that manual page-break repeat.
     */
    public function export()
    {
        $officeId = $this->request->getVar('office_id');
        $period   = date('Y-m');

        $empModel    = new EmployeeModel();
        $officeModel = new \App\Models\OfficeModel();

        $offices = $officeModel->getOfficesOrdered();
        if ($officeId) {
            $offices = array_values(array_filter($offices, fn ($o) => $o['id'] == $officeId));
        }

        $spreadsheet = new \PhpOffice\PhpSpreadsheet\Spreadsheet();
        $spreadsheet->removeSheetByIndex(0);

        $sheetIndex = 0;
        foreach ($offices as $office) {
            $employees = $empModel->select('employees.*,
                        deductions.gsis_premium, deductions.gsis_policy, deductions.gsis_other,
                        deductions.gsis_ouli, deductions.gsis_diff,
                        deductions.pagibig_premium, deductions.pagibig_loan, deductions.pagibig_mp2,
                        deductions.phic, deductions.phic_diff, deductions.withholding_tax,
                        deductions.bank_lbp, deductions.bank_other_payables, deductions.bank_mcc,
                        deductions.bank_1stvb, deductions.bank_rbt,
                        payroll_records.refund_rata, payroll_records.first_quincena,
                        payroll_records.second_quincena, payroll_records.net_pay')
                ->join('deductions', 'deductions.employee_id = employees.id', 'left')
                ->join('payroll_records', "payroll_records.employee_id = employees.id AND payroll_records.payroll_period = '{$period}'", 'left')
                ->where('employees.office_id', $office['id'])
                ->orderBy('employees.full_name', 'ASC')
                ->findAll();

            // Skip offices with nobody to pay this period — matches how the
            // source file only has a tab for offices that have a roster.
            if (empty($employees)) {
                continue;
            }

            $sheet = $spreadsheet->createSheet($sheetIndex++);
            $sheet->setTitle($this->safeSheetTitle($office['office_name']));

            $this->writeOfficeSheet($sheet, $office['office_name'], $employees, $period);
        }

        if ($sheetIndex === 0) {
            // Nothing to export — fall back to a single empty notice sheet
            $sheet = $spreadsheet->createSheet(0);
            $sheet->setTitle('Payroll');
            $sheet->setCellValue('A1', 'No employees with payroll data for ' . date('F Y') . '.');
        }

        $spreadsheet->setActiveSheetIndex(0);

        $writer = new \PhpOffice\PhpSpreadsheet\Writer\Xlsx($spreadsheet);

        $filename = 'municipal_payroll_' . date('Y-m') . ($officeId && !empty($offices) ? '_' . preg_replace('/[^a-zA-Z0-9]/', '', $offices[0]['office_name']) : '') . '.xlsx';

        header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
        header('Content-Disposition: attachment;filename="' . $filename . '"');
        header('Cache-Control: max-age=0');
        header('Expires: 0');

        $writer->save('php://output');
        exit;
    }

    private function safeSheetTitle(string $officeName): string
    {
        // Excel sheet names: max 31 chars, no : \ / ? * [ ]
        $title = preg_replace('/[:\\\\\/\?\*\[\]]/', '', $officeName);
        return mb_substr($title, 0, 31);
    }

    /**
     * Writes one full office roster onto $sheet, in the layout/style of the
     * source xls's office tabs.
     */
    private function writeOfficeSheet(
        \PhpOffice\PhpSpreadsheet\Worksheet\Worksheet $sheet,
        string $officeName,
        array $employees,
        string $period
    ): void {
        $arial = 'Arial';
        $currencyFmt = '_(* #,##0.00_);_(* (#,##0.00);_(* -??_);_(@_)';
        $thin = ['borderStyle' => Border::BORDER_THIN, 'color' => ['rgb' => '000000']];

        // ---- Page setup: landscape legal, matching the source ----
        $sheet->getPageSetup()
            ->setOrientation(\PhpOffice\PhpSpreadsheet\Worksheet\PageSetup::ORIENTATION_LANDSCAPE)
            ->setPaperSize(\PhpOffice\PhpSpreadsheet\Worksheet\PageSetup::PAPERSIZE_LEGAL);

        // ---- Column widths (A..T) ----
        $widths = [
            'A' => 4, 'B' => 22, 'C' => 16, 'D' => 10, 'E' => 10, 'F' => 10,
            'G' => 10, 'H' => 10, 'I' => 10, 'J' => 10, 'K' => 10, 'L' => 10,
            'M' => 10, 'N' => 10, 'O' => 10, 'P' => 10, 'Q' => 11, 'R' => 11,
            'S' => 12, 'T' => 13,
        ];
        foreach ($widths as $col => $w) {
            $sheet->getColumnDimension($col)->setWidth($w);
        }

        // ---- Title block ----
        $sheet->setCellValue('A1', self::LGU_NAME);
        $sheet->mergeCells('A1:T1');
        $sheet->setCellValue('A2', 'MUNICIPAL PAYROLL');
        $sheet->mergeCells('A2:T2');
        foreach (['A1', 'A2'] as $c) {
            $sheet->getStyle($c)->getFont()->setName($arial)->setSize(10)->setBold(true);
            $sheet->getStyle($c)->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);
        }

        $ack = sprintf(
            '           We hereby acknowledge to have received from %s. Treasurer of Mahinog, Camiguin the sums herein specified opposite our respective names, the same, being full compensation for our services rendered during the period stated below, to the correctness of which we hereby severally certify.',
            self::TREASURER_NAME
        );
        $sheet->setCellValue('A4', $ack);
        $sheet->mergeCells('A4:T4');
        $sheet->getStyle('A4')->getFont()->setName($arial)->setSize(9);
        $sheet->getStyle('A4')->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER)
            ->setVertical(Alignment::VERTICAL_CENTER)->setWrapText(true);
        $sheet->getRowDimension(4)->setRowHeight(30);

        // ---- Column header block (rows 6-11) ----
        $headerCells = [
            'A6' => 'No.', 'B6' => 'NAME', 'C6' => 'DESIGNATION',
            'G6' => 'DEDUCTIONS', 'Q6' => 'NET PAY',
            'S6' => 'Amount Paid in Cash', 'T6' => 'SIGNATURE',
            'F7' => 'REFUND', 'G7' => 'GSIS', 'J7' => 'PAG-IBIG',
            'L7' => 'PHIC', 'M7' => "BANK's / COOP's", 'P7' => 'BIR W/T Tax',
            'D8' => 'PERIOD ', 'E8' => 'MONTHLY', 'F8' => 'RATA',
            'G8' => 'PREMIUM', 'H8' => 'CONSO', 'I8' => 'GFAL',
            'J8' => 'PREMIUM', 'K8' => 'SALARY', 'L8' => 'PHIC',
            'M8' => 'LBP', 'N8' => 'MCC', 'O8' => '1stVB', 'R8' => '1st quincena',
            'D9' => 'OF', 'E9' => 'RATE OF', 'F9' => 'PERA  ACA',
            'G9' => '(Personal)', 'H9' => 'Policy', 'I9' => 'EMRGYLN',
            'J9' => '(Personal)', 'K9' => 'LOAN', 'M9' => 'Other Payables',
            'O9' => 'RBT', 'R9' => '2nd quincena',
            'D10' => 'SERVICE', 'E10' => 'PAY', 'G10' => 'OULI',
            'H10' => 'MPL', 'I10' => 'MPL LITE', 'K10' => 'MP2',
            'F11' => 'DIFFERENTIAL', 'G11' => 'DIFF-GSIS', 'I11' => 'CPL', 'L11' => 'PHIC-Diff',
        ];
        foreach ($headerCells as $coord => $val) {
            $sheet->setCellValue($coord, $val);
        }
        $sheet->mergeCells('A6:A11');
        $sheet->mergeCells('B6:B11');
        $sheet->mergeCells('C6:C11');
        $sheet->mergeCells('G6:P6');
        $sheet->mergeCells('Q6:Q11');
        $sheet->mergeCells('S6:S11');
        $sheet->mergeCells('T6:T11');
        $sheet->mergeCells('G7:I7');
        $sheet->mergeCells('J7:K7');
        $sheet->mergeCells('L7:L11');
        $sheet->mergeCells('M7:O7');
        $sheet->mergeCells('P7:P11');
        $sheet->mergeCells('F8:F10');

        $sheet->getStyle('A6:T11')->getFont()->setName($arial)->setSize(8)->setBold(true);
        $sheet->getStyle('A6:T11')->getAlignment()
            ->setHorizontal(Alignment::HORIZONTAL_CENTER)
            ->setVertical(Alignment::VERTICAL_CENTER)
            ->setWrapText(true);
        $sheet->getStyle('A6:T11')->getBorders()->getAllBorders()->applyFromArray($thin);

        $row = 12;
        $colNumbers = ['A'=>1,'B'=>2,'C'=>3,'D'=>4,'E'=>5,'F'=>6,'G'=>7,'H'=>8,'I'=>9,'J'=>10,
                       'K'=>11,'L'=>12,'M'=>14,'N'=>16,'O'=>17,'P'=>19,'Q'=>20,'R'=>21,'S'=>22,'T'=>23];
        foreach ($colNumbers as $col => $n) {
            $sheet->setCellValue($col . $row, $n);
        }
        $sheet->getStyle('A12:T12')->getFont()->setName($arial)->setSize(8)->setBold(true);
        $sheet->getStyle('A12:T12')->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);
        $sheet->getStyle('A12:T12')->getBorders()->getAllBorders()->applyFromArray($thin);

        // ---- Employee blocks (4 rows each) ----
        $startRow = 13;
        $r = $startRow;
        $quincenaRefs = [];
        $no = 1;

        foreach ($employees as $emp) {
            $r1 = $r;
            $r2 = $r + 1;
            $r3 = $r + 2;
            $r4 = $r + 3;

            $sheet->setCellValue("A{$r1}", $no++);
            $sheet->setCellValue("B{$r1}", $emp['full_name']);
            $sheet->setCellValue("C{$r1}", $emp['position']);
            $sheet->setCellValue("D{$r1}", date('m/01/Y') . '-' . date('m/t/Y'));
            $sheet->setCellValue("E{$r1}", (float) $emp['salary_rate']);

            $sheet->setCellValue("F{$r2}", (float) ($emp['refund_rata'] ?? 0));

            $sheet->setCellValue("G{$r1}", (float) ($emp['gsis_premium'] ?? 0));
            $sheet->setCellValue("G{$r3}", (float) ($emp['gsis_ouli'] ?? 0));
            $sheet->setCellValue("G{$r4}", (float) ($emp['gsis_diff'] ?? 0));
            $sheet->setCellValue("H{$r1}", (float) ($emp['gsis_policy'] ?? 0));
            $sheet->setCellValue("I{$r1}", (float) ($emp['gsis_other'] ?? 0));

            $sheet->setCellValue("J{$r1}", (float) ($emp['pagibig_premium'] ?? 0));
            $sheet->setCellValue("K{$r1}", (float) ($emp['pagibig_loan'] ?? 0));
            $sheet->setCellValue("K{$r3}", (float) ($emp['pagibig_mp2'] ?? 0));

            $sheet->setCellValue("L{$r1}", (float) ($emp['phic'] ?? 0));
            $sheet->setCellValue("L{$r4}", (float) ($emp['phic_diff'] ?? 0));

            $sheet->setCellValue("M{$r1}", (float) ($emp['bank_lbp'] ?? 0));
            $sheet->setCellValue("M{$r3}", (float) ($emp['bank_other_payables'] ?? 0));
            $sheet->setCellValue("N{$r1}", (float) ($emp['bank_mcc'] ?? 0));
            $sheet->setCellValue("O{$r1}", (float) ($emp['bank_1stvb'] ?? 0));
            $sheet->setCellValue("O{$r3}", (float) ($emp['bank_rbt'] ?? 0));

            $sheet->setCellValue("P{$r1}", (float) ($emp['withholding_tax'] ?? 0));

            // NET PAY = (Monthly + RATA) - all deduction columns across the block
            $sheet->setCellValue("Q{$r1}", "=SUM(E{$r1}:F{$r4})-SUM(G{$r1}:P{$r4})");
            $sheet->mergeCells("Q{$r1}:Q{$r4}");

            $sheet->setCellValue("R{$r1}", (float) ($emp['first_quincena'] ?? 0) ?: "=ROUND(Q{$r1}/2,2)");
            $sheet->setCellValue("R{$r2}", (float) ($emp['second_quincena'] ?? 0) ?: "=Q{$r1}-R{$r1}");
            $quincenaRefs[] = ["R{$r1}", "R{$r2}", "Q{$r1}"];

            $sheet->setCellValue("T{$r1}", $emp['contact_number'] ?? '');

            // Row/column merges within the block
            foreach (['A', 'B', 'C', 'D', 'E'] as $col) {
                $sheet->mergeCells("{$col}{$r1}:{$col}{$r4}");
            }
            $sheet->mergeCells("S{$r1}:S{$r4}");
            $sheet->mergeCells("T{$r1}:T{$r4}");

            $sheet->getStyle("A{$r1}:T{$r4}")->getFont()->setName($arial)->setSize(8);
            $sheet->getStyle("A{$r1}:C{$r4}")->getAlignment()
                ->setHorizontal(Alignment::HORIZONTAL_CENTER)->setVertical(Alignment::VERTICAL_CENTER)->setWrapText(true);
            $sheet->getStyle("D{$r1}:T{$r4}")->getAlignment()
                ->setHorizontal(Alignment::HORIZONTAL_CENTER)->setVertical(Alignment::VERTICAL_CENTER);
            $sheet->getStyle("E{$r1}:R{$r4}")->getNumberFormat()->setFormatCode($currencyFmt);
            // Outline only — one rectangle per column across its 4-row span,
            // not a full internal grid. This is what keeps the sheet's lines
            // as light as the source instead of the denser look a full
            // per-cell border produces.
            foreach (['A','B','C','D','E','F','G','H','I','J','K','L','M','N','O','P','Q','R','S','T'] as $col) {
                $sheet->getStyle("{$col}{$r1}:{$col}{$r4}")->getBorders()->getOutline()->applyFromArray($thin);
            }

            $r = $r4 + 1;
        }

        $lastEmployeeRow = $r - 1;

        // ---- 1st / 2nd Quincena + Totals ----
        $q1Row = $r++;
        $q2Row = $r++;
        $totalRow = $r++;

        $sheet->setCellValue("Q{$q1Row}", '1st Quincena');
        $sheet->setCellValue("R{$q1Row}", '=' . implode('+', array_column($quincenaRefs, 0)));
        $sheet->setCellValue("Q{$q2Row}", '2nd Quincena');
        $sheet->setCellValue("R{$q2Row}", '=' . implode('+', array_column($quincenaRefs, 1)));

        $sheet->setCellValue("A{$totalRow}", 'Total');
        foreach (['E','F','G','H','I','J','K','L','M','N','O','P'] as $col) {
            $sheet->setCellValue("{$col}{$totalRow}", "=SUM({$col}{$startRow}:{$col}{$lastEmployeeRow})");
        }
        $sheet->setCellValue("Q{$totalRow}", '=' . implode('+', array_column($quincenaRefs, 2)));
        $sheet->setCellValue("R{$totalRow}", "=R{$q1Row}+R{$q2Row}");

        $sheet->getStyle("A{$totalRow}")->getFont()->setBold(true);
        $sheet->getStyle("Q{$q1Row}:R{$totalRow}")->getFont()->setName($arial)->setSize(8)->setBold(true);
        $sheet->getStyle("E{$totalRow}:R{$totalRow}")->getFont()->setBold(true);
        $sheet->getStyle("E{$totalRow}:R{$totalRow}")->getNumberFormat()->setFormatCode($currencyFmt);
        $sheet->getStyle("A{$totalRow}:T{$totalRow}")->getBorders()->getAllBorders()->applyFromArray($thin);

        // ---- Certification / approval / signature footer ----
        $footerRow = $totalRow + 2;
        $sheet->setCellValue("B{$footerRow}", '(1)  I HEREBY CERTIFY on my official oath that the above PAYROLL is correct, and that services above stated have been duly rendered.  Payment for such services is also hereby approved from the appropriation indicated.');
        $sheet->setCellValue("J{$footerRow}", '(4) APPROVED:');
        $sheet->setCellValue("M{$footerRow}", '(5) I HEREBY CERTIFY on my official oath that I have paid in cash to each official and employee whose name appears on the above roll the amount set opposite his name, under column 17, the having signed or marked his name under column 20 above, in my presence and at the time that payment was made to him in acknowledgement of receipt of the money paid him.');
        $sheet->mergeCells("B{$footerRow}:I{$footerRow}");
        $sheet->mergeCells("M{$footerRow}:T{$footerRow}");
        $sheet->getStyle("B{$footerRow}:T{$footerRow}")->getFont()->setName($arial)->setSize(8);
        $sheet->getStyle("B{$footerRow}:T{$footerRow}")->getAlignment()->setWrapText(true)->setVertical(Alignment::VERTICAL_TOP);
        $sheet->getRowDimension($footerRow)->setRowHeight(45);

        $signRow1 = $footerRow + 1;
        $signRow2 = $footerRow + 2;
        $sheet->setCellValue("A{$signRow1}", 'Date: ' . date('F d, Y'));
        $sheet->setCellValue("C{$signRow1}", self::MAYOR_NAME);
        $sheet->setCellValue("J{$signRow1}", self::MAYOR_NAME);
        $sheet->setCellValue("Q{$signRow1}", self::TREASURER_NAME);
        $sheet->setCellValue("C{$signRow2}", self::MAYOR_TITLE);
        $sheet->setCellValue("J{$signRow2}", self::MAYOR_TITLE);
        $sheet->setCellValue("Q{$signRow2}", self::TREASURER_TITLE);

        $sheet->getStyle("A{$signRow1}:T{$signRow2}")->getFont()->setName($arial)->setSize(8)->setBold(true);
        $sheet->getStyle("C{$signRow1}:C{$signRow2}")->getBorders()->getTop()->applyFromArray($thin);
        $sheet->getStyle("J{$signRow1}:K{$signRow2}")->getBorders()->getTop()->applyFromArray($thin);
        $sheet->getStyle("Q{$signRow1}:S{$signRow2}")->getBorders()->getTop()->applyFromArray($thin);
        $sheet->getStyle("C{$signRow1}:T{$signRow2}")->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);
    }

}