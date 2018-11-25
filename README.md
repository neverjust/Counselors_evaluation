# 辅导员评测后台接口文档

- 无特别说明，API返回值及前端传递的值均为json格式

## User

功能：学生登录，获取学生评测信息

### login

- 功能：学生登录
- HTTP METHOD: POST
- url:   /user/login
- request:
```
  {
      'studentId':string, // 学号
      'password':string,  // 密码
  }
  ```

- return:
```
  {
      'date':string,   	 // 无数据
      'errorCode':string,  // 状态码
      'errorMsg':string    // 错误信息
  }
```

### store

* 功能：存储学生提交的表单

- HTTP METHOD: POST

- url:   /user/store

- request:

 ``` 
    {
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

```
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
- HTTP METHOD: GET
- url:   /admin/teachers
- request:

```
  {
  }
```

- return:

```
   {
      'date':array,   	 // 所有辅导员数据
      'errorCode':string,  // 状态码
      'errorMsg':string    // 错误信息
  }
```

### download_all

- 功能：下载所有辅导员数据
- HTTP METHOD: GET
- url:   /admin/download_all
- request:

```
{
    
}
```

return:

```
{
    
}
```

### teacher

- 功能：查看所有辅导员数据
- HTTP METHOD: POST
- url:   /admin/teachers
- request:

```
  {
  	'name':string             // 老师姓名
  }
```

- return:

  ```
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
- HTTP METHOD: GET
- url:   /admin/download_one
- request:

``` 
  {
    'name':string //老师姓名
  
  }
```

- return:

```
  {
  
  }
```



