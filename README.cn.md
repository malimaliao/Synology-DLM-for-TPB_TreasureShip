#Synology-DLM-for-TPB_TreasureShip

> "TPB的宝船号" 是一个 [Synology Download Station](https://www.synology.com/en-global/dsm/packages/DownloadStation) 免费搜索插件。

为纪念<b>郑和</b> 1405 年至1433 年间开创的航海事业，因此本插件以 <b>宝船号</b> 命名，您可以使用它在在TPB网络中航行。

[English](README.cn.md) | [简体中文](README.cn.md)


#### 如何使用

##### 安装

* 下载 TPB_TreasureShip.dlm；
* 打开 Synology Download Station，然后在 Download Station 中单击设置按钮；
* 在设置页面中，点击[BT搜索选项卡] > [添加]；
* 指定 .dlm 文件的路径，然后单击 [添加] 将搜索引擎添加到列表中；
* 列表中添加搜索模块。 单击“确定”进行确认。；
  
##### 更新

> 您需要到此网页下载最新版本。


##### 特点

+ 所有展现内容基于爬虫索引内容来自TPB或TPB镜像，并不是本插件所提供或生产的内容。
+ 在插件设置中，支持自定义TPB主机接口（使用账号映射）。
+ 在插件设置中，支持自定义trackerlist.txt 接入到磁力资源中（使用密码映射），提高磁力下载速度。

###### 关于本插件中的设置：

     * 账号: 填写正确的网址后将替代默认的TPB api 接口, 例: https://TPB.com/api?q=
     * 密码: 填写正确的网址后则会替代默认的trackerlist，例: https://trackerlist.io/trackers_best.txt
  
  > tracklist.txt 名单不应该太长，太长会被系统截断


#### 关于TPB

> 你要知道TPB在全球的域名和镜像是不稳定的，最好的办法是通过插件设置用户名&密码（即映射到TPB的主机地址，而不是真的用户名）而不是让插件帮你找到合适的TPB。



##### 相关链接

TPB:

    * https://proxy-bay.click/
    * https://proxybay.pages.dev/
 
 
Tracker list:

    * https://ngosang.github.io/trackerslist/
    * https://trackerslist.com/