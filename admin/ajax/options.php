<?php

require_once('../../EZ.php');

EZ::rmTransient('options'); // Since the user is changing the options
EZ::update('options_meta', $meta = true);
