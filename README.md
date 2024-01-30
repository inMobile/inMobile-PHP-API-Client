# Inmobile PHP SDK for v4

## Requirements
- PHP 7.4, 8.0, 8.1, 8.2 or 8.3. 

## Getting started
The latest version of the client can be found on packagist [here](https://packagist.org/packages/inmobile/inmobile-sdk)
1. `composer require inmobile/inmobile-sdk`
2. Initialize the `InmobileApi` class and start sending messages!

Each _endpoint_ is split into a different class.

So the **messages** API would be accessed by calling `->messages()`, **lists** API by calling `->lists()` and so on.

**Example:**

```php
/*
 * Require autoload, to automatically load the SDK, after installing via composer.
 * Execute the following command now, if you haven't already: composer require inmobile/inmobile-sdk
 */
require_once __DIR__ . '/vendor/autoload.php';

use Inmobile\InmobileSDK\InmobileApi;
use Inmobile\InmobileSDK\RequestModels\Message;

/*
 * Initialize the Inmobile API Client
 */
$api = new InmobileApi('my-api-token');

/*
 * Send the message
 */
$response = $api->messages()->send(
    Message::create('This is a message text to be sent')
        ->from('1245')
        ->to('4512345678')
);

$response->toArray();

/**
 * "results": [
 *     {
 *         "numberDetails": {
 *             "countryCode": "45",
 *             "phoneNumber": "12345678",
 *             "rawMsisdn": "45 12 34 56 78",
 *             "isValidMsisdn": true,
 *             "isAnonymized": false
 *         },
 *         "text": "This is a message text to be sent",
 *         "from": "1245",
 *         "smsCount": 1,
 *         "messageId": "INMBL",
 *         "encoding": "gsm7"
 *     }
 * ]
 */
```

You can find the full API documentation here: https://api.inmobile.com/docs/

## Endpoints

The SDK is split up into different classes for each "endpoint" in the InMobile API.

### Messages
This can be accessed by calling `->messages()` on `InmobileApi`. Below you will find an example of all the actions.

#### Send message
Send one or more messages.

```php
$api->messages()->send(
    Message::create('Hello World')
        ->from('INMBL')
        ->to(4512345678)
);

$api->messages()->send([
    Message::create('Foobar')
        ->from('INMBL')
        ->to(4512345678),
    Message::create('Barbiz')
        ->from('INMBL')
        ->to(4512345678)
        ->countryHint('DK')
]);
```

#### Send a message using a template

```php
$api->messages()->sendUsingTemplate(
    TemplateMessage::create()
        ->to(4512345678)
        ->setPlaceholders([
            '{name}' => 'John',
            '{lastname}' => 'Doe',
        ]),
    'ecdcb257-c1e9-4b44-8a4e-f05822372d82',
);

// Multiple
$placeholders = [
    '{name}' => 'John',
    '{lastname}' => 'Doe',
];

$api->messages()->sendUsingTemplate([
    TemplateMessage::create()->to(4512345678)->setPlaceholders($placeholders),
    TemplateMessage::create()->to(4512345678)->setPlaceholders($placeholders),
], 'ecdcb257-c1e9-4b44-8a4e-f05822372d82');
```

#### Send message using query
Send one message using query parameters.

```php
$api->messages()->sendUsingQuery(
    Message::create('Hello World')
        ->from('INMBL')
        ->to(4512345678)
);
```

#### Get status reports
Get all SMS reports

```php
$api->messages()->getStatusReport($limit = 20);
```

#### Cancel messages
Cancel one or multiple messages by their ID

```php
$api->messages()->cancel('MESSAGEID-1');

// Or multiple messages
$api->messages()->cancel(['MESSAGEID-1', 'MESSAGEID-2']);
```

### Lists
This can be accessed by calling `->lists()` on `InmobileApi`. Below you will find an example of all the actions.

#### Get all
Fetch all lists. This automatically runs through every page and returns an array of all lists.

```php
$api->lists()->getAll();
```

#### Find
Find a list by its ID

```php
$api->lists()->find($listId);
```

#### Create
Create a new list

```php
$api->lists()->create($listName);
```

#### Update
Update a list with a new name

```php
$api->lists()->update($listId, $newName);
```

#### Delete
Delete a list by its ID

```php
$api->lists()->delete($listId);
```

### Blacklist
This can be accessed by calling `->blacklist()` on `InmobileApi`. Below you will find an example of all the actions.

#### Get
Fetch a paginated list of all entries in your blacklist

```php
$api->blacklist()->get($pageLimit = 20);
```

#### Get all
Fetch all entries in a blacklist. This automatically runs through every page and returns an array of all entries.

```php
$api->blacklist()->getAll();
```

#### Find by ID
Find an entry by ID

```php
$api->blacklist()->findEntryById('ENTRYID-1');
```

#### Find by phone number
Find an entry by phone number

```php
$api->blacklist()->findEntryByNumber($countryCode = 45, $phoneNumber = 12345678);
```

#### Create
Create a new entry in the blacklist

```php
$api->blacklist()->createEntry($countryCode = 45, $phoneNumber = 12345678, $comment = null);
```

#### Delete by ID
Delete an entry by ID

```php
$api->blacklist()->deleteEntryById('ENTRYID-1');
```

#### Delete by phone number
Delete an entry by phone number

```php
$api->blacklist()->deleteEntryByNumber($countryCode = 45, $phoneNumber = 12345678);
```

### Recipients
This can be accessed by calling `->recipients()` on `InmobileApi`. Below you will find an example of all the actions.

#### Get
Get a paginated response of all recipients in a list

```php
$api->recipients()->get($listId = 'LIST-1', $limit = 20);
```

#### Get all
Fetch all recipients on a list. This automatically runs through every page and returns an array of all recipients.

```php
$api->recipients()->getAll($listId = 'LIST-1');
```

#### Find by ID
Find a recipient by ID

```php
$api->recipients()->findById($listId = 'LIST-1', $id = 'RECIPIENT-1');
```

#### Find by phone number
Find a recipient by phone number

```php
$api->recipients()->findByPhoneNumber($listId = 'LIST-1', $countryCode = 45, $phoneNumber = 12345678);
```

#### Create
Create a recipient on a list

```php
$api->recipients()->create(
    $listId = 'LIST-1',
    Recipient::create(45, 12345678)
        ->addField('firstname', 'John')
        ->addField('lastname', 'Doe')
        ->createdAt(new DateTime('2021-01-02 03:04:05'))
);
```

#### Update
Update a recipient on a list

```php
$api->recipients()->update(
    $listId = 'LIST-1',
    $id = 'RECIPIENT-1',
    Recipient::create(45, 12345678)
        ->addField('firstname', 'John')
        ->addField('lastname', 'Doe')
        ->createdAt(new DateTime('2021-01-02 03:04:05'))
);
```

#### Create or update
Create or update a recipient on a list if it doesn't exist.

```php
$api->recipients()->createOrUpdateByPhoneNumber(
    $listId = 'LIST-1',
    $countryCode = 45,
    $phoneNumber = 12345678,
    Recipient::create(45, 12345678)
        ->addField('firstname', 'John')
        ->addField('lastname', 'Doe')     
);
```

#### Delete by ID
Delete a recipient by ID

```php
$api->recipients()->deleteById($listId = 'LIST-1', $id = 'RECIPIENT-1');
```

#### Delete by phone number
Delete a recipient by phone number

```php
$api->recipients()->deleteByPhoneNumber($listId = 'LIST-1', $countryCode = 45, $phoneNumber = 12345678);
```

#### Delete all recipients on a list
This deletes all recipients on the given list

```php
$api->recipients()->deleteAllFromList($listId = 'LIST-1');
```

### GDPR

#### Create information deletion request

```php
$api->gdpr()->createDeletionRequest(NumberInfo::create($countryCode = '45', $phoneNumber = '12345678'));
```

### Tools

#### Parse phone numbers

```php
$api->tools()->numbersToParse([
    NumberToParse::create($countryHint = 'DK', $rawMsisdn = '12 34 56 78')
    NumberToParse::create($countryHint = '45', $rawMsisdn = '12 34 56 78')
]);

// If you wish to parse a single number, you can do so by passing a single NumberToParse object
$api->tools()->numbersToParse(NumberToParse::create($countryHint = 'DK', $rawMsisdn = '12 34 56 78'));
```

### Templates

#### Get all
Fetch all templates. This automatically runs through every page and returns an array of all templates.

```php
$api->templates()->getAll();
```

#### Find
Find a template by its ID

```php
$api->templates()->find($templateId);
```

### Emails

#### Send email

Send an email to one or more recipients with the specified subject and text / html body.

```php
$api->emails()->send(
    Email::create()
        ->from(EmailRecipient::create('john@example.com', 'John Doe'))
        ->to(EmailRecipient::create('jane@example.com', 'Jane Doe'))
        ->subject('Hello World')
        ->text('Hello World')
        ->html('<h1>Hello World</h1>')
);
```

#### Send email using template

Send an email to one or more recipients using a template.

```php
$api->emails()->sendUsingTemplate(
    Email::create()
        ->from(EmailRecipient::create('john@example.com', 'John Doe'))
        ->to(EmailRecipient::create('jane@example.com', 'Jane Doe'))
        ->templateId('ABCDEF12-3456-7890-ABCD-EF1234567890')
);
```

#### Get email events

Get a paginated list of email events.

```php
$api->emails()->getEvents($limit = 20);
```

### Email templates

#### Get all

Fetch all email templates. This automatically runs through every page and returns an array of all templates.

```php
$api->emailTemplates()->getAll();
```

#### Find

Find an email template by its ID

```php
$api->emailTemplates()->find($templateId);
```
