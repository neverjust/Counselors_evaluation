<?php

/**
* 用户模块 controller
*
* @author      星辰后端 17级 卞光贤
* @version     1.0
*/

namespace app\controller;

header('Access-Control-Allow-Methods:POST,GET');
header('Access-Control-Allow-Headers:DNT,X-CustomHeader,Keep-Alive,User-Agent,X-Requested-With,If-Modified-Since,Cache-Control,Content-Type');

use app\model\Stu as StuModel;
use app\model\Teacher as TeacherModel;
use think\Controller;
use Ldap;
use UestcApi;       


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
        if(!empty($_SESSION['name'])) {
            return msg('',1,'该用户已经登录');
        }
    if(!empty($_SESSION['admin']))
        return msg('',7,'');
        if (empty($_POST['studentId']) || empty($_POST['password'])) {
            return msg('',101,'参数不完全');
        }

        if ($_POST['studentId'] == "admin" && sha1($_POST['password']) == "028a5131a265ee674d5d29f3df90374bc65e887c" ) {
            $_SESSION['admin'] = 'admin';
            return msg('',7,'');
        }
        $studentInfo = new Ldap($_POST['studentId'],$_POST['password']);

        $res = $studentInfo->run();
        if ($res['errcode']) {
            $result = $this->Stumodel->where('stu_id',$_POST['studentId'])->find(); //认证成功返回
            if (!$result) {
               return msg("",10,"查无此人");
            }
            $_SESSION['name'] = $result['name'];
            $data = [
                'stu_name'      => $result['name'],
                'teacher_name'  => $result['teacher_name']
            ];
            return msg($data,0,"");
        }
        else {
            return msg("",6,"账号密码错误");
        }
    }

    public function check()
    {
        if(!empty($_SESSION['name']))
            return msg('',1,'该用户已经登录');
        elseif(!empty($_SESSION['admin']))
            return msg('',7,'该管理员已登录');
        else
            return msg('',0,'未登录');
    }

    public function logout()
    {
        session_destroy();
        unset($_SESSION['name']);
        unset($_SESSION['admin']);
    }



    public function remains()
    {
        if(empty($_SESSION['name'])) {
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

        if(empty($_SESSION['name'])) {
            return msg('',2,'该用户未登录');
        }

        if(empty($_POST['teacher'])) {
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
        for ($i=1; $i <= 10; $i++) {
            if (empty($_POST["ques_$i"])) {
                return msg('',101,'参数不完全');
            }
            $student["ques_$i"] = $_POST["ques_$i"];
            $teacher["ques_$i"] = round(($teacher["ques_$i"]*$teacher['finished']+$_POST["ques_$i"])/($teacher['finished']+1),2);
            $ques_all+=$_POST["ques_$i"];
        }

        $ques_all = round($ques_all,2);
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