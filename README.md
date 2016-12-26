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

## Limitations
If you just want to check whether a set of credentials is valid or not, Chalk might be the library for you. It can't do anything else at all. Give it a username and password and get back a boolean. That's it.

## Disclaimer
CHALK AND ITS AUTHOR(S) ARE NOT AFFILIATED WITH BLACKBOARD AND ALL OTHER BLACKBOARD PRODUCT NAMES ARE TRADEMARKS OR REGISTERED TRADEMARKS OF BLACKBOARD INC. ALL OTHER COMPANY AND PRODUCT NAMES ARE TRADEMARKS OR REGISTERED TRADEMARKS OF THEIR RESPECTIVE COMPANIES.
