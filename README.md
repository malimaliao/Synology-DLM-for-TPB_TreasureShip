#TPB TreasureShip

> "TPB 宝船号" 是一个基于[Synology Download Station](https://www.synology.com/en-global/dsm/packages/DownloadStation)开发的扩展搜索插件。在群晖NAS的Download Station中使用它可以轻松接入到<b>The Pirate Bay</b> 或它相关的镜像站点获取索引数据。

[English](README.en.md) | [简体中文](README.md)

* English introduction based on Google Translate。

##### 声明：

> TPB 是插件对<b>The Pirate Bay</b>的缩写，本程序仅作为学习研究之用，基于爬虫技术由使用者主动采集来自于TPB返回的索引数据，所有内容及行为均和本作者无关！


##### 安装:

* 下载 TPB_TreasureShip.dlm；
* 打开 Synology Download Station，然后在 Download Station 中单击设置按钮；
* 在设置页面中，点击[BT搜索选项卡] > [添加]；
* 指定 .dlm 文件的路径，然后单击 [添加] 将搜索引擎添加到列表中；
* 列表中添加搜索模块。 单击“确定”进行确认；
  
##### 更新:

> 插件不支持自动升级，如果需要新版本需要到此网页下载最新版本。


##### 特点:

+ 所有展现内容基于爬虫索引内容来自TPB或TPB镜像，并不是本插件所提供或生产的内容。
+ 在插件设置中，支持自定义TPB主机接口（使用账号映射）。
+ 在插件设置中，支持自定义trackerlist.txt 接入到磁力资源中（使用密码映射），提高磁力下载速度。

###### 关于本插件中的设置：

     * 账号: 填写正确的网址后将替代默认的TPB api 接口, 例: https://TPB.com/api?q=
     * 密码: 填写正确的网址后则会替代默认的trackerlist，例: https://trackerlist.io/trackers_best.txt
     * 除非您知道如何获取TPB api接口和trackerlist.txt的地址，否则请不要设置账号密码作为映射，它并不是必填的。如果您不设置账号和密码作为映射接口的话，插件会自动尝试获取云端的接口，若获取失败则会使用内置的接口地址
  
  > tracklist.txt 名单不应该太长，太长会被系统截断


#### 关于TPB & 特别说明

> TPB：海盗湾的简称

* 海盗湾在全球是不断被封杀屏蔽的，推荐通过插件设置账号&密码映射自己找到的接口和trackerlist（即设置用户名映射为TPB接口地址，设置密码映射为Trackerlist地址；）；

* 当使用者未设定账号/密码映射时候，插件将自动通过http访问[ts.css](ts.css)并从中获得接口的地址参数；

* 接口优先级：[用户自定义映射接口] -> [ts.css接口] -> [插件默认接口]；



##### 相关链接

TPB(The Pirate Bay):

    * https://thepiratebay.org
    * https://proxybay.pages.dev
 
 > 网络存在部分TPB的存档站点（非镜像），它们并不能获取到api接口请注意区分。
 

Tracker list:

    * https://ngosang.github.io/trackerslist/
    * https://github.com/XIU2/TrackersListCollection
    * https://newtrackon.com
