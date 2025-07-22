<?php

use Livewire\Volt\Volt;

Volt::route('/users', 'users.index');
Volt::route('/', 'dashboard')->name('dashboard');
Volt::route('/monitors', 'monitors.listmonitors');
Volt::route('/monitor/add', 'monitors.monitor');
//Volt::route('/monitor/delete/{id}','monitors.delete');