# GPS 后台Api接口

## 目录结构

[TOC]

---

## 学生模块

### 获取学生信息
* url(POST)
  http://47.106.64.85/v1/student/info
* header

  | uid  | appid | access_token |
  | ---- | ----- | ------------ |
  | int  | int   | string       |

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
* url(POST)
  http://47.106.64.85/v1/student/update
* header

  | uid  | appid | access_token |
  | ---- | ----- | ------------ |
  | int  | int   | string       |

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

### 获取已加入的班级内课程成绩
* url(POST)
  http://47.106.64.85/v1/student/classScore
* header

  | uid  | appid | access_token |
  | ---- | ----- | ------------ |
  | int  | int   | string       |

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
* url(POST)
  http://47.106.64.85/v1/student/classInfo
* header

  | uid  | appid | access_token |
  | ---- | ----- | ------------ |
  | int  | int   | string       |

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
* url(POST)
  http://47.106.64.85/v1/student/changeClass
* header

  | uid  | appid | access_token |
  | ---- | ----- | ------------ |
  | int  | int   | string       |

* post参数

    |type|cid|
    |-|-|
    |更改类型（0为退出班级 1为加入班级）|课程ID|

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