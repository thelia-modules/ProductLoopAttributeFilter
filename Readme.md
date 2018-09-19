# Product Loop Attribute Filter

Adds the possibility to filter by attribute on the product loop

## Installation

### Manually

* Copy the module into ```<thelia_root>/local/modules/``` directory and be sure that the name of the module is ProductLoopAttributeFilter.
* Activate it in your thelia administration panel

### Composer

Add it in your main thelia composer.json file

```
composer require thelia/product-loop-attribute-filter-module:~1.0.0
```

## Loop

### Loop Product

### New input arguments

|Argument |Type |Default value |Description |
|---      |--- |--- |--- |
|**attribute_extend** | boolean | false | make true for activate the filter |
|**attribute_availability** | int[] |  | list of ids |
|**attribute_min_stock** | int | 0 | Minimum quantity |

### Output arguments

http://doc.thelia.net/en/documentation/loop/product.html

### Exemple

```smarty

{loop attribute_extend=true attribute_availability="64" attribute_min_stock=1 name="product" type="product" visible="*"}

{/loop}
```
