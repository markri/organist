@ECHO OFF

REM ##########################################################################

REM The location of your yuidoc install
SET yuidoc_home=C:\yuidoc

REM The location of the files to parse.  Parses subdirectories, but will fail if
REM there are duplicate file names in these directories.  You can specify multiple
REM source trees:
REM      SET parser_in="c:\home\www\yahoo.dev\src\js c:\home\www\Event.dev\src"
SET parser_in=C:\www\OMS\js\mylibs

REM The location to output the parser data.  This output is a file containing a 
REM json string, and copies of the parsed files.
SET parser_out=C:\www\OMS\js\docs\parser

REM The directory to put the html file outputted by the generator
SET generator_out=C:\www\OMS\js\docs

REM The location of the template files.  Any subdirectories here will be copied
REM verbatim to the destination directory.
SET template=%yuidoc_home%\carlo-yuidoc-theme-dana-8a98396

REM The project version that will be displayed in the documentation.
SET version="1.0.0"

REM The project name
SET project="Netvlies_OMS"

REM The project url
SET projecturl="http://www.netvlies-demo.nl/_paul/OMS/"

REM The name to use in the copyright line at the bottom of the pages
SET copyright="Netvlies.nl"

"%yuidoc_home%\bin\yuidoc.py" "%parser_in%" -p "%parser_out%" -o "%generator_out%" -t "%template%" -v %version% -m "%project%" -u "%projecturl%" -C "%copyright%" -s