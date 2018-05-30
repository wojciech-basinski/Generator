# Generator

Simple project to generate unique codes.

It generating codes in two ways:
* by browser, go to / and fill form with number of codes and code length. Click on generate and you will get link to download file with codes.
* by CLI, php bin/console generatecodes --numberOfCodes --codeLength --fileName e.g.
* php bin/console generatecodes 1000 10 codes.txt  -> it will generate 1000 codes with length 10 and saved codes to file "codes.txt"
