# pywws charts rendered in highcharts

This is the code which I use on [info.riviera.org.uk](http://info.riviera.org.uk) to produce the weather graphs.

Click [here](https://raw.githubusercontent.com/rk295/pywws-highcharts/master/images/example.png) for a screenshot, or [here](http://info.riviera.org.uk) for my live example.

The [maplin](http://www.maplin.co.uk/p/silver-usb-wireless-touchscreen-weather-forecaster-n96fy) weather station is plugged into a Linux machine which runs [pywws](https://jim-easterbrook.github.io/pywws/) to pull the data off the USB line and run it through a series of templates. I've simply defined a couple of templates which output vaid JSON. These files are then read by JavaScript in the web page to display the graphs. [Twitter Bootstrap](http://getbootstrap.com/) is used to make some nice looking tabs and navigation elements.

## Use
### pywws
The templates for pywws are in the ```pywws-files``` directory, they are essentially all the same, they just offer different time spans. Although the ones ending ```.html``` are used as includes in the first tab for some textual info about recent weather.

You need to edit ```weather.ini``` and adjust to your installation. The important lines are:

* ```directory``` This directory needs to be readable by the webserver because the JavaScript needs to read the JSON (via a URL) from here.
* ```templates``` Where your pywws templates are held

Pywws can run data through any number of templates, so you can simply add the json templates to any existing ones you might have.

###php

Inside ```index.php``` there is a single PHP define ```PYWWS_OUTPUT_DIR``` which needs to point to the directory specified in the ```weather.ini``` which pywws uses.


### Example Template (24h)

```
{
    "data": [
#timezone local#
#raw#
#jump -300#
#loop 100#
    {
        "#idx "%m/%d/%Y %H:%M"#": {
            "TempOut": #temp_out "%.1f" "null"#,
            "FeelsLike": #calc "apparent_temp(data['temp_out'], data['hum_out'], data['wind_ave'])" "%.1f" "null"#,
            "HumidityOut": #hum_out "%d" "null"#,
            "DewPoint": #calc "dew_point(data['temp_out'], data['hum_out'])" "%.1f" "null"#,
            "WindDirection": " #wind_dir "%s" "-" "winddir_text(x)"# ",
            "WindAvg": #wind_ave "%.0f" "null" "wind_mph(x)"#,
            "WindGust": #wind_gust "%.0f" "null" "wind_mph(x)"#,
            "WindChill": #calc "wind_chill(data['temp_out'], data['wind_ave'])" "%.1f" "null"#,
            "Rain": #calc "data['rain']-prevdata['rain']" "%0.1f" "null"#,
            "AbsPressure": #abs_pressure "%.1f" "null"#
        }
    },
#jump 3#
#endloop#
    {}
    ]
}
```

## Other stuff

General todos I have in mind are:

* The JS is probably very kludgy, it was my first time writing it, and I've since learnt a bit more about JS and Bootstrap so could probably do away with the page loads for each time interval down the left hand side.
* The graphs also could do with some work, perhaps showing summaries as the time spans get longer.
* I've yet to find a way of getting wind rose data out of pywws textually, so the png is just included for now, but wrapped in a bootstrap ```img-responsive``` tag.
* The last tab is a simple include of one of the default pywws png graphs. Its motly redundant, I was simply using it for testing.
