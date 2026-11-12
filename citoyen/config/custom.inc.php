<?php

// Utilise le login (%u) et mot de passe (%p) IMAP pour le SMTP
$config['smtp_user'] = '%u';
$config['smtp_pass'] = '%p';
$config['smtp_auth_type'] = 'PLAIN';

$config['smtp_debug'] = true;

$config['username_domain'] = 'marche.be';
//$config['login_lc'] = true;
//$config['username_domain_forced'] = false;

$config['managesieve_host'] = 'host.docker.internal';
$config['managesieve_port'] = 4190;
$config['managesieve_auth_type'] = 'PLAIN';
$config['managesieve_vacation'] = 1;
$config['managesieve_vacation_addresses_init'] = true;
$config['managesieve_mbox_encoding'] = 'UTF-8';
$config['managesieve_forward'] = 1;
$config['managesieve_default_headers'] = ['Subject', 'From', 'To'];

$config['login_lc'] = 2;

$config['skin_logo'] = [
    "elastic:login[small]" => "/images/marche.jpg",
    "elastic:login" => "/images/marche.jpg",
];
$config['session_lifetime'] = 180;
$config['login_autocomplete'] = 2;
$config['max_message_size'] = '30M';
$config['identities_level'] = 3;

$config['message_sort_col'] = 'date';

$config['message_sort_order'] = 'DESC';
$config['collected_recipients'] = false;

$config['collected_senders'] = false;

$config['addressbook_sort_col'] = 'name';

$config['addressbook_name_listing'] = 0;

$config['show_images'] = 0;
$config['htmleditor'] = 1;
$config['logout_expunge'] = true;
$config['refresh_interval'] = 600;
$config['check_all_folders'] = true;
$config['reply_mode'] = 1;

$config['blocked_schemes'] = ['http'];

$config['password_driver'] = 'sql';
$config['password_db_dsn'] = 'mysqli://root:***@host.docker.internal/dovecot_auth';
$config['password_confirm_current'] = true;
$config['password_minimum_length'] = 12;
$config['password_algorithm'] = 'hash-argon2id';
$config['password_algorithm_prefix'] = '{ARGON2ID}';
$config['password_query'] = "UPDATE virtual_users SET userPassword = %P WHERE username = %u";

$config['log_driver'] = 'file';
$config['log_logins'] = true;
$config['log_errors'] = true;
