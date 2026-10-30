<?php
namespace App\Services;

use App\Models\SchoolStudent;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

class SchoolStudentNumberService
{
    public function create(int $companyId, array $attributes): SchoolStudent
    {
        return DB::transaction(function () use ($companyId, $attributes) {
            unset($attributes['student_number'], $attributes['company_id']);
            $student = SchoolStudent::create(array_merge($attributes, [
                'company_id' => $companyId,
                'student_number' => 'TMP-'.Str::uuid(),
            ]));
            $student->student_number = sprintf('SC-%d-%06d', $companyId, $student->id);
            $student->save();
            return $student;
        });
    }
}
