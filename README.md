# Chalk
Allows your users to authenticate against a Blackboard Learn installation.

By pointing Chalk at your web-facing installation of the Blackboard Learn software, you can provide a username/password pair and get back a boolean value indicating whether or not authentication is successful using those credentials.

## Usage
Couldn't be simpler.

```php
// URL of installation, false means don't validate SSL.
$client = new \Chalk\Authenticator('https://blackboard.mywebsite.com/', false);
$successful = $client->authenticate('username', 'password'); // True means login was successful.
```

## Precautions
Your Blackboard learn installation should be accessible over HTTPS only, as should the server that Chalk is being used from. If there is any plain HTTP in there your username and password will be sent in the clear.

Also setting the second constructor parameter to `false` to disable SSL verification opens you way up to a [MITM attack](https://en.wikipedia.org/wiki/Man-in-the-middle_attack) etc. The only reason to do so is if you're using a self-signed certificate on your Blackboard installation or one from an authority that [cURL doesn't trust for whatever reason](https://curl.haxx.se/docs/sslcerts.html). 

Additionally, the result passed back by an instance of `Authenticator` shouldn't be taken verbatim. The script works by checking if the page it recieves contains a flag (string of text) that indicates that the user has logged in successfully. By default, this is the string:

```
Modules you are studying:
```

It might be possible to craft a username or password which injects the flag into the login page and tricks Chalk into thinking the login attempt was successful when it wasn't. I haven't been able to do this, but that doesn't mean it isn't possible, depending on your server configuration. The flag can be changed using the third constructor parameter for `Authenticator`.

## Limitations
If you just want to check whether a set of credentials is valid or not, Chalk might be the library for you. It can't do anything else at all. Give it a username and password and get back a boolean. That's it.

## Contributing
For most intents and purposes, Chalk is considered to fulfil its original use case. Bug fixes and suggestions are welcome, however, from any member of the community.

## Disclaimer
CHALK AND ITS AUTHOR(S) ARE NOT AFFILIATED WITH BLACKBOARD AND ALL OTHER BLACKBOARD PRODUCT NAMES ARE TRADEMARKS OR REGISTERED TRADEMARKS OF BLACKBOARD INC. ALL OTHER COMPANY AND PRODUCT NAMES ARE TRADEMARKS OR REGISTERED TRADEMARKS OF THEIR RESPECTIVE COMPANIES.
