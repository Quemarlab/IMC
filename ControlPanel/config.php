<?php
// Deprecated. Credentials now live in the git-ignored /.env file and are read by app/bootstrap.php.
// The legacy ajax/, manage/ and authenticate/ folders that included this file are replaced by the
// new admin panel (login, dashboard, resource, messages…) and can be deleted.
http_response_code(410);
exit('Gone.');
