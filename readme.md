Yet Another TODO List Project
=============================

This is a simple application using the [Nette](https://nette.org). It is a basic application allowing you to create and manage multiple TODO lists for multiple users. 

Requirements
------------

- PHP 7.1
- Access to a database
- Composer installed (see [getcomposer.org](https://getcomposer.org))


Installation
------------

Use the following simple commands:

	git clone <this-project>
	cd this-project-folder
	composer install
	cp app/config/config.example.neon app/config/local.neon

Then, execute the script `db.sql` in your database and edit config file `app/config/local.neon` to match credentials to your database.


Make directories `temp/` and `log/` writable.


Web Server Setup
----------------

The simplest way to get started is to start the built-in PHP server in the root directory of your project:

	php -S localhost:8000 -t www

Then visit `http://localhost:8000` in your browser to see the welcome page.

For Apache or Nginx, setup a virtual host to point to the `www/` directory of the project and you
should be ready to go.

**It is CRITICAL that whole `app/`, `log/` and `temp/` directories are not accessible directly
via a web browser. See [security warning](https://nette.org/security-warning).**
