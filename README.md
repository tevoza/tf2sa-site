# tf2sa-site - reii 414 practical
 - Adam Coetzee - 29982995  
 - Thato Tau - 31542115

site has been deployed [here](http://tf2sa.xyz)

![alt text](https://github.com/tevoza/tf2sa-site/blob/main/sql/erd.png?raw=true)

# building the project
 - clone the repo `git clone https://github.com/tevoza/tf2sa-site`
 - project structure
 ```
├── composer.json  
├── composer.lock  
├── mysql_updater.py  
├── README.md  
├── .env.example  
├── sql  
│   ├── dump.sql   
├── src  
│   ├── assets   
│   ├── auth  
│   ├── data  
│   ├── demos  
│   ├── forum  
│   │   ├── files
│   │   └── ...  
│   ├── index.php  
│   ├── maps  
│   ├── rules  
│   ├── stats  
│   └── templates  
 ```
  - in the project root, php depedencies are installed with `composer` (a php package manager):
  `composer update`
   - rename `.env.example` to `.env` and edit the following lines with your database details
   ```
   MYSQL_USR="usr"
   MYSQL_PWD="pwd"
   MYSQL_DB="db"
   WWW="/tf2sa-site/src"
   ```
   the `WWW` variable is used for page links, and is adjusted relative to the root pointed to by the web server  
   assuming you have cloned into your web server's root, the current config should work.
    - an sql dump file is located in `sql/`
  
