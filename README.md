

# 辅导员评测后台接口文档

- 无特别说明，API返回值及前端传递的值均为json格式

- ```json
  //状态码
  0~0
      0	 //成功
  
  1~100	//用户
      1		//用户已经登录
      2		//用户未登录
      3		//用户未登录或权限不够
      4		//用户第一次登录
      5		//用户已经全部评测结束
      6		//账号密码错误
      7		//该用户是超级管理员
      8		//非法操作 权限不够
      9		//查无此辅导员
  	10		//该学生不在评测范围内
  	11		//该学生不评测该辅导员
  	12		//该辅导员已经评测
  
  100~110 //服务器
      100		//服务器插入错误
      101		//传输参数不完全
  
  
  
  ```

## User

功能：学生登录，获取学生评测信息

### login

- 功能：学生登录
- HTTP METHOD: POST
- url:   /user/login
- request:

- ``` json
  {
      'studentId':string, // 学号
      'password':string,  // 密码
  }
  ```

- return:

  ```json
  {
      'date':string,   	 // 无数据
      'errorCode':string,  // 状态码
      'errorMsg':string    // 错误信息
  }					
  ```

### remains

- 功能：学生登录

- HTTP METHOD: GET

- url:   /user/remains

- request:

- ```json
  {
      'studentId':string, // 学号
      'password':string,  // 密码
  }
  ```

- return:

  ```json
  {
      'date':string,   	 // 无数据
      'errorCode':string,  // 状态码
      'errorMsg':string    // 错误信息
  }					
  ```

### 

### store

* 功能：存储学生提交的表单

- HTTP METHOD: POST

- url:   /user/store

- request:

  - ```json
    {
    	'teacher':string
        'ques_1':float,
        'ques_2':float,
        'ques_3':float,
        'ques_4':float,
        'ques_5':float,
        'ques_6':float,
        'ques_7':float,
        'ques_8':float,
        'ques_9':float
    }
    ```

-  return:

  - ```json
    {
    'date':string,   	 // 无数据
    'errorCode':string,  // 状态码
    'errorMsg':string    // 错误信息
    }
    ```

## Admin

功能：查看、下载辅导员评测数据

### teachers

- 功能：查看所有辅导员数据
- HTTP METHOD: POST
- url:   /admin/teachers
- request:

- ```ss 
  {
  }
  ```

- return:

  ```json
   {
      'date':array,   	 // 所有辅导员数据
      'errorCode':string,  // 状态码
      'errorMsg':string    // 错误信息
  }
  ```

### download_all

- 功能：下载所有辅导员数据
- HTTP METHOD: POST
- url:   /admin/download_all
- request:

- ```ss 
  
  ```

- return:

  ```json
  
  ```

### 

### teacher

- 功能：查看所有辅导员数据
- HTTP METHOD: POST
- url:   /admin/teacher
- request:

- ```json
  {
  	'name':string             // 老师姓名
  }
  ```

- return:

  ```json
  {
      'date':array,  
      {
      	'questions':array // 各问题平均分
      	{
              'ques_1':float,
              'ques_2':float,
              'ques_3':float
              'ques_4':float,
              'ques_5':float,
              'ques_6':float,
              'ques_7':float,
              'ques_8':float,
              'ques_9':float,
      		'ques_all':float
  		},
  		'unfinished':int,
  		'finished':int,
  		'names'：
      	
  	}
      'errorCode':string,  // 状态码
      'errorMsg':string    // 错误信息
  }
  ```

### download_one

- 功能：查看所有辅导员数据
- HTTP METHOD: POST
- url:   /admin/download_one
- request:

- ```ss 
  {
  }
  ```

- return:

  ```json
  {
  }
  ```

### 

### 



