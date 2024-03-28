# Magento 2 Currency Symbol Position Module
This module allows setting the currency symbol position by creating a configuration option under 
"**Admin > Stores > Configuration > General > Currency Setup > Currency Options**" to define if the currency symbol 
should render to the right, as left is the default.

## Common Usage
1. Install this module via composer within your Magento installation by running from your Magento's root directory: 
    ``` 
    composer require ew/magento2-currency-symbol-position
    ```

2. Then, to register the module and make its configurations take effect, run:
    ```
    bin/magento setup:upgrade
    ```

License
----

MIT
