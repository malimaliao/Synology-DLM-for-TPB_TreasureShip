#TPB TreasureShip

> "TPB TreasureShip" is a search plug -in developed based on [Synology Download Station](https://www.synology.com/en-global/dsm/packages/DownloadStation), Using it in the Download Station of Synology NAS, it can easily access the index data to The Pirate Bay or its related mirror site.


[English](README.en.md) | [简体中文](README.md)

##### statement:

> TPB is the abbreviation of The Pirate Bay, English introduction based on Google Translate. This program is only for learning and research. Based on crawlers, the user actively collects index data returned from TPB. All content and behavior have nothing to do with this author!


##### install:

* Download the [TPB_TreasureShip.dlm];
* Open your Synology Download Station, and in Download Station, click the button for Setting;
* In Setting page, click [BT Search tab] > [Add]
* Specify the path of your .dlm file and click [Add] to add the search engine into the list.
* The search module is added into the list. Click [OK] to confirm. 
* Enjoy!
  
##### update:

> You need to this web page download last version.


##### features:

+ This plug-in is based on crawler index content from TPB or TPB mirror, not the content provided or produced by this plug-in.
+ In the plugin settings, support custom TPB host interface (using account mapping).
+ In the plugin settings, support custom trackerlist.txt access to magnetic resources (using password mapping) to improve magnetic download speed

###### About the settings in this plugin:

    * login username: it will be used as an alternative TPB api url, For example: https://TPB.com/api?q=
    * login password: it will be used as an alternative trackerlist txt url, For example: https://trackerlist.io/trackers_best.txt
    * Unless you know how to get the address of TPB api interface and trackerlist.txt, please do not set the account and password as mapping, it is not required. If you do not set the account and password as mapping interface, the plugin will automatically try to get the cloud interface, if the acquisition fails, it will use the built-in interface address

> The tracklist.txt list should not be too long. It is recommended to have no more than 50 items. If it is too long, it will be truncated by the system


#### About TPB & Special Note

> TPB: The Pirate Bay

* The Pirate Bay is constantly being blocked around the world. It is recommended to use the plugin to set up account & password mapping to the interface and trackerlist you find yourself (i.e. set the username to map to the TPB interface address, and set the password to map to the Trackerlist address);

* When the user does not set the account/password mapping, the plugin will automatically access [ts.css](ts.css) through http and obtain the interface address parameters from it;

* Interface priority: [User-defined mapping interface] -> [ts.css interface] -> [Plugin default interface];


##### Links

TPB(The Pirate Bay):

    * https://thepiratebay.org
    * https://proxybay.pages.dev
 
 > There are some archive sites (not mirrors) of TPB on the Internet. They do not get API interfaces. Please be careful to distinguish them. They will be marked with archive symbols on the website.
 
Tracker list:

    * https://ngosang.github.io/trackerslist/
    * https://github.com/XIU2/TrackersListCollection
