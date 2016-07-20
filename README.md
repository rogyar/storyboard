# Storyboard

Storyboard is a small system that allows to pass some text content via POST request, write this content to the file automatically and show this content as a web page.
 
## Installation

- Install [Composer](https://www.google.com) 
- Run `composer install` command using CLI from the Storyboard root directory
- Copy etc/config.yml.dist to etc/config.yml
- Set up your own secret token in etc/config.yml
- Make sure var/storage.html is writable by webserver

## Usage
- To write some content into the storage, just pass the content via `data` parameter using POST request to http://yourstoryboard.com/?token=yourtoken (change to your own URL)
- To read the written content just go to the index page http://yourstoryboard.com/?token=yourtoken (change to your own URL)

You are also able to edit the template file. For this change `templatePath` value to some custom template and make your changes inside of this template. 
