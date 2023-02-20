# Laravel DataTables Buttons Plugin CHANGELOG.

## v9.1.4 - 2023-02-20

- fix: applyScopes method to support Collection #168

## v9.1.3 - 2022-11-15

- Fixes some issues and confusion of datatable generator command #165

## v9.1.2 - 2022-10-29

- fix: Change for correct $action and $actionMethod #162
- chore: update stubs #161

## v9.1.1 - 2022-10-06

- fix(phpstan) Downgrade to 2.1.12 #158
- fix: Allow null on view #152

## v9.1.0 - 2022-10-05

- Correcting with parameter typehints #155
- Add contributing.md #156
- Fix #154

## v9.0.10 - 2022-06-25

- Fix scopes for Eloquent Relation - Exception: "TypeError" #150

## v9.0.9 - 2022-06-21

- Fix datatables.stub #149
- Add type hint and return type on stubs.

## v9.0.8 - 2022-05-19

- Improve exporting of array & other data type values

## v9.0.7 - 2022-05-19

- Remove try catch, fix stan

## v9.0.6 - 2022-05-19

- Fix exporting of boolean values

## v9.0.5 - 2022-05-19

- Fix exporting data via relationship

## v9.0.4 - 2022-05-13

- Fix nullable builder and request

## v9.0.3 - 2022-05-12

- Fix $htmlBuilder must not be accessed before initialization

## v9.0.2 - 2022-05-12

- Fix csv export extension

## v9.0.1 - 2022-05-10

- Quick fix for orthogonal column data
- Introduce in https://github.com/yajra/laravel-datatables-html/commit/c6cc9a707a5a3095bca47adef2bae0614406a254
- Add test for before & response callback for https://github.com/yajra/laravel-datatables/issues/2777

## v9.0.0 - 2022-05-07

- Added Laravel 9 specific support
- Bump major version to match with the framework
- Move `maatwebsite/excel` to suggest. #139
- Add phpstan static analysis
- Add tests
