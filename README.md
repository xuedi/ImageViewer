
[![Actions Status](https://github.com/xuedi/ImageViewer/workflows/PHP-Unit/badge.svg)](https://github.com/xuedi/ImageViewer/actions)
[![Code Coverage](https://raw.githubusercontent.com/xuedi/ImageViewer/master/app/tests/badge/coverage.svg?sanitize=true)](https://github.com/xuedi/ImageViewer/blob/master/app/tests/badge_generator.php)

## introduction
ImageViewer is a tool for managing your Images Collection its written
as a Shotwell replacement in PHP & JS (vue). The motivation for this
project is to have a bit of exercise and play around with TDD plus 
missing features and workflows in other photos Management software.

##### data handling
The project wont touch any of the photos if not explicitly confirmed
to do so (tag editing), the database can be easily be regenerated.
The folder of the image collection expects a certain format:

`Country\2010-02-18 name of the Event\<images>`

The Database is PDO abstracted, so mysql, postgres and sqlite should
be an option. 

##### software design
There are no frameworks used, just a few smaller packages like symfony
process & console, phinx for data migration as well as a few minimalistic
libraries. 

There are to parts to the Gallery. The **CLI** part manages the Image Collection
scanning and updating of the database, as well as multicore thumbnail
generation. The **frontend** part will be vue based and should feel similar
to Shotwell plus some more tag cloud navigation and other stuff i was missing.

##### PHP modules
 - PHP >= 7.4
 - ext-json
 - ext-exif
 - ext-pdo
 - ext-mbstring
 - ext-pcntl
 - ext-gd

### install
make install

### usage
make help

