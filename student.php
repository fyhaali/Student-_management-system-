<?php
// تضمين ملف الاتصال بقاعدة البيانات
require_once 'do.php';

$message = '';
$message_type = '';

// 1. معالجة ميزة الحذف (Delete Action) عند الضغط على زر الحذف
if (isset($_GET['delete_id'])) {
    $delete_id = filter_var($_GET['delete_id'], FILTER_VALIDATE_INT);
    
    if ($delete_id) {
        try {
            // استخدام Prepared Statement لحذف السجل بشكل آمن ومنع الـ SQL Injection
            $sql_delete = "DELETE FROM students WHERE id = :id";
            $stmt_delete = $pdo->prepare($sql_delete);
            $stmt_delete->execute([':id' => $delete_id]);
            
            $message = "تم حذف سجل الطالب بنجاح!";
            $message_type = "success";
        } catch (PDOException $e) {
            $message = "حدث خطأ أثناء محاولة الحذف: " . $e->getMessage();
            $message_type = "error";
        }
    }
}

// 2. جلب جميع سجلات الطلاب (Data Retrieval) باستخدام أمر SELECT
try {
    $sql_select = "SELECT * FROM students ORDER BY id DESC";
    $stmt_select = $pdo->query($sql_select);
    $students = $stmt_select->fetchAll();
} catch (PDOException $e) {
    die("خطأ في جلب البيانات: " . $e->getMessage());
}
?>

<!DOCTYPE html>
<html lang="ar" dir="rtl">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>إدارة الطلاب المسجلين</title>
    <style>
        body { font-family: Arial, sans-serif; background-color: #f4f7f6; margin: 0; padding: 20px; }
        .container { max-width: 900px; background: #fff; margin: 30px auto; padding: 20px; box-shadow: 0 4px 8px rgba(0,0,0,0.1); border-radius: 8px; }
        h2 { text-align: center; color: #333; }
        
        /* تنسيق جدول البيانات ليظهر بشكل نظيف واحترافي */
        table { width: 100%; border-collapse: collapse; margin-top: 20px; font-size: 14px; text-align: right; }
        th, td { padding: 12px; border: 1px solid #ddd; }
        th { background-color: #34495e; color: white; }
        tr:nth-child(even) { background-color: #f9f9f9; }
        tr:hover { background-color: #f1f1f1; }
        
        /* تنسيق زر الحذف والتنبيهات */
        .btn-delete { background-color: #e74c3c; color: white; padding: 6px 12px; text-decoration: none; border-radius: 4px; font-weight: bold; font-size: 12px; }
        .btn-delete:hover { background-color: #c0392b; }
        .alert { padding: 15px; margin-bottom: 20px; border-radius: 4px; font-weight: bold; text-align: center; }
        .success { background-color: #d4edda; color: #155724; border: 1px solid #c3e6cb; }
        .error { background-color: #f8d7da; color: #721c24; border: 1px solid #f5c6cb; }
        
        .nav-links { display: flex; justify-content: space-between; margin-top: 20px; }
        .btn-back { color: #3498db; text-decoration: none; font-weight: bold; }
        .btn-back:hover { text-decoration: underline; }
    </style>
</head>
<body>

<div class="container">
    <h2>قائمة الطلاب المسجلين بالنظام</h2>

    <?php if (!empty($message)): ?>
        <div class="alert <?php echo $message_type; ?>">
            <?php echo $message; ?>
        </div>
    <?php endif; ?>

    <table>
        <thead>
            <tr>
                <th>المعرف (ID)</th>
                <th>اسم الطالب</th>
                <th>البريد الإلكتروني</th>
                <th>الرقم الجامعي</th>
                <th>السنة الدراسية</th>
                <th>الدفعة</th>
                <th>تاريخ التسجيل</th>
                <th>الإجراءات</th>
            </tr>
        </thead>
        <tbody>
            <?php if (count($students) > 0): ?>
                <?php foreach ($students as $student): ?>
                    <tr>
                        <td><?php echo htmlspecialchars($student['id']); ?></td>
                        <td><?php echo htmlspecialchars($student['student_name']); ?></td>
                        <td><?php echo htmlspecialchars($student['email']); ?></td>
                        <td><?php echo htmlspecialchars($student['student_number']); ?></td>
                        <td><?php echo htmlspecialchars($student['year_of_study']); ?></td>
                        <td><?php echo htmlspecialchars($student['batch_name']); ?></td>
                        <td><?php echo htmlspecialchars($student['created_at']); ?></td>
                        <td>
                            <a href="students.php?delete_id=<?php echo $student['id']; ?>" 
                               class="btn-delete" 
                               onclick="return confirm('هل أنت متأكد من رغبتك في حذف هذا الطالب؟');">حذف</a>
                        </td>
                    </tr>
                <?php endforeach; ?>
            <?php else: ?>
                <tr>
                    <td colspan="8" style="text-align: center; font-weight: bold; color: #999;">لا يوجد طلاب مسجلين حالياً في قاعدة البيانات.</td>
                </tr>
            <?php endif; ?>
        </tbody>
    </table>

    <div class="nav-links">
        <a href="index.php" class="btn-back">← العودة لصفحة التسجيل</a>
    </div>
</div>

</body>
</html>
