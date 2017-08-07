@extends('common.layout')

@section('css')
<style>
  .input-group {
    padding-left: 10px !important;
    padding-right: 5px !important;
  }
</style>
@endsection

@section('content')
  <div class="">
    <div class="page-title">
      <div class="title_left">
        <h3>添加学生 （ {{ $course->course_name }} ）</h3>
      </div>
    </div>

    <div class="clearfix"></div>
    <hr>
    <div class="row">
      <div class="col-md-12 col-sm-12 col-xs-12">
        <div class="col-md-12 col-sm-12 col-xs-12">
          <div class="x_panel">
            <div class="x_title">
              <h2></h2>
              <ul class="nav navbar-right panel_toolbox">
                <li><a class="collapse-link"><i class="fa fa-chevron-up"></i></a>
                </li>
                <li><a href="#"><i class="fa fa-wrench"></i></a>
                </li>
                <li><a class="close-link"><i class="fa fa-close"></i></a>
                </li>
              </ul>
              <div class="clearfix"></div>
            </div>
            <div class="x_content">
              <form method="post" action="{{ url('teacher/course/student/add/' . $course->id) }}" class="form-horizontal form-label-left">
                {{ csrf_field() }}


                <div class="form-group">
                  <label class="control-label col-md-3 col-sm-3 col-xs-12" for="description"></label>
                  <div class="col-md-6 col-sm-6 col-xs-12">
                    <div class="alert alert-info">
                      <span>请下载下面所给的excel模板</span><br/>
                      <span>填写相应的学生信息后上传excel文件</span><br/>
                      <span><a href="">下载模板</a></span><br/>
                    </div>
                  </div>
                </div>

                <div class="form-group">
                  <label class="control-label col-md-3 col-sm-3 col-xs-12" for="description">学号</label>
                  <div class="col-md-6 col-sm-6 col-xs-12">
                    <!-- <textarea id="description" class="form-control col-md-7 col-xs-12" name="accounts" placeholder="请在此输入要添加的学生的学号" required="required" rows="7"></textarea> -->

                  </div>
                </div>


                <div class="form-group">
                  <div class="col-md-offset-3 col-md-9 col-sm-offset-3 col-sm-9 col-xs-12">
                    <input type="submit" value="上传文件" class="btn btn-primary col-md-2 col-sm-4 col-xs-5">
                    <a href="{{ url('teacher/course/'. $course->id) }}" class="btn btn-info col-md-2 col-sm-4 col-xs-5">返回</a>
                  </div>
                </div>

              </form>
            </div>
          </div>
        </div>
      </div>
    </div>
  </div>
@endsection

@section('javascript')

<script type="text/javascript">

  $('#file').change(function() {
    $('#name').val($('#file').val().substring(12));
  });

  $('#btn_file').click(function() {
    $('#file').click();
  });

</script>

@endsection