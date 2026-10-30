<?php

namespace App\Services\School;

use Illuminate\Support\Facades\DB;

class SchoolCouncilDecisions
{
    public function validatedCouncil(int $companyId, int $yearId, int $classId, string $kind, int $number): ?object
    {
        return DB::table('school_class_councils')
            ->where('company_id', $companyId)
            ->where('school_year_id', $yearId)
            ->where('school_class_id', $classId)
            ->where('period_kind', $kind)
            ->where('period_number', $kind === 'annual' ? 1 : $number)
            ->where('status', 'validated')->first();
    }

    public function forClass(int $companyId, int $yearId, int $classId, string $kind, int $number)
    {
        $council = $this->validatedCouncil($companyId, $yearId, $classId, $kind, $number);
        return $council
            ? DB::table('school_class_council_decisions')
                ->where('company_id', $companyId)
                ->where('school_class_council_id', $council->id)
                ->get()->keyBy('school_student_id')
            : collect();
    }

    public function forStudent(int $companyId, int $yearId, int $classId, int $studentId, string $kind, int $number): ?object
    {
        return $this->forClass($companyId, $yearId, $classId, $kind, $number)->get($studentId);
    }

    public function label(?string $decision): ?string
    {
        return [
            'felicitations' => 'Félicitations',
            'encouragements' => 'Encouragements',
            'tableau_honneur' => "Tableau d’honneur",
            'avertissement' => 'Avertissement',
            'blame' => 'Blâme',
            'aucune' => 'Sans distinction',
        ][$decision ?? ''] ?? null;
    }
}
