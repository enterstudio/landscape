Application Landscape Database
==============================

This application can be used to register your application landscape and make it accessible to the rest of your organisation.

The application landscape can be exported in an ArchiMate format. With Archi, a free ArchiMate modelling tool, you can create a graphical overview of your application landscape. You can use the images you create with Archi in presentations and documentation.

Banshee
-------
This application has been built upon Banshee, which is a PHP website framework with a main focus on security. It has a Model-View-Controller architecture and uses XSLT for the view. MySQL is being used as the default database, but with little effort other databases can be used as well. Although it's called a framework, it comes with a ready to use CMS, lots of libraries and modules like a forum, weblog and a guestbook.

Configure your webserver
------------------------
Use the directory 'public' as the webroot directory and allow PHP execution. If you use the Hiawatha webserver, you can use the following configuration:

	UrlToolkit {
		ToolkitID = banshee
		RequestURI isfile Return
		Match ^/(css|files|fonts|images|js)(/|$) Return
		Match ^/(favicon.ico|robots.txt)$ Return
		Match [^?]*(\?.*)? Rewrite /index.php$1
	}

	Directory {
		DirectoryID = files
		Path = /files
		StartFile = index.html
		ShowIndex = yes
		ExecuteCGI = no
	}

	Directory {
		DirectoryID = static
		Path = /css, /fonts, /images, /js
		ExpirePeriod = 2 weeks
	}

	VirtualHost {
		...
		UseToolkit = banshee
		UseDirectory = static, files
	}


For Apache, there is a .htaccess file in the 'public' directory which contains the required URL rewriting rules.

Configure PHP
-------------
Banshee needs PHP's MySQL, XML/XSLT and GD module. Use the following PHP settings:

	allow_url_include = Off
	cgi.fix_pathinfo = 0 (when using FastCGI PHP), 1 (otherwise)
	date.timezone = <your timezone>
	magic_quotes_gpc = Off
	register_globals = Off

Configure your database
-----------------------
Open the website in your browser and follow the instructions on your screen. In case of an error, add /setup to the URL.

Configure Cronjob
-----------------
Configure cronjob to run the script database/backup_database once per day.

Configure Banshee
-----------------
Go to the Settings page in the CMS and replace the present e-mail addresses with your own. Before going live, set the DEBUG_MODE flag in settings/website.conf to 'no'.
