<?php

namespace Modules\BehaviourRecords\Http\Controllers;

use App\Models\SmAcademicYear;
use App\Models\SmClass;
use App\Models\SmStudent;
use App\Models\StudentRecord;
use Brian2694\Toastr\Facades\Toastr;
use DataTables;
use Exception;
use Illuminate\Http\Request;
use Illuminate\Routing\Controller;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;
use Modules\BehaviourRecords\Entities\AssignIncident;
use Modules\BehaviourRecords\Entities\BehaviourRecordSetting;
use Modules\BehaviourRecords\Entities\Incident;

class BehaviourRecordsController extends Controller
{
    // assign incident
    public function assignIncident()
    {
        try {
            $classes = branchWise(SmClass::where('active_status', 1)
                ->where('academic_id', getAcademicId())
                ->where('school_id', Auth::user()->school_id)
                ->get());

            $students = branchWise(SmStudent::where('academic_id', getAcademicId())
                ->where('school_id', Auth::user()->school_id)
                ->get());

            $sessions = SmAcademicYear::where('active_status', 1)
                ->where('school_id', Auth::user()->school_id)
                ->get();
            $incidents = branchWise(Incident::get());

            return view('behaviourrecords::assignIncident.assignIncident', ['classes' => $classes, 'students' => $students, 'sessions' => $sessions, 'incidents' => $incidents]);
        } catch (Exception $exception) {
            Toastr::error('Operation Failed', 'Failed');

            return redirect()->back();
        }
    }

    public function assignIncidentSearch(Request $request)
    {
        if (! moduleStatusCheck('University')) {
            $validator = Validator::make($request->all(), [
                'academic_year' => 'required',
            ]);
            if ($validator->fails()) {
                Toastr::error('Validation Failed', 'Failed');

                return redirect()->route('behaviour_records.assign-incident')->withErrors($validator)->withInput();
            }
        }

        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $classes = branchWise(SmClass::where('active_status', 1)->where('academic_id', getAcademicId())->where('school_id', Auth::user()->school_id)->withoutGlobalScope(StatusAcademicSchoolScope::class)->get());
            $sessions = SmAcademicYear::where('school_id', Auth::user()->school_id)->get();
            $academic_year = $request->academic_year;
            $class_id = $request->class_id;
            $name = $request->name;
            $roll_no = $request->roll_no;
            $section = $request->section_id;
            $shift_id = shiftEnable() ? $request->shift : null;
            $un_session_id = $request->un_session_id;
            $un_academic_id = $request->un_academic_id;
            $un_faculty_id = $request->un_faculty_id;
            $un_department_id = $request->un_department_id;
            $un_semester_id = $request->un_semester_id;
            $un_semester_label_id = $request->un_semester_label_id;
            $un_section_id = $request->un_section_id;

            $data['academic_year'] = $request->academic_year;
            $data['class_id'] = $request->class_id;
            $data['section_id'] = $request->section_id;
            $data['shift_id'] = $request->shift;
            $data['name'] = $request->name;
            $data['roll_no'] = $request->roll_no;
            $data['branch_id'] = $request->branch_id ?? null;

            $incidents = branchWise(Incident::get());

            return view('behaviourrecords::assignIncident.assignIncident', ['classes' => $classes, 'class_id' => $class_id, 'name' => $name, 'roll_no' => $roll_no, 'sessions' => $sessions, 'section' => $section, 'shift_id' => $shift_id, 'academic_year' => $academic_year, 'data' => $data, 'incidents' => $incidents, 'un_session_id' => $un_session_id, 'un_faculty_id' => $un_faculty_id, 'un_department_id' => $un_department_id, 'un_academic_id' => $un_academic_id, 'un_semester_id' => $un_semester_id, 'un_semester_label_id' => $un_semester_label_id, 'un_section_id' => $un_section_id]);
        }

