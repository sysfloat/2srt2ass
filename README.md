# About

Merge all occurances of 2 .srt files with the same name and different country code into 1 .ass file. 
 
# Usage

## Example
    S01E01.de.srt
    S01E01.no.srt
    S01E04.no.srt

**php app.php -r subs/ -n1 de -n2 no** 

Will only merge the subtitles of S01E01 since there are no 2 versions of S01E04, german subs will be at top and the norwegian subs will be at the bottom.

**Result** : Original filename, without the country code, .ass as filetype extension.

## Options
| Argument        | Description 
| ------------- |:-------------:|
| **-r**     | Directory : Merge all .srt files with the same name. Country code infront of .srt expected! (e.g. subtitle1.en.srt) |
| **-n1**      | Country code of 1. subtitle (Top) |
| **-n2**      | Country code of 2. subtitle (Bottom) |
| **Optional**      | 
| **-d**      | Destination directory (Default: same as source) |
| **-f**      | Font name (Default: Arial) |
| **-s**      | Font size (Default: 16) |
| **-t**      | Top color (Default: FFFFFF) |
| **-b**      | Bottom color (Default: FFFFFF) |

# Original
Original by Vincent Rémond ([Source](https://github.com/vincentremond/2srt2ass/))