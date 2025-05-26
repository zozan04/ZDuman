 // Sayfa yüklendiğinde URL'den yemeği kontrol et ve yalnızca o yemeği göster
 window.onload = function() {
    const hash = window.location.hash.substring(1); // URL'deki hash kısmını al
    if (hash) {
        const items = document.querySelectorAll('.dish-item');
        items.forEach(item => {
            if (item.id !== hash) {
                item.style.display = 'none'; // Diğer yemekleri gizle
            }
        });
    }
};

//sepete ekleme
document.querySelectorAll(".add-to-cart").forEach(button => {
button.addEventListener("click", function () {
let mealId = this.getAttribute("data-dish-id");

fetch("sepetim.php", {
    method: "POST",
    headers: {
        "Content-Type": "application/x-www-form-urlencoded"
    },
    body: "meal_id=" + mealId
})
.then(response => response.json())
.then(data => {
    alert(data.message); // Kullanıcıya geri bildirim ver
})
.catch(error => console.error("Hata:", error));
});
});

function toggleLogoutMenu() {
    var menu = document.getElementById("logout-menu");
    if (menu.style.display === "none" || menu.style.display === "") {
        menu.style.display = "block";
    } else {
        menu.style.display = "none";
    }
}

// Sayfanın herhangi bir yerine tıklanınca menüyü kapatma
document.addEventListener("click", function (event) {
    var menu = document.getElementById("logout-menu");
    var icon = document.querySelector(".login-icon");
    
    if (!icon.contains(event.target) && !menu.contains(event.target)) {
        menu.style.display = "none";
    }
});
 // Tema değiştirme butonuna tıklama olayını dinleyin
 document.getElementById("light").addEventListener("click", function() {
    const body = document.body;
    const themeIcon = document.getElementById("themeIcon");
    const circle = document.querySelector(".light.circle");

    if (body.classList.contains("light-mode")) {
        // Karanlık moda geçiş
        body.classList.remove("light-mode");
        body.classList.add("dark-mode");
        themeIcon.classList.remove("fa-sun");
        themeIcon.classList.add("fa-moon");

        // Çemberin içini siyah yap, ay ikonunu beyaz yap
        circle.style.backgroundColor = "black";
        themeIcon.style.color = "white";
    } else {
        // Aydınlık moda geçiş
        body.classList.remove("dark-mode");
        body.classList.add("light-mode");
        themeIcon.classList.remove("fa-moon");
        themeIcon.classList.add("fa-sun");

        // Çemberin içini beyaz yap, güneş ikonunu siyah yap
        circle.style.backgroundColor = "white";
        themeIcon.style.color = "black";
    }
});
// Arama kutusunu açıp kapatma işlevi
function toggleSearchInput() {
    const searchBox = document.getElementById("search-box");
    searchBox.style.display = searchBox.style.display === "block" ? "none" : "block";
    if (searchBox.style.display === "block") {
        document.getElementById("searchInput").focus(); // Arama kutusuna odaklanma
    }
}

// Enter tuşuna basıldığında arama ve yönlendirme işlevi
document.getElementById("searchInput").addEventListener("keypress", function (event) {
    if (event.key === "Enter") {
        searchAndRedirect(event.target.value.toLowerCase());
    }
});

// Kategorilerin bulunduğu sayfanın URL'si
const categories = {
    "ana yemekler": {
        url: "ana_yemekler.html",
        dishes: [
            { name: "Ciğer Yahnisi", id: "ciger-yahnisi" },
            { name: "Baharatlı Piliç Külbastı", id: "baharatli-pilic-kulbasti" },
            { name: "Bonfile Kavurma", id: "bonfile-kavurma" },
            { name: "Köri Soslu Tavuk", id: "kori-soslu-tavuk" },
            { name: "Çökertme Kebabı", id: "cokertme-kebabi" },
            { name: "Beş Kardeş Yahnisi", id: "bes-kardes-yahnisi" },
            { name: "Kuzu Etli Enginar Yahni", id: "kuzu-etli-enginar-yahni" },
            { name: "Sakız Yahnisi", id: "sakiz-yahnisi" },
            { name: "Karnabahar Kavurması", id: "karnabahar-kavurmasi" },
            { name: "Kişniş Soslu Somon Balığı", id: "Kisnis-Soslu-Somon-Baligi" },
        ]
    },
    "sulu yemekler": {
        url: "sulu_yemekler.html",
        dishes: [
            { name: "Tarator", id: "tarator" },
            { name: "Mercimek Çorbası", id: "mercimek-corba" },
            { name: "Sebze Çorbası", id: "sebze-corba" }
        ]
    },
    "karbonhidrat lezzetleri": {
        url: "kuru_yemekler.html",
        dishes: [
            { name: "Pilav Üstü Döner", id: "pilav-ustu-doner" },
            { name: "Simit Kebabı", id: "simit-kebabi" },
            { name: "Sultan Kebabı", id: "sultan-kebabi" }
        ]
    },
    "aperatifler": {
        url: "aperatifler.html",
        dishes: [
            { name: "Zeytinyağlı Enginar", id: "zeytinyagli-enginar" },
            { name: "Humus", id: "humus" },
            { name: "Bruschetta", id: "bruschetta" }
        ]
    },
    "tatlilar": {
        url: "tatlilar.html",
        dishes: [
            { name: "Cheesecake", id: "cheesecake" },
            { name: "Baklava", id: "baklava" },
            { name: "Tiramisu", id: "tiramisu" }
        ]
    }
};

