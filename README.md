# Mass Update

Postgres mass update.

This query is used to bulk update in PostgreSQL. You can use this query to update multiple rows at once.

```sql
update "transactions" as "t"
set "value" = "m"."value"::decimal
from (values (1, 10), (2, 20), (3, 30)) as "m" (id, value)
where "m"."id"::bigint = "t"."id"
```

Added a new `joinFrom` method in the query builder to simplify the sql build.

# Install

```
composer require johdougss/laravel-mass-update
```

configure in app.php

```
'providers' => ServiceProvider::defaultProviders()->merge([
    ...

    Johdougss\Database\DatabaseMassUpdateServiceProvider::class,
])->toArray(),
```

# Usage

`joinFrom`

```php
DB::table('transactions as t')
    ->joinFrom([['id' => 1, 'value' => 10], ['id' => 2, 'value' => 20]], 'm', DB::raw('m.id::bigint'), '=', 't.id')
    ->updateFrom([
        'value' => DB::raw('m.value::decimal'),
    ]);
```

### Example 1:

create migration

```php
Schema::create('transactions', function (Blueprint $table) {
    $table->id();
    $table->decimal('value', 12, 2);
    $table->integer('type')->default(1);
    $table->string('status', 10)->default('pending');
    $table->timestamp('date');
    $table->timestamps();
});
```

from values with `join` laravel

```php
 $values = [
    [
        'id' => 1,
        'value' => 20,
    ],
    [
        'id' => 2,
        'value' => 30,
    ],
     [
        'id' => 3,
        'value' => 30,
    ],
];

DB::table('transactions as t')
    ->join(DB::raw('(values (1,10), (2, 20), (3, 30) ) as mu (id, value, date)'), 'mu.id', '=', 't.id')
    ->updateFrom([
        'value' => DB::raw('(mu.value::decimal + 1)::decimal'),
        'date' => DB::raw('mu.date::timestamp'),
        'type' => 2,
        'status' => DB::raw('case t.id when 1 then \'paid\' else t.status end'),
]);
```

from values with `joinFrom` laravel

```php
 $values = [
    [
        'id' => 1,
        'value' => 20,
    ],
    [
        'id' => 2,
        'value' => 30,
    ],
     [
        'id' => 3,
        'value' => 30,
    ],
];


DB::table('transactions as t')
    ->joinFrom($values, 'm', DB::raw('m.id::bigint'), '=', 't.id')
    ->updateFrom([
        'value' => DB::raw('m.value::decimal'),
    ]);
```

Output SQL

```sql
update "transactions" as "t"
set "value" = m.value::decimal
from (values (?, ?), (?, ?), (?, ?)) as m (id, value)
where m.id::bigint = "t"."id"
```

Debug:

```shell
array:1 [
  0 => array:3 [
    "query" => "update "transactions" as "t" set "value" = m.value::decimal from (values (?, ?), (?, ?), (?, ?)) as m (id, value) where m.id::bigint = "t"."id""
    "bindings" => array:6 [
      0 => 1
      1 => 20
      2 => 2
      3 => 30
      4 => 3
      5 => 30
    ]
    "time" => 2.5
  ]
]
```

### Example 2:

```php
 $values = [
    [
        'id' => 1,
        'date' => Carbon::now(),
        'value' => 20,
    ],
    [
        'id' => 2,
        'date' => '2023-01-02',
        'value' => 30,
    ],
];


DB::table('transactions as t')
    ->joinFrom($values, 'm', DB::raw('m.id::bigint'), '=', 't.id')
    ->updateFrom([
        'value' => DB::raw('(m.value::decimal + 1)::decimal'),
        'date' => DB::raw('m.date::timestamp'),
        'type' => 2,
        'status' => DB::raw('case t.id when 1 then \'paid\' else t.status end'),
    ]);
```

Output SQL

```sql
update "transactions" as "t"
set "value" = (m.value::decimal + 1)::decimal,
    "date"   = m.date::timestamp,
    "type"   = ?,
    "status" = case t.id when 1 then 'paid' else t.status
end
from (values (?, ?, ?), (?, ?, ?)) as m (id, date, value)
where m.id::bigint = "t"."id"
```

Debug:

```shell
array:1 [ 
  0 => array:3 [
    "query" => "update "transactions" as "t" set "value" = (m.value::decimal + 1)::decimal, "date" = m.date::timestamp, "type" = ?, "status" = case t.id when 1 then 'paid' else t.status end from (values (?, ?, ?), (?, ?, ?)) as m (id, date, value) where m.id::bigint = "t"."id""
    "bindings" => array:7 [
      0 => 2
      1 => 1
      2 => Carbon\Carbon @1689177399^ {#817
        ...
        date: 2023-07-12 15:56:39.887268 UTC (+00:00)
      }
      3 => 20
      4 => 2
      5 => "2023-01-02"
      6 => 30
    ]
    "time" => 6.6
  ]
]
```