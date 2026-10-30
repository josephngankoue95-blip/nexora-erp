<?php
namespace App\Models;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
class SchoolFeeStructure extends Model {
 protected $fillable=['company_id','school_year_id','school_level_id','registration_fee','installment_1','installment_2','installment_3'];
 public function year(): BelongsTo {return $this->belongsTo(SchoolYear::class,'school_year_id');}
 public function level(): BelongsTo {return $this->belongsTo(SchoolLevel::class,'school_level_id');}
}
