# Jotebook

This application is useful if you need to have a dynamic notebook to be able to write pages composed by notes that can be then browsed singularly for quick reference.

> This is a small app I developed for personal use. I started to use it everyday so I tought to share it. This is a work in progress and not a stable version. I am adding features while I am using it and my needs growing up. 

## Wiki

https://github.com/TheoRelativity/Jotebook/wiki

### Requirements

* PHP 7 up and running on your host machine
* Web Browser

### First Installation

1. Download or clone this repo in your computer. 
2. Copy the Jotebook folder into your web server directory.
3. Make a new folder where you prefer into your computer. Copy its location.
4. Open with a text editor the application.php file in Jotebook/config folder
5. Find the line
```php
/*
  * Folder that contains the Jotebook's folders.
*/
define("DATA_DIRECTORY","");
```
6. Paste the location between ""
