<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

use App\Http\Requests;
use App\Http\Controllers\Controller;

class FileController extends Controller
{
    public static function upload($file, $directory)
    {
        $date = date('Y-m-d');

        $path = storage_path('app/public/'.$directory.'/'.$date);

        if(!is_dir($path))
            mkdir($path, 0777, true);

        $filename = md5(time().$file->getClientOriginalName()).'.'.$file->getClientOriginalExtension();

        $path = $file->move($path, $filename)->getRealPath();

        return $path;
    }

    public static function experimentFileUpload($file)
    {
        return self::upload($file, 'experiments/files');
    }

    public static function downloadPublicFile(\App\File $file)
    {
        if($file->file_is_public)
            return response()->download($file->file_path, $file->file_name);
        return redirect('/')->with(['error' => '很抱歉，您没有权限下载此文件']);
    }

    public static function reportFileUpload($file)
    {
        return self::upload($file, 'reports/files');
    }

    public static function reportAttachUpload($file)
    {
        return self::upload($file, 'reports/attaches');
    }

    public static function downloadStudentReportFile(\App\Report $report)
    {
        if($report->report_user_id != session('er_userid'))
            return back()->with(['error' => '很抱歉，您没有权限下载此文件']);

        $file = $report->file;
        return response()->download($file->file_path, $file->file_name);
    }

    public static function downloadStudentReportAttach(\App\Report $report)
    {
        if($report->report_user_id != session('er_userid'))
            return back()->with(['error' => '很抱歉，您没有权限下载此文件']);

        $attach = $report->attach;
        return response()->download($attach->attach_path, $attach->attach_name);
    }

    public static function downloadTeacherReportFile(\App\Report $report)
    {
        if($report->experiment->course->course_teacher_id != session('er_userid'))
            return back()->with(['error' => '很抱歉，您没有权限下载此文件']);

        $file = $report->file;
        return response()->download($file->file_path, $file->file_name);
    }

    public static function downloadTeacherReportAttach(\App\Report $report)
    {
        if($report->experiment->course->course_teacher_id != session('er_userid'))
            return back()->with(['error' => '很抱歉，您没有权限下载此文件']);

        $attach = $report->attach;
        return response()->download($attach->attach_path, $attach->attach_name);
    }

    public static function downloadTeacherExperiment(\App\Experiment $experiment)
    {
        $zip = new \ZipArchive;

        $zippath = storage_path('app/protected/zips/experiment/');

        if(!is_dir($zippath))
            mkdir($zippath, 0777, true);

        if($zip->open($zippath.$experiment->id.'.zip', \ZIPARCHIVE::OVERWRITE) !== TRUE)
            if($zip->open($zippath.$experiment->id.'.zip', \ZIPARCHIVE::CREATE) !== TRUE)
                return back()->with(['error' => '文件打包失败!']);

        foreach ($experiment->reports as $report)
        {
            $report->load('user', 'file', 'attach');

            if(isset($report->file->file_name))
            {
                $path = $report->file->file_path;
                $name = '[报告]'.$report->user->user_name.$report->user->user_account.'.'.pathinfo($report->file->file_name, PATHINFO_EXTENSION);
                $zip->addFromString($name, file_get_contents($path));
            }

            if(isset($report->attach->attach_name))
            {
                $path = $report->attach->attach_path;
                $name = '[附件]'.$report->user->user_name.$report->user->user_account.'.'.pathinfo($report->attach->attach_name, PATHINFO_EXTENSION);
                $zip->addFromString($name, file_get_contents($path));
            }
        }

        $zip->close();

        return response()->download($zippath.$experiment->id.'.zip', $experiment->experiment_title.'.zip');
    }

    public function downloadTeacherStudent(\App\UserCourse $usercourse)
    {
        $zip = new \ZipArchive;

        $zippath = storage_path('app/protected/zips/students/');

        if(!is_dir($zippath))
            mkdir($zippath, 0777, true);

        if($zip->open($zippath.$usercourse->user_id.'.zip', \ZIPARCHIVE::OVERWRITE) !== TRUE)
            if($zip->open($zippath.$usercourse->user_id.'.zip', \ZIPARCHIVE::CREATE) !== TRUE)
                return back()->with(['error' => '文件打包失败!']);

        foreach ($usercourse->course->experiments as $experiment)
        {
            $experiment->load(['reports' => function($query) use($usercourse) {
                $query->where('report_user_id', $usercourse->user_id);
            }]);
            foreach ($experiment->reports as $report)
            {
                $report->load('user', 'file', 'attach');

                if(isset($report->file->file_name))
                {
                    $path = $report->file->file_path;
                    $name = '[报告]'.$experiment->experiment_title.'-'.$report->user->user_name.$report->user->user_account.'.'.pathinfo($report->file->file_name, PATHINFO_EXTENSION);
                    $zip->addFromString($name, file_get_contents($path));
                }

                if(isset($report->attach->attach_name))
                {
                    $path = $report->attach->attach_path;
                    $name = '[附件]'.$experiment->experiment_title.'-'.$report->user->user_name.$report->user->user_account.'.'.pathinfo($report->attach->attach_name, PATHINFO_EXTENSION);
                    $zip->addFromString($name, file_get_contents($path));
                }
            }
        }

        $zip->close();

        return response()->download($zippath.$usercourse->user_id.'.zip', $usercourse->user->user_name.'.zip');
    }

    public function downloadTeacherCourse(\App\Course $course)
    {
        $reports = \App\UCEReport::where('course_id', $course->id)
            ->whereNotNull('report_file_id')
            ->get();

        $zip = new \ZipArchive;

        $zippath = storage_path('app/protected/zips/courses/');

        if(!is_dir($zippath))
            mkdir($zippath, 0777, true);

        if($zip->open($zippath.$course->id.'.zip', \ZIPARCHIVE::OVERWRITE) !== TRUE)
            if($zip->open($zippath.$course->id.'.zip', \ZIPARCHIVE::CREATE) !== TRUE)
                return back()->with(['error' => '文件打包失败!']);

        foreach ($reports as $report)
        {
            $report->load('file', 'attach');

            if(isset($report->file->file_name))
            {
                $path = $report->file->file_path;
                $name = '[报告]'.$report->experiment_title.'-'.$report->user_name.$report->user_account.'.'.pathinfo($report->file->file_name, PATHINFO_EXTENSION);
                $zip->addFromString($name, file_get_contents($path));
            }

            if(isset($report->attach->attach_name))
            {
                $path = $report->attach->attach_path;
                $name = '[附件]'.$report->experiment_title.'-'.$report->user_name.$report->user_account.'.'.pathinfo($report->attach->attach_name, PATHINFO_EXTENSION);
                $zip->addFromString($name, file_get_contents($path));
            }
        }

        $zip->close();

        return response()->download($zippath.$course->id.'.zip', $course->course_name.'.zip');
    }

    
}
