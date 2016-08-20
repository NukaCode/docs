#! /bin/bash

base=/Users/stygian/Code/Personal/Docs/resources/docs/users/3.0

mkdir -p $base
cd $base
git init
git remote add origin git@github.com:NukaCode/users.git
git config core.sparsecheckout true
cp /Users/stygian/Code/Personal/Docs/build/sparse-checkout $base/.git/info/sparse-checkout
git pull origin master
s
