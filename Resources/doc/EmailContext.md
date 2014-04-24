# EmailContext

Makes checks on email for testing with Behat.

## Usage

Add an additionnal `X-Message-ID` header to your email to identify them.
Here is an example with SwiftMailer :

```php
$message->getHeaders()->addTextHeader('X-Message-ID', 'contact');
```

Import the context in your `FeatureContext.php` :

```php
$this->useContext('emailContext', new EmailContext());
```

Then use the steps in your Scenario :

```gherkin
And the "contact" mail should be sent to "sender@adresse.com"
```
