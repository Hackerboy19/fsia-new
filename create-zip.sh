#!/bin/bash

# Creates a deployment-ready zip of the fsia-modular project

zip -r fsia-modular-release.zip . -x "*.DS_Store" -x "*.git*" -x "create-zip.sh" -x "forms/*" -x "*.html"
echo "Success: fsia-modular-release.zip has been created."