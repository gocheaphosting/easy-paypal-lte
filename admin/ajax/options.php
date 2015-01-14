<?php

require_once('../../EZ.php');

rm_transient('options'); // Since the user is changing the options
EZ::update('options_meta', $meta = true);
