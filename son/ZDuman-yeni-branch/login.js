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
        window.location.href = 'admin.php';
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

document.getElementById('studentRegisterForm').addEventListener('submit', (event) => {
    event.preventDefault();  // Sayfa yenilenmesini engeller

    // Form verilerini al
    const studentName = document.getElementById('name').value;
    const studentEmail = document.getElementById('email').value;
    const studentPassword = document.getElementById('password').value;
    const studentDocument = document.getElementById('studentDocument').files[0];  // Dosya nesnesini al

    // FormData nesnesi oluştur
    const formData = new FormData();
    formData.append('name', studentName);
    formData.append('email', studentEmail);
    formData.append('password', studentPassword);
    formData.append('studentDocument', studentDocument);  // Dosyayı ekle

    // Formu gönder
    fetch('uploads.php', {
        method: 'POST',
        body: formData
    })
    .then(response => response.text())
    .then(data => {
        console.log(data);  // Sunucudan gelen yanıtı logla
    })
    .catch(error => {
        console.error('Hata:', error);
    });
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
