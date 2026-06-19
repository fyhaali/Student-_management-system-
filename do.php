<?php
// إعدادات الاتصال بالسيرفر المحلي
$host = 'localhost';
$db   = 'student_management';
$user = 'root'; // اسم المستخدم الافتراضي في XAMPP
$pass = '';     // كلمة المرور الافتراضية تكون فارغة
$charset = 'utf8mb4';

// نص الاتصال (DSN)
$dsn = "mysql:host=$host;dbname=$db;charset=$charset";

// خيارات الـ PDO لرفع مستوى الأمان والتحكم بالأخطاء
$options = [
    PDO::ATTR_ERRMODE            => PDO::ERRMODE_EXCEPTION, // تحويل الأخطاء إلى Exceptions
    PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,       // جلب البيانات على شكل مصفوفة ترابطية
    PDO::ATTR_EMULATE_PREPARES   => false,                  // إيقاف المحاكاة لاستخدام Prepared Statements الحقيقية والآمنة
];

try {
    // إنشاء كائن الـ PDO وبدء الاتصال
    $pdo = new PDO($dsn, $user, $pass, $options);
} catch (\PDOException $e) {
    // في حال فشل الاتصال يتم عرض رسالة الخطأ
    die("فشل الاتصال بقاعدة البيانات: " . $e->getMessage());
}
?>
