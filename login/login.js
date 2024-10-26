// Portal Butonları
const studentPortalBtn = document.getElementById('studentPortalBtn');
const userPortalBtn = document.getElementById('userPortalBtn');

// Portallar
const studentPortal = document.getElementById('studentPortal');
const userPortal = document.getElementById('userPortal');

// Öğrenci ve Kullanıcı Formları
const studentLoginBtn = document.getElementById('studentLoginBtn');
const studentRegisterBtn = document.getElementById('studentRegisterBtn');
const studentLoginForm = document.getElementById('studentLoginForm');
const studentRegisterForm = document.getElementById('studentRegisterForm');

const userLoginBtn = document.getElementById('userLoginBtn');
const userRegisterBtn = document.getElementById('userRegisterBtn');
const userLoginForm = document.getElementById('userLoginForm');
const userRegisterForm = document.getElementById('userRegisterForm');

// Portal seçiminde sadece seçilen portal görünür olacak
studentPortalBtn.addEventListener('click', () => {
    studentPortal.classList.remove('hidden');
    userPortal.classList.add('hidden');
});

userPortalBtn.addEventListener('click', () => {
    userPortal.classList.remove('hidden');
    studentPortal.classList.add('hidden');
});

// Öğrenci Formu geçişleri
studentLoginBtn.addEventListener('click', () => {
    studentLoginForm.classList.remove('hidden');
    studentRegisterForm.classList.add('hidden');
    studentLoginBtn.classList.add('active');
    studentRegisterBtn.classList.remove('active');
});

studentRegisterBtn.addEventListener('click', () => {
    studentRegisterForm.classList.remove('hidden');
    studentLoginForm.classList.add('hidden');
    studentRegisterBtn.classList.add('active');
    studentLoginBtn.classList.remove('active');
});

// Kullanıcı Formu geçişleri
userLoginBtn.addEventListener('click', () => {
    userLoginForm.classList.remove('hidden');
    userRegisterForm.classList.add('hidden');
    userLoginBtn.classList.add('active');
    userRegisterBtn.classList.remove('active');
});

userRegisterBtn.addEventListener('click', () => {
    userRegisterForm.classList.remove('hidden');
    userLoginForm.classList.add('hidden');
    userRegisterBtn.classList.add('active');
    userLoginBtn.classList.remove('active');
});
