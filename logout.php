<?php

session_start();
session_unset();
session_destroy();

header('Location: login.php?status=success&message=' . urlencode('You have been logged out successfully.'));
exit;
