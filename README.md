
[![Build Status](https://travis-ci.org/xuedi/ImageViewer.svg?branch=master)](https://travis-ci.org/xuedi/ImageViewer)
[![Codacy Badge](https://api.codacy.com/project/badge/Grade/4c69fa9f292244a4a9bcda202200f16f)](https://www.codacy.com/manual/xuedi/ImageViewer?utm_source=github.com&amp;utm_medium=referral&amp;utm_content=xuedi/ImageViewer&amp;utm_campaign=Badge_Grade)
[![Codacy Badge](https://api.codacy.com/project/badge/Coverage/4c69fa9f292244a4a9bcda202200f16f)](https://www.codacy.com/manual/xuedi/ImageViewer?utm_source=github.com&utm_medium=referral&utm_content=xuedi/ImageViewer&utm_campaign=Badge_Coverage)

## Introduction
ImageViewer is a tool for managing your Images Collection its written
as a Shotwell replacement in PHP & JS (vue).

The project wont touch any of the photos if not explicitly confirmed
to do so (tag editing), the database can be easily be regenerated.
The folder of the image collection expects a certain format:

`Country\2010-02-18 name of the Event\<images>`

The Database is PDO abstracted, so mysql, postgres and sqlite should
be an option. There are no frameworks used, just a few smaller packages
like symfony process & console, as well as a few minimalistic libraries.

There are to parts to the Gallery. The CLI part manages the Image Collection
scanning and updating of the database, as well as multicore thumbnail
generation. The frontend part will be vue based and should feel similar
to Shotwell plus some more tag cloud navigation. 

### Php module dependencies
 - PHP >= 7.4
 - ext-json
 - ext-exif
 - ext-pdo
 - ext-mbstring
 - ext-pcntl
 - ext-gd

### Install
make install

### usage
./ImageViewer help