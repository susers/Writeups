
Goals
==============
- Create a unified Drupal API for web services to be exposed in a variety of
  different server formats.
- Provide a service browser to be able to test methods.
- Allow distribution of API keys for developer access.

Documentation
==============
http://drupal.org/node/109782

Installation
============
If you are using the rest server you will need to download the latest version of SPYC:
wget https://raw.github.com/mustangostang/spyc/79f61969f63ee77e0d9460bc254a27a671b445f3/spyc.php -O  servers/rest_server/lib/spyc.php

Once downloaded you need to add spyc.php to the rest_server/lib folder which exists under
the location you have installed services in.

Documentation files
===================
You can find these files in /docs folder.
services.authentication.api.php -- hooks related to authentication plugins
services.servers.api.php -- servers definition hooks
services.services.api.php -- definition of new services
services.versions.api.php -- how to write versioned resources

Settings via variables
======================

'services_{$resource}_index_page_size' -- this variable controls maximum number of results that
will be displayed by index query. See services_resource_build_index_query() for more information.

'services_generate_error_body' -- boolean denoting whether a message body should be included with
certain HTTP error-related status codes. (According to IETF's RFC2616, 204 and 304 responses must
not include bodies.)
