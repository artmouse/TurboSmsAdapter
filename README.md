Turbo SMS
=========

Implement Turbo SMS adapter for sending SMS to users.

Implemented protocols:

* SOAP

Requirements
------------

* PHP 7.1 or higher.

Installation
------------

Add TurboSms package in your composer.json:

````json
{
    "require": {
        "sms-gate/turbo-sms": "~1.0"
    }
}
````

Now tell composer to download the library by running the command:

```bash
$ php composer.phar update sms-gate/turbo-sms
```

Usage
-----


```php
<?php

use SmsGate\Adapter\TurboSms\TurboSmsAdapterFactory;
use SmsGate\Sender\Sender;
use SmsGate\Message;
use SmsGate\Phone;

$login = 'my-login';
$password = 'my-password';

$adapter = TurboSmsAdapterFactory::soap($login, $password);
$sender = new Sender($adapter);

$sender->send(
    new Message('You have new messages on http://site.com', 'Sender name'),
    new Phone('380991234567')
);

```

> **Attention:** The name of sender is required for TurboSMS gate.

License
-------

This library is under the MIT license. See the complete license in library

```
LICENSE
```

Reporting an issue or a feature request
---------------------------------------

Issues and feature requests are tracked in the [Github issue tracker](https://github.com/Sms-Gate/TurboSms/issues).

Contributors:
-------------

Thanks to [everyone participating](https://github.com/Sms-Gate/TurboSms/graphs/contributors) in the development of this TurboSms library!

