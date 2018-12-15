<?php

/**
* 管理员模块 controller
*
* @author      星辰后端 17级 卞光贤
* @version     1.0
*/


namespace app\controller;
header('Access-Control-Allow-Origin:http://localhost:5000');
header('Access-Control-Allow-Credentials: true');
header('Access-Control-Allow-Methods:POST,GET');
header('Access-Control-Allow-Headers:DNT,X-CustomHeader,Keep-Alive,User-Agent,X-Requested-With,If-Modified-Since,Cache-Control,Content-Type');

use app\model\Stu as StuModel;
use app\model\Teacher as TeacherModel;
use think\Controller;
use think\Loader;



class Admin extends Controller
{
    function initialize()
    {
        session_start();
        $this->Stumodel = new StuModel();
        $this->Teachermodel = new TeacherModel();
        $this->Excel = new \PHPExcel();
    }

    public function teacher()
    {

        if(!isset($_SESSION['admin'])) {
            return msg('',2,'非法操作或未登录');
        }

        if (!isset($_POST['teacher_name'])) {
            return msg('',101,'未传递参数');
        }

        $teacher_name = $_POST['teacher_name'];

        $teacher = $this->Teachermodel->where('name',$teacher_name)->find();

        if (!$teacher) {
            return msg('',9,'查无此辅导员');
        }

        $questions = array();
    $questions['ques_all'] = $teacher['ques_all'];
        for ($i=1; $i <= 10 ; $i++) {
            $questions["ques_$i"] = $teacher["ques_$i"];
        }

        $students = $this->Stumodel->where('teacher_name',$teacher_name)->where('if_done',0)->column('name');

        $data = [
            'questions'   => $questions,
            'finished'    => $teacher['finished'],
            'unfinished'  => $teacher['unfinished'],
            'names'       => $students
        ];

        return msg($data,0,'');
    }

    public function teachers()
    {
        if(!isset($_SESSION['admin'])) {
            return msg('',3,'非法操作或未登录');
        }

        $teachers = $this->Teachermodel->select();
        return msg($teachers,0,'');
    }

