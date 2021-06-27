#!/bin/bash

userAgent='User-Agent: Mozilla/5.0 (Macintosh; Intel Mac OS X 10.15; rv:88.0) Gecko/20100101 Firefox/88.0'

plugins=$(curl -s "http://plugins.svn.wordpress.org" -H "$userAgent" | grep -E -o '\"[a-zA-Z0-9\/-]*\"' | sed 's/["\/]//g')

plugins_num=$(echo $plugins | wc -l)
for p in $(echo $plugins); do
  slug="{\"slug\" : \"$p\", "
  versions="\"versions\": ["
  tags=$(curl -s http://plugins.svn.wordpress.org/$p/tags/ -H "$userAgent" | grep -E -o "\"[0-9].*\"" | sed 's/["\/]//g')
  for t in $(echo $tags); do
    if [[ $versions == "\"versions\": [" ]]; then
      versions="$versions\"$t\""
    else
      versions="$versions, \"$t\""
    fi
  done
  versions="$versions]}"
  jsonRequest="${slug}${versions}"
  curl -s -H "Content-Type: application/json" --data "$jsonRequest" "http://localhost:8070/plugin"
done
