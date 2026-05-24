<?php
/**
 * GENERATE SUPER ADMIN - CASCADE
 * Akses: http://localhost/hidrologibbwsms/generate_superadmin.php
 * * ⚠️ HAPUS FILE INI SETELAH DIGUNAKAN!
 */

// Koneksi database
$host = 'localhost';
$user = 'root';      // Sesuaikan dengan username MySQL Anda
$pass = '';          // Sesuaikan dengan password MySQL Anda
$db   = 'db_hidrologibbwsms';

$conn = new mysqli($host, $user, $pass, $db);

if ($conn->connect_error) {
    die("❌ Koneksi gagal: " . $conn->connect_error);
}

echo "<div style='font-family: monospace; max-width: 700px; margin: 20px auto; padding: 20px; border: 1px solid #ccc; border-radius: 10px; background: #fff;'>";
echo "<h2 style='color: #000080;'>🔧 Pembuatan Akun Super Admin CASCADE</h2>";

// ============================================
// DATA LOGIN SUPER ADMIN BARU
// ============================================
$username = 'superadmin';
$new_password = 'superadmin123';

// Generate hash bcrypt yang divalidasi langsung oleh PHP local Anda
$new_hash = password_hash($new_password, PASSWORD_BCRYPT, ['cost' => 12]);

echo "<div style='background: #f0f9ff; border: 1px solid #bae6fd; padding: 15px; border-radius: 8px; margin-bottom: 20px;'>";
echo "<h3 style='margin: 0 0 10px 0; color: #0369a1;'>📋 Detail Akun yang Diproses</h3>";
echo "<p><b>Username:</b> <code>{$username}</code></p>";
echo "<p><b>Password Baru:</b> <code>{$new_password}</code></p>";
echo "<p><b>Hash Baru:</b> <br><code style='word-break: break-all; font-size: 11px;'>{$new_hash}</code></p>";
echo "</div>";

// ============================================
// CEK APAKAH USER SUDAH ADA
// ============================================
$check_sql = "SELECT id_user, username, nama_lengkap FROM users WHERE username = '{$username}'";
$check_result = $conn->query($check_sql);

if ($check_result && $check_result->num_rows > 0) {
    $user_data = $check_result->fetch_assoc();
    
    echo "<div style='background: #fefce8; border: 1px solid #fef08a; padding: 15px; border-radius: 8px; margin-bottom: 20px;'>";
    echo "<h3 style='margin: 0 0 10px 0; color: #854d0e;'>⚠️ User Sudah Terdaftar</h3>";
    echo "<p>Akun dengan username <b>'{$username}'</b> sudah ada dengan Nama: <b>{$user_data['nama_lengkap']}</b>.</p>";
    echo "<p>Proses dibatalkan untuk menghindari duplikasi data.</p>";
    echo "</div>";
    
} else {
    // ============================================
    // PROSES INSERT AKUN SUPER ADMIN
    // ============================================
    $nama  = 'Super Admin Pusat';
    $email = 'superadmin@bbwsms.go.id';
    $role  = 'superadmin'; // Otoritas tertinggi
    
    $insert_sql = "INSERT INTO users (username, email, password, nama_lengkap, role, status, id_pos, created_at) 
                   VALUES ('{$username}', '{$email}', '{$new_hash}', '{$nama}', '{$role}', 'aktif', NULL, NOW())";
    
    if ($conn->query($insert_sql) === TRUE) {
        echo "<div style='background: #d4edda; border: 2px solid #28a745; padding: 20px; border-radius: 8px; margin-bottom: 20px;'>";
        echo "<h3 style='color: #155724; margin: 0 0 10px 0;'>✅ SUPER ADMIN BERHASIL DIBUAT!</h3>";
        echo "<p style='margin: 0;'>Data telah berhasil dimasukkan ke database local dengan enkripsi penyeimbang.</p>";
        echo "</div>";
        
        // Verifikasi langsung kecocokan hash PHP
        $verify = password_verify($new_password, $new_hash);
        echo "<div style='background: " . ($verify ? '#d4edda' : '#f8d7da') . "; padding: 15px; border-radius: 8px; margin-bottom: 20px;'>";
        echo "<h3 style='margin: 0 0 5px 0;'>🧪 Uji Validasi Password</h3>";
        echo "<p style='margin: 0;'><b>password_verify() internal:</b> " . ($verify ? '✅ SINKRON (Siap Digunakan!)' : '❌ EROR (Hash tidak cocok)') . "</p>";
        echo "</div>";
        
    } else {
        echo "<div style='background: #f8d7da; border: 2px solid #dc3545; padding: 20px; border-radius: 8px; margin-bottom: 20px;'>";
        echo "<h3 style='color: #721c24; margin: 0 0 10px 0;'>❌ GAGAL MENULIS KE DATABASE</h3>";
        echo "<p style='margin: 0;'>Error: " . $conn->error . "</p>";
        echo "</div>";
    }
}

// ============================================
// INFORMASI LOGIN
// ============================================
echo "<hr style='margin: 20px 0; border: none; border-top: 1px solid #e5e7eb;'>";
echo "<h3 style='color: #000080;'>🔑 Kredensial Akses</h3>";
echo "<table style='width: 100%; border-collapse: collapse; margin-bottom: 20px;'>";
echo "<tr style='background: #f8f9fa;'>
        <td style='padding: 10px; border: 1px solid #dee2e6; font-weight: bold; width: 150px;'>URL Login</td>
        <td style='padding: 10px; border: 1px solid #dee2e6;'><code>http://localhost/testhidrologibbwsms/auth</code></td>
      </tr>";
echo "<tr>
        <td style='padding: 10px; border: 1px solid #dee2e6; font-weight: bold;'>Username</td>
        <td style='padding: 10px; border: 1px solid #dee2e6;'><code>{$username}</code></td>
      </tr>";
echo "<tr style='background: #f8f9fa;'>
        <td style='padding: 10px; border: 1px solid #dee2e6; font-weight: bold;'>Password</td>
        <td style='padding: 10px; border: 1px solid #dee2e6;'><code>{$new_password}</code></td>
      </tr>";
echo "</table>";

// ============================================
// PERINGATAN KEAMANAN
// ============================================
echo "<div style='background: #fee2e2; border: 2px solid #ef4444; padding: 15px; border-radius: 8px; text-align: center;'>";
echo "<p style='color: #991b1b; font-weight: bold; margin: 0; font-size: 16px;'>⚠️ AMANKAN PROJECT ANDA!</p>";
echo "<p style='color: #991b1b; margin: 5px 0 0 0; font-size: 13px;'>Hapus file <u>generate_superadmin.php</u> ini dari direktori htdocs setelah berhasil masuk.</p>";
echo "</div>";

echo "</div>";

$conn->close();
?>