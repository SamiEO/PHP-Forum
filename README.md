# PHP-Forum
This is my project for the web apps PHP/SQL course in Laurea University of Applied Sciences. As this is done for the course the 
installation and usage portions of this file may refer to documentation provided by the lecturer.

The goal of the project is to create a simple internet forum that allows users to create accounts and the allows them to create
new topics and add comments to already existing topics.

# Installation
Installing and running PHP-Forum is relatively simple. In order to run the php-scripts and connect to the database you need to have 
XAMP installed. Copy the project files into the htdocs folder under directly under the XAMP installation folder. It's a good idea
to create a new folder there and copy the php files and css, js and fonts folders there. The following parts require you to use XAMP. Click "Start" for both Apache and MySQL. Open PHPMyAdmin by clicking MySQLs "Admin"-action. This should open your default browser and the PHPMyAdmin view. Create a new database called "Forum" and import tables to it from the forum.sql file. Now the forum should work locally and be accessable via the url: http://localhost/foldername/index.php. Note that you should replace the "foldername" with whatever you named the folder with the project files. If you have changed the ports used by XAMP you may have to add it to the URL (for example: http://localhost:8080/foldername/index.php).

# Usage
 Once you have the project locally running you can see how it works in action. You can also follow the instructions provided by the course to install it to Eclipse and use it for debugging and seeing how the code works.
 
 The basic usage of the forum is registration, logging in or out, creating new topics and commenting on existing ones. You can also edit old comments and topics that you have made.
