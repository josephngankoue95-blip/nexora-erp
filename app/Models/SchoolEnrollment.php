<?php
namespace App\Models;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
class SchoolEnrollment extends Model {
 protected $fillable=['company_id','school_student_id','school_year_id','school_class_id','status','enrolled_on'];
 protected function casts(): array {return ['enrolled_on'=>'date'];}
 public function student(): BelongsTo {return $this->belongsTo(SchoolStudent::class,'school_student_id');}
 public function year(): BelongsTo {return $this->belongsTo(SchoolYear::class,'school_year_id');}
 public function schoolClass(): BelongsTo {return $this->belongsTo(SchoolClass::class,'school_class_id');}
}
