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
    event.preventDefault(); // Sayfa yenilenmesini engeller

    // Form verilerini al
    const studentName = document.getElementById('name').value;
    const studentEmail = document.getElementById('email').value;
    const studentPassword = document.getElementById('password').value;
    const studentDocument = document.getElementById('studentDocument').files[0]; // Dosya nesnesini al

    // FormData nesnesi oluştur
    const formData = new FormData();
    formData.append('name', studentName);
    formData.append('email', studentEmail);
    formData.append('password', studentPassword);
    formData.append('studentDocument', studentDocument); // Dosyayı ekle

    // Formu gönder
    fetch('uploads.php', {
        method: 'POST',
        body: formData
    })
    .then(response => response.text())
    .then(data => {
        console.log(data); // Sunucudan gelen yanıtı logla

        // Portalı gizle ve sadece bilgilendirme mesajını göster
        hidePortal();
        showNotification("Veriler başarıyla gönderildi!");
    })
    .catch(error => {
        console.error('Hata:', error);

        // Portalı gizle ve sadece hata mesajını göster
        hidePortal();
        showNotification("Veriler gönderilirken bir hata oluştu!");
    });
});

// Portalı gizleyen fonksiyon
// Portalı kaldıran fonksiyon
function hidePortal() {
    studentPortal.style.display = 'none'; // Öğrenci Portalını gizler
    userPortal.style.display = 'none'; // Kullanıcı Portalını gizler
}


// Bilgilendirme mesajını gösteren fonksiyon
function showNotification(message) {
    // Bildirim öğesini oluştur
    const notification = document.createElement('div');
    notification.textContent = message;
    notification.style.position = 'fixed';
    notification.style.bottom = '450px';
    notification.style.right = '300px';
    notification.style.backgroundColor = '#4caf50'; // Başarılı mesaj için yeşil renk
    notification.style.color = 'white';
    notification.style.padding = '10px 20px';
    notification.style.borderRadius = '5px';
    notification.style.boxShadow = '0 4px 6px rgba(0, 0, 0, 0.1)';
    notification.style.zIndex = '1000';
    document.body.appendChild(notification);

    // 5 saniye sonra bildirimi gizle ve DOM'dan kaldır
    setTimeout(() => {
        notification.style.opacity = '0';
        setTimeout(() => {
            notification.remove();
        }, 2000);
    }, 2000);
}

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