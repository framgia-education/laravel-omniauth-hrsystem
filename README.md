
## Documentation

To get started with **FramgiaAuth**, use Composer to add the package to your project's dependencies:

    composer require framgia-education/laravel-omniauth-hrsystem

### Configuration

After installing the **FramgiaAuth** library, register the `Framgia\Education\Auth\FramgiaAuthServiceProvider` in your `config/app.php` configuration file:

```php
'providers' => [
    // Other service providers...

    Framgia\Education\Auth\FramgiaAuthServiceProvider::class,
],
```

Also, add the `FAuth` facade to the `aliases` array in your `app` configuration file:

```php
'aliases' => [
    // Other aliases

    'FAuth' => Framgia\Education\Auth\Facades\FramgiaAuth::class,
],
```

You will also need to add credentials for the OAuth services your application utilizes. These credentials should be placed in your `config/services.php` configuration file, and use the key `framgia`. For example:
```php
'framgia' => [
    'client_id' => 'your-framgia-auth-app-id',
    'client_secret' => 'your-framgia-auth-app-secret',
    'redirect' => 'http://your-callback-url',
],
```
### Basic Usage

Next, you are ready to authenticate users! You will need two routes: one for redirecting the user to the OAuth provider, and another for receiving the callback from the provider after authentication. We will access **Framgia Auth** using the `FAuth` facade:

```php
<?php

namespace App\Http\Controllers\Auth;

use FAuth;

class LoginController extends Controller
{
    /**
     * Redirect the user to the GitHub authentication page.
     *
     * @return Response
     */
    public function redirectToFramgiaAuth()
    {
        return FAuth::redirect();
    }

    /**
     * Obtain the user information from GitHub.
     *
     * @return Response
     */
    public function handleFramgiaAuthCallback()
    {
        $user = FAuth::user();

        // $user->token;
    }
}
```

The `redirect` method takes care of sending the user to the **Framgia Auth** provider, while the `user` method will read the incoming request and retrieve the user's information from the provider.

Of course, you will need to define routes to your controller methods:

```php
Route::get('login/framgia', 'Auth\LoginController@redirectToFramgiaAuth');
Route::get('login/framgia/callback', 'Auth\LoginController@handleFramgiaAuthCallback');
```

#### Retrieving User Details

Once you have a user instance, you can grab a few more details about the user:

```php
$user = FAuth::user();

$token = $user->token;
$refreshToken = $user->refreshToken; // not always provided
$expiresIn = $user->expiresIn;

// Example infomation:
$user->getId(); // Or maybe $user->id
$user->getName(); // Or maybe $user->name
$user->getEmail(); // Or maybe $user->email
$user->getAvatar(); // Or maybe $user->avatar
$user->getGender(); // Or maybe $user->gender
$user->getBirthday(); // Or maybe $user->birthday
$user->getPhoneNumber(); // Or maybe $user->phoneNumber

// All infomation about user will be stored here:
$user->getRaw(); // Or maybe $user->user
```

#### Retrieving User Details From Token

If you already have a valid access token for a user, you can retrieve their details using the `userFromToken` method:

```php
$user = FAuth::userFromToken($token);
```
