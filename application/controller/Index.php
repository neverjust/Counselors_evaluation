<?php

/** 
* 用户模块 controller
*
* @author      星辰后端 17级 卞光贤
* @version     1.0
*/

namespace app\controller;

use think\Controller;
use app\model\Stu as StuModel;
use app\model\Teacher as TeacherModel;
use think\Loader;


class Index extends Controller
{
	function initialize()
    {
        session_start();
        $this->Stumodel = new StuModel();
        $this->Teachermodel = new TeacherModel();
    }
    public function teacher()
    {
    	$file_path = "static/all.xlsx";
    	$ext = strtolower(pathinfo($file_path,PATHINFO_EXTENSION));

		if($ext == 'xlsx'){
		    $objReader=\PHPExcel_IOFactory::createReader('Excel2007');
		    $objPHPExcel = $objReader->load($file_path,'utf-8');
		}elseif($ext == 'xls'){
		    $objReader=\PHPExcel_IOFactory::createReader('Excel5');
		    $objPHPExcel = $objReader->load($file_path,'utf-8');
		}
		$sheet = $objPHPExcel->getSheet(0);
		$highestRow = $sheet->getHighestRow(); // 取得总行数
		$highestColumn = $sheet->getHighestColumn(); // 取得总列数
		$data=array();
        for ($i = 1; $i <= $highestRow; $i++){
            $item['name']=$sheet->getCell('A'.$i)->getValue();
            $item['unfinished']=$sheet->getCell('B'.$i)->getValue();
            $this->Teachermodel->insert($item);
        }
    }

    public function stu()
    {
    	$file_path = "static/stu.xlsx";
    	$ext = strtolower(pathinfo($file_path,PATHINFO_EXTENSION));

		if($ext == 'xlsx'){
		    $objReader=\PHPExcel_IOFactory::createReader('Excel2007');
		    $objPHPExcel = $objReader->load($file_path,'utf-8');
		}elseif($ext == 'xls'){
		    $objReader=\PHPExcel_IOFactory::createReader('Excel5');
		    $objPHPExcel = $objReader->load($file_path,'utf-8');
		}
		$sheet = $objPHPExcel->getSheet(0);
		$highestRow = $sheet->getHighestRow(); // 取得总行数
		$highestColumn = $sheet->getHighestColumn(); // 取得总列数
		$data=array();
        for ($i = 1; $i <= $highestRow; $i++){
            $item['name']=$sheet->getCell('C'.$i)->getValue();
            $item['stu_id']=$sheet->getCell('B'.$i)->getValue();
            $item['teacher_name']=$sheet->getCell('A'.$i)->getValue();
            $this->Stumodel->insert($item);
        }
    }
}