        return null;
    }

    public function assignIncidentDatatable(Request $request)
    {
        $records = branchWise(StudentRecord::query());
        $records->where('is_promote', 0)->where('school_id', auth()->user()->school_id);

        $records->when(moduleStatusCheck('University') && $request->filled('un_academic_id'), function ($u_query) use ($request) {
            $u_query->where('un_academic_id', $request->un_academic_id);
        }, function ($query) use ($request) {
            $query->when($request->academic_year, function ($query) use ($request) {
                $query->where('academic_id', $request->academic_year);
            });
        })
            ->when($request->shift_id, function ($query) use ($request) {
                $query->where('shift_id', $request->shift_id);
            })
            ->when(moduleStatusCheck('University') && $request->filled('un_faculty_id'), function ($u_query) use ($request) {
                $u_query->where('un_faculty_id', $request->un_faculty_id);
            }, function ($query) use ($request) {
                $query->when($request->class, function ($query) use ($request) {
                    $query->where('class_id', $request->class);
                });
            })
            ->when(moduleStatusCheck('University') && $request->filled('un_department_id'), function ($u_query) use ($request) {
                $u_query->where('un_department_id', $request->un_department_id);
            }, function ($query) use ($request) {
                $query->when($request->section, function ($query) use ($request) {
                    $query->where('section_id', $request->section);
                });
            })
            ->when(! $request->academic_year && moduleStatusCheck('University') === false, function ($query) {
                $query->where('academic_id', getAcademicId());
            })
            ->when(moduleStatusCheck('University') && $request->filled('un_session_id'), function ($query) use ($request) {
                $query->where('un_session_id', $request->un_session_id);
            })
            ->when(moduleStatusCheck('University') && $request->filled('un_semester_label_id'), function ($query) use ($request) {
                $query->where('un_semester_label_id', $request->un_semester_label_id);
            });

        $roll_no = $request->roll_no;

        if (generalSetting()->multiple_roll && $roll_no) {
            $records->where('roll_no', 'like', '%'.$roll_no.'%');
        }

        if (moduleStatusCheck('Branch') && $request->filled('branch_id')) {
            $records->where('branch_id', $request->branch_id);
        }

        $student_records = $records->where('is_promote', 0)->whereHas('student')->get(['student_id'])->unique('student_id')->toArray();


        $relations =[
            'studentRecords',
            'studentRecords.class',
            'studentRecords.section',
            'incidents',
            'parents:id,fathers_name',
            'gender:id,base_setup_name',
            'category:id,category_name',
        ];
        if (moduleStatusCheck('Branch')){
            $relations[] = 'branch';
        }

        $all_students = SmStudent::with($relations)
            ->withCount('incidents')
            ->withSum('incidents', 'point')
            ->whereIn('id', $student_records)
            ->where('active_status', 1)
            ->when($request->name, function ($query) use ($request) {
                $query->where('full_name', 'like', '%'.$request->name.'%');
            });

        if (! generalSetting()->multiple_roll && $roll_no) {
            $all_students->where('roll_no', 'like', '%'.$roll_no.'%');
        }

        return DataTables::of($all_students)
            ->addIndexColumn()
            ->addColumn('branch_name', function ($row) {
                if (moduleStatusCheck('Branch')){
                    return $row->branch->branch_name ?? '-';
                }else{
                    return '';
                }
            })
            ->addColumn('full_name', function ($row) {
                return '<a target="_blank" href="'.route('student_view', [$row->id]).'">'.$row->first_name.' '.$row->last_name.'</a>';
            })
            ->addColumn('mobile', function ($row) {
                return '<a href="tel:'.$row->mobile.'">'.$row->mobile.'</a>';
            })
            ->addColumn('class_sec', function ($row) {
                $class_sec = [];
                foreach ($row->studentRecords as $classSec) {
                    if (moduleStatusCheck('University')) {
                        $class_sec[] = $classSec->unFaculty->name.'('.$classSec->unDepartment->name.')';
                    } else {
                        if (shiftEnable()) {
                            $class_sec[] = $classSec->class->class_name.'('.$classSec->section->section_name.')'.'['.$classSec?->shift?->name.']';
                        } else {
                            $class_sec[] = $classSec->class->class_name.'('.$classSec->section->section_name.')';
                        }
                    }
                }

                return implode(', ', $class_sec);
            })
            ->addColumn('action', function ($row) {
                return (string) view('behaviourrecords::assignIncident.assignIncidentAction', ['row' => $row]);
            })
            ->addColumn('incidents_sum_point', function ($row) {
                return $row->incidents->sum('point');
            })

            ->rawColumns(['action', 'full_name', 'mobile', 'class_sec'])
            ->make(true);
    }

    public function assignIncidentSave(Request $request)
    {
        try {
            foreach ($request->incident_ids as $incident_id) {
                $incident = Incident::find($incident_id);
                if ($incident) {
                    $assignIncident = new AssignIncident();
                    $assignIncident->point = $incident->point;
                    $assignIncident->incident_id = $incident_id;
                    $assignIncident->student_id = $request->student_id;
                    $assignIncident->record_id = $request->record_id;
                    $assignIncident->added_by = Auth::user()->id;
                    $assignIncident->academic_id = getAcademicId();
                    if (moduleStatusCheck('Branch')) {
                        $assignIncident->branch_id = $incident->branch_id;
                    }
                    $assignIncident->save();
                }
            }

            return response()->json(['message' => 'Successful']);
        } catch (Exception $exception) {
            return response()->json(['message' => $exception->getMessage()]);
        }
    }

    public function assignIncidentDelete($id)
    {
        try {
            $assignIncidentDelete = AssignIncident::find($id);
            $assignIncidentDelete->delete();

            return response()->json(['message' => 'Successful']);
        } catch (Exception $exception) {
            return response()->json(['message' => $exception->getMessage()]);
        }
    }

    public function getStudentIncident(Request $request)
    {
        $student = SmStudent::find($request->studentId);

        return view('behaviourrecords::assignIncident.assign_incident_list', ['student' => $student]);
    }

    // incident
    public function incident()
    {
        try {
            $incidents = branchWise(Incident::get());

            return view('behaviourrecords::incidents.incident', ['incidents' => $incidents]);
        } catch (Exception $exception) {
            Toastr::error('Operation Failed', 'Failed');

            return redirect()->back();
        }
    }

    public function incidentCreate(Request $request)
    {
        $input = $request->all();
        $validator = Validator::make($input, [
            'title' => 'required',
            'point' => 'required',
        ]);
        if ($validator->fails()) {
            Toastr::error('Empty Submission', 'Failed');

            return redirect()->back()->withErrors($validator)->withInput();
        }

        try {
            $incident = new Incident();
            $incident->title = $request->title;
            $incident->point = $request->negativePoint == 1 ? -$request->point : $request->point;
            if (moduleStatusCheck('Branch')) {
                $incident->branch_id = $request->branch_id;
            }
            $incident->description = $request->description;
            $incident->save();

            return redirect()->route('behaviour_records.incident');
        } catch (Exception $exception) {
            Toastr::error('Operation Failed', 'Failed');

            return redirect()->back();
        }
    }

    public function incidentUpdate(Request $request)
    {
        try {
            $incidentUpdate = Incident::find($request->id);
            $incidentUpdate->title = $request->title;
            $incidentUpdate->point = $request->editNegativePoint == 1 ? -$request->point : $request->point;
            if (moduleStatusCheck('Branch')) {
                $incidentUpdate->branch_id = $request->branch_id;
            }
            $incidentUpdate->description = $request->description;
            $incidentUpdate->save();

            return redirect()->route('behaviour_records.incident');
        } catch (Exception $exception) {
            Toastr::error('Operation Failed', 'Failed');

            return redirect()->back();
        }
    }

    public function incidentDelete(Request $request, $id)
    {
        try {
            $incidentDelete = Incident::where('id', $id)->first();
            $incidentDelete->destroy($request->id);
            Toastr::success('Deleted successfully', 'Success');

            return redirect()->route('behaviour_records.incident');
        } catch (Exception $exception) {
            Toastr::error('Operation Failed', 'Failed');

            return redirect()->back();
        }
    }

    // report
    public function studentIncidentReport()
    {
        try {
            $classes = branchWise(SmClass::get());
            $sessions = SmAcademicYear::where('school_id', Auth::user()->school_id)->get();

            return view('behaviourrecords::reports.student_incident_report', ['classes' => $classes, 'sessions' => $sessions]);
        } catch (Exception $exception) {
            Toastr::error('Operation Failed', 'Failed');

            return redirect()->back();
        }
    }

    public function studentIncidentReportSearch(Request $request)
    {
        $input = $request->all();
        $branchModuleAvailable = moduleStatusCheck('Branch') && class_exists('Modules\\Branch\\Entities\\Branch');

        $validator = Validator::make($input, [
            'academic_year' => 'required',
            'class_id' => 'required',
            'section_id' => 'required',
        ]);
        if ($validator->fails()) {
            return redirect()->back()->withErrors($validator)->withInput();
        }
        $branch_id = $branchModuleAvailable ? ($request->branch_id ?: userBranch()) : null;
        try {
            if (moduleStatusCheck('University')) {
                $classes = SmClass::get();
                $sessions = SmAcademicYear::where('school_id', Auth::user()->school_id)->get();
                $relations = ['student', 'student.gender', 'incidents.incident', 'unDepartment:id,name', 'unFaculty:id,name'];
                if ($branchModuleAvailable) {
                    $relations[] = 'branch';
                }
                $student_records = StudentRecord::with($relations)
                    ->withCount('incidents')
                    ->withSum(['incidents as incidents_positive_point' => function ($query) {
                        $query->where('incident_id', 1);
                    }], 'point')
                    ->withSum(['incidents as incidents_negative_point' => function ($query) {
                        $query->where('incident_id', 2);
                    }], 'point')
                    ->where('active_status', 1)
                    ->where('un_faculty_id', $request->un_faculty_id)
                    ->where('un_academic_id', $request->un_academic_id)
                    ->where('un_section_id', $request->un_section_id)
                    ->where('un_department_id', $request->un_department_id)
                    ->where('un_semester_id', $request->un_semester_id)
                    ->where('un_semester_label_id', $request->un_semester_label_id)
                    ->where('un_session_id', $request->un_session_id)
                    ->when($branchModuleAvailable && $branch_id, function ($query) use ($branch_id) {
                        $query->where('branch_id', $branch_id);
                    })
                    ->get();

                $un_session_id = $request->un_session_id;
                $un_faculty_id = $request->un_faculty_id;
                $un_department_id = $request->un_department_id;
                $un_academic_id = $request->un_academic_id;
                $un_semester_id = $request->un_semester_id;
                $un_semester_label_id = $request->un_semester_label_id;
                $un_section_id = $request->un_section_id;
                $un_subject_id = $request->un_subject_id;

                return view('behaviourrecords::reports.student_incident_report', ['student_records' => $student_records, 'un_session_id' => $un_session_id, 'un_faculty_id' => $un_faculty_id, 'un_department_id' => $un_department_id, 'un_academic_id' => $un_academic_id, 'un_semester_id' => $un_semester_id, 'un_semester_label_id' => $un_semester_label_id, 'un_section_id' => $un_section_id, 'branch_id' => $branch_id]);

            }
            $classes = branchWise(SmClass::get());
            $sessions = SmAcademicYear::where('school_id', Auth::user()->school_id)->get();
            $relations = ['student', 'student.gender', 'incidents.incident', 'class', 'section'];
            if ($branchModuleAvailable) {
                $relations[] = 'branch';
            }
            $student_records = StudentRecord::with($relations)
                ->withCount('incidents')
                ->withSum(['incidents as incidents_positive_point' => function ($query) {
                    $query->where('incident_id', 1);
                }], 'point')
                ->withSum(['incidents as incidents_negative_point' => function ($query) {
                    $query->where('incident_id', 2);
                }], 'point')
                ->where('active_status', 1)
                ->where('academic_id', $request->academic_year)
                ->where('class_id', $request->class_id)
                ->where('section_id', $request->section_id)
                ->when($request->shift_id && shiftEnable(), function ($query) use ($request) {
                    $query->where('shift_id', $request->shift);
                })
                ->when($branchModuleAvailable && $branch_id, function ($query) use ($branch_id) {
                    $query->where('branch_id', $branch_id);
                })
                ->get();
            $class_id = $request->class_id;
            $section_id = $request->section_id;
            $shift_id = $request->shift ? $request->shift : '';
            $academic_year = $request->academic_year;

            return view('behaviourrecords::reports.student_incident_report', ['academic_year' => $academic_year, 'shift_id' => $shift_id, 'section_id' => $section_id, 'class_id' => $class_id, 'student_records' => $student_records, 'classes' => $classes, 'sessions' => $sessions, 'branch_id' => $branch_id]);

        } catch (Exception $exception) {
            Toastr::error('Operation Failed', 'Failed');

            return redirect()->back();
        }
    }

    public function viewStudentAllIncidentModal($id)
    {
        try {
            $all_incident = AssignIncident::with('incident', 'user', 'academicYear')
                ->where('record_id', $id)
                ->when(moduleStatusCheck('Branch') && userBranch(), function ($query) {
                    $query->where('branch_id', userBranch());
                })
                ->get();

            return view('behaviourrecords::reports.student_incident_report_modal', ['all_incident' => $all_incident]);
        } catch (Exception $exception) {
            Toastr::error('Operation Failed', 'Failed');

            return redirect()->back();
        }
    }

    public function studentBehaviourRankReport()
    {
        try {
            $classes = branchWise(SmClass::get());
            $sessions = SmAcademicYear::where('school_id', Auth::user()->school_id)->get();

            return view('behaviourrecords::reports.student_behaviour_rank_report', ['classes' => $classes, 'sessions' => $sessions]);
        } catch (Exception $exception) {
            Toastr::error('Operation Failed', 'Failed');

            return redirect()->back();
        }
    }

    public function studentBehaviourRankReportSearch(Request $request)
    {

        $input = $request->all();
        $is_university = moduleStatusCheck('University');
        if ($is_university) {
            $validator = Validator::make($input, [
                'un_session_id' => 'required',
                'un_faculty_id' => 'required',
                'un_department_id' => 'required',
                'un_academic_id' => 'required',
                'un_semester_id' => 'required',
                'un_semester_label_id' => 'required',
                'un_section_id' => 'required',
            ]);
        } else {
            $validator = Validator::make($input, [
                'academic_year' => 'required',
            ]);
        }
        if ($validator->fails()) {
            return redirect()->back()->withErrors($validator)->withInput();
        }
        $branch_id = $request->branch_id ?? null;
        try {

            if ($is_university) {
                $student_records = branchWise(StudentRecord::with('student')
                    ->where('active_status', 1)
                    ->when($request->un_academic_id, function ($q) use ($request): void {
                        $q->where('un_academic_id', $request->un_academic_id);
                    })
                    ->when($request->un_session_id, function ($q) use ($request): void {
                        $q->where('un_session_id', $request->un_session_id);
                    })
                    ->when($request->un_faculty_id, function ($q) use ($request): void {
                        $q->where('un_faculty_id', $request->un_faculty_id);
                    })
                    ->when($request->un_department_id, function ($q) use ($request): void {
                        $q->where('un_department_id', $request->un_department_id);
                    })
                    ->when($request->un_semester_id, function ($q) use ($request): void {
                        $q->where('un_semester_id', $request->un_semester_id);
                    })
                    ->when($request->un_semester_label_id, function ($q) use ($request): void {
                        $q->where('un_semester_label_id', $request->un_semester_label_id);
                    })
                    ->when($request->un_section_id, function ($q) use ($request): void {
                        $q->where('un_section_id', $request->un_section_id);
                    })
                    ->when($request->branch_id, function ($query) use ($request) {
                        $query->where('branch_id', $request->branch_id);
                    }));
                $student_ids = $student_records->pluck('student_id');

                $students = branchWise(SmStudent::whereIn('id', $student_ids)
                    ->whereHas('incidents', function ($query) use ($request) {
                        if ($request->type === 'greater_than_or_equal') {
                            $query->select(DB::raw('student_id'))
                                ->groupBy('student_id')
                                ->havingRaw('SUM(point) >= ?', [$request->point]);
                        }

                        if ($request->type === 'lesser_than_or_equal') {
                            $query->select(DB::raw('student_id'))
                                ->groupBy('student_id')
                                ->havingRaw('SUM(point) <= ?', [$request->point]);
                        }
                    })
                    ->with([
                        'gender',
                        'studentRecords',
                        'studentRecords.unDepartment',
                        'studentRecords.unFaculty',
                    ])
                    ->withSum('incidents', 'point') // optional: if you still want to retrieve the sum
                    ->get());

                $un_session_id = $request->un_session_id;
                $un_faculty_id = $request->un_faculty_id;
                $un_department_id = $request->un_department_id;
                $un_academic_id = $request->un_academic_id;
                $un_semester_id = $request->un_semester_id;
                $un_semester_label_id = $request->un_semester_label_id;
                $un_section_id = $request->un_section_id;
                $type = $request->type;
                $point = $request->point;

                return view('behaviourrecords::reports.student_behaviour_rank_report', ['students' => $students, 'type' => $type, 'point' => $point, 'un_session_id' => $un_session_id, 'un_faculty_id' => $un_faculty_id, 'un_department_id' => $un_department_id, 'un_academic_id' => $un_academic_id, 'un_semester_id' => $un_semester_id, 'un_semester_label_id' => $un_semester_label_id, 'un_section_id' => $un_section_id, 'branch_id' => $branch_id]);

            }

            $classes = branchWise(SmClass::get());
            $sessions = SmAcademicYear::where('school_id', Auth::user()->school_id)->get();
            $student_records = branchWise(StudentRecord::with('student')
                ->where('active_status', 1)
                ->when($request->academic_year, function ($q) use ($request): void {
                    $q->where('academic_id', $request->academic_year);
                })
                ->when($request->class_id, function ($q) use ($request): void {
                    $q->where('class_id', $request->class_id);
                })
                ->when($request->shift_id, function ($q) use ($request): void {
                    $q->where('shift_id', $request->shift);
                })
                ->when($request->section_id, function ($q) use ($request): void {
                    $q->where('section_id', $request->section_id);
                })
                ->when($request->branch_id, function ($query) use ($request) {
                    $query->where('branch_id', $request->branch_id);
                }));
            $student_ids = $student_records->pluck('student_id');

            $students = branchWise(SmStudent::whereIn('id', $student_ids)
                ->whereHas('incidents', function ($query) use ($request) {
                    if ($request->type === 'greater_than_or_equal') {
                        $query->select(DB::raw('student_id'))
                            ->groupBy('student_id')
                            ->havingRaw('SUM(point) >= ?', [$request->point]);
                    }

                    if ($request->type === 'lesser_than_or_equal') {
                        $query->select(DB::raw('student_id'))
                            ->groupBy('student_id')
                            ->havingRaw('SUM(point) <= ?', [$request->point]);
                    }
                })
                ->with(['gender', 'studentRecords'])
                ->withSum('incidents', 'point')
                ->get());
            $class_id = $request->class_id;
            $section_id = $request->section_id;
            $shift_id = $request->shift_id ? $request->shift_id : '';
            $academic_year = $request->academic_year;

            return view('behaviourrecords::reports.student_behaviour_rank_report', ['academic_year' => $academic_year, 'shift_id' => $shift_id, 'section_id' => $section_id, 'class_id' => $class_id, 'students' => $students, 'classes' => $classes, 'sessions' => $sessions, 'branch_id' => $branch_id]);

        } catch (Exception $exception) {
            Toastr::error('Operation Failed', 'Failed');

            return redirect()->back();
        }
    }

    public function viewBehaviourRankModal($id)
    {
        try {
            $student = SmStudent::find($id);
            if ($student) {
                $all_incident = $student->incidents->load('incident', 'user', 'academicYear');
            }

            return view('behaviourrecords::reports.student_behaviour_rank_report_modal', ['all_incident' => $all_incident]);
        } catch (Exception $exception) {
            Toastr::error('Operation Failed', 'Failed');

            return redirect()->back();
        }
    }

    public function classSectionWiseRankReport()
    {
        try {
            $classes = branchWise(SmClass::with('groupclassSections')->withCount('records')->withSum('allIncident', 'point')->orderBy('all_incident_sum_point', 'DESC')->get());

            return view('behaviourrecords::reports.class_section_wise_rank_report', ['classes' => $classes]);
        } catch (Exception $exception) {
            Toastr::error('Operation Failed', 'Failed');

            return redirect()->back();
        }
    }

    public function viewClassSectionWiseModal($id)
    {
        try {
            $class = branchWise(SmClass::with('records.studentDetail.incidents.incident', 'records.class', 'records.section')->where('id', $id)->firstOrFail());

            return view('behaviourrecords::reports.class_section_wise_rank_report_modal', ['class' => $class]);
        } catch (Exception $exception) {
            Toastr::error('Operation Failed', 'Failed');

            return redirect()->back();
        }
    }

    public function incidentWiseReport()
    {
        try {
            $branch_id = moduleStatusCheck('Branch') ? userBranch() : null;
            $incidents = branchWise(Incident::with(['incidents' => function ($query) use ($branch_id) {
                if (moduleStatusCheck('Branch') && $branch_id) {
                    $query->where(function ($query) use ($branch_id) {
                        $query->where('branch_id', $branch_id)
                            ->orWhereNull('branch_id')
                            ->orWhere('branch_id', 0);
                    });
                }
            }])->get());

            return view('behaviourrecords::reports.incident_wise_report', ['incidents' => $incidents]);
        } catch (Exception $exception) {
            Toastr::error('Operation Failed', 'Failed');

            return redirect()->back();
        }
    }

    public function viewIncidentWiseReportModal($id)
    {
        try {
            $studentRecords = branchWise(AssignIncident::where('incident_id', $id)->with('studentRecord.studentDetail', 'studentRecord.class', 'studentRecord.section')->get());

            return view('behaviourrecords::reports.incident_wise_report_modal', ['studentRecords' => $studentRecords]);
        } catch (Exception $exception) {
            Toastr::error('Operation Failed', 'Failed');

            return redirect()->back();
        }
    }

    // setting
    public function setting()
    {
        try {
            $setting = BehaviourRecordSetting::where('id', 1)
                ->select(['student_comment', 'parent_comment', 'student_view', 'parent_view', 'school_id'])
                ->first();

            return view('behaviourrecords::setting.setting', ['setting' => $setting]);
        } catch (Exception $exception) {
            Toastr::error('Operation Failed', 'Failed');

            return redirect()->back();
        }
    }

    public function settingUpdate(Request $request)
    {
        try {
            $settingUpdate = BehaviourRecordSetting::find(1);
            if ($request->type === 'comment') {
                $settingUpdate->student_comment = $request->studentComment;
                $settingUpdate->parent_comment = $request->parentComment;
            }

            if ($request->type === 'view') {
                $settingUpdate->student_view = $request->studentView;
                $settingUpdate->parent_view = $request->parentView;
            }

            $settingUpdate->update();

            return redirect()->back();
        } catch (Exception $exception) {
            Toastr::error('Operation Failed', 'Failed');

            return redirect()->back();
        }
    }
}
