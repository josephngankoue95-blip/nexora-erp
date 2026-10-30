<?php
namespace App\Models;
use Illuminate\Database\Eloquent\Model;
class SchoolSubjectGroup extends Model {
 protected $table = 'school_subject_groups';
 protected $fillable = ['company_id','section_id','name','code','description'];
 protected $casts = [];
 public function subjects(){ return $this->hasMany(SchoolSubject::class, 'subject_group_id'); }
}
