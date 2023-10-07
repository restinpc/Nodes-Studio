# Nodes Studio - Web Framework

Software platform for express development of web-sites based on PHP, HTML, CSS, MySQL, JavaScript, jQuery.

![](https://github.com/restinpc/Nodes-Studio/blob/master/nodes.jpg?raw=true)

Nodes Studio is a framework (contain legacy, require refactoring) with integrated content management system (CMS), which is successful solutions for express development.

Framework provides developers with an object-oriented representation of the site and a library of functions to work with it.

Integrated CMS allows to fully manage both the content of the site and its software-functional component.

[Software technical description][site] ([Website installed example][demo]).

## Installation

Nodes Studio requires PHP 5.3 and MySQL 5.1 support or higher to run.

1. Download and extract the Nodes Studio [Archive][download] | [Mirror 1][mirror] | [Mirror 2][mirror_2].

2. Copy the contents of the folder /public_html to the root directory of the site.

3. Set permissions for site files 0755 (rwxr-xr-x).

4. Create a MySQL database for the site and the user with full privileges.

5. Go to the site through a browser, where you are installing and updating framework to the latest version.

6. Set up cron - program to work with the file /cron.php with an interval of 1 minute.

7. Sign in to admin panel /admin, where perform the initial configuration of the site ("Config").

8. Develop the necessary backend modules, scripts, styles, customize template and fill the site content.

## Load Test

Nodes Studio load testing results (public hosting)

[![Load testing](https://github.com/restinpc/Nodes-Studio/blob/master/loadest_nodes.png?raw=true)][loadest]  
  
  
Apache HTTP server load testing results (for comparison)

[![Load test](https://github.com/restinpc/Nodes-Studio/blob/master/loadest_apache.png?raw=true)][loadest]


## Contacts

If there are any questions, or you need more information, please contact us via:

[Github][gh] | [Email][email]

[demo]: <https://framework.nodes-studio.com>
[site]: <https://nodes-studio.com>
[download]: <https://nodes-studio.com/source/nodes_studio.zip>
[mirror]: <https://drive.google.com/open?id=0B5PrSx06jievRVdHWHZDdUU3UmM>
[mirror_2]: <https://www.dropbox.com/sh/d7z6lkiztlv4ghz/AABGAibKZt4fyr2tPLOoTo8Xa?dl=0>
[gh]: <https://github.com/restinpc>
[email]: <mailto:developing@nodes-tech.ru>
[loadest]: <http://loadest.io>
