<?php

namespace App\Models;

use CodeIgniter\Model;

// NEW
class OfficeModel extends Model
{
    protected $table            = 'offices';
    protected $primaryKey       = 'id';
    protected $useAutoIncrement = true;
    protected $returnType       = 'array';
    protected $allowedFields    = ['office_name', 'description'];

    protected $validationRules = [
        'office_name' => 'required|is_unique[offices.office_name]'
    ];

    /**
     * Custom office ordering for dropdowns: fixed priority list first,
     * unmatched offices next, "rata" offices last, "VICE-SB rata" absolute last.
     */
    public function getOfficesOrdered()
    {
        $priority = [
            "MAYOR'S", 'VICE-SB', 'MCR', 'MPDC', "ENGR'NG", 'ACCTG.', 'MTO',
            'MASSO', 'MBO-MARKET', 'MHO', 'MAO', 'MBDO', 'LDRRMO', 'MSWD',
        ];

        $offices = $this->findAll();

        usort($offices, function ($a, $b) use ($priority) {
            return $this->officeSortRank($a['office_name'], $priority)
                 <=> $this->officeSortRank($b['office_name'], $priority);
        });

        return $offices;
    }

    private function officeSortRank($name, $priority)
    {
        $norm = $this->normalize($name);

        // "rata" offices go last, VICE-SB rata absolute last of all
        if (strpos($norm, 'RATA') !== false) {
            return strpos($norm, 'VICESB') !== false ? 9999 : 9000;
        }

        foreach ($priority as $i => $keyword) {
            if (strpos($norm, $this->normalize($keyword)) !== false) {
                return $i;
            }
        }

        return 500; // unmatched offices: after the known list, before rata group
    }

    /**
     * Strips punctuation/spaces and uppercases, so "ACCTG.", "ACCTG",
     * "ENGR'NG", "ENGRNG" etc. all compare equal regardless of formatting.
     */
    private function normalize($str)
    {
        return preg_replace('/[^A-Z0-9]/', '', strtoupper($str));
    }
}