<?php
echo 'users cols: ' . implode(',', Schema::getColumnListing('users')) . PHP_EOL;
echo 'children cols: ' . implode(',', Schema::getColumnListing('children')) . PHP_EOL;
echo 'schools: ' . \App\Models\School::count() . ', users: ' . \App\Models\User::count() . ', children: ' . \App\Models\Child::count() . PHP_EOL;
$s = \App\Models\School::first();
echo 'school: ' . $s->name . ' | color:' . $s->color . ' | logo:' . ($s->logo ?? 'null') . PHP_EOL;
$orphan = \App\Models\Child::whereNull('school_id')->count();
echo 'children without school_id: ' . $orphan . PHP_EOL;
