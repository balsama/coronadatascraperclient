# CaronoDataScraper Client
PHP Client for the caronadatascraper.com timeseries-byLocation.json data set.

See https://caronadatascraper.com and that project's repo here: https://github.com/covidatlas/coronadatascraper

## Usage
Include in your project:
```
$ composer require balsama/caronadatascraperclient
```

### `RegionsBase`
Instantiating `RegionsBase` will get the latest info from caronadatascraper.com and format it into predicatble `Region`
objects:

```php
$regionsBase = new Balsama\RegionsBase();
```

RegionsBase has two public methods for getting Regions:
```php
$regionsBase = new Balsama\RegionsBase();

// Get all regions:
$regions = $regionsBase->getRegions();

// Get a specific region:
$spain = $regionsBase->getRegion('ESP');

// Regions include countries, states, counties, and cities.
// States:
$kentucky = $regionsBase->getRegion('KY/ USA');
$newSouthWales = $regionsBase->getRegion('South Australia/ AUS');
$bugei = $regionsBase->getRegion('Hebei/ CHN');

// Counties:
$westchesterCoNy = $regionsBase->getRegion('Westchester County/ NY/ USA');
$lafayetteParishLa = $regionsBase->getRegion('Lafayette Parish/ LA/ USA');

// Cities:
$quebec = $regionsBase->getRegion('Quebec/ CAN');

// Inspect the keys of the array of Region objects returned by $regionBase->getResgions for a complete list of regions.
```

There are also some shortcut methods for getting arrays of Regions.
```php
$regionsBase = new Balsama\RegionsBase();

// Get all US States
$usStates = $regionsBase->getCountrysStates('USA');
```

### `Region`
You can use the public methods on a specific Region class to get information about that region:
```php
$regionsBase = new Balsama\RegionsBase();
$spain = $regionsBase->getRegion('ESP');

// Basic information:
$spain->getName(); // The name of the region.
$spain->getType(); // The type, either country, state, county, or city.
$spain->getCountry(); // The country to which the region belongs.
$spain->getLatestCount(); // The latest count of cumulative positive test results.
$spain->getLatestDeaths(); // The latest count of cumulative deaths.

// US Counties have a FIPS number.
$lafayetteParishLa = $regionsBase->getRegion('Lafayette Parish/ LA/ USA');
$lafayetteParishLa->getFips(); // The FIPS code for the county.

// Sets of numbers:
// An array of all available positive cases counts keyed by the timestamp of the day of the count.
$spain->getCases();
// An array of all death count keyed by the timestamp of the day of the count.
$spain->getDeaths();
// An array of the percentage pf the population which has tested positive keyed by the day of the count.
$spain->getPercentages();
// `getPercentages()` takes an optional argument to get the same for deaths instead of cases.
$spain->getPercentages('deaths');
// An array of numbers representing the number of positive test cases per 100,000 people in the region. The $n argument
// strips values less than $n. So if you want to see the trajectory of a region once it has reached 10 in 100,000 cases
// you would pass `10` as $n. 
$spain->getPer100kAboveN(10);
``` 

### `Utilities`
There is also a Utilities class that allows you to export information gathered to a CSV suitable for using with a
graphing tool. It's fairly limited now and specific to my use case.
```php
$regionsBase = new Balsama\RegionsBase();
$usStates = $regionsBase->getCountrysStates('ESP');
$usStatesPer100kAbove10 = [];
foreach ($usStates as $stateName => $state) {
    $usStatesPer100kAbove10[$stateName] = $state->getPer100kAboveN(10);
}

// The `writeCsvTableFromData` method expects the data to be in an array keyed by the region.
Balsama\Utilities::writeCsvTableFromData($usStatesPer100kAbove10);
``` 

See scripts/examples.php for a few examples.
