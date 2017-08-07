<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

use App\Http\Requests;
use App\Http\Controllers\Controller;

use App\System;

use Sklcc\Csteaching;

use DB;

class TeacherController extends Controller
{
    /**
     * 显示教师当前学期所有实验课程
     * @author LiCxi <2992368059@qq.com>
     * @datetime 2017-08-01T12:07:33+0800
     * @return   [type]                   [description]
     */
    public function teacher()
    {
        $data = [
            'semesters' => \App\Semester::where('id', session('er_semester_id'))
                ->with(['courses' => function($query) {
                    $query->where('course_teacher_id', session('er_userid'))
                        ->where('course_is_computer', true)
                        ->with('major');
                }])->get()
        ];

        return view('teacher.teacher', $data);
    }

    /**
     * 显示课程详情
     * @author LiCxi <2992368059@qq.com>
     * @datetime 2017-08-01T14:55:50+0800
     * @param    \App\Course              $course [description]
     * @return   [type]                           [description]
     */
    public function course(\App\Course $course)
    {
        $data = [
            'course' => $course,
            'etable' => System::$LABELS['table']['experiment'],
            'students' => \App\UCEReport::where('course_id', $course->id)
                // ->whereNotNull('report_file_id')
                ->groupBy('user_id')
                ->select(\DB::raw('user_name, user_account, count(report_file_id) as count_report_id, user_course_id'))
                ->get()
        ];

        return view('teacher.course', $data);
    }

    /**
     * 显示实验详情
     * @author LiCxi <2992368059@qq.com>
     * @datetime 2017-08-02T16:19:51+0800
     * @param    \App\Experiment          $experiment 目标实验
     * @return   view                                 视图
     */
    public function experiment(\App\Experiment $experiment)
    {
        $data = [
            'experiment' => $experiment,
            'students' => \App\UCEReport::where('experiment_id', $experiment->id)
                ->get()
        ];
        return view('teacher.experiment.experiment', $data);
    }

    /**
     * 显示实验添加页面
     * @author LiCxi <2992368059@qq.com>
     * @datetime 2017-08-01T23:44:11+0800
     * @param    \App\Course              $course [description]
     * @return   [type]                           [description]
     */
    public function experimentAdd(\App\Course $course)
    {
        if($course->course_teacher_id != session('er_userid'))
            return back()->with(['error' => '很抱歉，您只能为您自己的课程添加实验']);

        $data = [
            'course' => $course,
            'form' => System::$LABELS['form']['experiment']
        ];

        return view('teacher.experiment.add', $data);
    }

    public function experimentAdd2(\App\Course $course, Request $request)
    {
        if($request->hasFile('excel'))
        {
            $file = $request->file('excel');

            if(!$file->isValid())
                return back()->with(['error' => '文件上传失败']);

            $path = FileController::experimentFileUpload($file);
            $name = $file->getClientOriginalName();

            $file = new \App\File();
            $file->file_name = $name;
            $file->file_path = $path;
            $file->file_is_public = true;
            $file->save();
        }


        $experiment = new \App\Experiment();
        $experiment->experiment_course_id = $course->id;
        $experiment->experiment_title = $request->get('title');
        $experiment->experiment_description = $request->get('description');
        $experiment->experiment_stop_time = $request->get('stop_time').' 23:59:59';
        $experiment->experiment_file_id = isset($file->id) ? $file->id : null;
        $experiment->save();

        return redirect('teacher/course/course/'.$course->id)->with(['success' => '实验添加成功']);
    }

    /**
     * 关闭实验（停止实验提交）
     * @author LiCxi <2992368059@qq.com>
     * @datetime 2017-08-02T12:59:15+0800
     * @param    \App\Experiment          $experiment 指定实验
     * @return   view                                 视图
     */
    public function experimentStop(\App\Experiment $experiment)
    {
        $experiment->experiment_stoped_time = date('Y-m-d H:i:s');
        $experiment->save();

        return back()->with(['success' => '实验关闭成功']);
    }

    /**
     * 开启实验（开始实验提交）
     * @author LiCxi <2992368059@qq.com>
     * @datetime 2017-08-02T14:25:39+0800
     * @param    \App\Experiment          $experiment 指定实验
     * @return   view                                 视图
     */
    public function experimentStart(\App\Experiment $experiment)
    {
        $experiment->experiment_stoped_time = null;
        $experiment->save();

        return back()->with(['success' => '实验开启成功']);
    }

    /**
     * 编辑实验
     * @author LiCxi <2992368059@qq.com>
     * @datetime 2017-08-03T20:00:49+0800
     * @param    \App\Experiment          $experiment [description]
     * @return   [type]                               [description]
     */
    public function experimentEdit(\App\Experiment $experiment)
    {
        $data = [
            'experiment' => $experiment,
            'form' => System::$LABELS['form']['experiment']
        ];

        return view('teacher.experiment.edit', $data);
    }

    /**
     * 编辑实验
     * @author LiCxi <2992368059@qq.com>
     * @datetime 2017-08-05T13:17:06+0800
     * @param    \App\Experiment          $experiment [description]
     * @param    Request                  $request    [description]
     * @return   [type]                               [description]
     */
    public function experimentEdit2(\App\Experiment $experiment, Request $request)
    {
        if($request->hasFile('excel'))
        {
            $file = $request->file('excel');

            if(!$file->isValid())
                return back()->with(['error' => '文件上传失败']);

            $path = FileController::experimentFileUpload($file);
            $name = $file->getClientOriginalName();

            $file = new \App\File();
            $file->file_name = $name;
            $file->file_path = $path;
            $file->file_is_public = true;
            $file->save();
        }

        $experiment->experiment_title = $request->get('title');
        $experiment->experiment_description = $request->get('description');
        $experiment->experiment_stop_time = $request->get('stop_time').' 23:59:59';

        if(isset($file->id))
            $experiment->experiment_file_id =  $file->id;
        $experiment->save();

        return redirect('teacher/course/'.$experiment->experiment_course_id)->with(['success' => '实验编辑成功']);
    }

