# cascade

An abstraction class for the Cascade Server by Hannon Hill

# Requirements

- PHP >= 7.0

# Installation

1. Download and Install PHP Composer.

   ``` sh
   curl -sS https://getcomposer.org/installer | php
   ```

2. Add the following to your composer.json file.
   ```json
	"repositories": [
        {
        	"type" : "vcs",
        	"url": "https://github.com/unoadis/cascade"
        }
   ```
   ```json
   "require" : {
        "unoadis/cascade" : "dev-master"
   }
   ```

3. Then run Composer's install or update commands to complete installation.

   ```sh
   php composer.phar install
   ```

4. Authentication
   ```
   Copy .env.example to .env and populate values
   ```
   or
   pass in credentials
   ```
   $page = new Page(['base_uri'=>'https://mycmsdomain.com/',
      'username'=>'myusername',
      'password'=>'mypassword',);
   ```

# Example

## Page
```php
require '../vendor/autoload.php';

use Cascade\Page;

try {
      echo "<pre>";
      $page = new Page();
      $page->read('sitename','index');
      echo $page->getId()."\n";
      echo "</pre>";
} catch (Exception $e) {
      echo($e->getMessage());
}
```

## Site
```php
require '../vendor/autoload.php';

use Cascade\Site;

try {
      echo "<pre>";
      $site = new Site();
      $site->read('');
      echo $site->getId()."\n";
      echo "</pre>";
} catch (Exception $e) {
      echo($e->getMessage());
}
```