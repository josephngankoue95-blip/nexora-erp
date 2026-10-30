<?php
namespace App\Models;
use Illuminate\Database\Eloquent\Model;
class SchoolSubject extends Model {
 protected $table = 'school_subjects';
 protected $fillable = ['company_id','section_id','subject_group_id','name','code','coefficient','is_active'];
 protected $casts = ['coefficient'=>'decimal:2','is_active'=>'boolean'];
 public function group(){ return $this->belongsTo(SchoolSubjectGroup::class, 'subject_group_id'); } public function assignments(){ return $this->hasMany(SchoolTeacherAssignment::class, 'subject_id'); }
}
