# DataGrid library

A library that along with GenericDataFeedable_model builds advanced database mapping. Implements pagination, sort,
filters (search). You can register callbacks to form cell contents.

Features of DataGrid:

* Generate database HTML views with minimum effort using grid definition
* Customize cell values by passing them by registered callbacks
* Generate filters for restricting result set
* Order data set by column (ASC and DESC)
* Pagination of the results – result set is split into pages
* Can handle database foreign keys

## DataGrid filters

Filters are used to restrict the dataset by applying "where" conditions to the query in a way that
is transparent to the user.

Every filter can have "filter_condition" parameter associated with it that specifies the condition
type. You can have several filters associated with a field as long as the filter condition differs –
for example you can implement "date between" filter by using "date ge" and "date le" at the
same time.

### Possible filter types

| Filter types              | Description                                                                                                                         |
|---------------------------|-------------------------------------------------------------------------------------------------------------------------------------|
| FILTER_BASIC              | Basic text field input, user can search for any value, "like" condition by default                                                  |
| FILTER_SELECT             | Drop box with predefined values. Values can be specified by programmer or they can be automatically obtained from a column values.  |
| FILTER_DATE               | Accepts input in date field. A calendar widget is generated when the user focuses the input.                                        |
| FILTER_MULTIPLE_SELECT    | Similar to FILTER_SELECT but the user can choose multiple values.                                                                   |
| FILTER_MULTIPLE_CHECKBOX  | Similar to FILTER_MULTIPLE_SELECT but user can check multiple checkboxes.                                                           |
| FILTER_FORCED             | Filter that is not displayed in the grid, used for manually setting filter values.                                                  |

### Possible filter conditions

| Filter types                       | Description                                                                                                     |
|------------------------------------|-----------------------------------------------------------------------------------------------------------------|
| FILTER_CONDITION_EQUAL             | selects everything that is strictly equal to searched query field = input_value                                 |
| FILTER_CONDITION_NOT_EQUAL         | selects everything that is different from searched query field != input_value                                   |
| FILTER_CONDITION_GREATER           | selects everything that is strictly greater than searched query field > input_value                             |
| FILTER_CONDITION_GREATER_OR_EQUAL  | selects everything that is greater or equal to searched query field >= input_value – tip: applies also to DATE  |
| FILTER_CONDITION_LESS              | selects everything that is strictly less than searched query field < input_value                                |
| FILTER_CONDITION_LESS_OR_EQUAL     | selects everything that is less or equal to searched query field <= input_value– tip: applies also to DATE      |
| FILTER_CONDITION_LIKE              | selects everything that contains searched query field LIKE %input_value% - default for text search              |


## Usage

Complete

```php
$this->load->library('DataGrid');
echo $this->datagrid->setFiltersShownBeforeGrid(TRUE)
    ->setFiltersShownAfterGrid(TRUE)
    ->setOrderable(TRUE)
    ->setTableHeadVisible(TRUE)
    ->setTitle("My Table")
    ->setBaseUrl(admin_url() . 'somepage/edit') // All links will be generated with respect to this base URL
    ->setDefaultOrder('id', 'asc')
    ->setItemsPerPage(300)
    ->setDefinition($definition) 
    ->addFilter('Since', 'published_since_datetime', DataGrid::FILTER_DATE, FALSE, DataGrid::FILTER_CONDITION_LESS_OR_EQUAL)
    ->addFilter('To', 'published_since_datetime', DataGrid::FILTER_DATE, FALSE, DataGrid::FILTER_CONDITION_GREATER_OR_EQUAL)
    ->setRowCssClassFormattingFunction(function ($line) {
        if ($line->is_active == 1) {
            return DataGrid::ROW_COLOR_GREEN;
        } else {
            return DataGrid::ROW_COLOR_RED;
        }
    })
    ->setFeedObject($this->MyFavourite_model)
    ->generate();
```

Minimalistic, with implicite values

```php
$this->load->library('DataGrid');
echo $this->datagrid->setBaseUrl(admin_url() . 'somepage/edit') // All links will be generated with respect to this base URL
    ->setDefinition($definition)
    ->setTable('items') // Will automatically instantiate Generic_model for 'items' table
    ->generate();
```

## DataGrid cell value formatting callbacks

Using cell value formatting callbacks you can modify the value of the cell on run time, for example add a string suffix,
display image or insert a link. 

A callback must be a function or a public method that returns a string and that takes two parameters: cell inline value
and the object representing line (row) values. Usually a callback method is defined inside the controller and is
prefixed with "_" (underscore) to prevent it from being accessed via HTTP. 

```php
public function _datagrid_format_order_value( $cell_value, &$line ) { 
    return $cell_value.' PLN'; 
}
```