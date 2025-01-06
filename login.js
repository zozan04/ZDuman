// Portal Butonları
const studentPortalBtn = document.getElementById('studentPortalBtn');
const userPortalBtn = document.getElementById('userPortalBtn');
const adminPortalBtn = document.getElementById('adminPortalBtn');

// Portallar
const studentPortal = document.getElementById('studentPortal');
const userPortal = document.getElementById('userPortal');
const adminPortal = document.getElementById('adminPortal');

// Admin giriş formu ve hata mesajı
const adminLoginForm = document.getElementById('adminLoginForm');
const adminEmailInput = document.getElementById('adminEmail');
const adminPasswordInput = document.getElementById('adminPassword');
const errorMessage = document.getElementById('errorMessage');

// Öğrenci ve Kullanıcı kayıt formları
const studentRegisterForm = document.getElementById('studentRegisterForm');
const userRegisterForm = document.getElementById('userRegisterForm');

// Belge yükleme formu
const documentUploadForm = document.getElementById('documentUploadForm');

// Doğru admin bilgileri
const correctAdminEmail = 'zozanduman3030@gmail.com';
const correctAdminPassword = '12345';

// Portal seçiminde sadece seçilen portal görünür olacak
studentPortalBtn.addEventListener('click', () => {
    studentPortal.classList.remove('hidden');
    userPortal.classList.add('hidden');
    adminPortal.classList.add('hidden');
});

userPortalBtn.addEventListener('click', () => {
    userPortal.classList.remove('hidden');
    studentPortal.classList.add('hidden');
    adminPortal.classList.add('hidden');
});

adminPortalBtn.addEventListener('click', () => {
    adminPortal.classList.remove('hidden');
    studentPortal.classList.add('hidden');
    userPortal.classList.add('hidden');
});

// Admin Giriş Formu Kontrolü
adminLoginForm.addEventListener('submit', (event) => {
    event.preventDefault(); // Formun sayfayı yenilemesini engelle

    const email = adminEmailInput.value;
    const password = adminPasswordInput.value;

    if (email === correctAdminEmail && password === correctAdminPassword) {
        // Başarılı giriş, admin.html sayfasına yönlendir
        window.location.href = 'phpmyadmin/admin.php';
    } else {
        // Hatalı giriş, hata mesajını göster
        errorMessage.classList.remove('hidden');
    }
});

// Öğrenci Kayıt Formu Gönderimi
studentRegisterForm.addEventListener('submit', (event) => {
    event.preventDefault();
    const studentName = document.getElementById('studentName').value;
    const studentEmail = document.getElementById('studentRegEmail').value;
    const studentPassword = document.getElementById('studentRegPassword').value;

    console.log("Öğrenci Kayıt: ", studentName, studentEmail, studentPassword);
    // Öğrenci kayıt formu başarıyla tamamlandığında, belge yükleme formunu göster
    studentRegisterForm.classList.add('hidden');  // Öğrenci kayıt formunu gizle
    documentUploadForm.classList.remove('hidden');  // Belge yükleme formunu göster
});

// Belge Yükleme Formu Gönderimi
documentUploadForm.addEventListener('submit', (event) => {
    event.preventDefault();

    // Burada belge yükleme işlemi yapılabilir.

    // Formu gizle
    documentUploadForm.classList.add('hidden');

    // Bilgilendirme mesajını göster
    const confirmationMessage = document.getElementById('confirmationMessage');
    confirmationMessage.classList.remove('hidden');
    confirmationMessage.classList.add('visible');
});

document.addEventListener('DOMContentLoaded', () => {
    const closeMessageButton = document.getElementById('closeMessage');
    const confirmationMessage = document.getElementById('confirmationMessage');

    // Eğer "closeMessage" butonuna tıklanırsa
    if (closeMessageButton) {
        closeMessageButton.addEventListener('click', () => {
            // confirmationMessage öğesini gizle
            confirmationMessage.classList.add('hidden');
        });
    }

    // Eğer confirmationMessage görünürse
    const showMessageButton = document.getElementById('showMessageButton'); // Mesajı gösterecek buton
    if (showMessageButton) {
        showMessageButton.addEventListener('click', () => {
            confirmationMessage.classList.remove('hidden');
            confirmationMessage.classList.add('visible');
        });
    }
});

// Kullanıcı Kayıt Formu Gönderimi
userRegisterForm.addEventListener('submit', (event) => {
    event.preventDefault();
    const userName = document.getElementById('userName').value;
    const userEmail = document.getElementById('userRegEmail').value;
    const userPassword = document.getElementById('userRegPassword').value;

    console.log("Kullanıcı Kayıt: ", userName, userEmail, userPassword);
    // Burada kullanıcıyı kaydedebilir veya bir işlem yapabilirsiniz.
    alert('Kullanıcı başarıyla kayıt oldu!');
});

// Form geçişlerini yönetme
document.getElementById('studentLoginBtn').addEventListener('click', () => {
    document.getElementById('studentLoginForm').classList.remove('hidden');
    document.getElementById('studentRegisterForm').classList.add('hidden');

    // Aktif buton sınıfı ekle
    document.getElementById('studentLoginBtn').classList.add('active');
    document.getElementById('studentRegisterBtn').classList.remove('active');
});

document.getElementById('studentRegisterBtn').addEventListener('click', () => {
    document.getElementById('studentLoginForm').classList.add('hidden');
    document.getElementById('studentRegisterForm').classList.remove('hidden');

    // Aktif buton sınıfı ekle
    document.getElementById('studentRegisterBtn').classList.add('active');
    document.getElementById('studentLoginBtn').classList.remove('active');
});

document.getElementById('userLoginBtn').addEventListener('click', () => {
    document.getElementById('userLoginForm').classList.remove('hidden');
    document.getElementById('userRegisterForm').classList.add('hidden');

    document.getElementById('userLoginBtn').classList.add('active');
    document.getElementById('userRegisterBtn').classList.remove('active');
});

document.getElementById('userRegisterBtn').addEventListener('click', () => {
    document.getElementById('userLoginForm').classList.add('hidden');
    document.getElementById('userRegisterForm').classList.remove('hidden');

    document.getElementById('userRegisterBtn').classList.add('active');
    document.getElementById('userLoginBtn').classList.remove('active');
});
