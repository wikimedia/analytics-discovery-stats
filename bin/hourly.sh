#!/bin/bash

BASEDIR=`dirname "$0"`/..

/usr/bin/php $BASEDIR/tracking-category-count.php
/usr/bin/php $BASEDIR/geo-tag-counts.php
