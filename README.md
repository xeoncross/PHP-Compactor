## PHP Source-Code Compactor

Do not use this to speed up your PHP by compacting it. I will come for you. Use a [real bytecode cache](http://en.wikipedia.org/wiki/List_of_PHP_accelerators).

Instead use this to get an idea of the TRUE size of projects by comparing the actual number of characters required to run that class - without long variable names, comments, or other added "fluff".

## Usage

To use, simply call the CLI `compact` script with the path to the directory to scan.

    $ php compact ../project/dir

If you want the output saved in the `results/` directory then pass a truthy value as the second param.

    $ php compact ../project/dir TRUE

If you want the output saved in a custom directory then pass an existed directory as the second param.

    $ php compact ../project/dir ./dist

## License (MIT License)

Copyright (c) 2011 [David Pennington](http://xeoncross.com)

Permission is hereby granted, free of charge, to any person obtaining a copy of this software and associated documentation files (the 'Software'), to deal in the Software without restriction, including without limitation the rights to use, copy, modify, merge, publish, distribute, sublicense, and/or sell copies of the Software, and to permit persons to whom the Software is furnished to do so, subject to the following conditions:

The above copyright notice and this permission notice shall be included in all copies or substantial portions of the Software.

THE SOFTWARE IS PROVIDED 'AS IS', WITHOUT WARRANTY OF ANY KIND, EXPRESS OR IMPLIED, INCLUDING BUT NOT LIMITED TO THE WARRANTIES OF MERCHANTABILITY, FITNESS FOR A PARTICULAR PURPOSE AND NONINFRINGEMENT. IN NO EVENT SHALL THE AUTHORS OR COPYRIGHT HOLDERS BE LIABLE FOR ANY CLAIM, DAMAGES OR OTHER LIABILITY, WHETHER IN AN ACTION OF CONTRACT, TORT OR OTHERWISE, ARISING FROM, OUT OF OR IN CONNECTION WITH THE SOFTWARE OR THE USE OR OTHER DEALINGS IN THE SOFTWARE.


