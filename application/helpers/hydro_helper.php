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