// Arama fonksiyonu
function searchAndRedirect(query) {
    // Kategori ve yemekleri kontrol et
    for (const category in categories) {
        if (category.includes(query)) {
            window.location.href = categories[category].url; // Kategori sayfasına yönlendir
            return;
        }
        const foundDish = categories[category].dishes.find(dish => dish.name.toLowerCase() === query);
        if (foundDish) {
            window.location.href = `${categories[category].url}#${foundDish.id}`; // Yemek ID'si ile yönlendirme
            return;
        }
    }
    alert("Aradığınız yemek veya kategori bulunamadı.");
}

//çıkış yapma
function toggleLogoutMenu() {
    var menu = document.getElementById("logout-menu");
    if (menu.style.display === "none" || menu.style.display === "") {
        menu.style.display = "block";
    } else {
        menu.style.display = "none";
    }
}

// Sayfanın herhangi bir yerine tıklanınca menüyü kapatma
document.addEventListener("click", function (event) {
    var menu = document.getElementById("logout-menu");
    var icon = document.querySelector(".login-icon");
    
    if (!icon.contains(event.target) && !menu.contains(event.target)) {
        menu.style.display = "none";
    }
});
// Sayfa yüklendiğinde URL'den yemeği kontrol et ve yalnızca o yemeği göster
window.onload = function() {
    const hash = window.location.hash.substring(1); // URL'deki hash kısmını al
    if (hash) {
        const items = document.querySelectorAll('.dish-item');
        items.forEach(item => {
            if (item.id !== hash) {
                item.style.display = 'none'; // Diğer yemekleri gizle
            }
        });
    }
};

document.querySelectorAll(".add-to-cart").forEach(button => {
button.addEventListener("click", function () {
let mealId = this.getAttribute("data-dish-id");

fetch("sepetim.php", {
    method: "POST",
    headers: {
        "Content-Type": "application/x-www-form-urlencoded"
    },
    body: "meal_id=" + mealId
})
.then(response => response.json())
.then(data => {
    alert(data.message); // Kullanıcıya geri bildirim ver
})
.catch(error => console.error("Hata:", error));
});
});


// Arttırma ve azaltma butonları için
document.querySelectorAll(".increase-btn").forEach(button => {
    button.addEventListener("click", function() {
        let mealId = this.closest('.cart-item').getAttribute('data-meal-id');
        updateCart(mealId, 'increase');
    });
});

document.querySelectorAll(".decrease-btn").forEach(button => {
    button.addEventListener("click", function() {
        let mealId = this.closest('.cart-item').getAttribute('data-meal-id');
        updateCart(mealId, 'decrease');
    });
});

function updateCart(mealId, action) {
    fetch("guest_cart.php", {
        method: "POST",
        headers: {
            "Content-Type": "application/x-www-form-urlencoded"
        },
        body: "action=" + encodeURIComponent(action) + "&meal_id=" + encodeURIComponent(mealId)
    })
    .then(response => response.json())
    .then(data => {
        console.log(data.message);
     updateTotalPrice(); // veya sayfayı yenilemeden DOM'u güncellemek isteyebilirsin
    })
    .catch(error => console.error("Hata:", error));
}

