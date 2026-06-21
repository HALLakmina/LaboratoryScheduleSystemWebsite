<?php
// Served from Apache's DocumentRoot (/var/www/html), which is one level above
// the LaboratoryScheduleSystemWebsite app folder (see Dockerfile WORKDIR).
// Visiting http://localhost/ with no index here causes Apache's
// "No matching DirectoryIndex found" error — this redirects to the real app.
header('Location: /LaboratoryScheduleSystemWebsite/Frontend/');
exit;
