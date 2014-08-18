NERDZ
=====
*A mix between a Social Network and a forum*


This is the code repository for [nerdz.eu](http://www.nerdz.eu).

About
=====
Nerdz is a mix between a forum and a social network. To understand what NERDZ really is, the easy way is to go on [nerdz.eu](http://www.nerdz.eu) and enjoy the experience.

Development
===========

There is a lot of work to do:

*  <del> Move templates to separate repositories, so designers can just fork the template they need and work on it. </del>
*  <del> Improve the User Experience (work on the design of templates located in the `tpl` folder. </del>
*  <del> Create a file containing associations between template number and name, and... </del>
*  <del> Create an option to make it possible to choose between templates in the user's preferences. </del>
*  Create REST APIs (in a new repository, written in go) and create the required tables and options in this part of the code.
*  On [nerdz.eu](http://www.nerdz.eu), once the APIs are ready, a reverse proxy Node server will run on api.nerdz.eu:80, hopefully giving us proper APIs!
*  And more...


Over the years the authors of the original code also wrote "temporary" files and C utils to help run the site. Those files are not included in this repository. Those files are not just utilities, they're also used on the website for "statistical" features like the (Top|Monthly) 100 list. They will not work without them, and since we're not providing them, they will not work _period_. Don't ask why they aren't working in an issue.

Contributing
============
You're welcome to contribute via pull request.

Before you send a pull request, you probably want to setup your local version of nerdz, so you can run your changes on a test server before requesting a pull. To do that, just follow the instructions in `setup/README.md`

Get it
======
To get a complete version of nerdz (including all templates, included as Git submodules) run:
```bash
git clone --recursive git://github.com/nerdzeu/nerdz.eu.git
```

Update [hint]
======
To update your local version of the repository (including the submodules) use
```bash
cd nerdz.eu
git checkout master
git pull
git submodule update --init --recursive
```


[![Bitdeli Badge](https://d2weczhvl823v0.cloudfront.net/nerdzeu/nerdz.eu/trend.png)](https://bitdeli.com/free "Bitdeli Badge")