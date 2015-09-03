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

Second, the original datasource may well change over time, and so the artifact maintain


### Dataset - Format

We ..