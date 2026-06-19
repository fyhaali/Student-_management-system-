<?php
// تضمين ملف الاتصال بقاعدة البيانات الذي أنشأته
require_once 'do.php';

// متغيرات لحفظ رسائل التنبيه للمستخدم
$message = '';
$message_type = ''; // يمكن أن تكون success أو error

// التحقق مما إذا تم إرسال النموذج باستخدام طريقة POST
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    
    // 1. استقبال البيانات وتطهيرها (Sanitization & Validation)
    $student_name   = trim($_POST['student_name'] ?? '');
    $email          = filter_var(trim($_POST['email'] ?? ''), FILTER_VALIDATE_EMAIL);
    $student_number = trim($_POST['student_number'] ?? '');
    $year_of_study  = filter_var($_POST['year_of_study'] ?? '', FILTER_VALIDATE_INT);
    $batch_name     = trim($_POST['batch_name'] ?? '');

    // التحقق من أن جميع الحقول تم إدخالها بشكل صحيح
    if (empty($student_name) || !$email || empty($student_number) || !$year_of_study || empty($batch_name)) {
        $message = "الرجاء التأكد من ملء جميع الحقول بشكل صحيح وبصيغة بريد إلكتروني صالحة.";
        $message_type = "error";
    } else {
        try {
            // 2. استخدام الاستعلامات المجهزة (Prepared Statements) لمنع هجمات SQL Injection
            $sql = "INSERT INTO students (student_name, email, student_number, year_of_study, batch_name) 
                    VALUES (:student_name, :email, :student_number, :year_of_study, :batch_name)";
            
            $stmt = $pdo->prepare($sql);
            
            // ربط القيم بالمعاملات وتنفيذ الاستعلام
            $stmt->execute([
                ':student_name'   => $student_name,
                ':email'          => $email,
                ':student_number' => $student_number,
                ':year_of_study'  => $year_of_study,
                ':batch_name'     => $batch_name
            ]);

            $message = "تم تسجيل الطالب بنجاح في قاعدة البيانات!";
            $message_type = "success";
            
        } catch (PDOException $e) {
            // التحقق من الأخطاء الناتجة عن تكرار البيانات الفريدة (مثل الإيميل أو الرقم الجامعي المشابه)
            if ($e->getCode() == 23000) {
                $message = "خطأ: البريد الإلكتروني أو الرقم الجامعي مسجل مسبقاً لمستخدم آخر.";
            } else {
                $message = "حدث خطأ غير متوقع أثناء الحفظ: " . $e->getMessage();
            }
            $message_type = "error";
        }
    }
}
?>

<!DOCTYPE html>
<html lang="ar" dir="rtl">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>نظام تسجيل الطلاب</title>
    <style>
        /* تنسيقات CSS أساسية واحترافية للمشروع */
        body { font-family: Arial, sans-serif; background-color: #f4f7f6; margin: 0; padding: 20px; }
        .container { max-width: 500px; background: #fff; margin: 30px auto; padding: 20px; box-shadow: 0 4px 8px rgba(0,0,0,0.1); border-radius: 8px; }
        h2 { text-align: center; color: #333; }
        .form-group { margin-bottom: 15px; }
        label { display: block; margin-bottom: 5px; color: #666; font-weight: bold; }
        input[type="text"], input[type="email"], input[type="number"], select { width: 100%; padding: 10px; border: 1px solid #ccc; border-radius: 4px; box-sizing: border-box; }
        button { width: 100%; padding: 12px; background-color: #2ecc71; border: none; color: white; font-size: 16px; font-weight: bold; border-radius: 4px; cursor: pointer; }
        button:hover { background-color: #27ae60; }
        /* تنسيق رسائل التنبيه الملونة المطلوبة */
        .alert { padding: 15px; margin-bottom: 20px; border-radius: 4px; font-weight: bold; text-align: center; }
        .success { background-color: #d4edda; color: #155724; border: 1px solid #c3e6cb; }
        .error { background-color: #f8d7da; color: #721c24; border: 1px solid #f5c6cb; }
        .nav-link { display: block; text-align: center; margin-top: 15px; color: #3498db; text-decoration: none; font-weight: bold; }
        .nav-link:hover { text-decoration: underline; }
    </style>
</head>
<body>

<div class="container">
    <h2>استمارة تسجيل طالب جديد</h2>

    <?php if (!empty($message)): ?>
        <div class="alert <?php echo $message_type; ?>">
            <?php echo $message; ?>
        </div>
    <?php endif; ?>

    <form action="index.php" method="POST">
        <div class="form-group">
            <label for="student_name">اسم الطالب الفعلي:</label>
            <input type="text" id="student_name" name="student_name" required>
        </div>

        <div class="form-group">
            <label for="email">البريد الإلكتروني:</label>
            <input type="email" id="email" name="email" required>
        </div>

        <div class="form-group">
            <label for="student_number">الرقم الجامعي:</label>
            <input type="text" id="student_number" name="student_number" required>
        </div>

        <div class="form-group">
            <label for="year_of_study">السنة الدراسية:</label>
            <input type="number" id="year_of_study" name="year_of_study" min="1" max="7" required>
        </div>

        <div class="form-group">
            <label for="batch_name">اسم الدفعة:</label>
            <input type="text" id="batch_name" name="batch_name" required>
        </div>

        <button type="submit">تسجيل الطالب</button>
    </form>
    
    <a href="students.php" class="nav-link">عرض وإدارة الطلاب المسجلين ←</a>
</div>

</body>
</html>
