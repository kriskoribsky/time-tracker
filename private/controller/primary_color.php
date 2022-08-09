<?php
// this file is called with AJAX in js to query db and output primary project-group colors
$project_group_id = $_GET['groupid'];

Database::query('SELECT primary_color FROM project_groups WHERE id = :group_id', [
    'group_id' => $project_group_id;
])