    public function downloads()
    {
        if(!isset($_SESSION['admin'])) {
            return msg('',3,'非法操作或未登录');
        }
        $filename = "辅导员评测结果统计";
        $teachers = $this->Teachermodel->select();
        $this->download_header($filename);

        $objPHPExcel = new \PHPExcel();


        for ($i="A"; $i < "Z" ; $i++) {
            $objPHPExcel->getActiveSheet()->getColumnDimension($i)->setWidth(15);
        }
        $objPHPExcel->getActiveSheet()->mergeCells('A1:K1');
        $objPHPExcel->getActiveSheet()->getDefaultStyle()->getFont()->setSize(16);
        $objPHPExcel->getActiveSheet()->getStyle('A1')->getFont()->setSize(30);
        $objPHPExcel->getActiveSheet()->getStyle('A1:E1')->getFont()->setBold(true);
        $objPHPExcel->getActiveSheet()->getRowDimension('1')->setRowHeight(80);
        $objPHPExcel->getDefaultStyle()->getAlignment()->setHorizontal(\PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
        $objPHPExcel->getDefaultStyle()->getAlignment()->setVertical(\PHPExcel_Style_Alignment::VERTICAL_CENTER);
        $objPHPExcel->getActiveSheet()->getStyle('A1')->getAlignment()->setHorizontal(\PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
        $objPHPExcel->setActiveSheetIndex(0)->getStyle('A1')->getAlignment()->setVertical(\PHPExcel_Style_Alignment::VERTICAL_CENTER);
        $objPHPExcel->getActiveSheet()->setCellValue('A1', '辅导员评测数据统计(具体未完成同学和具体分数数据可在页面中辅导员姓名查看)');
        $objPHPExcel->getActiveSheet()->setCellValue('A2', '辅导员姓名');
        $objPHPExcel->getActiveSheet()->setCellValue('B2', '总平均分');
        $objPHPExcel->getActiveSheet()->setCellValue('C2', '已完成人数');
        $objPHPExcel->getActiveSheet()->setCellValue('D2', '未完成人数');

        $n = 3;
        foreach ($teachers as $teacher) {
            $objPHPExcel->getActiveSheet()->setCellValue("A$n", $teacher['name']);
            $objPHPExcel->getActiveSheet()->setCellValue("B$n", $teacher['ques_all']);
            $objPHPExcel->getActiveSheet()->setCellValue("C$n", $teacher['finished']);
            $objPHPExcel->getActiveSheet()->setCellValue("D$n", $teacher['unfinished']);
            $n++;
        }
        $objWriter = \PHPExcel_IOFactory::createWriter($objPHPExcel, 'Excel5');
        $objWriter->save('php://output');
    }

    public function download()
    {
        if(!isset($_SESSION['admin'])) {
            return msg('',2,'非法操作或未登录');
        }
        if (!isset($_GET['teacher'])) {
            return msg("",101,"无参数");
        }
        $name = $_GET['teacher'];
        $filename = "辅导员$name 评测结果统计";
        $teacher = $this->Teachermodel->where('name',$name)->find();
        $this->download_header($filename);

        $objPHPExcel = new \PHPExcel();
        if (!$teacher) {
            return msg("",9,"查无此辅导员");
        }
        $students = $this->Stumodel->where('teacher_name',$name)->where('if_done',0)->column('name');

        for ($i="A"; $i < "Z" ; $i++) {
            $objPHPExcel->getActiveSheet()->getColumnDimension($i)->setWidth(15);
        }
        $objPHPExcel->getActiveSheet()->mergeCells("A1:J1");
        for ($i=2; $i <= 13; $i++) {
            $objPHPExcel->getActiveSheet()->mergeCells("A$i:I$i");
        }
        $objPHPExcel->getActiveSheet()->mergeCells("B14:J14");
        $objPHPExcel->getActiveSheet()->getDefaultStyle()->getFont()->setSize(16);
        $objPHPExcel->getActiveSheet()->getStyle('A1')->getFont()->setSize(30);
        $objPHPExcel->getActiveSheet()->getStyle('A2:E2')->getFont()->setSize(25);
        $objPHPExcel->getActiveSheet()->getStyle('A2:E2')->getFont()->setBold(true);
        $objPHPExcel->getActiveSheet()->getStyle('A1:E1')->getFont()->setBold(true);
        $objPHPExcel->getActiveSheet()->getRowDimension('1')->setRowHeight(80);
        $objPHPExcel->getDefaultStyle()->getAlignment()->setHorizontal(\PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
        $objPHPExcel->getDefaultStyle()->getAlignment()->setVertical(\PHPExcel_Style_Alignment::VERTICAL_CENTER);
        $objPHPExcel->getActiveSheet()->getStyle('A1')->getAlignment()->setHorizontal(\PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
        $objPHPExcel->getActiveSheet()->getStyle('A2')->getAlignment()->setHorizontal(\PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
        $objPHPExcel->getActiveSheet()->getStyle('A1')->getAlignment()->setVertical(\PHPExcel_Style_Alignment::VERTICAL_CENTER);
        $objPHPExcel->getActiveSheet()->getStyle('B14')->getAlignment()->setHorizontal(\PHPExcel_Style_Alignment::HORIZONTAL_JUSTIFY);
        $objPHPExcel->getActiveSheet()->setCellValue('A1', "辅导员$name 评测结果统计");
        $objPHPExcel->getActiveSheet()->setCellValue('A2', "分项题目");
        $objPHPExcel->getActiveSheet()->setCellValue('J2', "题目平均分");
        $objPHPExcel->getActiveSheet()->setCellValue('A13', "总平均分");
        $objPHPExcel->getActiveSheet()->setCellValue('J13', $teacher['ques_all']);
        $stu_names = implode("、", $students);
        for ($i=3; $i <= 12; $i++) {
            $pro = $i-2;
            $point = $teacher["ques_$pro"];
            $objPHPExcel->getActiveSheet()->setCellValue("A$i",question($pro));
            $objPHPExcel->getActiveSheet()->setCellValue("J$i",$point);
        }
        $objPHPExcel->getActiveSheet()->setCellValue("A14","未评测学生:");
        $objPHPExcel->getActiveSheet()->setCellValue("B14",$stu_names);


        $objWriter = \PHPExcel_IOFactory::createWriter($objPHPExcel, 'Excel5');
        $objWriter->save('php://output');
    }


    private function download_header($filename)
    {

        ob_end_clean();
        header('Content-Type: application/vnd.ms-excel');
        header('Content-Disposition: attachment;filename="'.$filename.'.xls"');
        header('Cache-Control: max-age=0');
        header('Cache-Control: max-age=1');
        header('Expires: Mon, 26 Jul 1997 05:00:00 GMT'); // Date in the past
        header('Last-Modified: ' . gmdate('D, d M Y H:i:s') . ' GMT'); // always modified
        header('Cache-Control: cache, must-revalidate'); // HTTP/1.1
        header('Pragma: public'); // HTTP/1.012
    }
}