//sepette silme işlemleri
document.addEventListener('DOMContentLoaded', function() {
    const deleteButtons = document.querySelectorAll('.delete-btn');

    deleteButtons.forEach(function(button) {
        button.addEventListener('click', function() {
            const cartItem = this.closest('.cart-item');
            const mealId = cartItem.getAttribute('data-meal-id');

            fetch('', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/x-www-form-urlencoded',
                },
                body: `action=delete&meal_id=${mealId}`
            })
            .then(response => response.json())
            .then(data => {
                if (data.status === 'success') {
                    cartItem.remove();

                    const cartItems = document.querySelectorAll('.cart-item');
                    if (cartItems.length === 0) {
                        document.getElementById('cart-message').innerHTML = `
                            <p>Sepetinizde yemek yok.</p>
                            <a href="index.html#yemekler" class="add-dish-button">Yemek Eklemeye Başla</a>
                        `;
                    }
                } else {
                    alert('Bir hata oluştu.');
                }
            })
            .catch(error => {
                console.error('Hata:', error);
                alert('İşlem sırasında bir hata oluştu.');
            });
        });
    });
});







//not ekleme işlemi
document.addEventListener('DOMContentLoaded', function() {
    const addNoteButtons = document.querySelectorAll('.add-note-btn');
  

    addNoteButtons.forEach(function(button) {
        button.addEventListener('click', function() {
            const mealItem = this.closest('.cart-item');
            const mealId = mealItem.getAttribute('data-meal-id');
            const noteTextarea = mealItem.querySelector('.meal-note');
            const note = noteTextarea.value.trim();

            if (note!=null) {
                // Not ekleme işlemi
                fetch('sepetim.php', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/x-www-form-urlencoded',
                    },
                    body: `action=add_note&meal_id=${mealId}&note=${encodeURIComponent(note)}`
                })
                .then(response => response.json())
                .then(data => {
                    if (data.status === 'success') {
                        // Başarılı not ekleme mesajı
                        const successMessage = mealItem.querySelector('.note-success-message');
                        successMessage.textContent = "İsteklerinizi ekledik!";
                        successMessage.style.display = 'block';

                        // Text alanını temizle
                        noteTextarea.value = '';
                        
                        // 2 saniye sonra mesajı gizle
                        setTimeout(function() {
                            successMessage.style.display = 'none';
                        }, 2000); // 2000 ms = 2 saniye
                    } else {
                        alert('Bir hata oluştu. Lütfen tekrar deneyin.');
                    }
                })
                .catch(error => {
                    console.error('Hata:', error);
                    alert('İşlem sırasında bir hata oluştu.');
                });
            } else {
                alert('Lütfen bir not girin.');
            }
        });
    });



    // Güncelle butonunun işlevselliği
      
   const updateNoteButtons = document.querySelectorAll('.update-note-btn');

updateNoteButtons.forEach(function(button) {
    button.addEventListener('click', function() {
        const mealItem = this.closest('.cart-item');
        const mealId = mealItem.getAttribute('data-meal-id');
        const noteTextarea = mealItem.querySelector('.meal-note');
        const note = noteTextarea.value.trim();

        if (note !== "") {
            // Not güncelleme işlemi
            fetch('sepetim.php', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/x-www-form-urlencoded',
                },
                body: `action=update_note&meal_id=${mealId}&note=${encodeURIComponent(note)}`
            })
            .then(response => response.json())
            .then(data => {
                if (data.status === 'success') {
                    const successMessage = mealItem.querySelector('.note-success-message');
                    successMessage.textContent = "İsteklerinizi güncelledik!";
                    successMessage.style.display = 'block';

                    noteTextarea.value = '';

                    setTimeout(function() {
                        successMessage.style.display = 'none';
                    }, 2000);
                } else {
                    alert(data.message);
                }
            })
            .catch(error => {
                console.error('Hata:', error);
                alert('Güncelleme sırasında bir hata oluştu.');
            });
        } else {
            alert('Güncellemek için boş olmayan bir not girin.');
        }
    });
});

});

                          //sipariş işlemeleri
// Toplam fiyatı güncelle
function updateTotalPrice() {
    let total = 0;
    const cartItems = document.querySelectorAll('.cart-item');
    const shippingCost = 2; // Sabit kargo ücreti (2 TL)

    cartItems.forEach(item => {
        const priceText = item.querySelector('.cart-item-price').innerText; // Örneğin: "35,00 ₺"
        const quantity = parseInt(item.querySelector('.cart-item-quantity').innerText);
        
        // Türkçe formatta , ve . farklıdır, o yüzden değiştireceğiz:
        const price = parseFloat(priceText.replace(',', '.').replace(' ₺', ''));

        total += price * quantity;
    });

    // Kargo ücretini ekle
    total += shippingCost;

    // Toplam fiyatı güncelle
    document.getElementById('total-price').innerText = total.toFixed(2).replace('.', ',') + '₺';
    document.getElementById('shipping-cost').innerText = shippingCost.toFixed(2).replace('.', ',') + '₺'; // Kargo ücreti
    
}

