#!/bin/bash

if [[ $# -eq 2 ]]; then
  wget -r -np http://plugins.svn.wordpress.org/$1/tags/$2/ > /dev/null 2>&1
  mkdir /usr/plugins/$1-$2 > /dev/null 2>&1
  cp -R plugins.svn.wordpress.org/$1/tags/$2/* /usr/plugins/$1-$2/ > /dev/null 2>&1
  rm -fr plugins.svn.wordpress.org > /dev/null 2>&1
elif [[ $# -eq 1 ]]; then
  wget -r -np http://plugins.svn.wordpress.org/$1/trunk/ > /dev/null 2>&1
  mkdir /usr/plugins/$1 > /dev/null 2>&1
  cp -R plugins.svn.wordpress.org/$1/trunk/* /usr/plugins/$1/ > /dev/null 2>&1
  rm -fr plugins.svn.wordpress.org > /dev/null 2>&1
fi
