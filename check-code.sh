#!/usr/bin/env bash

INVALID_FILES_OUTPUT=`find ./pepiscms/ -type f -name "*.php" -not -path "./pepiscms/resources/module_template/*" -exec php -l {} \; 2>&1 | grep "PHP Parse error"`

NUMBER_OF_INVALID_FILES=`echo "$INVALID_FILES_OUTPUT" | grep "PHP Parse error" |  wc -l`

if [ $NUMBER_OF_INVALID_FILES -ne 0 ];
then
    echo "You have $NUMBER_OF_INVALID_FILES file(s) having syntax errors:"
    echo "$INVALID_FILES_OUTPUT"
    exit -1
fi

echo "Everything OK"