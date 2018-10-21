# GPS 后台Api接口

## 目录结构

[TOC]

---

## Token
* url(POST)
  
    http://47.106.64.85/v1/token

* header

NULL

* POST参数

    | appid | mobile | passwd | nonce | timestamp | sign |
    |-----------|-------------|---------------|------------|--------------------|--------|
    | gps | 15968158372 | 123456 | 124134124 | 时间戳 | MD5签名 |

* 返回数据参考
```json
{
    "code": 200,
    "message": "OK",
    "data": {
        "access_token": "o81xa76qtv5GFC4cOTyEIL2u1MSmgY4X",
        "refresh_token": "v34wH3ZWt26aY8ogkMCiF1cRAsGBeQbd",
        "uid": 4
    }
}
```

---

## 注册模块
* url(POST)

    http://47.106.64.85/v1/register

* header

    NULL
    
* post参数
  
    |mobile|password|name|username|sex|address|idcard|email|education|school|major|practice|hobby|speciality|honor|type|
    |-|-|-|-|-|-|-|-|-|-|-|-|-|-|-|-|
    |手机号(必)|密码(必)|姓名(必)|昵称(必)|性别(必)|地址|身份证|邮箱(必)|教育背景|学校|专业|实习经历|爱好|特长|荣誉|类型(0:学生 1:企业)|

* 返回数据参考:
```json
{
    "code": 200,
    "message": "OK",
    "data": {
        "mobile": "18958041798",
        "name": "KK",
        "sex": "f",
        "email": "weqrq@aliyun.com",
        "school": "ZUT",
        "username": "hh"
    }
}
```
---

## 学生模块

### 获取学生信息
* url(GET)
  http://47.106.64.85/v1/student/info
* header

    | access_token |
  | ---- |
  | string (Oauth 2.0)|

* post参数

    NULL

* 返回数据参考:
```json
{
    "code": 200,
    "message": "OK",
    "data": {
        "username": "ac", //昵称
        "name": "ac", //姓名
        "sex": "男",
        "address": "hz",
        "birthday": "2018-09-19",
        "idcard": "33018419960207864",
        "email": "fecodoo@aliyun.com",
        "photo": null,
        "mobile": "13588888888",
        "education": "bachelor", //教育背景
        "school": "ZJUT",
        "major": "EE", //专业
        "practice": null, //实习经历
        "hobby": "Game",
        "speciality": "Nothing", //特长
        "honor": "校会主席" //荣誉
    }
}
```

### 更改学生信息
* url(PUT)
  http://47.106.64.85/v1/student/update
* header

  | access_token |
  | ---- |
  | string (Oauth 2.0)|

* post参数

    |mobile|username|password|name|sex|photo|birthday|address|idcard|email|education|school|major|practice|hobby|speciality|honor|
    |-|-|-|-|-|-|-|-|-|-|-|-|-|-|-|-|-|
    | 手机号 | 昵称 |密码|姓名|性别|照片|生日|地址|身份证|电子邮件|教育经历|学校|专业|实习经历|爱好|特长|荣誉|

* 返回数据参考:
```json
{
    "code": 200,
    "message": "OK",
    "data": {
        "sex": "女"
        //省略
    }
}
```
### 上传学生头像
* url(GET)
  http://47.106.64.85/v1/student/getPortraitOss
* header
  
  | access_token |
  | ---- |
  | string (Oauth 2.0)|

* post参数

    NULL

