<?php

namespace App\Controllers;

class Dashboard extends BaseController
{
    public function index()
    {
        if (!session()->get('isLoggedIn')) return redirect()->to('/auth/login');

        $db = \Config\Database::connect();
        $period = date('Y-m');

        $officeRows = $db->table('offices')->select('id, office_name')->get()->getResultArray();
        $rataOfficeIds = [];
        foreach ($officeRows as $officeRow) {
            $norm = preg_replace('/[^A-Z0-9]/', '', strtoupper($officeRow['office_name'] ?? ''));
            if (strpos($norm, 'RATA') !== false) {
                $rataOfficeIds[] = (int)$officeRow['id'];
            }
        }

        $empCountQuery = $db->table('employees');
        if (!empty($rataOfficeIds)) {
            $empCountQuery->groupStart()
                ->whereNotIn('office_id', $rataOfficeIds)
                ->orWhere('office_id IS NULL')
                ->groupEnd();
        }
        $data['total_employees'] = $empCountQuery->countAllResults();

        $officeCountQuery = $db->table('offices');
        if (!empty($rataOfficeIds)) {
            $officeCountQuery->whereNotIn('id', $rataOfficeIds);
        }
        $data['total_offices'] = $officeCountQuery->countAllResults();

        $processedQuery = $db->table('payroll_records pr')
            ->join('employees e', 'e.id = pr.employee_id')
            ->where('pr.payroll_period', $period);
        if (!empty($rataOfficeIds)) {
            $processedQuery->groupStart()
                ->whereNotIn('e.office_id', $rataOfficeIds)
                ->orWhere('e.office_id IS NULL')
                ->groupEnd();
        }
        $processedCount = $processedQuery->countAllResults();
        $data['processed_payroll'] = $processedCount;
        $data['pending_payroll'] = max(0, $data['total_employees'] - $processedCount);

        $summary = $db->table('payroll_records')
            ->selectSum('gross_pay')
            ->selectSum('total_deductions')
            ->selectSum('net_pay')
            ->selectSum('refund_rata')
            ->selectSum('cash_paid')
            ->where('payroll_period', $period)
            ->get()
            ->getRow();

        $data['total_gross_pay'] = (float)($summary->gross_pay ?? 0);
        $data['total_deductions'] = (float)($summary->total_deductions ?? 0);
        $data['total_net_pay'] = (float)($summary->net_pay ?? 0);
        $data['total_refund_rata'] = (float)($summary->refund_rata ?? 0);
        $data['total_cash_paid'] = (float)($summary->cash_paid ?? 0);

        // All-Time Cumulative Analytics
        $allTimeSummary = $db->table('payroll_records')
            ->selectSum('gross_pay')
            ->selectSum('total_deductions')
            ->selectSum('net_pay')
            ->selectSum('cash_paid')
            ->select('COUNT(*) as total_records')
            ->get()
            ->getRow();

        $data['all_time_gross'] = (float)($allTimeSummary->gross_pay ?? 0);
        $data['all_time_deductions'] = (float)($allTimeSummary->total_deductions ?? 0);
        $data['all_time_net'] = (float)($allTimeSummary->net_pay ?? 0);
        $data['all_time_cash'] = (float)($allTimeSummary->cash_paid ?? 0);
        $data['all_time_records_count'] = (int)($allTimeSummary->total_records ?? 0);

        // Historical Monthly Payroll Trend (6-month sequence with active roster fallback)
        $monthlyTrend = [];
        $recordsByPeriod = [];
        $rawTrend = $db->table('payroll_records')
            ->select('payroll_period, SUM(gross_pay) as gross, SUM(total_deductions) as deductions, SUM(net_pay) as net', false)
            ->groupBy('payroll_period')
            ->get()
            ->getResultArray();

        foreach ($rawTrend as $r) {
            $recordsByPeriod[$r['payroll_period']] = $r;
        }

        $baseGrossRow = $db->table('employees')->where('is_active', 1)->selectSum('salary_rate')->get()->getRow();
        $baseGross = (float)($baseGrossRow->salary_rate ?? 0);
        
        $baseDeductRow = $db->table('deductions')
            ->select('SUM(gsis_premium + gsis_policy + gsis_other + gsis_ouli + gsis_diff +
                        pagibig_premium + pagibig_loan + pagibig_mp2 + phic + phic_diff +
                        bank_lbp + bank_other_payables + bank_mcc + bank_1stvb + bank_rbt +
                        withholding_tax + loans + government_cont + other_deduct) as total_deduct', false)
            ->get()
            ->getRow();
        $baseDeductions = (float)($baseDeductRow->total_deduct ?? 0);
        $baseNet = max(0, $baseGross - $baseDeductions);

        for ($i = 5; $i >= 0; $i--) {
            $periodKey = date('Y-m', strtotime("-{$i} month"));
            $periodLabel = date('M Y', strtotime("-{$i} month"));

            if (isset($recordsByPeriod[$periodKey])) {
                $rec = $recordsByPeriod[$periodKey];
                $monthlyTrend[] = [
                    'payroll_period' => $periodLabel,
                    'gross'          => (float) $rec['gross'],
                    'deductions'     => (float) $rec['deductions'],
                    'net'            => (float) $rec['net'],
                ];
            } else {
                $monthlyTrend[] = [
                    'payroll_period' => $periodLabel,
                    'gross'          => $baseGross,
                    'deductions'     => $baseDeductions,
                    'net'            => $baseNet,
                ];
            }
        }
        $data['monthly_trend'] = $monthlyTrend;

        $deductionFields = [
            'gsis_premium'    => 'GSIS Premium',
            'gsis_policy'     => 'GSIS Policy',
            'gsis_other'      => 'GSIS Other',
            'pagibig_premium' => 'PAG-IBIG Premium',
            'pagibig_loan'    => 'PAG-IBIG Loan',
            'phic'            => 'PHIC',
            'bank_lbp'        => 'Bank LBP',
            'bank_mcc'        => 'Bank MCC',
            'bank_1stvb'      => '1stVB',
            'withholding_tax' => 'BIR Tax',
            'loans'           => 'Loans',
            'government_cont' => 'Govt. Contrib.',
            'other_deduct'    => 'Other Deductions',
        ];

        $sumFields = implode(', ', array_map(function ($f) {
            return "SUM({$f}) AS {$f}";
        }, array_keys($deductionFields)));

        $deductRow = $db->table('deductions')->select($sumFields)->get()->getRow();

        $deductionsData = [];
        foreach ($deductionFields as $field => $label) {
            $deductionsData[] = [
                'label' => $label,
                'value' => (float)($deductRow->$field ?? 0),
            ];
        }
        $data['deductions_breakdown'] = $deductionsData;

        $payrollByOffice = $db->table('payroll_records pr')
            ->select('o.office_name, SUM(pr.gross_pay) as gross, SUM(pr.net_pay) as net, SUM(pr.total_deductions) as deductions', false)
            ->join('employees e', 'e.id = pr.employee_id')
            ->join('offices o', 'o.id = e.office_id', 'left')
            ->where('pr.payroll_period', $period)
            ->groupBy('o.office_name')
            ->orderBy('gross', 'DESC')
            ->get()
            ->getResultArray();
        $data['payroll_by_office'] = $payrollByOffice;

        $employeesByOfficeQuery = $db->table('employees e')
            ->select('o.office_name, COUNT(*) as count', false)
            ->join('offices o', 'o.id = e.office_id', 'left');
        if (!empty($rataOfficeIds)) {
            $employeesByOfficeQuery->groupStart()
                ->whereNotIn('e.office_id', $rataOfficeIds)
                ->orWhere('e.office_id IS NULL')
                ->groupEnd();
        }
        $employeesByOffice = $employeesByOfficeQuery
            ->groupBy('o.office_name')
            ->orderBy('count', 'DESC')
            ->get()
            ->getResultArray();
        $data['employees_by_office'] = $employeesByOffice;

        $recentPayroll = $db->table('payroll_records pr')
            ->select('pr.*, e.full_name, e.employee_id as emp_code, o.office_name')
            ->join('employees e', 'e.id = pr.employee_id')
            ->join('offices o', 'o.id = e.office_id', 'left')
            ->where('pr.payroll_period', $period)
            ->orderBy('pr.gross_pay', 'DESC')
            ->limit(10)
            ->get()
            ->getResultArray();
        $data['recent_payroll'] = $recentPayroll;

        $topDeductionsQuery = $db->table('employees e')
            ->select('e.id, e.employee_id as emp_code, e.full_name, o.office_name,
                        (d.gsis_premium + d.gsis_policy + d.gsis_other + d.pagibig_premium +
                         d.pagibig_loan + d.phic + d.bank_lbp + d.bank_mcc + d.bank_1stvb +
                         d.withholding_tax + d.loans + d.government_cont + d.other_deduct) as total_deduct', false)
            ->join('offices o', 'o.id = e.office_id', 'left')
            ->join('deductions d', 'd.employee_id = e.id', 'left')
            ->where('e.is_active', 1);
        if (!empty($rataOfficeIds)) {
            $topDeductionsQuery->groupStart()
                ->whereNotIn('e.office_id', $rataOfficeIds)
                ->orWhere('e.office_id IS NULL')
                ->groupEnd();
        }
        $topDeductions = $topDeductionsQuery
            ->having('total_deduct > 0')
            ->orderBy('total_deduct', 'DESC')
            ->limit(5)
            ->get()
            ->getResultArray();
        $data['top_deductions'] = $topDeductions;

        $empStatusQuery = $db->table('employees');
        if (!empty($rataOfficeIds)) {
            $empStatusQuery->groupStart()
                ->whereNotIn('office_id', $rataOfficeIds)
                ->orWhere('office_id IS NULL')
                ->groupEnd();
        }
        $employmentStatus = $empStatusQuery
            ->select('employment_status, COUNT(*) as count', false)
            ->groupBy('employment_status')
            ->get()
            ->getResultArray();
        $data['employment_status_counts'] = $employmentStatus;

        $avgSalaryQuery = $db->table('employees');
        if (!empty($rataOfficeIds)) {
            $avgSalaryQuery->groupStart()
                ->whereNotIn('office_id', $rataOfficeIds)
                ->orWhere('office_id IS NULL')
                ->groupEnd();
        }
        $avgSalary = $avgSalaryQuery
            ->selectAvg('salary_rate')
            ->get()
            ->getRow()
            ->salary_rate ?? 0;
        $data['avg_salary'] = (float)$avgSalary;

        $avgDeduction = $processedCount > 0
            ? ($data['total_deductions'] / $processedCount)
            : 0;
        $data['avg_deduction'] = $avgDeduction;

        $avgNetPay = $processedCount > 0
            ? ($data['total_net_pay'] / $processedCount)
            : 0;
        $data['avg_net_pay'] = $avgNetPay;

        $deductionRate = $data['total_gross_pay'] > 0
            ? ($data['total_deductions'] / $data['total_gross_pay'] * 100)
            : 0;
        $data['deduction_rate'] = round($deductionRate, 2);

        $payrollRate = $data['total_employees'] > 0
            ? ($processedCount / $data['total_employees'] * 100)
            : 0;
        $data['payroll_completion_rate'] = round($payrollRate, 2);

        return view('dashboard/index', $data);
    }
}
