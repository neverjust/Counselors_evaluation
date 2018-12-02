<?php

/** 
* 用户模块 controller
*
* @author      星辰后端 17级 卞光贤
* @version     1.0
*/

namespace app\controller;

use app\model\Stu as StuModel;
use app\model\Teacher as TeacherModel;
use think\Controller;
use Ladp;


class User extends Controller
{
    function initialize()
    {
        session_start();
        $this->Stumodel = new StuModel();
        $this->Teachermodel = new TeacherModel();
    }

    public function login()
    {
        if(isset($_SESSION['name']) || isset($_SESSION['admin'])) {
            return msg('',1,'该用户已经登录');
        }

        if (!isset($_POST['studentId']) || !isset($_POST['password'])) {
            return msg('',101,'参数不完全');
        }

        if ($_POST['studentId'] == "admin" && $_POST['password'] = "stuhome") {
            $_SESSION['admin'] = 'admin';
            return msg('',7,'');
        }
        $labp = new Ladp($_POST['studentId'],$_POST['password']); //传入用户名和密码返回
        $res = $labp->run();
        if ($res['errcode']) {
            $result = $this->Stumodel->where('name',$res['name'])->find(); //认证成功返回
            if (!$result) {
               return msg("",10,"查无此人");
            }
            $_SESSION['name'] = $res['name'];
            $data = [
                'stu_name'      => $res['name'],
                'teacher_name'  => $result['teacher_name']
            ];
            return msg($data,0,"");
        }
        else {
            return msg("",6,$res['errmsg']);
        }
    }

    public function remains()
    {
        if(!isset($_SESSION['name'])) {
            return msg('',2,'该用户未登录');
        }

        $stu = $this->Stumodel->where('name',$_SESSION['name'])->where('if_done',0)->find();
        if (!$stu) {
            return msg('',5,'该用户已经评测完所有项目');
        }

        $student = $this->Stumodel->where('name',$_SESSION['name'])->select();
        $teacher_finished = [];
        $teacher_unfinished = [];
        foreach ($student as $stu) {
            switch ($stu['if_done']) {
                case '0':
                    $teacher_unfinished[] = $stu['teacher_name'];
                    break;
                case '1':
                    $teacher_finished[] = $stu['teacher_name'];
                    break;
            }
        }
        $data = [
            'finished'   => $teacher_finished,
            'unfinished' => $teacher_unfinished
        ];
        return msg($data,0,'');
    }

    public function store()
    {

        if(!isset($_SESSION['name'])) {
            return msg('',2,'该用户未登录');
        }

        if(!isset($_POST['teacher'])) {
            return msg('',101,'参数不完全');
        }

        $student = $this->Stumodel->where('name',$_SESSION['name'])->where('teacher_name',$_POST['teacher'])->find();
        if (!$student) {
            return msg('',11,'不能评测');
        }
        if ($student['if_done']) {
            return msg('',12,'该辅导员已经评测');
        }

        $teacher = $this->Teachermodel->where('name',$_POST['teacher'])->find();
        if (!$teacher) {
            return msg('',100,'查无此辅导员');
        }
        $ques_all = 0;
        for ($i=1; $i < 10; $i++) { 
            if (!isset($_POST["ques_$i"])) {
                return msg('',101,'参数不完全');
            }
            $teacher["ques_$i"] = round(($teacher["ques_$i"]*$teacher['finished']+$_POST["ques_$i"])/($teacher['finished']+1),2);
            $ques_all+=$_POST["ques_$i"];
        }

        $ques_all = round($ques_all/9,2);
        $teacher["ques_all"] = round(($teacher["ques_all"]*$teacher['finished']+$ques_all)/($teacher['finished']+1),2);
        $teacher['finished']+=1;
        $teacher['unfinished']-=1;
        $student['if_done'] = 1;
        $student->save();
        $result = $teacher->save();
        if (!$result) {
            return msg('',100,'服务器错误');
        }

        return msg('',0,'');
    }
}