* 返回数据参考:
```json
{
    "code": 200,
    "message": "OK",
    "data": {
        "accessid": "LTAIergYt01cBfX1",
        "host": "http://gps-server.oss-cn-shenzhen.aliyuncs.com",
        "policy": "eyJleHBpcmF0aW9uIjoiMjAxOC0xMC0wOSAxNToyODozOSIsImNvbmRpdGlvbnMiOlt7ImNvbnRlbnQtbGVuZ3RoLXJhbmdlIjoiMC0xMDQ4NTc2MDAwIn0seyJzdGFydHMtd2l0aCI6InBvcnRyYWl0XyJ9XX0=",
        "signature": "jH8OzKngedOQ50XSpDa5hShWg7k=",
        "expire": "2018-10-09 15:28:39",
        "callback": "eyJjYWxsYmFja1VybCI6Imh0dHA6XC9cLzQ3LjEwNi42NC44NVwvdjFcL3N0dWRlbnRcL2dldFBvcnRyYWl0Q2FsbGJhY2siLCJjYWxsYmFja0JvZHkiOiJmaWxlbmFtZT0ke29iamVjdH0mc2l6ZT0ke3NpemV9Jm1pbWVUeXBlPSR7bWltZVR5cGV9JmhlaWdodD0ke2ltYWdlSW5mby5oZWlnaHR9JndpZHRoPSR7aW1hZ2VJbmZvLndpZHRofSIsImNhbGxiYWNrQm9keVR5cGUiOiJhcHBsaWNhdGlvblwveC13d3ctZm9ybS11cmxlbmNvZGVkIn0=",
        "dir": "image/"
    }
}
```

### 获取已加入的班级内课程成绩
* url(GET)
  http://47.106.64.85/v1/student/classScore
* header

  | access_token |
  | ---- |
  | string (Oauth 2.0)|

* post参数

    NULL

* 返回数据参考:
```json
{
    "code": 200,
    "message": "OK",
    "data": [
        {
            "class": "操作系统实验班",//课程名称
            "name": "ac",//学员姓名
            "school": "ZJUT",//学员学校
            "score": 65,//学员成绩
            "progress": 21//当前课程进度
        },
        {
            "class": "数据结构",
            "name": "ac",
            "school": "ZJUT",
            "score": 89,
            "progress": 78
        }
    ]
}
```

### 获取已加入班级及课程信息
* url(GET)
  http://47.106.64.85/v1/student/classInfo
* header

  | access_token |
  | ---- |
  | string (Oauth 2.0)|

* post参数

    NULL

* 返回数据参考:
```json
{
    "code": 200,
    "message": "OK",
    "data": {
        "class": [
            {
                "id": 2,//班级ID
                "name": "数据结构",//班级名称
                "position": "SH",//班级地址
                "header": "颜世航",//班主任
                "eid": 1,//企业ID
                "picture": null,//封面图片（暂无）
                "recon": 0,//是否被推荐（1是 0否）
                "max": 6,//学生最大加入人数
                "header_info": "浙工大讲师",//班主任信息
                "info": "数据结构实验班",//班级信息
                "evaluate": null,//学生评价
                "content": "期末演讲",//考核方式
                "priod": 8//已开办期数
            }
        ],
        "course": [
            {
                "id": 0,//数组下标
                "length": 7,//课程期数
                "type": "CS",//课程类型
                "video": null,//视频地址（暂无）
                "info": "cs course",//课程信息
                "title": "cs course",//课程名称
                "owner": "Baidu",//课程所有者
                "teacher": "BC",//老师
                "cid": 2//课程ID
            }
        ]
    }
}
```

### 更改班级
* url(PUT)
  http://47.106.64.85/v1/student/changeClass
* header

  | access_token |
  | ---- |
  | string (Oauth 2.0)|

* post参数

    | type | cid |
    |------|-----|
    | 更改类型(0为退出班级 1为加入班级) | 课程ID |

* 返回数据参考:

```json
//已加入该班级
{
    "code": 401,
    "message": "已在该班级内",
    "data": []
}
//加入(退出)成功
{
    "code": 200,
    "message": "OK",
    "data": []
}
//退出失败
{
    "code": 401,
    "message": "你不在该班级内",
    "data": []
}
```

---

## 流程

-  客户端获取浏览器中COOKIE，若无COOKIE则调用http://47.106.64.85/v1/token接口获取token
-  访问相应的url，例如：http://47.106.64.85/v1/student/info
- Token使用了Oauth 2.0协议,请将Token字符串前加上Bearer+空格,放入请求头部authorization字段中
