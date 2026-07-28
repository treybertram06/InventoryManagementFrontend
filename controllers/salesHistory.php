<?php

Core\Common::require_login($db);

view('salesHistory.view.php', ['db' => $db, 'sales' => $db->get_all_sales()]);