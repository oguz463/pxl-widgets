## Notes
- Live example: [link](http://209.126.8.190/)
- For parsing JSON efficently, fast, and without any concern of memory limits [cerbero90/lazy-json](https://github.com/cerbero90/lazy-json) which uses [halaxa/json-machine](https://github.com/halaxa/json-machine) is used.
- For performance and avoiding memory limits LazyCollections (Created from PHP Generators) used.
- Laravel's queue system is used with Redis for making process continue after any cut-offs.
- For any further request from client the parser is designed extendable.
- All filters have been added (Between ages(null), consecutive numbers.)
- All DB records can be exported as CSV file.

## Second Commit
- Now configured as whole data is also thrown to the queue and configured queue priorities for writing data with a correct order in any case. (Faster process to show to client.)