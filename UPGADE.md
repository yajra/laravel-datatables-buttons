# UPGRADE GUIDE

## Upgrade from 10.x to 11.x

1. Update the composer.json file and change the version of the package to `^11.0`:

```json
"require": {
    "yajra/laravel-datatables-buttons": "^11.0"
}
```

2. Run `composer update` to update the package.

3. If you are using a custom class of laravel-excel to export the data, you need to update the collection method and include the return type `Collection|LazyCollection` in the method signature.

```php
public function collection(): Collection|LazyCollection
{
    return $this->collection;
}
```
