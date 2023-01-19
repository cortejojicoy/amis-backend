<?php
namespace App\Services;

use App\Exports\ExcelExport;
use App\Models\Coi;
use App\Models\Prerog;
use App\Models\StudentTerm;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;
use Maatwebsite\Excel\Facades\Excel;

class DownloadModule{
    function downloadCoi($request){
        $toBeExcluded = ['AMIS 500', 'ABT 190','AERS 190','AGR 190','ANSC 190','ENT 190','FST 190','HORT 190','PPTH 190','WSC 190','AERS 191','ANSC 191','ABT 198','AERS 198','AGR 198','ANSC 198','ASYS 198','ENT 198','FST 198','HORT 198','LAF 198','PPTH 198','WSC 198','ABT 199','AERS 199','AGR 199','AGRI 199','ANSC 199','ASYS 199','CRSC 199','ENT 199','FST 199','HORT 199','LAF 199','PPTH 199','SOIL 199','WSC 199','ENT 200A','ANSC 200A','PPTH 200','WSC 200A','ABT 200A','HORT 200A','AERS 200','AGR 200','SOIL 200A','HORT 200','ENT 200','FST 200','LAF 200A','AGR 200A','LAF 200','ASYS 200','AERS 200A','WSC 200','ASYS 200A','ABT 200','ANSC 200','SOIL 200','FOR 200A','PPT 200','FOR 200','PPT 200A','VMED 156','VMCB 124'];

        $active_term = StudentTerm::where('status', 'ACTIVE')->first();

        if($request->type == 'ocs') {
            $downloadData = DB::table('cois AS c')
            ->select(DB::raw("c.coi_id, co.term, co.acad_group, co.subject, co.catalog, co.section, c.class_id, u.sais_id, s.campus_id, spr.acad_group as student_college, u.last_name, u.first_name, u.middle_name, u.email, co.offer_nbr, c.created_at"))
            ->join('students AS s', 's.sais_id', '=', 'c.sais_id')
            ->join('student_program_records AS spr', 'spr.campus_id', '=', 's.campus_id')
            ->join('users AS u', 's.sais_id', '=', 'u.sais_id')
            ->join('course_offerings AS co', 'co.class_nbr', '=', 'c.class_id')
            ->where(function($query) use ($toBeExcluded, $active_term){
                $query->where('c.status', 'Approved');
                $query->where('c.last_action', NULL);
                $query->where('c.term', $active_term->term_id);
                $query->where('spr.status', '=', 'ACTIVE');
                $query->where('co.term', $active_term->term_id);
                $query->whereIn('co.course', $toBeExcluded);
            })
            ->orWhere(function($query) use ($active_term) {
                $query->where('c.status', 'Approved');
                $query->where('c.last_action', NULL);
                $query->where('c.term', $active_term->term_id);
                $query->where('spr.status', '=', 'ACTIVE');
                $query->where('spr.acad_group', '=', 'CAFS');
                $query->where('co.term', $active_term->term_id);
                $query->where('co.acad_group', '=', 'CAFS');
            })
            ->get()
            ->toArray();
            
            $headers = [
                'Reference ID',
                'Term',
                'College',
                'Subject',
                'Catalog',
                'Section',
                'Class Number',
                'SAIS ID',
                'Student Number',
                'Student College',
                'Last Name',
                'First Name',
                'Middle Name',
                'Email',
                'Offer Number',
                'Created Date'
            ];
        } else if($request->type == 'sais') {
            $downloadData = DB::table('cois AS c')
            ->select(DB::raw("c.coi_id, co.institution, co.term, co.subject, co.catalog, co.career, co.class_nbr, co.offer_nbr, u.sais_id"))
            ->join('users AS u', 'c.sais_id', '=', 'u.sais_id')
            ->join('course_offerings AS co', 'co.class_nbr', '=', 'c.class_id')
            ->where('c.status', 'Approved')
            ->where('c.last_action', NULL)
            ->where('c.term', $active_term->term_id)
            ->where('co.term', $active_term->term_id)
            ->whereNotIn('co.course', $toBeExcluded)
            ->get()
            ->toArray();

            $headers = [
                'Reference ID',
                'Institution',
                'Term',
                'Subject',
                'Catalog',
                'Career',
                'Class Number',
                'Offer Number',
                'SAIS ID'
            ];
        }

        DB::beginTransaction();

        try {

            $dt = Carbon::now('Asia/Manila');

            $last_action = $request->type == 'ocs' ? 'coi_ocs_approved' : "coi_sais_approved";
            $filename = $last_action . '-' . $dt->format('Y-m-d_H-i-s') . '.xlsx';

            //export transaction_history
            $export = new ExcelExport($downloadData, $headers);

            //store the created file in storage/app
            Excel::store($export, $filename);

            if($request->type == 'ocs') {
                foreach($downloadData as $dd) {
                    Coi::find($dd->coi_id)->update(['last_action' => 'For OCS Evaluation', 'last_action_date' => Carbon::now('Asia/Manila')]);
                }
            } else if($request->type == 'sais') {
                foreach($downloadData as $dd) {
                    Coi::find($dd->coi_id)->update(['last_action' => 'Sent to SAIS', 'last_action_date' => Carbon::now('Asia/Manila')]);
                }
            }

            DB::commit();

            //download the file
            return $filename;

        } catch (\Exception $ex) {
            //if there is an error, rollback to previous state of db before beginTransaction
            DB::rollback();

            //return error
            return response()->json(
                [
                    'message' => $ex->getMessage()
                ], 500
            );
        }
    }

    function downloadPrerog() {
        $downloadData = DB::table('prerogs AS prg')
            ->select(DB::raw("prg.prg_id, co.institution, co.term, co.subject, co.catalog, co.career, co.class_nbr, co.offer_nbr, u.sais_id"))
            ->join('users AS u', 'prg.sais_id', '=', 'u.sais_id')
            ->join('course_offerings AS co', 'co.class_nbr', '=', 'prg.class_id')
            ->where('prg.status', 'Approved by FIC')
            ->where('prg.submitted_to_sais', NULL)
            ->get()
            ->toArray();

        $headers = [
            'Reference ID',
            'Institution',
            'Term',
            'Subject',
            'Catalog',
            'Career',
            'Class Number',
            'Offer Number',
            'SAIS ID'
        ];

        DB::beginTransaction();

        try {
            $dt = Carbon::now('Asia/Manila');

            $filename = "prerog_sais_approved" . '-' . $dt->format('Y-m-d_H-i-s') . '.xlsx';

            $export = new ExcelExport($downloadData, $headers);

            //store the created file in storage/app
            Excel::store($export, $filename);

            foreach($downloadData as $dd) {
                Prerog::find($dd->prg_id)->update(['submitted_to_sais' => Carbon::now('Asia/Manila')]);
            }

            DB::commit();

            //download the file
            return $filename;

        } catch (\Exception $ex) {
            //if there is an error, rollback to previous state of db before beginTransaction
            DB::rollback();

            //return error
            return response()->json(
                [
                    'message' => $ex->getMessage()
                ], 500
            );
        }
    }
}