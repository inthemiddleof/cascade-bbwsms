<?php
defined('BASEPATH') OR exit('No direct script access allowed');

$config['auth'] = [
    // Keamanan Password
    'password_algo' => PASSWORD_BCRYPT,
    'password_cost' => 12, // Cost factor bcrypt (semakin tinggi semakin aman tapi lambat)
    
    // Session
    'session_expire' => 28800, // 8 jam dalam detik
    'remember_me_expire' => 604800, // 7 hari
    
    // Rate Limiting (Brute Force Protection)
    'max_login_attempts' => 5, // Maksimal percobaan gagal
    'lockout_duration' => 900, // 15 menit dalam detik
    'reset_attempts_after' => 3600, // Reset counter setelah 1 jam
    
    // CSRF Protection (gunakan bawaan CI3)
    'csrf_protection' => TRUE,
    
    // Password Policy
    'min_password_length' => 8,
    'require_uppercase' => TRUE,
    'require_lowercase' => TRUE,
    'require_number' => TRUE,
    'require_special_char' => TRUE,
];