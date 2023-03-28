<?php

namespace App\Models;

use App\Services\TagProcessor;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;

class UserPermissionTag extends Model
{
    use HasFactory;

    protected $fillable = [
        'model_id',
        'permission_id',
        'tags',
    ];

    public function scopeFilter($query, $filters, TagProcessor $tagProcessor)
    {
        $query = DB::table('coitxns')
            ->select(DB::raw("co.acad_group, co.acad_org, co.career, c.coi_id as reference_id, c.term, co.course as class, co.section, s.campus_id as student_no, to_char(coitxns.created_at, 'DD MON YYYY hh12:mi AM') as trx_date, coitxns.action as trx_status, u.email as last_commit, c.last_action, to_char(c.last_action_date, 'DD MON YYYY hh12:mi AM') as last_action_date"))
            ->join('cois AS c', 'coitxns.coi_id', '=', 'c.coi_id')
            ->join('students AS s', 's.sais_id', '=', 'c.sais_id')
            ->join('users AS u', 'u.sais_id', '=', 'coitxns.committed_by')
            ->join('course_offerings AS co', 'co.class_nbr', '=', 'c.class_id')
            ->join('student_program_records as spr', 's.campus_id', 'spr.campus_id')
            ->where('spr.status', 'ACTIVE');

        $query = $tagProcessor->process($query, $filters, "can view coi");
    }
}
