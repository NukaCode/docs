#! /bin/bash

base=/Users/stygian/Code/Personal/Docs/resources/docs/core/master

mkdir -p $base
cd $base
git init
git remote add origin git@github.com:NukaCode/core.git
git config core.sparsecheckout true
cp /Users/stygian/Code/Personal/Docs/build/sparse-checkout $base/.git/info/sparse-checkout
git pull origin master
