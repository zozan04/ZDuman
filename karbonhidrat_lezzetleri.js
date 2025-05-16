
// Aydınlık ve Karanlık Mod Ayarı
document.getElementById("light").addEventListener("click", function() {
    const body = document.body;
    const themeIcon = document.getElementById("themeIcon");
    const circle = document.querySelector("#light .circle");

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

// Filtreleme kutusunu göster/gizle
function toggleFilterInput() {
    const filterText = document.querySelector(".filter-icon .filter-text");
    const filterBox = document.getElementById("filter-box"); // Filtre kutusunun ID'si

    // Filtre kutusunu göster veya gizle
    if (filterBox.style.display === "none" || filterBox.style.display === "") {
        filterBox.style.display = "block"; // Filtre kutusunu göster
        filterText.style.display = "none"; // "Filtrele" yazısını gizle
        document.getElementById("filterInput").focus(); // Giriş kutusuna odaklan
    } else {
        filterBox.style.display = "none"; // Filtre kutusunu gizle
        filterText.style.display = "inline"; // "Filtrele" yazısını göster
    }
}

// Yemekleri filtreleme işlevi
function filterDishes() {
    const filterInput = document.getElementById("filterInput").value.toLowerCase(); // Filtreleme girişini al
    const dishItems = document.querySelectorAll(".dish-item"); // Tüm yemek öğelerini al

    dishItems.forEach(item => {
        const title = item.querySelector(".dish-title").textContent.toLowerCase(); // Başlığı al
        // Başlık, filtreleme girişine göre eşleşiyorsa göster, aksi takdirde gizle
        item.style.display = title.includes(filterInput) ? "block" : "none";
    });
}

// Sayfa yenilendiğinde varsayılan olarak "Filtrele" yazısını geri getir
window.addEventListener("load", () => {
    const filterText = document.querySelector(".filter-icon .filter-text");
    const filterBox = document.getElementById("filter-box");

    filterText.style.display = "inline";
    filterBox.style.display = "none"; // Filtre kutusunu başlangıçta gizle
});
document.addEventListener('click', function(event) {
    const filterBox = document.getElementById("filter-box");
    const filterIcon = document.querySelector(".filter-icon");

    if (!filterIcon.contains(event.target) && filterBox.style.display === "block") {
        filterBox.style.display = "none"; // Eğer kutunun dışında tıklanırsa kutuyu gizle
        document.querySelector(".filter-icon .filter-text").style.display = "inline"; // "Filtrele" yazısını göster
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
        url: "ana_yemekler.php",
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
        url: "sulu_yemekler.php",
        dishes: [
            { name: "Tarator", id: "tarator" },
            { name: "Mercimek Çorbası", id: "mercimek-corba" },
            { name: "Sebze Çorbası", id: "sebze-corba" }
        ]
    },
    "karbonhidrat lezzetleri": {
        url: "karbonhidrat_lezzetleri.php",
        dishes: [
            { name: "Pilav Üstü Döner", id: "pilav-ustu-doner" },
            { name: "Simit Kebabı", id: "simit-kebabi" },
            { name: "Sultan Kebabı", id: "sultan-kebabi" }
        ]
    },
    "aperatifler": {
        url: "aperatifler.php",
        dishes: [
            { name: "Zeytinyağlı Enginar", id: "zeytinyagli-enginar" },
            { name: "Humus", id: "humus" },
            { name: "Bruschetta", id: "bruschetta" }
        ]
    },
    "tatlı çeşitleri": {
        url: "tatli_cesitleri.php",
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


//Sepete ekleme işlemi
document.querySelectorAll('.add-to-cart').forEach(button => {
    button.addEventListener('click', function(event) {
        event.preventDefault(); // Varsayılan davranışı engelle

        // Yemek bilgilerini al
        const dishId = this.getAttribute('data-dish-id');
        const dishName = this.getAttribute('data-dish-name');
        const dishPrice = this.getAttribute('data-dish-price');

        // Sepete ekleme işlemi
        let cart = JSON.parse(localStorage.getItem('cart')) || []; // Sepeti al ya da yeni bir dizi oluştur
        cart.push({ id: dishId, name: dishName, price: dishPrice }); // Yeni öğeyi ekle
        localStorage.setItem('cart', JSON.stringify(cart)); // Sepeti güncelle

        // Başarılı ekleme mesajı göster
        showMessage(dishId);
    });
});

// Mesajı göstermek için bir fonksiyon
function showMessage(dishId) {
    const dishItem = document.getElementById(dishId); // İlgili yemek öğesini al
    const messageBox = document.createElement('div');
    messageBox.textContent = 'Sepete Eklendi!';
    messageBox.className = 'message-box'; // Mesaj kutusuna sınıf ekle
    dishItem.appendChild(messageBox); // Mesaj kutusunu yemek öğesine ekle

    // Mesajı 3 saniye sonra kaldır
    setTimeout(() => {
        messageBox.remove();
    }, 3000);
}

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








