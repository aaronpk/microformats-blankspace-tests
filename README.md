Microformats Blankspace Tests
=============================

Some test cases for blank space handling in Microformats.

More details:

* https://github.com/microformats/microformats2-parsing/issues/15
* https://github.com/microformats/microformats2-parsing/issues/48


Test Results
------------

https://pin13.net/mf2/blankspace.html


Compiling Results
-----------------

Run the parser against all the tests by running:

```
php run.php
```

That will send the test HTML to each parser, and store the results in `results/output.json`. It will also compile the results into an HTML file `results/results.html` which you can view in a browser.
