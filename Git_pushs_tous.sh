#!/bin/bash
sudo chown $USER: src patch -R
cd patch/patch_crud
gcp $1
cd ../../src/Twig
gcp $1
cd ../Command
gcp $1
cd ../Command
gcp $1
cd ../Validator
gcp $1
cd ../Form/Type
gcp $1
cd ..
