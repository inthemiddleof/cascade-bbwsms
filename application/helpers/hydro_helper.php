<?php
defined('BASEPATH') OR exit('No direct script access allowed');

/**
 * Konversi TMA dari cm ke meter (untuk input user)
 */
function cm_to_m($value) {
    if ($value === null || $value === '' || $value === false) return null;
    $floatVal = (float) $value;
    if ($floatVal == 0 && $value !== 0 && $value !== '0' && $value !== 0.0) return null;
    return round($floatVal / 100, 3);
}

/**
 * Konversi TMA dari meter ke cm (untuk form edit)
 */
function m_to_cm($value) {
    if ($value === null || $value === '' || $value === false) return null;
    return round((float) $value * 100, 2);
}

/**
 * Format angka ke format Indonesia: koma (,) sebagai desimal, titik (.) sebagai ribuan
 */
function id_number($value, $dec = 2) {
    if ($value === null || $value === '') return '';
    return number_format((float) $value, $dec, ',', '.');
}

/**
 * Format nilai TMA untuk tampilan (meter)
 */
function fmt_tma($value, $dec = 2) {
    if ($value === null || $value === '') return '-';
    return id_number($value, $dec) . ' m';
}

/**
 * Format nilai curah hujan untuk tampilan (mm)
 */
function fmt_rain($value, $dec = 1) {
    if ($value === null || $value === '') return '-';
    return id_number($value, $dec) . ' mm';
}

/**
 * Format nilai debit untuk tampilan (m³/s atau m³/dt)
 */
function fmt_debit($value, $dec = 3, $unit = 'm³/s') {
    if ($value === null || $value === '') return '-';
    return id_number($value, $dec) . ' ' . $unit;
}

/**
 * Format nilai volume untuk tampilan (jt.m³)
 */
function fmt_volume($value, $dec = 2) {
    if ($value === null || $value === '') return '-';
    return id_number($value, $dec) . ' jt.m³';
}

/**
 * Format nilai luas untuk tampilan (km²)
 */
function fmt_luas($value, $dec = 4) {
    if ($value === null || $value === '') return '-';
    return id_number($value, $dec) . ' km²';
}

/**
 * Format nilai rembesan h (cm)
 */
function fmt_rembesan_h($value, $dec = 1) {
    if ($value === null || $value === '') return '-';
    return id_number($value, $dec) . ' cm';
}

/**
 * Format nilai rembesan Q (lt/s)
 */
function fmt_rembesan_q($value, $dec = 2) {
    if ($value === null || $value === '') return '-';
    return id_number($value, $dec) . ' lt/s';
}

/**
 * Format nilai NWL (m)
 */
function fmt_nwl($value, $dec = 2) {
    if ($value === null || $value === '') return '-';
    return id_number($value, $dec) . ' m';
}

/**
 * Format nilai elevasi mercu (m, bisa negatif)
 * Digunakan untuk tampilan data bendung
 */
function fmt_mercu($value, $dec = 2) {
    if ($value === null || $value === '') return '-';
    return id_number($value, $dec) . ' m';
}

/**
 * Format nilai sluice gate (m³/dt)
 */
function fmt_sluice($value, $dec = 3) {
    if ($value === null || $value === '') return '-';
    return id_number($value, $dec) . ' m³/dt';
}

// ==========================================
// FUNGSI KHUSUS BENDUNG
// ==========================================

/**
 * Format nilai Q-Total / debit bendung (m³/dt)
 */
function fmt_qtotal($value, $dec = 3) {
    if ($value === null || $value === '') return '-';
    return id_number($value, $dec) . ' m³/dt';
}

/**
 * Format nilai Q-FC1 (m³/dt)
 */
function fmt_qfc1($value, $dec = 3) {
    if ($value === null || $value === '') return '-';
    return id_number($value, $dec) . ' m³/dt';
}

/**
 * Format nilai Q-FC2 (m³/dt)
 */
function fmt_qfc2($value, $dec = 3) {
    if ($value === null || $value === '') return '-';
    return id_number($value, $dec) . ' m³/dt';
}

/**
 * Format nilai Q-Saluran Induk (m³/dt)
 */
function fmt_qsal_induk($value, $dec = 3) {
    if ($value === null || $value === '') return '-';
    return id_number($value, $dec) . ' m³/dt';
}

/**
 * Format nilai Q-Limpas (m³/dt)
 */
function fmt_qlimpas($value, $dec = 3) {
    if ($value === null || $value === '') return '-';
    return id_number($value, $dec) . ' m³/dt';
}

/**
 * Format nilai Q-Sungai (m³/dt)
 */
function fmt_qsungai($value, $dec = 3) {
    if ($value === null || $value === '') return '-';
    return id_number($value, $dec) . ' m³/dt';
}

/**
 * Format nilai Q-SPAM KPBU (m³/dt)
 */
function fmt_qspam($value, $dec = 3) {
    if ($value === null || $value === '') return '-';
    return id_number($value, $dec) . ' m³/dt';
}

/**
 * Format nilai bukaan pintu (m)
 */
function fmt_bukaan($value, $dec = 2) {
    if ($value === null || $value === '') return '-';
    return id_number($value, $dec) . ' m';
}

// ==========================================
// FUNGSI KHUSUS BENDUNGAN
// ==========================================

/**
 * Format nilai volume NWL (jt.m³)
 */
function fmt_nwl_volume($value, $dec = 2) {
    if ($value === null || $value === '') return '-';
    return id_number($value, $dec) . ' jt.m³';
}

/**
 * Format nilai luas NWL (km²)
 */
function fmt_nwl_luas($value, $dec = 4) {
    if ($value === null || $value === '') return '-';
    return id_number($value, $dec) . ' km²';
}

/**
 * Format nilai elevasi puncak (m)
 */
function fmt_elevasi_puncak($value, $dec = 2) {
    if ($value === null || $value === '') return '-';
    return id_number($value, $dec) . ' m';
}

/**
 * Format nilai tinggi embung (m)
 */
function fmt_tinggi_embung($value, $dec = 2) {
    if ($value === null || $value === '') return '-';
    return id_number($value, $dec) . ' m';
}

/**
 * Format nilai panjang tubuh (m)
 */
function fmt_panjang_tubuh($value, $dec = 2) {
    if ($value === null || $value === '') return '-';
    return id_number($value, $dec) . ' m';
}