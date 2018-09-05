# jFW Php Lightweight Framework

jFW is a lightweight Php Framework. It is designed to implement the MVC framework but with minimum dependencies initially. 
Additional functionality can be added in but jFW will work by itself for any small projects.

## Getting Started

Currently, the only way to obtain all of the files is to either create a clone of the git repository or download the files directly.

Your sever needs the minimum requirements of Apache, Php, and MySQL (although MySQL can be optional).

Once in place, there are two SQL files you can use if you wish to maintain settings from the database instead of the configuration files. One is within the **config** folder and the other is in the **config/settings** folder. If you choose to use the database, it is suggested to use both of these SQL files as templates for the tables. 

Using the database isn't necessary however. You can simply edit the files within the **config/settings** folder manually if you desire and achieve the same effect.

Once these settings are in place you can begin programming your site as needed.

### Prerequisites

The current configuration has only been tested on the following
```
PHP 7.0
Apache 2.4.23
Mysql 5.7.4
```

## Authors

* **Joshua Weeks** - *Initial work* - [jFW](https://github.org/jokka85)

## License

This project is licensed under the MIT License - see the [LICENSE.md](LICENSE.md) file for details

## Acknowledgments

* phpUnit was used for testing during and after development of the project.
