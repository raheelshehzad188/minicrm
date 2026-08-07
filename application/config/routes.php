<?php
defined('BASEPATH') OR exit('No direct script access allowed');

$route['default_controller'] = 'auth';
$route['404_override'] = '';
$route['translate_uri_dashes'] = FALSE;

/* Auth */
$route['login'] = 'auth/login';
$route['logout'] = 'auth/logout';
$route['auth'] = 'auth/login';
$route['auth/login'] = 'auth/login';
$route['auth/do_login'] = 'auth/do_login';
$route['auth/logout'] = 'auth/logout';
$route['auth/forgot'] = 'auth/forgot';
$route['auth/do_forgot'] = 'auth/do_forgot';
$route['auth/reset/(:any)'] = 'auth/reset/$1';
$route['auth/do_reset'] = 'auth/do_reset';
$route['auth/profile'] = 'auth/profile';
$route['auth/update_profile'] = 'auth/update_profile';
$route['auth/upload_avatar'] = 'auth/upload_avatar';
$route['auth/password'] = 'auth/password';
$route['auth/do_change_password'] = 'auth/do_change_password';

/* Organization */
$route['organization'] = 'organization/index';
$route['organization/update'] = 'organization/update';
$route['organization/upload_logo'] = 'organization/upload_logo';
$route['organization/regenerate_api_key'] = 'organization/regenerate_api_key';

/* Users */
$route['users'] = 'users/index';
$route['users/datatable'] = 'users/datatable';
$route['users/get/(:num)'] = 'users/get/$1';
$route['users/store'] = 'users/store';
$route['users/update/(:num)'] = 'users/update/$1';
$route['users/delete/(:num)'] = 'users/delete/$1';
$route['users/set_status/(:num)'] = 'users/set_status/$1';
$route['users/reset_password/(:num)'] = 'users/reset_password/$1';

/* Roles & Permissions */
$route['roles'] = 'roles/index';
$route['roles/permissions/(:num)'] = 'roles/permissions/$1';
$route['roles/save_permissions/(:num)'] = 'roles/save_permissions/$1';

/* Leads */
$route['leads'] = 'leads/index';
$route['leads/kanban'] = 'leads/kanban';
$route['leads/profile/(:num)'] = 'leads/profile/$1';
$route['leads/datatable'] = 'leads/datatable';
$route['leads/kanban_data'] = 'leads/kanban_data';
$route['leads/meta'] = 'leads/meta';
$route['leads/get/(:num)'] = 'leads/get/$1';
$route['leads/store'] = 'leads/store';
$route['leads/update/(:num)'] = 'leads/update/$1';
$route['leads/delete/(:num)'] = 'leads/delete/$1';
$route['leads/restore/(:num)'] = 'leads/restore/$1';
$route['leads/force_delete/(:num)'] = 'leads/force_delete/$1';
$route['leads/change_status/(:num)'] = 'leads/change_status/$1';
$route['leads/change_stage/(:num)'] = 'leads/change_stage/$1';
$route['leads/assign/(:num)'] = 'leads/assign/$1';
$route['leads/bulk'] = 'leads/bulk';
$route['leads/check_duplicate'] = 'leads/check_duplicate';
$route['leads/notes/(:num)'] = 'leads/notes/$1';
$route['leads/note_store/(:num)'] = 'leads/note_store/$1';
$route['leads/note_update/(:num)'] = 'leads/note_update/$1';
$route['leads/note_delete/(:num)'] = 'leads/note_delete/$1';
$route['leads/attachments/(:num)'] = 'leads/attachments/$1';
$route['leads/attachment_upload/(:num)'] = 'leads/attachment_upload/$1';
$route['leads/attachment_delete/(:num)'] = 'leads/attachment_delete/$1';
$route['leads/timeline/(:num)'] = 'leads/timeline/$1';
$route['leads/export'] = 'leads/export';
$route['leads/import_preview'] = 'leads/import_preview';
$route['leads/import_run'] = 'leads/import_run';
$route['leads/saved_filters'] = 'leads/saved_filters';
$route['leads/save_filter'] = 'leads/save_filter';
$route['leads/delete_filter/(:num)'] = 'leads/delete_filter/$1';
$route['leads/stages_by_pipeline/(:num)'] = 'leads/stages_by_pipeline/$1';

/* Reports */
$route['reports'] = 'reports/index';
$route['reports/leads'] = 'reports/leads';
$route['reports/leads_data'] = 'reports/leads_data';

/* API v1 */
$route['api/v1/leads'] = 'api/leads/create';

/* App */
$route['dashboard'] = 'dashboard/index';
$route['dashboard/data'] = 'dashboard/data';
$route['ui'] = 'ui/components';
$route['ui/components'] = 'ui/components';
$route['ui/forms'] = 'ui/forms';
$route['ui/tables'] = 'ui/tables';
