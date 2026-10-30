<?php
namespace App\Models;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
class SchoolPayment extends Model {
 protected $fillable=['company_id','school_student_fee_id','reference','amount','fee_type','method','paid_on','external_reference','notes'];
 protected function casts(): array {return ['paid_on'=>'date'];}
 public function studentFee(): BelongsTo {return $this->belongsTo(SchoolStudentFee::class,'school_student_fee_id');}
}
