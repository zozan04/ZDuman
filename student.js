  /* Açılır menüyü aç/kapat */
  function toggleDropdown() {
    document.getElementById("myDropdown").classList.toggle("show");
}

// Kullanıcı ekranın herhangi bir yerine tıkladığında açılır menüyü kapat
window.onclick = function(event) {
    if (!event.target.matches('.dropbtn')) {
        var dropdowns = document.getElementsByClassName("dropdown-content");
        var i;
        for (i = 0; i < dropdowns.length; i++) {
            var openDropdown = dropdowns[i];
            if (openDropdown.classList.contains('show')) {
                openDropdown.classList.remove('show');
            }
        }
    }
}

// Sayfa yüklendikten sonra mesajı 2 saniye sonra gizle
window.onload = function() {
    setTimeout(function() {
        const message = document.getElementById('message');
        if (message) {
            message.style.display = 'none';
        }
    }, 2000); // 2000 milisaniye = 2 saniye
}

// çıkış yapma işlevi
// Logout menüsünü göster-gizle
function toggleLogoutMenu() {
    var logoutMenu = document.getElementById('logout-menu');
    // Menüyü görünür yapmak
    logoutMenu.style.display = (logoutMenu.style.display === 'block') ? 'none' : 'block';
}

// Çıkış yap işlemi
function logout() {
    // Kullanıcıyı login sayfasına yönlendir
    window.location.href = "login.php";
}




// Kontrol Et menüsü için tıklama ile aç/kapat
document.addEventListener('DOMContentLoaded', function () {
    const dropbtn = document.querySelector('.dropbtn');
    const dropdownContent = document.querySelector('.dropdown-content');

    dropbtn.addEventListener('click', function (e) {
        e.stopPropagation(); // Menü dışı tıklamayı engelle
        dropdownContent.classList.toggle('show');
    });
});
// Giriş ikonu için logout menüsünü açma/kapama
function toggleLogoutMenu() {
    const logoutMenu = document.getElementById('logout-menu');
    logoutMenu.classList.toggle('show');
}

// Ayarlar modalını açma
function openSettingsModal() {
    document.getElementById('settingsModal').style.display = 'block';
}

// Ayarlar modalını kapama
function closeSettingsModal() {
    document.getElementById('settingsModal').style.display = 'none';
}

// Menüyü kapatmak için tıklama dışarıya yapıldığında
window.addEventListener('click', function(event) {
    const dropdownContent = document.querySelector('.dropdown-content');
    const logoutMenu = document.getElementById('logout-menu');
    if (!event.target.closest('.dropdown') && dropdownContent.classList.contains('show')) {
        dropdownContent.classList.remove('show');
    }
    if (!event.target.closest('.login-containers') && logoutMenu.classList.contains('show')) {
        logoutMenu.classList.remove('show');
    }
});


//profil güncelleme
document.getElementById("updateStudentForm").addEventListener("submit", function(e) {
    e.preventDefault(); // Sayfanın yenilenmesini engelle

    const form = e.target;
    const formData = new FormData(form);

    fetch("update_settings.php", {
        method: "POST",
        body: formData
    })
    .then(res => res.json())
    .then(data => {
        const messageDiv = document.getElementById("updateMessage");
        messageDiv.style.color = data.success ? "" : "";
        messageDiv.textContent = data.message;

        if (data.success) {
            // Gerekirse alanları sıfırla veya modalı kapat
            // closeSettingsModal(); // otomatik kapatma istenirse
        }
    })
    .catch(error => {
        document.getElementById("updateMessage").textContent = "Bir hata oluştu.";
        console.error("Hata:", error);
    });
});


//başarı mesajı
function showUpdateMessage(message, success = true) {
    var messageDiv = document.getElementById('updateMessage');
    messageDiv.textContent = message;

    // Mesajın rengini belirle
    messageDiv.style.color = success ? '' : '';
    
    // Mesajı göster
    messageDiv.style.display = 'block';

    // 2 saniye sonra mesajı kaybettir
    setTimeout(function() {
        messageDiv.classList.add('fadeOut');
        
        // Animasyon bitince mesajı tamamen gizle
        setTimeout(function() {
            messageDiv.style.display = 'none';
            messageDiv.classList.remove('fadeOut'); // Animasyon sonrası temizle
        }, 1000); // Animasyon süresiyle aynı
    }, 2000); // 2 saniye sonra kaybolmaya başlasın
}


// Form gönderildiğinde bu fonksiyonu çalıştır
document.getElementById("updateStudentForm").addEventListener("submit", function(event) {
    event.preventDefault(); // Formun normal gönderimini engelle

    var formData = new FormData(this); // Form verilerini al

    // AJAX isteği
    var xhr = new XMLHttpRequest();
    xhr.open("POST", "update_settings.php", true);
    xhr.setRequestHeader("Accept", "application/json");
    
    xhr.onload = function() {
        var response = JSON.parse(xhr.responseText);

        // Eğer başarıyla güncellendiyse
        if (response.success) {
            showUpdateMessage(response.message, true); // Başarı mesajını göster
        } else {
            showUpdateMessage(response.message, false); // Hata mesajını göster
        }
    };
    xhr.send(formData); // Form verilerini gönder
});
