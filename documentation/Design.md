## Design of the CBE Data Service

There are several key principles and concepts underlying the design of the CBE Data Service.

### Dataset - Definition
The first key principle is that datasets on the service are not themselves considered
to be the primary sources of information. Rather:

> A dataset is an artifact that reflects the (configured) state of a data source at a particular time.

Let's unpack that.

First, the service assumes that the data it manages is a processed reflection of
some other, original source. That source may be a file that has been uploaded to a 
special-purpose repository, an API endpoint, a file on an FTP server, etc. But the data under
direct management by the CPS is derived from something else.

Second, the original data source may well change over time, and so the artifact maintained by the CDS 
reflects a specific time when the fetch was made. Both to avoid conflict and to maintain past versions,
each fetch creates a new copy (with some sort of purging/dropping when no change). 

Finally, the artifact may not contain all the information available from the data source,
but may be configured to bring back particular data (fields, years, aggregated vs transactional, etc.).
 
So a dataset is an artifact that depends on:
 * The source (typically a URL)
 * The last fetch time
 * The configuration of the fetch command


### Dataset - Format

The format is intended to be derived from this [http://fiscal.dataprotocols.org/](Budget Data Package)
definition.

