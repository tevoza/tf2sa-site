# tf2sa-site - reii 414 practical
site has been deployed [here](http://tf2sa.xyz)

# todo
 - add progress graphs for different classes.

![alt text](https://github.com/tevoza/tf2sa-site/blob/main/sql/erd.png?raw=true)

# building the project
 - clone the repo `git clone https://github.com/tevoza/tf2sa-site`
 - in the project root, php depedencies are installed with `composer` (a php package manager):
  `composer update`
 - rename `.env.example` to `.env` and edit the following lines with your database details
   assuming you have cloned into your web server's root, the current config should work.
 - create a `files` directory in `src/forum` and give the webserver write permissions to it. 
 - an sql dump file is located in `sql/` 
