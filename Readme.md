# Smarty Filter

Allow you to add some filter in smarty render

## Installation

### Manually

* Copy the module into ```<thelia_root>/local/modules/``` directory and be sure that the name of the module is SmartyFilter.
* Activate it in your thelia administration panel

### Composer

Add it in your main thelia composer.json file

```
composer require your-vendor/smarty-filter-module:~1.0
```

## Usage

### Activate Filter

In Tools menu clic on SmartyFilter. You just have to activate the filters you want.

Think to clean your cache after activate or desactivate filters.

## Add your own Filter

You can add your filter in others module and use this module to integrate it. There are 3 steps to check.

### Service

First create a class with a public method name filter. Add your filter in it.

Exemple :

```
class EmailFilter
{

    public function filter($tpl_output, $smarty)
    {
        $tpl_output =
            preg_replace('!(\S+)@([a-zA-Z0-9\.\-]+\.([a-zA-Z]{2,3}|[0-9]{1,3}))!',
                '$1%40$2', $tpl_output);

        return $tpl_output;
    }
}
```

### Configuration Service

Declare your service in the config.xml with one of the follow tags :

* thelia.parser.register_pre_filter
* thelia.parser.register_post_filter
* thelia.parser.register_output_filter

Exemple :

```
 <service id="smartyfilter.filter.email" class="SmartyFilter\Filter\EmailFilter" >
      <tag name="thelia.parser.register_output_filter"/>
 </service>
```

### Configuration File

To add the filter in base ( and activate it ) you have to create a configuration file name smarty-filter.xml and 
your configuration in it as the exemple :

```
<?xml version="1.0" encoding="UTF-8" ?>
<smartyfilters xmlns="urn:thelia:module:smarty-filter">
    <smartyfilter code="smartyfilter.filter.email">
        <descriptive locale="fr_FR">
            <title>EmailFilter</title>
            <description>Filtre pour sécurisation des emails</description>
            <type>output</type>
        </descriptive>
    </smartyfilter>
</smartyfilters>
```

## Loop

[smarty_filter]

### Input arguments

|Argument |Description |
|---      |--- |
|id| filter by on or more ID|
|filtertype | filter by one or more type ( pre, post or output) |
|order | order the result  (alpha, alpha-reverse, random , given_id )|

### Output arguments

|Variable   |Description |
|---        |--- |
|$ID    | filter's ID |
|$IS_TRANSLATED   | check translation for the filter |
|$LOCALE  | the locale |
|$TITLE  | title string|
|$DESCRIPTION   | description string |
|$ACTIVATE  | boolean to check filter enabled or not |
|$TYPE  | filter type ( pre, post, output) |



### Exemple

```
{loop name="smarty_filter" type="smarty_filter"}
    <tr>
        <td class="object-title">
            {$TITLE}
        </td>
        <td class="object-title">
            {$DESCRIPTION}
        </td>
        <td>
            {$CODE}
        </td>
        <td>
            {$TYPE}
        </td>
        <td class="actions">
            <div class="btn-group">
                <div class="make-switch switch-small module-activation" data-id="{$ID}"
                    data-on="success" data-off="danger"
                    data-on-label="<i class='glyphicon glyphicon-ok-circle'></i>"
                    data-off-label="<i class='glyphicon glyphicon-remove-circle'></i>">
                    <input type="checkbox" {if $ACTIVATE}checked{/if}>
                </div>
            </div>
        </td>
    </tr>
{/loop}
```                     
