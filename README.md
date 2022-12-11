![image](https://user-images.githubusercontent.com/54909696/144947502-ef90f2a8-efcb-415d-b30d-5eba9d56fa65.png)
# <p align="center">🟣 Project 8 🟣<br /> Upgrade an existing application ToDo & Co</p>

## 🌞 How to contribute

See the contribution notice here : [How to contribute](CONTRIBUTING.md)

## 🧩 Prerequisites
The project use `Symfony 5.4.12` and require `PHP >8.0`.


## 📌️ Install steps
**1.** First you need to copy the repository by using git clone `https://github.com/ledukilian/LeduKilian_P8_26082022`

**2.** Use `composer install` command to install required packages

**3.** Copy the `.env` file located in the root folder to `.env.local` and fill `APP_ENV` and `DATABASE_URL`

**4.** Run `php bin/console server:start` to create a build for the app


## ⚙️ Database

**1.** Create database with `php bin/console doctrine:database:create`

**2.** Update the database schema with `php bin/console doctrine:schema:update --force`

You can use intial fixtures dataset with `php bin/console doctrine:fixtures:load`


## 🔐 Login
If you use the fixtures, you can use the admin account for the first login :

- [ ] **Username** : `Administrateur`
- [ ] **Password** : `test`

Or one of the 2  other default user account :

- [ ] `JudasBricot` | `test`
- [ ] `AlonzoSki` | `test`

## ✅ Testing

To run tests you can use `php bin/phpunit`

To generate HTML code coverage report you can use `php bin/phpunit --coverage-html public/test_coverage`

The last coverage report is available in `/public/test_coverage/`
