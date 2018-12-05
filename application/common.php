<?php
// +----------------------------------------------------------------------
// | ThinkPHP [ WE CAN DO IT JUST THINK ]
// +----------------------------------------------------------------------
// | Copyright (c) 2006-2016 http://thinkphp.cn All rights reserved.
// +----------------------------------------------------------------------
// | Licensed ( http://www.apache.org/licenses/LICENSE-2.0 )
// +----------------------------------------------------------------------
// | Author: 流年 <liu21st@gmail.com>
// +----------------------------------------------------------------------

// 应用公共文件

/**  
* 前后端统一接口
*
* @access public 
* @param array  $data      传输的数据
* @param int    $errorCode 错误代码
* @param string $message   错误信息
* @return json
*/ 
function msg($data,$errorCode,$message)
{
    $info = ['data'=>$data,'errorCode'=>$errorCode,'errorMsg'=>$message];
    return json_encode($info,JSON_UNESCAPED_UNICODE);
}

/**  
* 获取问题序号对应的问题内容
*
* @access public 
* @param int $num 问题数字
* @return string
*/ 
function question($num)
{
	switch ($num) {
		case 1:
			return "作风正派，责任心强，工作有激情";
			break;
		case 2:
			return "工作能力强，有成效";
			break;
		case 3:
			return "关心同学，帮助解决思想上的困惑";
			break;
		case 4:
			return "引导同学成长成才，指导职业规划、就业考研和创新创业";
			break;
		case 5:
			return "关注同学学业，深入课堂，关心同学的学习进步和变化";
			break;
		case 6:
			return "深入寝室，与同学交心谈心，开展寝室文化建设和卫生安全教育活动";
			break;
		case 7:
			return "在各类评选、评定中公正无私，真实透明";
			break;
		case 8:
			return "关心关爱家庭经济困难学生";
			break;
		case 9:
			return "妥善协调同学关系，及时化解各种矛盾";
			break;
		
		default:
			return "程序错误";
			break;
	}
 
}