<?php
namespace App\Models;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
class SchoolStudentFee extends Model {
 protected $fillable=['company_id','school_enrollment_id','school_fee_structure_id','registration_fee','installment_1','installment_2','installment_3'];
 public function enrollment(): BelongsTo {return $this->belongsTo(SchoolEnrollment::class,'school_enrollment_id');}
 public function payments(): HasMany {return $this->hasMany(SchoolPayment::class,'school_student_fee_id');}
}