// Adet artırma/azaltma ve silme olaylarını ekle
document.addEventListener('DOMContentLoaded', function() {
    document.querySelectorAll('.increase-btn').forEach(btn => {
        btn.addEventListener('click', function() {
            const quantitySpan = this.previousElementSibling;
            quantitySpan.innerText = parseInt(quantitySpan.innerText) + 1;
            updateTotalPrice();
        });
    });

    document.querySelectorAll('.decrease-btn').forEach(btn => {
        btn.addEventListener('click', function() {
            const quantitySpan = this.nextElementSibling;
            let quantity = parseInt(quantitySpan.innerText);
            if (quantity > 1) {
                quantitySpan.innerText = quantity - 1;
                updateTotalPrice();
            }
        });
    });

    document.querySelectorAll('.delete-btn').forEach(btn => {
        btn.addEventListener('click', function() {
            const cartItem = this.closest('.cart-item');
            cartItem.remove();
            updateTotalPrice();
        });
    });

    // Sayfa ilk yüklenince toplamı hesapla
    updateTotalPrice();
});

// Siparişi Tamamla butonuna tıklandığında adres ve ödeme bilgilerini göster
document.querySelector('.complete-order-btn').addEventListener('click', function() {
    // Adres ve ödeme bilgilerini içeren div'i göster
    const addressPaymentInfo = document.getElementById('address-payment-info');
    addressPaymentInfo.style.display = 'block';

    // Sipariş özeti bölümünü gizleyebilirsin (isteğe bağlı)
    // this.closest('.order-summary').style.display = 'none';
});

//burada kredi kartı 4 lü halde ayrılır
const ccInput = document.getElementById('credit-card-number');

ccInput.addEventListener('input', function(e) {
    let value = e.target.value.replace(/\D/g, ''); // Sadece rakamları al
    value = value.substring(0, 16); // En fazla 16 rakam

    // 4'lü gruplara ayır ve boşluk ekle
    const formattedValue = value.replace(/(.{4})/g, '$1 ').trim();

    e.target.value = formattedValue;
});

//adres kaydetme işlemleri
const savedAddressSelect = document.getElementById('saved-address');
const addressTextarea = document.getElementById('address');
const addAddressBtn = document.getElementById('add-address-btn');

// Yeni adresi kaydet
addAddressBtn.addEventListener('click', function () {
    const newAddress = addressTextarea.value.trim();
    if (newAddress) {
        const xhr = new XMLHttpRequest();
        xhr.open("POST", "sepetim.php", true);
        xhr.setRequestHeader("Content-Type", "application/x-www-form-urlencoded");

        xhr.onload = function () {
            if (xhr.status === 200) {
                const response = xhr.responseText.trim();
                if (response.startsWith("Adres başarıyla kaydedildi")) {
                    location.reload(); // ✅ Başarıyla kayıt olduysa sayfayı yenile
                } else {
                    alert(response); // ⚠️ Sunucudan gelen hata mesajı varsa göster
                }
            } else {
                alert("Bir hata oluştu.");
            }
        };

        xhr.send("address=" + encodeURIComponent(newAddress));
    } else {
        alert("Adres alanı boş bırakılamaz.");
    }
});

// Seçili adres textarea’ya gelsin
savedAddressSelect.addEventListener('change', function () {
    addressTextarea.value = this.value || '';
});

// Adres silme butonuna tıklanma olayı
const deleteAddressBtn = document.getElementById('delete-address-btn');
const successMessageDiv = document.getElementById('success-message'); // Success message div

deleteAddressBtn.addEventListener('click', function () {
    const selectedAddress = savedAddressSelect.value;

    if (!selectedAddress) {
        alert("Lütfen silinecek bir adres seçin.");
        return;
    }

    // Ajax isteği ile adres silme
    const xhr = new XMLHttpRequest();
    xhr.open("POST", "sepetim.php", true);
    xhr.setRequestHeader("Content-Type", "application/x-www-form-urlencoded");

    xhr.onload = function () {
        if (xhr.status === 200) {
            const response = xhr.responseText.trim();
            if (response.startsWith("Adres başarıyla silindi")) {
                successMessageDiv.style.display = 'block'; // Mesajı göster
                successMessageDiv.textContent = "Adres başarıyla silindi."; // Mesajı yaz
                // Sayfayı yeniden yüklememek için işlemi bitir
                setTimeout(function() {
                    location.reload(); // Sayfayı yenile
                }, 2000); // 2 saniye sonra sayfayı yenile
            } else {
                alert(response); // Hata varsa göster
            }
        } else {
            alert("Bir hata oluştu.");
        }
    };

    xhr.send("delete_address=" + encodeURIComponent(selectedAddress));
});





