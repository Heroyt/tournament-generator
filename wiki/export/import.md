## Importing tournament

**TournamentGenerator** has one **Importer** class that handles importing of previously exported data. It reconstructs
the whole tournament into the original classes.

### Validating the import structure

Before importing, the data is validated using a `ImportValidator` class.

```php
use TournamentGenerator\Import\ImportValidator;

ImportValidator::validate($data); // This will return true if the data is valid and false if not
ImportValidator::validate($data, true) // This will return true if the data is valid and throw an InvalidImportDataException if not
```

### Example import

```php
use TournamentGenerator\Import\Importer;

$data = [
    'tournament' => [
        'type'       => 'general',
        'name'       => 'Test',
        'skip'       => false,
        'categories' => [1, 2],
    ],
    'categories' => [
        [
            'name' => 'Category 1',
            'id'   => 1,
            'skip' => true,
        ],
        [
            'name' => 'Category 2',
            'id'   => 2,
            'skip' => true,
        ],
    ],
];

$tournament = Importer::import($data); // Will create a tournament class with 2 categories
```

### Example import from JSON

```php
use TournamentGenerator\Import\Importer;

$data = '{
    "tournament": {
        "type": "general",
        "name": "Test",
        "skip": false,
        "categories": [
            1,
            2
        ]
    },
    "categories": [
        {
            "name": "Category 1",
            "id": 1,
            "skip": true
        },
        {
            "name": "Category 2",
            "id": 2,
            "skip": true
        }
    ]
}';

$tournament = Importer::importJson($data); // Will create a tournament class with 2 categories
```