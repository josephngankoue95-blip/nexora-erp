<?php
namespace App\Models;
use Illuminate\Database\Eloquent\Model;
class SchoolClassSubject extends Model {
 protected $fillable=['company_id','school_class_id','school_subject_id','school_subject_group_id','coefficient','sort_order','is_active'];
 protected $casts=['coefficient'=>'decimal:2','is_active'=>'boolean'];
 public function subject(){return $this->belongsTo(SchoolSubject::class,'school_subject_id');}
 public function group(){return $this->belongsTo(SchoolSubjectGroup::class,'school_subject_group_id');}
}