    /**
     * 删除实验
     * @author LiCxi <2992368059@qq.com>
     * @datetime 2017-08-05T13:17:56+0800
     * @param    \App\Experiment          $experiment [description]
     * @return   [type]                               [description]
     */
    public function experimentDelete(\App\Experiment $experiment)
    {
        $experiment->delete();
        return back()->with(['success' => '实验删除成功']);
    }

    /**
     * 评阅实验
     * @author LiCxi <2992368059@qq.com>
     * @datetime 2017-08-05T13:18:05+0800
     * @param    \App\Experiment          $experiment [description]
     * @return   [type]                               [description]
     */
    public function experimentGrade(\App\Experiment $experiment)
    {
        $data = [
            'experiment' => $experiment,
            'students' => \App\UCEReport::where('experiment_id', $experiment->id)->get(),
            'form' => System::$LABELS['form']['experiment']
        ];

        return view('teacher.experiment.grade', $data);
    }

    /**
     * 删除实验文件
     * @author LiCxi <2992368059@qq.com>
     * @datetime 2017-08-05T13:18:21+0800
     * @param    \App\Experiment          $experiment [description]
     * @return   [type]                               [description]
     */
    public function experimentFileDelete(\App\Experiment $experiment)
    {
        if($experiment->experiment_stoped_time != null)
            return back()->with('很抱歉，您不能删除已经关闭或归档过的实验文件');

        if($experiment->course->course_teacher_id != session('er_userid'))
            return back()->with('很抱歉，您只能删除自己课程实验的文件');

        $experiment->experiment_file_id = null;
        $experiment->save();

        return back();
    }

    //============================================================//

    /**
     * 显示学生详情
     * @author LiCxi <2992368059@qq.com>
     * @datetime 2017-08-01T16:56:07+0800
     * @return   [type]                   [description]
     */
    public function student()
    {

    }

    /**
     * 显示学生添加页面
     * @author LiCxi <2992368059@qq.com>
     * @datetime 2017-08-01T17:31:44+0800
     * @param    \App\Course              $course [description]
     * @return   [type]                           [description]
     */
    public function studentAdd(\App\Course $course)
    {
        if($course->course_teacher_id != session('er_userid'))
            return back()->with(['error' => '很抱歉，您只能为您自己的课程添加学生']);

        $data = [
            'course' => $course
        ];

        return view('teacher.student.add', $data);
    }

    /**
     * 添加学生
     * @author LiCxi <2992368059@qq.com>
     * @datetime 2017-08-01T17:41:58+0800
     * @param    \App\Course              $course [description]
     * @return   [type]                           [description]
     */
    public function studentAdd2(\App\Course $course, Request $request)
    {
        if(!$request->has('accounts'))
            return back()->with(['error' => '参数错误']);

        $accounts = explode("\r\n", $request->get('accounts'));

        $not_find = '';
        $not_student = '';
        $flag = false;

        foreach ($accounts as $account)
        {
            $user = \App\User::where('user_account', $account)->first();

            if(!$user)
            {
                $not_find .= ($account.'<br />');
                continue;
            }

            if($user->user_identity_id != System::STUDENT_ID)
            {
                $not_student.= ($account.'<br />');
                continue;
            }
            $flag = true;
            $usercourse = \App\UserCourse::firstOrNew(['user_id' => $user->id, 'course_id' => $course->id]);
            $usercourse->save();
        }

        $error = '';
        if($not_find != '')
            $error .= ($not_find.'以上学号不存在<br /><br />');
        if($not_student != '')
            $error .= (''.$not_student.'以上学号并非学生');

        if($error == '')
            return redirect('teacher/course/'.$course->id)->with(['success' => '全部学号添加成功（已存在的学生将被覆盖）']);
        elseif(!$flag)
            return back()->with(['error' => $error]);
        else
            return back()->with(['success' => '部分学号添加成功（已存在的学生将被覆盖）', 'error' => $error, 'errorhide' => 'false']);

    }

    /**
     * 将学生从指定课程中移除
     * @author LiCxi <2992368059@qq.com>
     * @datetime 2017-08-02T14:38:23+0800
     * @param    \App\UserCourse          $usercourse [description]
     * @return   [type]                               [description]
     */
    public function studentDelete(\App\UserCourse $usercourse)
    {
        $usercourse->delete();

        return back()->with(['error' => '学生删除成功'/*' <a href="'.url('teacher/course/student/recovery/'.$usercourse->id).'">撤销删除</a>'*/ ]);
    }

    public function studentGrade(\App\UserCourse $usercourse)
    {
        $data = [
            'user' => $usercourse->user,
            'reports' => \App\UCEReport::where('user_course_id', $usercourse->id)
                ->orderBy('experiment_id', 'asc')->get()
        ];

        return view('teacher.student.grade', $data);
    }



    //============================================================//

    /**
     * 编辑成绩 api
     * @author LiCxi <2992368059@qq.com>
     * @datetime 2017-08-04T02:01:31+0800
     * @param    \App\Report              $report [description]
     * @param    [int]                    $mask   [description]
     * @return   [int]                            [成绩]
     */
    public function reportGrade(\App\Report $report, $mask)
    {
        $report->load('file', 'attach');
        if(!isset($report->file->file_name)) return '-1';
        $report->report_mask = $mask;
        $report->save();
        return $report->report_mask;
    }


}
