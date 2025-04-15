# Magento 2 Currency Symbol Position Module
This module allows setting the currency symbol position by creating a configuration option under 
"**Admin > Stores > Configuration > General > Currency Setup > Currency Options**" to define if the currency symbol 
should render on the right or on the left.

## Install
1. Install this module via composer within your Magento installation by running from your Magento's root directory: 
    ``` 
    composer require ew/magento2-currency-symbol-position
    ```

2. Then, to register the module and make its configurations take effect, run:
    ```
    bin/magento setup:upgrade
    ```

## Common Usage
After installing this module you will find in the admin area under 
"**Admin > Stores > Configuration > General > Currency Setup > Currency Options**" 
different options to configure the currency symbol position:
- "**Default**" : This is Magento's default behavior, which will usually render the currency symbol on the left, 
  but for some store locales it might render on the right (e.g. `fr_CH`).
- "**Left**" : This will make the currency symbol **always** render on the left.
- "**Right**" : This will make the currency symbol **always** render on the right.

Also, keep in mind that if you require any white space between the currency symbol and the actual price, 
you can add such space by updating the currency symbol in the admin area under "**Admin > Stores > Currency Symbols**",
and including any left or right spaces you need.

License
----

OSL-3.0
